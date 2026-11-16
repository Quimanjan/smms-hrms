<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use App\Models\Application;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\JobOpening;
use App\Models\LeaveRequest;
use App\Models\LoanRequest;
use App\Models\NonWorkingDay;
use App\Models\OffboardingCase;
use App\Models\TimeCorrection;
use App\Models\User;
use App\Support\PayPeriod;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Sample data for showing the system to the client.
 *
 *   php artisan demo:data          adds the sample data (safe to run twice)
 *   php artisan demo:data --clear  removes everything it added and puts back blanks it filled
 *
 * Everything added is recorded in storage/app/demo-data.json so --clear removes exactly that.
 * Existing records are only filled where a field is empty; nothing real is overwritten.
 */
class DemoData extends Command
{
    protected $signature = 'demo:data {--clear : Remove the sample data}';

    protected $description = 'Add (or remove) sample data for demonstrating the system';

    private const LEDGER = 'demo-data.json';

    private array $ledger = ['created' => [], 'filled' => []];

    public function handle(): int
    {
        $this->ledger = json_decode(Storage::disk('local')->get(self::LEDGER) ?? '', true) ?: $this->ledger;

        if ($this->option('clear')) {
            return $this->clear();
        }
        if (! empty($this->ledger['created'])) {
            $this->warn('Sample data is already in the system. Run "php artisan demo:data --clear" first to start over.');

            return self::SUCCESS;
        }

        DB::transaction(function () {
            $this->fillExistingEmployees();
            $extra = $this->addEmployees();
            $this->addHolidaysAndProctoring();
            $this->addAttendance();
            $this->confirmDtrs();
            $this->addRequests($extra);
            $this->addApplicants();
            $this->addAnnouncements();
        });

        Storage::disk('local')->put(self::LEDGER, json_encode($this->ledger, JSON_PRETTY_PRINT));
        \App\Support\SiteContent::forget();

        foreach ($this->ledger['created'] as $table => $ids) {
            $this->line(sprintf('  %-26s %d added', $table, count($ids)));
        }
        $this->line(sprintf('  %-26s %d employees', 'details filled in', count($this->ledger['filled'])));
        $this->info('Sample data added.');

        return self::SUCCESS;
    }

    /* ------------------------------------------------------------------ */

    private function track(string $table, int $id): void
    {
        $this->ledger['created'][$table][] = $id;
    }

    private function currentEmployees()
    {
        return Employee::query()->excludeInternalStaff()->current()->orderBy('id')->get();
    }

    /** Fills only empty details on the employees already in the system. */
    private function fillExistingEmployees(): void
    {
        $hired = ['2012-06-04', '2016-06-01', '2019-07-15', '2021-06-07', '2015-06-01', '2023-06-05'];
        $education = [
            'tertiary' => ['course' => 'Bachelor of Elementary Education', 'school' => 'Cavite State University', 'years' => '2006 - 2010'],
            'senior_high' => ['strand' => 'General Academic Strand', 'school' => 'Kawit National High School', 'years' => '2004 - 2006'],
        ];
        $defaults = [
            'civil_status' => 'Single',
            'address' => '25 Mabini Street, , Poblacion, Kawit, Cavite, 4104',
            'emergency_name' => 'Rosa Santos',
            'emergency_contact' => '09181234567',
            'sss_ee' => 1000, 'sss_er' => 2030,
            'philhealth_ee' => 625, 'philhealth_er' => 625,
            'pagibig_ee' => 200, 'pagibig_er' => 200,
        ];

        foreach ($this->currentEmployees() as $i => $e) {
            $filled = [];
            $values = $defaults + ['date_hired' => $hired[$i % count($hired)], 'education' => $education];
            foreach ($values as $field => $value) {
                if (blank($e->{$field})) {
                    // Keep the stored value exactly (it may be an empty or unreadable encrypted string).
                    $this->ledger['raw'][$e->id][$field] = $e->getRawOriginal($field);
                    $e->{$field} = $value;
                    $filled[] = $field;
                }
            }
            // A sample birthday when the one on file makes the employee younger than 18.
            if ($e->birthday && Carbon::parse($e->birthday)->age < 18) {
                $this->ledger['birthdays'][$e->id] = Carbon::parse($e->birthday)->toDateString();
                $e->birthday = Carbon::parse($e->birthday)->setYear(1990);
            }
            if ($filled) {
                $e->save();
                $this->ledger['filled'][$e->id] = $filled;
            }
        }
    }

