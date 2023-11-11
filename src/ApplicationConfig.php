<?php

namespace Admin;

use Admin\Core\ConfigExtensionProvider;
use Admin\Facades\AdminFacade;

class ApplicationConfig extends ConfigExtensionProvider
{
    /**
     * @return string
     */
    public function js(): string
    {
        $respond = "";
        if(Respond::glob()->count()) {
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
            '<meta name="puller-guard" content="admin">',
            '<meta name="notification-server-key" content="admin">'
        ];
    }
}
