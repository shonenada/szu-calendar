<?php

namespace Model;

/** 
 * @Entity 
 * @Table(name="calendar")
 *
 * @property integer   $id
 * @property string    $type
 * @property string    $title
 * @property string    $description
 * @property integer   $accountId
 * @property datetime  $startTime
 * @property datetime  $endTime
 * @property string    $workPlace
 * @property text      $remark
 * @property datetime  $created
 * @property boolean   $isDeleted
 *
 **/

class Calendar extends ModelBase {

    const TYPE_FREE = 'FREE';
    const TYPE_BOOKED = 'BOOKED';

    static public $presetColor = array(
        'red' => array(
            'color' => '#d15b47',
            'textColor' => '#fff',
        ),
        'gray' => array(
            'color' => '#b0b0b0',
            'textColor' => '#fff',
        ),
        'green' => array(
            'color' => '#1ba15f',
            'textColor' => '#fff',
        ),
        'blue' => array(
            'color' => '#3A87AD',
            'textColor' => '#fff',
        ),
        'yellow' => array(
            'color' => '#D19C47',
            'textColor' => '#fff',
        ),
    );
    static public $defatulColor = 'blue';

    /**
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue
     **/
    public $id;

    /**
     * @Column(name="type", type="string", length=10)
     **/
    public $type;

    /**
     * @Column(name="title", type="string", length=50)
     **/
    public $title;

    /**
     * @Column(name="description", type="string", length=300)
     **/
    public $description;

    /**
     * @ManyToMany(targetEntity="AccountGroup" , inversedBy="calendars")
     * @JoinTable(name="calendar_group_mapping",
     *      joinColumns={@JoinColumn(name="calendar_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     **/
    public $visibleGroups;

    /**
     * @Column(name="account_id", type="integer")
     **/
    public $accountId;

    /**
     * @ManyToOne(targetEntity="Account", inversedBy="calendars")
     * @JoinColumn(name="account_id", referencedColumnName="id")
     **/
    public $account;

    /**
     * @OneToOne(targetEntity="Appointment", mappedBy="calendar")
     **/
    public $appointment;

    /**
     * @Column(name="start_time", type="datetime")
     **/
    public $startTime;

    /**
     * @Column(name="end_time", type="datetime")
     **/
    public $endTime;

    /**
     * @Column(name="work_place", type="string")
     **/
    public $workPlace;

    /**
     * @Column(name="remark", type="text")
     **/
    public $remark;

    /**
     * @Column(name="created", type="datetime")
     **/
    public $created;

    /**
     * @Column(name="is_deleted", type="boolean")
     **/
    public $isDeleted;

