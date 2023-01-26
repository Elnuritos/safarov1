<?php

namespace App\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\Actions\ActionInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class InsalesUpdateBundle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $tries=3;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private readonly ActionInterface $action)
    {
        $this->onQueue('insales_reworker_bundle');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->action->run();
    }
    public function backoff()
    {
        return [2,10,20];
    }
    public function failed(Throwable $exception)
    {
        Log::channel('daily')->alert($exception->getMessage());

    }
}
