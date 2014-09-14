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
 * @property integer   $teacherId
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
     * @Column(name="teacher_id", type="integer")
     **/
    public $teacherId;

    /**
     * @ManyToOne(targetEntity="Account", inversedBy="calendars")
     * @JoinColumn(name="teacher_id", referencedColumnName="id")
     **/
    public $teacher;

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
    }

    static public function convertForFullCalendar($calendarArr, $start, $end, $type) {
        $calendarArr = array_filter($calendarArr, function($one) use($start, $end, $type) {
            return ($one->startTime->getTimestamp() >= $start
                    && $one->endTime->getTimestamp() <= $end
                    && $one->type == $type
                    && $one->isDeleted == false);
        });
        $output = array();
        foreach($calendarArr as $one) {
            $output[] = array(
                'id' => $one->id,
                'title' => $one->title,
                'description' => $one->description,
                'start' => $one->startTime->format('Y-m-d\TH:i:s'),
                'end' => $one->endTime->format('Y-m-d\TH:i:s'),
                'color' => '#d15b47',
                'editable' => true,
                'startEditable' => false,
                'durationEditable' => false,
            );
        }
        return $output;
    }

}