    public function __construct() {
        $this->isDeleted = false;
        $this->visibleGroups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getVisibleGroupsName() {
        $output = array();
        foreach ($this->visibleGroups->toArray() as $each) {
            $output[] = $each->name;
        }
        return $output;
    }

    public function canVisitedByAccount($account) {
        $groupsArray = $this->visibleGroups->toArray();
        $accountGroups = $account->stuGroups->toArray();
        foreach ($accountGroups as $each) {
            if (in_array($each, $groupsArray)) {
                return true;
            }
        }
        return false;
    }

    static public function generateInterval($start, $end, $mins) {
        $interval = \DateInterval::createFromDateString(sprintf('%s minutes', $mins));
        $output = array();
        $saveTime = $start;
        while($saveTime <= $end) {
            $output[] = \DateTime::createFromFormat('Y-m-d H:i:s', $saveTime->format('Y-m-d H:i:s'));
            $saveTime->add($interval);
        }
        return $output;
    }

    static public function spliteOneCalendarTime($calendar, $mins) {
        $output = array();
        $prevCdar = null;
        $start = $calendar->startTime;
        $end = $calendar->endTime;
        $pieces = self::generateInterval($start, $end, $mins);
        array_pop($pieces);
        foreach ($pieces as $p) {
            $cdar = clone $calendar;
            $cdar->startTime = $p;
            $output[] = $cdar;
            if ($prevCdar) {
                $prevCdar->endTime = $p;
            }
            $prevCdar = $cdar;
        }
        return $output;
    }

    static public function splitCalendarsTime($calendars, $mins) {
        $output = array();
        foreach($calendars as $each) {
            $eachSplited = self::spliteOneCalendarTime($each, $mins);
            $output = array_merge($output, $eachSplited);
        }
        return $output;
    }

    static public function convertForFullCalendar($calendarArray, $inputArgs = array(), $patch = null) {
        $defaultArgs = array(
            'color' => static::$presetColor[static::$defatulColor]['color'],
            'textColor' => static::$presetColor[static::$defatulColor]['textColor'],
            'editable' => false,
            'startEditable' => false,
            'durationEditable' => false,
        );

        $args = array_merge($defaultArgs, $inputArgs);

        $output = array();
        foreach($calendarArray as $one) {
            $insert = array(
                'id' => $one->id,
                'title' => $one->title,
                'start' => $one->startTime->format('Y-m-d\TH:i:s'),
                'end' => $one->endTime->format('Y-m-d\TH:i:s'),
                'color' => $args['color'],
                'textColor' => $args['textColor'],
                'editable' => $args['editable'],
                'startEditable' => $args['startEditable'],
                'durationEditable' => $args['durationEditable'],
            );
            if ($patch)
                $insert = $patch($insert, $one);
            $output[] = $insert;
        }
        return $output;
    }

    static public function getStudentVisiableCalendar($student, $start, $end, $inputArgs = array()) {
        $defaultArgs = array(
            'convert' => false,
        );
        $args = array_merge($defaultArgs, $inputArgs);

        $stuGroups = $student->stuGroups->toArray();
        $calendars = array();

        foreach ($stuGroups as $group) {
            $calendars = array_merge($calendars, $group->calendars->toArray());
        }

        $calendars = array_filter($calendars, function($one) use($start, $end) {
            return ($one->startTime->getTimestamp() >= $start
                    && $one->endTime->getTimestamp() <= $end
                    && $one->isDeleted == false);
        });

        if ($args['convert']) {
            return self::convertForFullCalendar($calendars);
        } else {
            return $calendars;
        }

    }

    static public function getTeacherWorkArrangement($calendarArr, $start, $end, $inputArgs = array()) {
        $defaultArgs = array(
            'type' => null,
            'convert' => false,
        );
        $args = array_merge($defaultArgs, $inputArgs);
        $calendarArr = array_filter($calendarArr, function($one) use($start, $end, $args) {
            $ret = ($one->isDeleted == false);

            if (isset($start) && $start != null)
                $ret = $ret && ($one->startTime->getTimestamp() >= $start);

            if (isset($end) && $end != null)
                $ret = $ret && ($one->endTime->getTimestamp() <= $end);

            if ($args['type'] != null) {
                return $ret && $one->type == $args['type'];
            } else {
                return $ret;
            }
        });
        if ($args['convert']) {
            $args = array(
                'color' => static::$presetColor['red']['color'],
                'textColor' => static::$presetColor['red']['textColor'],
            );
            return self::convertForFullCalendar($calendarArr, $args, function ($insert, $one) {
                if ($one->appointment) {
                    $insert['editable'] = false;
                    $insert['hasAppointment'] = true;
                    $insert['appointmentAccountName'] = $one->appointment->account->name;
                    $insert['appointmentAccountPhone'] = $one->appointment->account->phone;
                    $insert['appointmentAccountShortPhone'] = $one->appointment->account->shortPhone;
                    $insert['appointmentAccountEmail'] = $one->appointment->account->email;
                    $insert['appointmentRemark'] = $one->appointment->remark;
                    $insert['color'] = static::$presetColor['yellow']['color'];
                } else {
                    $insert['editable'] = true;
                    $insert['hasAppointment'] = false;
                    $insert['color'] = static::$presetColor['red']['color'];
                }
                $insert['visibleGroups'] = $one->getVisibleGroupsName();
                $insert['description'] = $one->description;
                return $insert;
            });
        } else {
            return $calendarArr;
        }
    }

    static public function getTeacherAppointment($teacherAccount, $noBefore=False) {
        $appointments = array();
        $now = new \DateTime();
        $calendars = $teacherAccount->calendars;
        foreach ($calendars as $cls) {
            if ($noBefore && $cls->startTime < $now) {
                continue;
            }
            if ($cls->appointment) {
                $appointments[] = $cls->appointment;
            }
        }
        usort($appointments, function($one, $two) {
            return $one->startTime < $two->startTime;
        });
        return $appointments;
    }

}
