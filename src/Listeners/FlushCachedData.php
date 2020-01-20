<?php

namespace Laravie\Cabinet\Listeners;

class FlushCachedData
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login|\Illuminate\Auth\Events\Logout $event
     * @return void
     */
    public function handle($event)
    {
        if (! \is_null($event->user)) {
            $event->user->cabinet()->flush();
        }
    }
}
