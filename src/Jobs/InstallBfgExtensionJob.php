<?php

namespace Admin\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class InstallBfgExtensionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $extension
    ) {}

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

        $process = new Process(['composer', 'require', $this->extension], base_path(), $env);

        $process->setTimeout(null);

        try {
            $process->mustRun();
            \Log::info('Extension install completed successfully', ['output' => $process->getOutput()]);
        } catch (ProcessFailedException $exception) {
            \Log::error('Extension install failed', ['error' => $exception->getMessage()]);
        }
    }
}
