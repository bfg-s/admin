<?php

declare(strict_types=1);

namespace Admin\Commands\Generators;

use Admin\Interfaces\AdminHelpGeneratorInterface;
use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;
use Throwable;

class GenerateNotificationKeys implements AdminHelpGeneratorInterface
{
    /**
     * Handle call method.
     *
     * @param  Command  $command
     * @return string
     */
    public function handle(Command $command)
    {
        try {
            $input = file_get_contents(app()->environmentFilePath());

            if (
                !str_contains($input, 'ADMIN_NOTIFICATION_PUBLIC_KEY')
                || !str_contains($input, 'ADMIN_NOTIFICATION_PRIVATE_KEY')
            ) {
                $result = VAPID::createVapidKeys();
            }

            $edited = false;

            if (!str_contains($input, 'ADMIN_NOTIFICATION_PUBLIC_KEY') && isset($result)) {
                $input .= "\nADMIN_NOTIFICATION_PUBLIC_KEY=".$result['publicKey'];
                $edited = true;
            }

            if (!str_contains($input, 'ADMIN_NOTIFICATION_PRIVATE_KEY') && isset($result)) {
                $input .= "\nADMIN_NOTIFICATION_PRIVATE_KEY=".$result['privateKey']."\n";
                $edited = true;
            }

            if ($edited) {
                file_put_contents(app()->environmentFilePath(), $input);
            }
        } catch (Throwable) {
        }

        return '';
    }
}
