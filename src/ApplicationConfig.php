<?php

declare(strict_types=1);

namespace Admin;

use Admin\Core\ConfigExtensionProvider;
use Admin\Facades\Admin;

/**
 * Abstract class for application configuration.
 * Your configuration class `app/Admin/Config.php` should inherit from this class.
 */
abstract class ApplicationConfig extends ConfigExtensionProvider
{
    /**
     * Method for initializing the configuration. Called when the configuration is loaded.
     *
     * @return void
     */
    public function boot(): void
    {
        $lang = strtolower(Admin::nowLang());

        $validationLocalizationFile = 'admin/plugins/jquery-validation/localization/messages_'
            .$lang
            .'.min.js';

        if ($lang !== 'en' && is_file(public_path($validationLocalizationFile))) {
            $this->mergeScripts([$validationLocalizationFile]);
        }
    }

    /**
     * Method for adding JavaScript scripts to the admin panel.
     * In this case, a global respond is sent for execution.
     *
     * @return string
     */
    public function js(): string
    {
        $respond = "";
        if (Respond::glob()->count()) {
            $respond .= 'exec('.Respond::glob()->toJson().");\n";
        }

        if (Admin::guest()) {
            return '';
        } else {
            return $respond;
        }
    }

    /**
     * Method for adding meta tags to the admin panel.
     *
     * @return string[]
     */
    public function metas(): array
    {
        return [
            '<meta name="csrf-token" content="'.csrf_token().'">',
        ];
    }
}
