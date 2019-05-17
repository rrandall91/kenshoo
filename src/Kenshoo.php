<?php

namespace Kenshoo;

class Kenshoo
{
    const API_ENDPOINT = 'https://api.kenshoo.com';
    const API_VERSION = 'v2';
    protected static $username;
    protected static $password;
    protected static $ks_id;

    public static function configure($username, $password, $ks_id)
    {
        self::setUsername($username);
        self::setPassword($password);
        self::setKenshooId($ks_id);
    }

    public static function getUsername()
    {
        return self::$username;
    }

    public static function getPassword()
    {
        return self::$password;
    }

    public static function getKenshooId()
    {
        return self::$ks_id;
    }

    private static function setUsername(String $username)
    {
        self::$username = $username;
    }

    private static function setPassword(String $password)
    {
        self::$password = $password;
    }

    private static function setKenshooId(String $ks_id)
    {
        self::$ks_id = $ks_id;
    }

    public static function getEndpoint()
    {
        return self::API_ENDPOINT . '/' . self::API_VERSION . '/';
    }
}