<?php

namespace App\Providers;

use App\Listeners\SyncZKTecoAttendanceRecords;
use HafizAsifAli\ZkTeco\Laravel\Events\AttendanceSynced;
use App\Models\JobOpening;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(AttendanceSynced::class, SyncZKTecoAttendanceRecords::class);

        // The landing page is rendered from three places (home, login, and
        // sign-up). This gives all of them the live list of open positions.
        View::composer('welcome', function ($view) {
            $openings = JobOpening::where('is_open', true)->orderBy('title')->get();

            $view->with([
                'homeOpenings' => $openings,
                'homeYearsOfService' => max(0, (int) now()->year - 1961),
                // Homepage text edited by HR and Admin (Homepage page).
                'cms' => \App\Support\SiteContent::all(),
            ]);
        });
    }
    
}
