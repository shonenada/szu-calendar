<?php

/**
 * 模型基础类
 * @author shonenada
 *
 */

namespace Model;

class ModelBase {

    // 永久化对象
    public function save($flush=true) {
        static::em()->persist($this);
        if ($flush) {
            self::flush();
        }
    }

    // 移除对象
    public function remove($flush=true) {
        static::em()->remove($this);
        if ($flush) {
            self::flush();
        }
    }

    public function delete() {
        $this->isDeleted = true;
        $this->save();
        self::flush();
    }

    // 从 array 中赋值 property
    public function populateFromArray($array=array()) {
        foreach($array as $key => $value){
            if ($key != 'id' && property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        return $this;
    }

    static public function dump($input) {
        return \Doctrine\Common\Util\Debug::dump($input);
    }

    static public function flush(){
        static::em()->flush();
    }

    static public function find($id) {
        return static::em()->find(get_called_class(), $id);
    }

    static public function findBy($criteria, $order_by=null, $limit=null, $offset=null) {
        return static::query()->findBy($criteria, $order_by, $limit, $offset);
    }

    static public function findOneBy($array, $order_by=null) {
        return static::query()->findOneBy($array, $order_by);
    }

    static public function query()
    {
        return static::em()->getRepository(get_called_class());
    }

    static public function em() {
        return ORMManager::getEntityManager();
    }

    static public function getBy($key, $value) {
        $query = static::query()->findOneBy(array($key => $value, 'isDeleted' => false));
        return $query;
    }

    static public function getByArray($array) {
        $query = static::query()->findOneBy(array_merge($array, array('isDeleted' => false)));
        return $query;
    }

    static public function isExistBy($field, $value) {
        $query = static::query()->findOneBy(array($field => $value, 'isDeleted' => false));
        return $query != null;
    }

    static public function isExistByArray($array) {
        $query = static::query()->findOneBy(array_merge($array, array('isDeleted' => false)));
        return $query != null;
    }

    static public function paginate($page, $pagesize) {
        $dql = sprintf(
            'SELECT n FROM %s n '.
            'WHERE n.isDeleted = 0 ',
            get_called_class()
        );
        $query = static::em()->createQuery($dql)->setFirstResult($pagesize*($page-1))->setMaxResults($pagesize);
        $pager = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        return $pager;
    }

}


class ORMManager {

    static public $entityManager = null;

    public static function init() {
        if (!file_exists(APPROOT . 'config/database.conf.php'))
            exit('Database config file not found!');

        $db_params = require(APPROOT . 'config/database.conf.php');

        $config = new \Doctrine\ORM\Configuration();
        $eventManager = new \Doctrine\Common\EventManager();

        $driver = $config->newDefaultAnnotationDriver(array(APPROOT . "Model/"));

        $config->setMetadataDriverImpl($driver);
        $config->setProxyDir(APPROOT. 'cache/');
        $config->setProxyNamespace("DoctrineProxy");

        if (extension_loaded('wincache')) {
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\WinCache());
            $config->setQueryCacheImpl(new \Doctrine\Common\Cache\WinCache());
            $config->setResultCacheImpl(new \Doctrine\Common\Cache\WinCache());
        } else if (extension_loaded('apc')) {
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ApcCache());
            $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ApcCache());
            $config->setResultCacheImpl(new \Doctrine\Common\Cache\ApcCache());
        } else {
            $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        }
        self::$entityManager = \Doctrine\ORM\EntityManager::create($db_params, $config, $eventManager);
    }

    static public function getEntityManager() {
        return self::$entityManager;
    }

} ORMManager::init();