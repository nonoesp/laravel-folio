<?php

namespace Nonoesp\Folio\Notifications;

use Spatie\Honeypot\SpamDetected;
use Nonoesp\Folio\Models\Subscriber;

class SpamNotification
{
    public function __construct()
    {
        //
    }

    public function handle(SpamDetected $event)
    {
        $path = $event->request->input('path');
        $email = $event->request->input('email');
        $data = \Arr::flatten($event->request->all());
        $ip = \Thinker::clientIp();

        array_push($data, $ip);

        \Mail::send(
            'folio::email.new-subscriber',
            [
                'email' => $email,
                'path' => $path,
                'data' => $data
            ],
            function ($m) {

                $m->from(
                    config('folio.subscribers.from.email'),
                    config('folio.subscribers.from.name')
                );
                
                $m->to(
                    config('folio.subscribers.to.email'),
                    config('folio.subscribers.to.name')
                    )
                  ->subject('[SPAM] Subscriber to '.config('folio.title-short'));

            }
        );
    }
}