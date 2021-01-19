<?php

namespace Admin\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;

/**
 * Class AdminLoginEvent
 * @package Admin\Events
 */
class AdminLoginEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * AdminLoginEvent constructor.
     * @param  string  $login
     * @param  string  $password
     * @param  Request  $request
     */
    public function __construct(
        public string $login,
        public string $password,
        public Request $request,
    ) {
        //
    }
}
