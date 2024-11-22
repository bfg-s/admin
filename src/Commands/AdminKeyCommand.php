<?php

declare(strict_types=1);

namespace Admin\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

/**
 * This class is designed to process the command that generates the admin key.
 */
class AdminKeyCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin key generate';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $key = $this->generateRandomKey();

        if (! $this->setKeyInEnvironmentFile($key)) {
            return 0;
        }

        $this->laravel['config']['admin.key'] = $key;

        $this->components->info('Admin key set successfully.');

        return 0;
    }

    /**
     * Generate a random key for the application.
     *
     * @return string
     */
    protected function generateRandomKey(): string
    {
        return base64_encode(openssl_random_pseudo_bytes(32));
    }

    /**
     * Set the application key in the environment file.
     *
     * @param  string  $key
     * @return bool
     */
    protected function setKeyInEnvironmentFile(string $key): bool
    {
        $currentKey = $this->laravel['config']['admin.key'] ?: '';

        if (strlen($currentKey) !== 0 && (! $this->confirmToProceed())) {
            return false;
        }

        if (! $this->writeNewEnvironmentFileWith($key)) {
            return false;
        }

        return true;
    }

    /**
     * Write a new environment file with the given key.
     *
     * @param  string  $key
     * @return bool
     */
    protected function writeNewEnvironmentFileWith(string $key): bool
    {
        $input = file_get_contents($this->laravel->environmentFilePath());

        if (! str_contains($input, 'ADMIN_KEY')) {

            $input = $input.PHP_EOL.'ADMIN_KEY='.PHP_EOL;
        }

        $replaced = preg_replace(
            $this->keyReplacementPattern(),
            'ADMIN_KEY='.$key,
            $input
        );

        if ($replaced === $input || $replaced === null) {
            $this->error('Unable to set application key. No APP_KEY variable was found in the .env file.');

            return false;
        }

        file_put_contents($this->laravel->environmentFilePath(), $replaced);

        return true;
    }

    /**
     * Get a regex pattern that will match env APP_KEY with any random key.
     *
     * @return string
     */
    protected function keyReplacementPattern(): string
    {
        $escaped = preg_quote('='.$this->laravel['config']['admin.key'], '/');

        return "/^ADMIN_KEY{$escaped}/m";
    }
}
