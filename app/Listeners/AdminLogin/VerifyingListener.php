<?php

namespace Admin\Listeners\AdminLogin;

use Admin\Events\AdminLoginEvent;

/**
 * Class VerifyingListener
 * @package Admin\Listeners\AdminLogin
 */
class VerifyingListener
{
    /**
     * Handle the event.
     * @param  AdminLoginEvent  $event
     */
    public function handle(AdminLoginEvent $event)
    {
        $data = $event->request->transform();

        $login = false;

        if (\Admin::guard()->attempt($data['auth'], $data['remember'])) {

            $event->request->session()->regenerate();

            //respond('toast.success', "User success auth by Login");

            $login = true;
        }

        else if (\Admin::guard()->attempt($data['auth'], $data['remember'])) {

            $event->request->session()->regenerate();

            //respond('toast.success', "User success auth by E-Mail");

            $login = true;
        }

        else {

            respond('alert_brake', "User not found!");
        }

        if ($login && $event->request->session()->has('return_authenticated_url')) {

            respond('doc.location', $event->request->session()->pull('return_authenticated_url'));

        } else if ($login) {

            respond('doc.reload');
        }

        return ['result' => $login];
    }
}
