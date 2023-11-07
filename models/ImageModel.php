<?php

use MongoDB\BSON\ObjectId;

class ImageModel
{
  public ?ObjectId $_id;
  public string $title;
  public string $author;
  public bool $public;
  public int $createdAt;

  public string $checked = '';

  public function save(): ImageModel
  {
    $data = $this->toArray();

    if ($this->_id) {
      DB::updateOne('images', ['_id' => $this->_id], $data);
    } else {
      $this->_id = DB::insertOne('images', $data)->getInsertedId();
    }

    return $this;
  }

  public static function findBy(string $key, string $value): ImageModel|null
  {
    $user = DB::findOne('images', [$key => $value]);

    return $user ? ImageModel::fromArray($user) : null;
  }

  public static function find(array $query = [], array $options = []): array
  {
    $images = DB::find('images', $query, $options);

    return array_map(function ($image) {
      return ImageModel::fromArray($image);
    }, $images);
  }

  public static function fromArray(array $array)
  {
    $image = new ImageModel;
    $image->_id = isset($array['_id']) ? $array['_id'] : null;
    $image->title = $array['title'];
    $image->author = $array['author'];
    $image->public = $array['public'];
    $image->createdAt = $array['createdAt'];
    return $image;
  }

  public function toArray()
  {
    return [
      'title' => $this->title,
      'author' => $this->author,
      'public' => $this->public,
      'createdAt' => $this->createdAt,
    ];
  }
}
