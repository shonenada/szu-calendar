<?php

namespace Model;

/** 
 * @Entity 
 * @Table(name="appointment")
 *
 * @property integer   $id
 * @property integer   $account_id
 * @property integer   $calendar_id
 * @property text      $remark
 * @property datetime  $created
 * @property boolean   $isDeleted
 *
 **/

class Appointment extends ModelBase {

    /**
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue
     **/
    public $id;

    /**
     * @Column(name="account_id", type="integer")
     **/
    public $accountId;

    /**
     * @ManyToOne(targetEntity="Account", inversedBy="appointment")
     * @JoinColumn(name="account_id", referencedColumnName="id")
     **/
    public $account;

    /**
     * @Column(name="calendar_id", type="integer")
     **/
    public $calendarId;

    /**
     * @OneToOne(targetEntity="Calendar", inversedBy="appointment")
     * @JoinColumn(name="calendar_id", referencedColumnName="id")
     **/
    public $calendar;

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
        $this->accounts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->calendars = new \Doctrine\Common\Collections\ArrayCollection();
    }

}
