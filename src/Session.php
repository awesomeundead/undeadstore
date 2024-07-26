<?php

namespace Awesomeundead\Undeadstore;

class Session
{
    private static $session;

    private static $__flash;

    private function __construct()
    {
        ini_set('session.use_strict_mode', 1);

        session_set_cookie_params([
            'domain' => $_SERVER['SERVER_NAME'],
            'httponly' => true,
            'lifetime' => 1800,
            'samesite' => 'Lax'
        ]);

        session_start();

        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        $address = $_SERVER['HTTP_CLIENT_IP']
        ?? $_SERVER['HTTP_X_FORWARDED_FOR']
        ?? $_SERVER['HTTP_X_FORWARDED']
        ?? $_SERVER['HTTP_FORWARDED_FOR']
        ?? $_SERVER['HTTP_FORWARDED']
        ?? $_SERVER['REMOTE_ADDR']
        ?? 'UNKNOWN';

        if (isset($_SESSION['regeneration']))
        {
            if (time() - $_SESSION['regeneration'] >= 300)
            {
                session_regenerate_id(true);
                $_SESSION['regeneration'] = time();
            }

            if ($_SESSION['user_agent'] != $user_agent || $_SESSION['address'] != $address)
            {
                session_destroy();
            }
        }
        else
        {
            session_regenerate_id(true);
            $_SESSION['regeneration'] = time();
            $_SESSION['user_agent'] = $user_agent;
            $_SESSION['address'] = $address;
        }
    }

    public function __destruct()
    {
        unset($_SESSION['__flash']);

        if (isset(self::$__flash))
        {
            $_SESSION['__flash'] = self::$__flash;
        }
    }

    public function flash(string $index, mixed $value = null)
    {
        if (isset($value))
        {
            self::$__flash[$index] = $value;
        }
        else
        {
            return $_SESSION['__flash'][$index] ?? null;
        }
    }

    public function get(string $index)
    {        
        return $_SESSION[$index] ?? null;
    }

    public function has(string $index)
    {
        return isset($_SESSION[$index]);
    }

    public function remove(string $index)
    {
        if (isset($_SESSION[$index]))
        {
            unset($_SESSION[$index]);
        }
    }

    public function set(string $index, mixed $value)
    {
        $_SESSION[$index] = $value;
    }
    
    public static function create()
    {
        if (!isset(self::$session))
        {
            self::$session = new Session();
        }

        return self::$session;
    }
}