<?php

class LoginController
{
  public static string $path = '/login';

  function GET()
  {
    if (Auth::getUser()) {
      return Response::redirect(Router::$basePath . '/');
    } else {
      return Response::render('login', ['title' => 'Logowanie']);
    }
  }

  function POST()
  {
    $error = Auth::login($_POST['username'], $_POST['password']);

    if ($error) {
      return Response::render('login', ['title' => 'Logowanie', 'error' => $error]);
    } else {
      return Response::redirect(Router::$basePath . '/');
    }
  }
}
