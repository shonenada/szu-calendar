<?php

namespace Model;

/** 
 * @Entity 
 * @Table(name="event")
 *
 * @property integer   $id
 * @property integer   $type
 * @property string    $title
 * @property text      $remark
 * @property datetime  $created
 * @property integer   $accountId
 * @property integer   $calendarId
 * @property boolean   $isDeleted
 *
 **/

class Event extends ModelBase {

    const TYPE_STUDENT_AGENDA = 200;

    /**
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue
     **/
    public $id;

    /**
     * @Column(name="type", type="integer")
     **/
    public $type;

    /**
     * @Column(name="title", type="string", length=100)
     **/
    public $title;

    /**
     * @Column(name="remark", type="text")
     **/
    public $remark;

    /**
     * @Column(name="account_id", type="integer")
     **/
    public $accountId;

    /**
     * @OneToOne(targetEntity="Account")
     * @JoinColumn(name="account_id", referencedColumnName="id")
     **/
    public $account;

    /**
     * @Column(name="calendar_id", type="integer")
     **/
    public $calendarId;

    /**
     * @OneToOne(targetEntity="Calendar")
     * @JoinColumn(name="calendar_id", referencedColumnName="id")
     **/
    public $calendar;

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
}
