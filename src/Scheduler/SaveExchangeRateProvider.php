<?php

namespace App\Scheduler;

use App\Scheduler\Message\SaveExchangeRate;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('SaveExchangeRate')]
class SaveExchangeRateProvider implements ScheduleProviderInterface
{
    private readonly Schedule $schedule;

    public function __construct()
    {
        $this->schedule = new Schedule();
    }

    public function getSchedule(): Schedule
    {
        return $this->schedule
            ->with(
                RecurringMessage::every('5 minutes', new SaveExchangeRate(['BTCEUR', 'ETHEUR', 'LTCEUR']))
            );
    }
}
