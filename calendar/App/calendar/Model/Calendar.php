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
 * @property text      $remark
 * @property datetime  $created
 * @property boolean   $isDeleted
 *
 **/

class Calendar extends ModelBase {

    const TYPE_FREE = 'FREETIME';

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
     *      joinColumns={@JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="calendar_id", referencedColumnName="id")}
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
     * @Column(name="start_time", type="datetime")
     **/
    public $startTime;

    /**
     * @Column(name="end_time", type="datetime")
     **/
    public $endTime;

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

    public function getVisibleGroupForFullCalendar() {
        $output = array();
        foreach ($this->visibleGroups->toArray() as $each) {
            $output[] = $each->name;
        }
        return $output;
    }

    static private function convertForFullCalendar($calendarArray, $args = array(), $patch = null) {
        $default = array(
            'color' => '#d15b47',
            'editable' => false,
            'startEditable' => false,
            'durationEditable' => false,
        );

        $args = array_merge($default, $args);

        $output = array();
        foreach($calendarArray as $one) {
            $insert = array(
                'id' => $one->id,
                'title' => $one->title,
                'start' => $one->startTime->format('Y-m-d\TH:i:s'),
                'end' => $one->endTime->format('Y-m-d\TH:i:s'),
                'color' => $args['color'],
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

    static public function getStudentVisiableCalendar($student, $start, $end) {
        $stuGroups = $student->stuGroups->toArray();
        $calendars = array();
        foreach ($stuGroups as $group) {
            $calendars = array_merge($calendars, $group->calendars->toArray());
        }
        return self::convertForFullCalendar($calendars);
    }

    static public function getArrangementJson($calendarArr, $start, $end, $type = null) {
        $calendarArr = array_filter($calendarArr, function($one) use($start, $end, $type) {
            $ret = ($one->startTime->getTimestamp() >= $start
                    && $one->endTime->getTimestamp() <= $end
                    && $one->isDeleted == false);
            if ($type != null) {
                return $ret && $one->type == $type;
            } else {
                return $ret;
            }
        });
        return self::convertForFullCalendar($calendarArr, array(), function ($arr, $one) {
            $arr['visibleGroups'] = $one->getVisibleGroupForFullCalendar();
            $arr['description'] = $one->description;
            return $arr;
        });
    }

}
