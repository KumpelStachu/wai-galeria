<?php

use MongoDB\BSON\ObjectId;

class UserModel
{
  public ?ObjectId $_id;
  public string $username;
  public string $email;
  public string $password;
  public string $sessionId;

  public function save(): UserModel
  {
    $data = $this->toArray();

    if ($this->_id) {
      DB::updateOne('users', ['_id' => $this->_id], $data);
    } else {
      $this->_id = DB::insertOne('users', $data)->getInsertedId();
    }

    return $this;
  }

  public static function findBy(string $key, string $value): UserModel|null
  {
    $user = DB::findOne('users', [$key => $value]);

    return $user ? UserModel::fromArray($user) : null;
  }

  public static function fromArray(array $array)
  {
    $user = new UserModel;
    $user->_id = isset($array['_id']) ? $array['_id'] : null;
    $user->username = $array['username'];
    $user->email = $array['email'];
    $user->password = $array['password'];
    $user->sessionId = $array['sessionId'];
    return $user;
  }

  public function toArray()
  {
    return [
      'username' => $this->username,
      'email' => $this->email,
      'password' => $this->password,
      'sessionId' => $this->sessionId,
    ];
  }
}
