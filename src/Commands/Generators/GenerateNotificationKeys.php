<?php

namespace Admin\Commands\Generators;

use Admin\Page;
use ErrorException;
use Illuminate\Console\Command;
use Bfg\Entity\Core\Entities\ClassEntity;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use Minishlink\WebPush\VAPID;
use ReflectionClass;

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
            $result = VAPID::createVapidKeys();

            $input = file_get_contents(app()->environmentFilePath());

            $edited = false;

            if (! str_contains($input, 'ADMIN_NOTIFICATION_PUBLIC_KEY')) {
                $input .= "\nADMIN_NOTIFICATION_PUBLIC_KEY=" . $result['publicKey'];
                $edited = true;
            }

            if (! str_contains($input, 'ADMIN_NOTIFICATION_PRIVATE_KEY')) {
                $input .= "\nADMIN_NOTIFICATION_PRIVATE_KEY=" . $result['privateKey']."\n";
                $edited = true;
            }

            if ($edited) {
                file_put_contents(app()->environmentFilePath(), $input);
            }
        } catch (\Throwable) {

        }

        return '';
    }
}
