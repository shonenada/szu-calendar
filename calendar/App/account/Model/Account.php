<?php

/**
 * 用户模型类
 * @author shonenada
 * 
 */

namespace Model;

/** 
 * @Entity 
 * @Table(name="account")
 *
 * @property integer   $id
 * @property string    $username
 * @property string    $password
 * @property string    $name
 * @property string    $gender
 * @property string    $college
 * @property string    $szuno
 * @property string    $card_id
 * @property string    $identityNumber
 * @property integer   $rankNum
 * @property string    $email
 * @property string    $phone
 * @property string    $shortPhone
 * @property datetime  $created
 * @property datetime  $lastLogin
 * @property string    $lastIP
 * @property string    $token
 * @property integer   $isAdmin
 * @property integer   $isDeleted
 *
 **/

class Account extends ModelBase {

    /**
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue
     **/
    private $id;

    /**
     * @Column(name="username", type="string", length=50)
     **/
    private $username;

    /**
     * @Column(name="password", type="string", length=64)
     **/
    private $password;

    /**
     * @Column(name="name", type="string", length=50)
     **/
    private $name;

    /**
     * @Column(name="gender", type="string", length=2)
     **/
    private $gender;

    /**
     * @Column(name="college", type="string", length=20)
     **/
    private $college;

    /**
     * @Column(name="szuno", type="string", length=10)
     **/
    private $szuno;

    /**
     * @Column(name="card_id", type="string", length=6)
     **/
    private $cardId;

    /**
     * @Column(name="identity_number", type="string", length=18)
     **/
    private $identityNumber;

    /**
     * @Column(name="rank_num", type="integer", length=2)
     **/
    private $rankNum;

    /**
     * @Column(name="email", type="string", length=150)
     **/
    private $email;

    /**
     * @Column(name="phone", type="string", length=11)
     **/
    private $phone;

    /**
     * @Column(name="short_phone", type="string", length=11)
     **/
    private $shortPhone;

    /**
     * @Column(name="created", type="datetime")
     **/
    public $created;

    /**
     * @Column(name="last_login", type="datetime")
     **/
    public $lastLogin;

    /**
     * @Column(name="last_ip", type="string", length=64)
     **/
    public $lastIP;

    /**
     * @Column(name="token", type="string", length=64)
     **/
    private $token;

    /**
     * @Column(name="is_admin", type="boolean")
     **/
    private $isAdmin;

    /**
     * @Column(name="is_deleted", type="boolean")
     **/
    private $isDeleted;

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function setPassword($raw, $salt) {
        $hashPassword = User::hashPassword($raw, $salt);
        $this->password = $hashPassword;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getPhone() {
        return $this->phone;
    }

    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function setIp($ip) {
        $this->lastIP = $ip;
    }

    public function setLastLogin($datetime) {
        $this->lastLogin = $datetime;
    }

    public function setToken($token) {
        $this->token = $token;
    }

    public function getToken() {
        return $this->token;
    }

    public function isActivity() {
        return ($this->isDeleted == false);
    }

    public function isAdmin() {
        return ($this->isAdmin == true);
    }

    public function __construct() {
        $this->isAdmin = false;
        $this->isDeleted = false;
        $this->permissions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function checkPassword($rawPassword, $salt) {
        $password = User::hashPassword($rawPassword, $salt);
        return ($this->password == $password);
    }

    public function validateToken($token) {
        return $this->token == $token;
    }

    public function validateIp($ip) {
        return $this->lastIP == $ip;
    }

    public function delete() {
        $this->isDeleted = true;
        $this->save();
    }

    static public function hashPassword($password, $salt) {
        $hash = md5("{$salt}{$password}{$salt}");
        return $hash;
    }

    static public function findByUsername($username) {
        $query = static::query()->findOneBy(array('username' => $username, 'isDeleted' => false));
        return $query;
    }

    static public function isExistBy($field, $value) {
        $query = static::query()->findOneBy(array($field => $value, 'isDeleted' => false));
        return $query != null;
    }

}
