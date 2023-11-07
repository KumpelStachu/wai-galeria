<?php

class Cookie
{
  public static function get($key): string|null
  {
    return isset($_COOKIE[$key]) ? $_COOKIE[$key] : null;
  }
  public static function set(string $key, string $value, int $ttl = 24 * 60 * 60, $httpOnly = false)
  {
    setcookie($key, $value, ['expires' => time() + $ttl,  'httponly' => $httpOnly]);
  }
}
