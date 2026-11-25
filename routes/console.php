<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Investor;
use App\Services\WebPushService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('push:test {investor_id} {--delay=0}', function ($investorId) {
    $delay = (int) $this->option('delay');
    if ($delay > 0) {
        sleep($delay);
    }

    $investor = Investor::find($investorId);
    if (!$investor) {
        $this->error("Investor {$investorId} not found");
        return 1;
    }

    $sent = WebPushService::sendToInvestor(
        $investor,
        '🔔 إشعار من دارك (وصلك والمتصفح مغلق)',
        'مبروك! تم استلام الإشعار بنجاح في الخلفية أثناء إغلاق الموقع.',
        route('investor.bookings.index'),
        'test-bg-' . time()
    );

    $this->info("Dispatched {$sent} notifications to investor {$investorId}");
    return 0;
})->purpose('Send delayed or immediate test WebPush to an investor');