    /** Three sample staff without portal accounts, so lists and payroll have more than a handful of names. */
    private function addEmployees(): array
    {
        $people = [
            ['Ana', 'Reyes', 'Santos', 'Female', 'Teacher I', 'Elementary', 'Teaching Staff', 'regular', 3, '1991-02-14', '2017-06-05'],
            ['Mark', 'Lopez', 'Villanueva', 'Male', 'Senior High Teacher', 'Senior High School', 'Teaching Staff', 'non regular', 1, '1996-09-03', '2025-06-02'],
            ['Liza', 'Cruz', 'Domingo', 'Female', 'Utility Staff', 'Maintenance', 'Maintenance Staff', 'regular', 2, '1985-11-21', '2014-07-01'],
        ];
        $made = [];
        foreach ($people as [$first, $middle, $last, $gender, $position, $dept, $roleType, $status, $step, $birthday, $hired]) {
            $id = Employee::generateEmployeeId();
            $e = (new Employee)->forceFill([
                'employee_id' => $id,
                'zkteco_user_id' => $id,
                'first_name' => $first, 'middle_name' => $middle, 'last_name' => $last,
                'name' => "$first $middle $last",
                'position' => $position, 'department' => $dept, 'role_type' => $roleType,
                'employment_status' => $status, 'step_no' => $step,
                'email' => strtolower("$first.$last").'@demo.smms.local',
                'phone' => '0917'.random_int(1000000, 9999999),
                'birthday' => $birthday, 'gender' => $gender, 'civil_status' => 'Married',
                'address' => '8 Rizal Street, , Tabon, Kawit, Cavite, 4104',
                'emergency_name' => 'Family contact', 'emergency_contact' => '09171112222',
                'date_hired' => $hired,
                'education' => ['tertiary' => ['course' => 'Bachelor of Secondary Education', 'school' => 'De La Salle University Dasmariñas', 'years' => '2009 - 2013']],
                'sss_ee' => 900, 'sss_er' => 1830, 'philhealth_ee' => 550, 'philhealth_er' => 550, 'pagibig_ee' => 200, 'pagibig_er' => 200,
            ]);
            $e->save();
            $this->track('employees', $e->id);
            $made[] = $e;
        }

        return $made;
    }

    private function addHolidaysAndProctoring(): void
    {
        $holidays = [
            ['2026-10-30', 'Sample local holiday (demo)', 'Local Holiday'],
            ['2026-11-30', 'Bonifacio Day', 'National Holiday'],
            ['2026-12-08', 'Feast of the Immaculate Conception', 'National Holiday'],
            ['2026-12-25', 'Christmas Day', 'National Holiday'],
            ['2026-12-30', 'Rizal Day', 'National Holiday'],
        ];
        foreach ($holidays as [$date, $name, $category]) {
            if (NonWorkingDay::whereDate('date', $date)->exists()) {
                continue;
            }
            $d = NonWorkingDay::create(['date' => $date, 'name' => $name, 'category' => $category,
                'pay_type' => NonWorkingDay::PAID, 'paid_days' => 1, 'paid_hours' => 8]);
            $this->track('non_working_days', $d->id);
        }
        foreach (['2026-10-27' => 'Midterm exam proctoring (demo)', '2026-10-28' => 'Midterm exam proctoring (demo)'] as $date => $name) {
            if (DB::table('proctoring_days')->whereDate('date', $date)->exists()) {
                continue;
            }
            $this->track('proctoring_days', DB::table('proctoring_days')->insertGetId(['date' => $date, 'name' => $name, 'created_at' => now(), 'updated_at' => now()]));
        }
    }

