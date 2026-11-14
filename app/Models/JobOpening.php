<?php

// app/Models/JobOpening.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class JobOpening extends Model
{
    protected $fillable = ['title', 'job_type', 'description', 'is_open'];

    protected $casts = [
        'is_open' => 'boolean',
    ];

    public function filterType(): string
    {
        if (in_array($this->job_type, ['teaching', 'non-teaching', 'teaching-related'], true)) {
            return $this->job_type;
        }

        $title = mb_strtolower($this->title);
        $normalizedTitle = preg_replace('/[^[:alnum:]]+/u', ' ', $title) ?? $title;

        foreach ([
            'teaching assistant',
            'teacher assistant',
            'assistant teacher',
            'teaching aide',
            'teacher aide',
            'instructional assistant',
            'learning support',
            'teaching related',
            'tutor',
        ] as $teachingRelatedTerm) {
            if (str_contains($normalizedTitle, $teachingRelatedTerm)) {
                return 'teaching-related';
            }
        }

        return str_contains($normalizedTitle, 'teach') ? 'teaching' : 'non-teaching';
    }

    /**
     * Shape expected by the partial: job details and applicant counts.
     *
     * Applicants are rows in `applications` whose `position` column holds the
     * job title (applications are kept when an opening is deleted, so they are
     * matched by title, not by foreign key).
     */
    public static function withApplicantCounts(): Collection
    {
        return static::query()
            ->orderBy('title')
            ->get()
            ->map(fn (self $job) => [
                'id'         => $job->id,
                'title'      => $job->title,
                'job_type'   => $job->filterType(),
                'description' => $job->description,
                'is_open'    => $job->is_open,
                'applicants' => (int) \Illuminate\Support\Facades\DB::table('applications')
                    ->where('position', $job->title)
                    ->count(),
            ]);
    }
}