<?php

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

class DB
{
  private static Client $client;
  private static Database $database;

  public static function connect(string $host, int $port, string $username, string $password, string $database)
  {
    DB::$client = new Client("mongodb://$username:$password@$host:$port");
    DB::$database = DB::$client->{$database};
  }

  public static function from(string $collection): Collection
  {
    return DB::$database->{$collection};
  }

  public static function findOne(string $collection, array $query): array|null
  {
    $result = DB::from($collection)->findOne($query);
    return $result ? $result->getArrayCopy() : null;
  }

  public static function find(string $collection, array $query = [], array $options = []): array|null
  {
    $results = DB::from($collection)->find($query, $options)->toArray();
    return array_map(function ($result) {
      return $result->getArrayCopy();
    }, $results);
  }

  public static function insertOne(string $collection, array $data)
  {
    return DB::from($collection)->insertOne($data);
  }

  public static function updateOne(string $collection, array $query, array $update, bool $upsert = false)
  {
    return DB::from($collection)->updateOne($query, ['$set' => $update], ['upsert' => $upsert]);
  }
}
