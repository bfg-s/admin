<?php

namespace Admin\Observers;

use Admin\Models\AdminUser;

class AdminUserObserver
{
    /**
     * Handle the AdminUser "created" event.
     *
     * @param  \Admin\Models\AdminUser  $user
     * @return void
     */
    public function created(AdminUser $user): void
    {
        $dashboard = $user->dashboards()->create(['name' => 'Main']);

        foreach (config('admin.widgets') as $order => $widgets) {

            $dashboard->rows()->create([
                'order' => $order,
                'admin_user_id' => $user->id,
                'widgets' => $widgets
            ]);
        }
    }
}
