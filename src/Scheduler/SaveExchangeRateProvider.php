<?php

declare(strict_types=1);

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

    /**
     * @param string[] $exchangeRatePairs
     */
    public function __construct(private readonly array $exchangeRatePairs)
    {
        $this->schedule = new Schedule();
    }

    public function getSchedule(): Schedule
    {
        return $this->schedule
            ->with(
                RecurringMessage::every('5 minutes', new SaveExchangeRate($this->exchangeRatePairs)),
            );
    }
}