    /** Weekday logs from October 21 to yesterday, a few late days, one absence each, the sample holiday paid. */
    private function addAttendance(): void
    {
        $end = now(config('app.display_timezone'))->subDay()->toDateString();
        $holidays = NonWorkingDay::pluck('date')->map(fn ($d) => Carbon::parse($d)->toDateString())->all();

        foreach ($this->currentEmployees() as $k => $e) {
            $absentOn = Carbon::parse('2026-11-04')->addDays($k % 3)->toDateString();
            foreach (CarbonPeriod::create('2026-10-21', $end) as $day) {
                if ($day->isWeekend()) {
                    continue;
                }
                $date = $day->toDateString();
                if (Attendance::where('employee_id', $e->id)->whereDate('date', $date)->exists()) {
                    continue;
                }
                if (in_array($date, $holidays, true)) {
                    $row = ['time_in' => null, 'time_out' => null, 'hours_worked' => 8, 'status' => Attendance::PAID_HOLIDAY];
                } elseif ($date === $absentOn) {
                    $row = ['time_in' => null, 'time_out' => null, 'hours_worked' => 0, 'status' => Attendance::ABSENT];
                } else {
                    // Times follow the employee's real schedule (7:00 to 4:30, or 6:00 to 3:00 for maintenance).
                    $sched = \App\Support\AttendanceSchedule::forEmployee($e->role_type, $e->position, \Illuminate\Support\Carbon::parse($date));
                    $start = \Illuminate\Support\Carbon::parse($date.' '.$sched->start);
                    $end = \Illuminate\Support\Carbon::parse($date.' '.$sched->end);
                    $seed = crc32($e->id.$date);
                    $in = $seed % 7 === 0 ? $start->copy()->addMinutes(15 + $seed % 20) : $start->copy()->subMinutes(20)->addMinutes($seed % 28);
                    $out = $seed % 11 === 0 ? $end->copy()->subMinutes(30) : $end->copy()->addMinutes($seed % 12);
                    $late = $sched->lateMinutes($in) > 0;
                    $under = $sched->undertimeMinutes($out) > 0;
                    $status = $late ? Attendance::LATE : ($under ? Attendance::UNDERTIME : Attendance::PRESENT);
                    $row = ['time_in' => $in->format('H:i:s'), 'time_out' => $out->format('H:i:s'),
                        'hours_worked' => $sched->hoursWorked($in, $out), 'status' => $status];
                }
                $a = Attendance::create(['employee_id' => $e->id, 'date' => $date, 'archived' => false] + $row);
                $this->track('attendance_records', $a->id);
            }
        }
    }

    /** Everyone except the first employee with a portal account confirms the Oct 21 to Nov 5 DTR, so the confirm step can be shown live. */
    private function confirmDtrs(): void
    {
        $keepForDemo = $this->currentEmployees()->firstWhere(fn ($e) => $e->user_id && Str::contains($e->name, 'Juan'))
            ?? $this->currentEmployees()->firstWhere(fn ($e) => $e->user_id && ! $e->user?->hasAnyRole(['Admin', 'HR', 'Accounting']));
        foreach ($this->currentEmployees() as $e) {
            if ($keepForDemo && $e->id === $keepForDemo->id) {
                continue;
            }
            if (DB::table('dtr_confirmations')->where(['employee_id' => $e->id, 'period' => '2026-11', 'cutoff' => '15th'])->exists()) {
                continue;
            }
            $this->track('dtr_confirmations', DB::table('dtr_confirmations')->insertGetId([
                'employee_id' => $e->id, 'period' => '2026-11', 'cutoff' => '15th',
                'confirmed_at' => '2026-11-06 09:15:00', 'created_at' => now(), 'updated_at' => now(),
            ]));
        }
    }

