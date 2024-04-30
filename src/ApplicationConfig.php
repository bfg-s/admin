<?php

declare(strict_types=1);

namespace Admin;

use Admin\Core\ConfigExtensionProvider;
use Admin\Facades\AdminFacade;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ApplicationConfig extends ConfigExtensionProvider
{
    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function boot()
    {
        $lang = strtolower(AdminFacade::nowLang());

        $validationLocalizationFile = 'admin/plugins/jquery-validation/localization/messages_'
            .$lang
            .'.min.js';

        if ($lang !== 'en' && is_file(public_path($validationLocalizationFile))) {
            $this->mergeScripts([$validationLocalizationFile]);
        }
    }

    /**
     * @return string
     */
    public function js(): string
    {
        $respond = "";
        if (Respond::glob()->count()) {
            $respond .= 'exec('.Respond::glob()->toJson().");\n";
        }

        if (AdminFacade::guest()) {
            return '';
        } else {
            return (string) $respond;
        }
    }

    /**
     * @return string[]
     */
    public function metas(): array
    {
        return [
            '<meta name="csrf-token" content="'.csrf_token().'">',
        ];
    }
}
