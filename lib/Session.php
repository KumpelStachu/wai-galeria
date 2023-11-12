<?php

class Session
{
  public static function start()
  {
    session_start();
  }

  public static function get(string $key, mixed $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  public static function set(string $key, mixed $value)
  {
    return $_SESSION[$key] = $value;
  }
}
