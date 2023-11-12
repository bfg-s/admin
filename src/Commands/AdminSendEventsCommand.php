<?php

namespace Admin\Commands;

use Admin\Models\AdminEvent;
use Illuminate\Console\Command;

class AdminSendEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:send-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all admin events notifications';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var AdminEvent[] $list */
        $list = AdminEvent::whereBetween('start', [now()->subMinute(), now()])->with('user')->get();

        foreach ($list as $item) {

            $url = $item->url && $item->url !== 'null' ? $item->url : null;

            $item->user?->notifyMe($item->title, $item->description, $url);

            $this->info('User ' . $item->user->email . ' notify item: ' . $item->id);
        }


        return 0;
    }
}
