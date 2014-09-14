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
 * @property boolean   $isAdmin
 * @property boolean   $isActive
 * @property boolean   $isDeleted
 *
 **/

class Account extends ModelBase {

    const PASSWORD_SALT = 'TODO';
    const TOKEN_SALT = 'TOKENTODO';

    public static $RANK_NAME = array(
        '01' => '本科生',
        '02' => '研究生',
        '03' => '博士生',
        '04' => '留学生',
        '05' => '教工',
        '06' => '',
        '07' => '教工家属',
        '08' => '测试人员',
        '09' => '',
        '10' => '',
        '11' => '成教学生',
        '12' => '自考生',
        '13' => '工作人员',
        '14' => '离退休教工',
        '15' => '',
        '16' => '外联办学生',
        '17' => '合作银行',
        '18' => '',
        '19' => '',
        '20' => '外籍教师',
        '21' => '博士后',
        '22' => '',
        '23' => '校友',
        '24' => '校外人员',
        '25' => '校内工作',
        '26' => '校企人员',
        '27' => '',
        '28' => '交换留学生',
        '29' => '消费卡贵宾',
        '30' => '校外绿色通道',
    );

    /**
     * @Column(name="id", type="integer", nullable=false)
     * @Id
     * @GeneratedValue
     **/
    public $id;

    /**
     * @Column(name="username", type="string", length=50)
     **/
    public $username;

    /**
     * @Column(name="password", type="string", length=64)
     **/
    private $password;

    /**
     * @Column(name="name", type="string", length=50)
     **/
    public $name;

    /**
     * @Column(name="gender", type="string", length=2)
     **/
    public $gender;

    /**
     * @Column(name="college", type="string", length=20)
     **/
    public $college;

    /**
     * @Column(name="szuno", type="string", length=10)
     **/
    public $szuno;

    /**
     * @Column(name="card_id", type="string", length=6)
     **/
    public $cardId;

    /**
     * @Column(name="identity_number", type="string", length=18)
     **/
    public $identityNumber;

    /**
     * @Column(name="rank_num", type="string", length=2)
     **/
    public $rankNum;

    /**
     * @Column(name="email", type="string", length=150)
     **/
    public $email;

    /**
     * @Column(name="phone", type="string", length=11)
     **/
    public $phone;

    /**
     * @Column(name="short_phone", type="string", length=11)
     **/
    public $shortPhone;

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
    public $token;

    /**
     * @Column(name="is_admin", type="boolean")
     **/
    public $isAdmin;

    /**
     * @OneToMany(targetEntity="Calendar", mappedBy="teacher")
     **/
    public $calendars;

    /**
     * @Column(name="is_active", type="boolean")
     **/
    public $isActive;

    /**
     * @Column(name="is_deleted", type="boolean")
     **/
    public $isDeleted;

    public function __construct() {
        $this->isActive = true;
        $this->isAdmin = false;
        $this->isDeleted = false;
        $this->calendars = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function login($app) {
        $now = new \DateTime('now', new \DateTimezone('Asia/Shanghai'));
        $ip = $app->request->getIp();
        $token = \Extension\Encryption::generateToken($ip, $now, self::TOKEN_SALT);
        $this->token = $token;
        $this->lastIP = $ip;
        $this->lastLogin = $now;
        $this->save();
        Account::flush();
        $app->setEncryptedCookie('userid', $this->id);
        $app->setEncryptedCookie('token', $token);
    }

    public function isEmptyPassword() {
        $isEmptyPassword = $this->password == self::hashPassword('');
        return ($isEmptyPassword);
    }

    public function setPassword($raw) {
        $hashPassword = Account::hashPassword($raw, self::PASSWORD_SALT);
        $this->password = $hashPassword;
    }

    public function isActive() {
        return ($this->isActive == true);
    }

    public function isAdmin() {
        return ($this->isAdmin == true);
    }

    public function checkPassword($rawPassword) {
        $password = Account::hashPassword($rawPassword, self::PASSWORD_SALT);
        return ($this->password == $password);
    }

    public function hasPerm($routeName) {
        $app = \Slim\Slim::getInstance();
        $resource = $app->urlFor($routeName);
        $matched = preg_match('/\[(\S+)\]/', $routeName, $match);
        if (!$matched)
            return false;
        $method = $match[1];
        return $app->auth->accessiable($this, $resource, $method);
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
        Account::flush();
    }

    static public function factory($value = array()) {
        $account = new Account();

        $account->name = $value['name'];
        $account->szuno = $value['szuno'];

        if (isset($value['username']))
            $account->username = $value['username'];
        else
            $account->username = '';

        if (isset($value['password']))
            $account->setPassword($value['password']);
        else
            $account->setPassword('');

        if (isset($value['college']))
            $account->college = $value['college'];

        if (isset($value['gender']))
            $account->gender = $value['gender'];

        if (isset($value['cardId']))
            $account->cardId = $value['cardId'];

        if (isset($value['identityNumber']))
            $account->identityNumber = $value['identityNumber'];

        if (isset($value['rankNum']))
            $account->rankNum = $value['rankNum'];

        if (isset($value['email']))
            $account->email = $value['email'];

        if (isset($value['phone']))
            $account->phone = $value['phone'];

        if (isset($value['shortPhone']))
            $account->shortPhone = $value['shortPhone'];

        return $account;
    }

    static public function authenticate($username, $password) {
        $account = self::getBy('username', $username);
        if (!$account) {
            return false;
        }
        if (!$account->checkPassword($password)) {
            return false;
        }
        if (!$account->isActive()) {
            return null;
        }
        return $account;
    }

    static public function hashPassword($password) {
        $salt = self::PASSWORD_SALT;
        $hash = md5("{$salt}{$password}{$salt}");
        return $hash;
    }

    static public function getBy($key, $value) {
        $query = static::query()->findOneBy(array($key => $value, 'isDeleted' => false));
        return $query;
    }

    static public function isExistBy($field, $value) {
        $query = static::query()->findOneBy(array($field => $value, 'isDeleted' => false));
        return $query != null;
    }

    static public function getRankName($rankNumber) {
        return static::$RANK_NAME[$rankNumber];
    }

}
