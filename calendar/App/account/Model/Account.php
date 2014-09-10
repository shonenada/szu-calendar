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
 * @property string    $szuno
 * @property string    $cardid
 * @property string    $email
 * @property string    $phone
 * @property datetime  $created
 * @property datetime  $last_login
 * @property string    $last_ip
 * @property string    $token
 * @property integer   $is_admin
 * @property integer   $is_deleted
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
     * @Column(name="password", type="string", length=50)
     **/
    private $password;

    /**
     * @Column(name="name", type="string", length=50)
     **/
    private $name;

    /**
     * @Column(name="szuno", type="string", length=10)
     **/
    private $szuno;

    /**
     * @Column(name="cardid", type="string", length=6)
     **/
    private $cardid;

    /**
     * @Column(name="email", type="string", length=150)
     **/
    private $email;

    /**
     * @Column(name="phone", type="string", length=11)
     **/
    private $phone;

    /**
     * @Column(name="created", type="datetime")
     **/
    public $created;

    /**
     * @Column(name="last_login", type="datetime")
     **/
    public $last_login;

    /**
     * @Column(name="last_ip", type="string", length=64)
     **/
    public $last_ip;

    /**
     * @Column(name="token", type="string", length=64)
     **/
    private $token;

    /**
     * @Column(name="is_admin", type="boolean")
     **/
    private $is_admin;

    /**
     * @Column(name="is_deleted", type="boolean")
     **/
    private $is_deleted;

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
        $this->last_ip = $ip;
    }

    public function setLastLogin ($datetime) {
        $this->last_login = $datetime;
    }

    public function setToken ($token) {
        $this->token = $token;
    }

    public function getToken() {
        return $this->token;
    }

    public function isActivity() {
        return ($this->is_deleted == 0);
    }

    public function isAdmin() {
        return ($this->is_admin == 1);
    }

    public function __construct() {
        $this->is_admin = false;
        $this->is_deleted = false;
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
        return $this->last_ip == $ip;
    }

    public function delete() {
        $this->is_deleted = true;
        $this->save();
    }

    static public function hashPassword($password, $salt) {
        $hash = md5("{$salt}{$password}{$salt}");
        return $hash;
    }

    static public function findByUsername($username) {
        $query = static::query()->findOneBy(array('username' => $username, 'is_deleted' => false));
        if ($query != null){
            return $query;
        }
        else {
            return null;
        }
    }

    static public function checkExist($username) {
        return (self::findByUsername($username) != null);
    }

}
