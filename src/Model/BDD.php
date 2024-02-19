<?php

namespace src\Model;

class BDD
{
    private static $instance = null;
    protected function __construct(){}
    protected function __clone(){}

    private static function initInstance() :void
    {
        // Code de connexion
        $host = "buy-mobile-php-mariadb106";
        $username = "docker";
        $password = "docker";
        $dbname = "docker";
        $port = 3306;

        try{
            SELF::$instance = new \PDO(
                dsn: "mysql:host=".$host.";port=".$port.";dbname=".$dbname.";charset=utf8",
                username:$username,
                password: $password
            );
            SELF::$instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }catch (\Exception $e){
            die("Erreur = {$e->getMessage()}");
        }
    }

    public static function getInstance() : \PDO
    {
        if(SELF::$instance == null){
            SELF::initInstance();
        }
        return SELF::$instance;
    }
}