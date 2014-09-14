<?php

namespace Helper;


class DateTime {

    public function splitTimes($times, $mins) {
        $timeArr = array();
        foreach($times as $time) {
            $start = $time['start'];
            $end = $time['end'];
            $durings = self::splitOneTime($start, $end, $mins);
            $timeArr = array_merge($timeArr, $durings);
        }
        return $timeArr;
    }

    public static function splitOneTime($start, $end, $mins) {
        $startDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $start);
        $endDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $end);
        $interval = \DateInterval::createFromDateString(sprintf('%s minutes', $mins));
        $durings = array();
        $saveTime = $startDateTime;
        while($saveTime <= $endDateTime) {
            $durings[] = \DateTime::createFromFormat('Y-m-d H:i:s', $saveTime->format('Y-m-d H:i:s'));
            $saveTime->add($interval);
        }
        return $durings;
    }
}
