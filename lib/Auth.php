<?php

class Auth
{
  public static function login(string $username, string $password): false|string
  {
    $user = UserModel::findBy('username', $username, true);

    if (!$user) {
      return 'Nie znaleziono użytkownika!';
    }

    if ($user->password !== md5($password)) {
      return 'Nieprawidłowe hasło!';
    }

    $sessionId = Utils::randomId();

    $user->sessionId = $sessionId;
    $user->save();

    Cookie::set('sessionId', $sessionId);

    return false;
  }

  public static function register(string $email, string $username, string $password): false|string
  {
    $user = UserModel::findBy('username', $username);

    if ($user) {
      return 'Nazwa użytkownika jest zajęta!';
    }

    $user = UserModel::findBy('email', $email);

    if ($user) {
      return 'Email jest już używany!';
    }

    $sessionId = Utils::randomId();

    Cookie::set('sessionId', $sessionId);
    UserModel::fromArray([
      'username' => $username,
      'email' => $email,
      'password' => md5($password),
      'sessionId' => $sessionId,
    ])->save();

    return false;
  }

  public static function getUser(): UserModel|null
  {
    $sessionId = Cookie::get('sessionId');
    return $sessionId ? UserModel::findBy('sessionId', $sessionId) : null;
  }
}
