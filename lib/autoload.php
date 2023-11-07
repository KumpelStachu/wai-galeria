<?php

class Autoloader
{
  public static function register()
  {
    spl_autoload_register(function ($class) {
      if (str_ends_with($class, 'Controller')) {
        $class = join(DIRECTORY_SEPARATOR, ['..', 'controllers', $class]);
      } else if (str_ends_with($class, 'Model')) {
        $class = join(DIRECTORY_SEPARATOR, ['..', 'models', $class]);
      }

      $file = join(DIRECTORY_SEPARATOR, [__DIR__, str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php']);

      if (file_exists($file)) {
        require $file;
        return true;
      }

      return false;
    });
  }
}

Autoloader::register();
