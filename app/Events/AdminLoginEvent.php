<?php

namespace Admin\Events;

use Admin\Http\Requests\AdminLoginRequest;
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
     * @param  AdminLoginRequest  $request
     */
    public function __construct(
        public AdminLoginRequest $request,
    ) {}
}
