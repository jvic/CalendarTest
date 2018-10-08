<?php

namespace Calendar;
require_once "CalendarInterface.php";

use DateTime;
use DateTimeInterface;

class Calendar implements CalendarInterface
{
    protected $date;

    /**
     * @param DateTimeInterface $datetime
     */
    public function __construct(DateTimeInterface $datetime)
    {
        $this->date = $datetime;
    }

    /**
     * Get the day
     *
     * @return int
     */
    public function getDay()
    {
        return (int)$this->date->format('j');
    }

    /**
     * Get the weekday (1-7, 1 = Monday)
     *
     * @return int
     */
    public function getWeekDay()
    {
        return (int)$this->date->format('N');
    }

    /**
     * Get the first weekday of this month (1-7, 1 = Monday)
     *
     * @return int
     */
    public function getFirstWeekDay()
    {
        return (int)(new DateTime($this->date->format('Y-m-01')))->format('N');
    }

    /**
     * Get the first week of this month (18th March => 9 because March starts on week 9)
     *
     * @return int
     */
    public function getFirstWeek()
    {
        return (int)(new DateTime($this->date->format('Y-m-01')))->format('W');
    }

    /**
     * Get the number of days in this month
     *
     * @return int
     */
    public function getNumberOfDaysInThisMonth()
    {
        return (int)$this->date->format('t');
    }

    /**
     * Get the number of days in the previous month
     *
     * @return int
     */
    public function getNumberOfDaysInPreviousMonth()
    {
        return (int)(new DateTime($this->date->format('Y-m-01')))->modify('-1 days')->format('j');
    }

    /**
     * Get the calendar array
     *
     * @return array
     */
    public function getCalendar()
    {
        $day = new DateTime($this->date->format('Y-m-01'));

        if ($this->getFirstWeekDay() !== 1) {
            $day->modify('-' . ($this->getFirstWeekDay() - 1) . ' day');
        }

        $res = [];
        for ($i = 0; $i < 6; $i++) {
            $selectedWeek = $this->getSelectedWeek();
            $highlighted = $this->isHighlighted($day, $selectedWeek);
            $hidden = $this->isHidden($day);

            $weekNumber = (int)$day->format('W');
            $thisWeek = [];
            for ($d = 1; $d <= 7; $d++) {
                $dayNum = (new Calendar($day))->getDay();
                $thisWeek[$dayNum] = $highlighted;
                $day->modify('+1 day');
            }

            if ($hidden && $day->format('m') !== $this->date->format('m')) {
                continue;
            }
            $res[$weekNumber] = $thisWeek;
        }
        return $res;
    }

    /**
     * @return DateTime
     */
    protected function getSelectedWeek()
    {
        $d = new DateTime($this->date->format('Y-m-d'));
        return $d->modify('Monday this week');
    }

    /**
     * @param DateTime $startDay
     * @param DateTime $selectedWeek
     * @return bool
     */
    protected function isHighlighted(DateTime $startDay, DateTime $selectedWeek)
    {
        return $startDay < $selectedWeek && $startDay->diff($selectedWeek)->format('%a') == 7;
    }

    /**
     * @param DateTime $startDay
     * @return bool
     */
    protected function isHidden(DateTime $startDay)
    {
        return $startDay->format('m') !== $this->date->format('m');
    }
}