<?php

class LogoutController
{
  public static string $path = '/logout';

  function GET()
  {
    Cookie::set('sessionId', '', 0);
    return Response::redirect(Router::$basePath . '/');
  }
}
