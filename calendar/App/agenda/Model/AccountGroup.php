<?php

namespace Model;

/** 
 * @Entity 
 * @Table(name="account_group")
 *
 * @property integer   $id
 * @property string    $type
 * @property integer   $ownerId
 * @property string    $name
 * @property string    $remark
 * @property datetime  $created
 * @property boolean   $isDeleted
 *
 **/

class AccountGroup extends ModelBase {

    const TYPE_STUDENT_GROUP = 'STUDENT_GROUP';

    /**
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue
     **/
    public $id;

    /**
     * @Column(name="type", type="string", length=30)
     **/
    public $type;

    /**
     * @Column(name="account_id", type="integer")
     **/
    public $ownerId;

    /**
     * @ManyToOne(targetEntity="Account", inversedBy="accountGroups")
     * @JoinColumn(name="account_id", referencedColumnName="id")
     **/
    public $owner;

    /**
     * @ManyToMany(targetEntity="Account")
     * @JoinTable(name="account_group_mapping",
     *      joinColumns={@JoinColumn(name="account_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     **/
    public $accounts;

    /**
     * @Column(name="name", type="string")
     **/
    public $name;

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
        $this->student = new \Doctrine\Common\Collections\ArrayCollection();
    }

    static public function getUserGroup($user) {
        $groups = AccountGroup::findBy(array('ownerId' => $user->id, 'isDeleted' => false));
        return $groups;
    }

}
