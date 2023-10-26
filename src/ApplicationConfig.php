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
        if(session()->has('respond')) {
            $respond .= 'exec('.session('respond').");\n";
        }
        if(Respond::glob()->count()) {
            $respond .= 'exec('.Respond::glob()->toJson().");\n";
        }

        if (AdminFacade::guest()) {
            return '';
        } else {
            return (string) $respond;
        }
    }
}