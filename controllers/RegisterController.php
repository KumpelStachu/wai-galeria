<?php

class RegisterController
{
  public static string $path = '/register';

  function GET()
  {
    if (Auth::getUser()) {
      return Response::redirect(Router::$basePath . '/');
    } else {
      return Response::render('register', ['title' => 'Rejestracja']);
    }
  }

  function POST()
  {

    if ($_POST['password'] !== $_POST['password-repeat']) {
      $error = 'Hasła nie są takie same!';
    } else {
      $error = Auth::register($_POST['email'], $_POST['username'], $_POST['password']);
    }

    if ($error) {
      return Response::render('register', ['title' => 'Rejestracja', 'error' => $error]);
    } else {
      return Response::redirect(Router::$basePath . '/');
    }
  }
}
