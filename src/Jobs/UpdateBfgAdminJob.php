<?php

namespace Admin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UpdateBfgAdminJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $homeDirectory = getenv('HOME') ?: base_path();
        $env = [
            'HOME' => $homeDirectory,
            'COMPOSER_HOME' => $homeDirectory,
        ];

        if (is_file(base_path('composer'))) {
            $process = new Process(['php', 'composer', 'update', 'bfg/admin'], base_path(), $env);
        } else {
            $process = new Process(['composer', 'update', 'bfg/admin'], base_path(), $env);
        }
        $process->setTimeout(null);

        try {
            $process->mustRun();
            \Log::info('Composer update completed successfully', ['output' => $process->getOutput()]);
        } catch (ProcessFailedException $exception) {
            \Log::error('Composer update failed', ['error' => $exception->getMessage()]);
        }
    }
}