    /** Pending requests waiting for HR, so each review screen has something to approve or reject. */
    private function addRequests(array $extra): void
    {
        $withLogin = $this->currentEmployees()->filter(fn ($e) => $e->user_id && ! $e->user?->hasAnyRole(['Admin', 'HR', 'Accounting']))->values();
        $staff = $withLogin->first() ?? $this->currentEmployees()->first();

        if ($staff && $staff->user_id) {
            $l = LeaveRequest::create([
                'user_id' => $staff->user_id, 'employee_code' => $staff->employee_id, 'employee_name' => $staff->name,
                'department' => $staff->department, 'type' => 'Sick Leave', 'requested_date' => now()->toDateString(),
                'start_date' => '2026-11-23', 'end_date' => '2026-11-23', 'half_day' => true, 'half_day_period' => 'am',
                'reason' => 'Check-up at the health center in the morning.', 'leave_balance' => '3',
                'status' => LeaveRequest::PENDING, 'hr_status' => LeaveRequest::PENDING, 'admin_status' => LeaveRequest::PENDING,
            ]);
            $this->track('leave_requests', $l->id);
        }
        if ($staff) {
            $t = TimeCorrection::create([
                'employee_id' => $staff->id, 'correction_date' => '2026-11-10', 'correction_type' => 'Time In',
                'incorrect_time' => '08:20', 'correct_time' => '07:50',
                'note' => 'The biometric did not read my finger on the first try.',
                'hr_status' => TimeCorrection::PENDING, 'admin_status' => TimeCorrection::PENDING,
            ]);
            $this->track('time_corrections', $t->id);
            $loan = LoanRequest::create(['employee_id' => $staff->id, 'loan_type' => 'Cash Advance', 'amount' => 3000,
                'reason' => 'School supplies for my children.', 'requested_at' => now()]);
            $this->track('loan_requests', $loan->id);
        }
        if ($leaving = collect($extra)->last()) {
            $c = OffboardingCase::create(['employee_id' => $leaving->id, 'reason' => 'Resignation',
                'last_working_day' => '2026-12-15', 'employee_message' => 'Moving to the province to be with family.',
                'status' => 'Open', 'progress' => 10]);
            $this->track('offboarding_cases', $c->id);
        }
    }

    /** Two applicants with uploaded files, one waiting for HR and one with an interview set. */
    private function addApplicants(): void
    {
        $openings = JobOpening::orderBy('id')->get();
        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj 2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj 3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 612 792]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";
        $people = [
            ['Carla', 'Mendoza', 'Bautista', 'Female', 'Single', "Bachelor's Degree", 'Philippine Normal University', '2022', 'pending', null],
            ['Paolo', 'Garcia', 'Ramos', 'Male', 'Married', "Bachelor's Degree", 'Cavite State University', '2019', 'pending', '2026-11-20'],
        ];
        foreach ($people as $i => [$first, $middle, $last, $gender, $civil, $edu, $school, $year, $status, $interview]) {
            $email = strtolower("$first.$last").'@demo.smms.local';
            $user = User::firstOrCreate(['email' => $email], ['name' => "$first $last", 'password' => Hash::make(Str::random(40))]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->syncRoles(['Applicant']);
            $this->track('users', $user->id);

            $files = [];
            foreach (['resume', 'coverletter', 'appletter'] as $doc) {
                $path = "applications/{$user->id}/$doc.pdf";
                Storage::disk(Application::DOCUMENT_DISK)->put($path, $pdf);
                $files[$doc] = $path;
                $files[$doc.'_original_name'] = ucfirst($doc).' - '.$last.'.pdf';
            }
            $opening = $openings[$i % max(1, $openings->count())] ?? null;
            $a = Application::create([
                'user_id' => $user->id, 'fullname' => "$first $middle $last",
                'first_name' => $first, 'middle_name' => $middle, 'last_name' => $last,
                'email' => $email, 'phone' => '0918'.random_int(1000000, 9999999),
                'home_address' => 'Binakayan, Kawit, Cavite', 'birth_date' => (1990 + $i * 3).'-05-12',
                'gender' => $gender, 'civil_status' => $civil, 'education' => $edu,
                'education_school' => $school, 'education_year' => $year,
                'job_type' => 'Full-time', 'position' => $opening?->title ?? 'Teacher I',
                'status' => $status,
                'interview_date' => $interview, 'interview_time' => $interview ? '09:00' : null,
                'interview_location' => $interview ? "Principal's Office" : null,
            ] + $files);
            $this->track('applications', $a->id);
        }
    }

