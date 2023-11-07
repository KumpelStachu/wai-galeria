<?php

class Router
{
  private static array $handlers = [
    'OPTIONS' => [],
    'GET' => [],
    'POST' => [],
    'DELETE' => [],
    'PUT' => [],
    'PATCH' => [],
  ];
  private static array $controllers = [];
  private static ?Closure $defaultResponse;
  private static array $staticPaths;
  public static string $basePath;

  public static function registerHandler(string $path, string $method, callable $handler)
  {
    Router::$handlers[$method][$path] = $handler;
  }

  public static function registerController(string $controller)
  {
    Router::$controllers[($controller)::$path] = new $controller;
  }

  public static function notFound(?Closure $handler)
  {
    Router::$defaultResponse = $handler;
  }

  public static function serveFiles(string $path)
  {
    Router::$staticPaths[] = $path;
  }

  public static function basePath($path)
  {
    Router::$basePath = rtrim($path, '/');
  }

  public static function handle(?string $path = null, ?string $method = null)
  {
    $path ??= $_SERVER['REQUEST_URI'];
    if (strlen($path) > 1) $path = Utils::removeStart($path, Router::$basePath);
    if (strlen($path) > 1) $path = rtrim($path, '/');
    $path = explode('?', $path)[0];

    $method ??= $_SERVER['REQUEST_METHOD'];
    Router::validateMethod($method);

    $prepareHandler = Router::findHandler($path, $method);

    if (count(Router::$staticPaths)) {
      foreach (Router::$staticPaths as $staticPath) {
        $filePath = join(DIRECTORY_SEPARATOR, [__DIR__, '..', $staticPath, $path]);

        if (file_exists($filePath) && !is_dir($filePath)) {
          $prepareHandler = function () use ($filePath) {
            return Response::file($filePath);
          };
        }
      }
    }

    $prepareHandler ??= Router::$defaultResponse;
    $prepareHandler ??= function () {
      return Response::notFound();
    };

    $handler = $prepareHandler();
    $handler();
  }

  private static function validateMethod($method)
  {
    if (!isset(Router::$handlers[$method])) {
      throw 'invalid method';
    }
  }

  private static function findHandler(string $path, string $method): ?callable
  {
    $handlers = Router::$handlers[$method];

    if ($matched = Router::matchPath($path, array_keys($handlers))) {
      return $handlers[$matched];
    }

    if ($matched = Router::matchPath($path, array_keys(Router::$controllers))) {
      return [Router::$controllers[$matched], $method];
    }

    return null;
  }

  private static function matchPath(string $path, array $array): false|string
  {
    if (array_search($path, $array) !== false) {
      return $path;
    }

    return false;
  }
}

Template::addGenerator(function (&$params) {
  $params['base'] = Router::$basePath;
  $params['user'] = Auth::getUser();
});
