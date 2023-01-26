<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command("rw_status:get")->hourly();
        $schedule->command("sbp_status:get")->everyMinute();

        $schedule->command("insales_status:get")->everyMinute();
        $schedule->command("test:insales")->everyMinute();
        $schedule->command("get:insales_pyrus")->everyMinute();
        $schedule->command("get:insales_sber")->everyMinute();
        $schedule->command("get:crm_errors")->everyMinute();
        $schedule->command("update:insales_reworker_remain")->everyTenMinutes();
        $schedule->command("update:insales_products")->everyMinute();

        $schedule->command("get:ozon_db")->hourly();
        $schedule->command("get:ozon_fbo_db")->hourly();
        $schedule->command("upd:fbs_rfbs_status")->hourly();
        $schedule->command("upd:fbo_status")->hourly();



    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