    private function addAnnouncements(): void
    {
        $by = User::role(['HR'])->value('id');
        foreach ([
            ['Enrollment for School Year 2027 to 2028', "Early enrollment opens on January 5, 2027 at the Registrar's Office, 8:00 AM to 4:00 PM, Monday to Friday. Bring the learner's report card and PSA birth certificate.", '2026-11-16'],
            ['We are hiring teachers', 'St. Mary Magdalene School is looking for licensed teachers for Elementary and Senior High School. See the open positions below and apply online.', '2026-11-10'],
        ] as [$title, $body, $date]) {
            $a = Announcement::create(['title' => $title, 'body' => $body, 'posted_on' => $date, 'is_published' => true, 'created_by' => $by]);
            $this->track('announcements', $a->id);
        }
    }

    /* ------------------------------------------------------------------ */

    private function clear(): int
    {
        if (empty($this->ledger['created']) && empty($this->ledger['filled'])) {
            $this->info('There is no sample data to remove.');

            return self::SUCCESS;
        }

        DB::transaction(function () {
            $c = $this->ledger['created'];
            foreach (Application::whereIn('id', $c['applications'] ?? [])->get() as $a) {
                Storage::disk(Application::DOCUMENT_DISK)->deleteDirectory('applications/'.$a->user_id);
            }
            $order = ['announcements', 'applications', 'offboarding_cases', 'loan_requests', 'time_corrections', 'leave_requests',
                'dtr_confirmations', 'attendance_records', 'proctoring_days', 'non_working_days'];
            foreach ($order as $table) {
                if (! empty($c[$table])) {
                    DB::table($table)->whereIn('id', $c[$table])->delete();
                }
            }
            // Payroll made from sample employees during the demo goes with them.
            if (! empty($c['employees'])) {
                foreach (['payslips', 'payroll_records', 'attendance_records', 'dtr_confirmations', 'loan_requests', 'time_corrections', 'offboarding_cases'] as $t) {
                    if (\Illuminate\Support\Facades\Schema::hasColumn($t, 'employee_id')) {
                        DB::table($t)->whereIn('employee_id', $c['employees'])->delete();
                    }
                }
                DB::table('employees')->whereIn('id', $c['employees'])->delete();
            }
            if (! empty($c['users'])) {
                DB::table('model_has_roles')->whereIn('model_id', $c['users'])->where('model_type', User::class)->delete();
                DB::table('users')->whereIn('id', $c['users'])->delete();
            }
            $required = ['address', 'emergency_name', 'emergency_contact'];
            foreach ($this->ledger['filled'] as $id => $fields) {
                $restore = [];
                foreach ($fields as $f) {
                    $restore[$f] = array_key_exists($f, $this->ledger['raw'][$id] ?? [])
                        ? $this->ledger['raw'][$id][$f]
                        : (in_array($f, $required, true) ? '' : null);
                }
                DB::table('employees')->where('id', $id)->update($restore);
            }
            foreach ($this->ledger['birthdays'] ?? [] as $id => $date) {
                Employee::whereKey($id)->update(['birthday' => $date]);
            }
        });

        Storage::disk('local')->delete(self::LEDGER);
        \App\Support\SiteContent::forget();
        $this->info('Sample data removed.');

        return self::SUCCESS;
    }
}
