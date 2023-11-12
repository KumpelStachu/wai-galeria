<?php

use MongoDB\BSON\ObjectId;

class CartController
{
  public static string $path = '/cart';

  function GET()
  {
    $user = Auth::getUser();
    $id = $user ? $user->username : '';
    $userWhere = "this.public != false || (this.author == '{$id}')";

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $pageSize = 12;

    $checked = [...Session::get('checked', [])];
    array_walk($checked, function (&$id) {
      $id = new ObjectId($id);
    });

    $images = ImageModel::find(['$where' => $userWhere, '_id' => ['$in' => $checked]], ['limit' => $pageSize + 1, 'skip' => $pageSize * ($page - 1), 'sort' => ['createdAt' => -1]]);
    array_walk($images, function (&$image) {
      $image->checked = 'checked';
    });

    if (!count($images)) {
      return Response::redirect(Router::$basePath . '/');
    }

    return Response::render('index', [
      'title' => 'Strona główna',
      'images' => array_slice($images, 0, $pageSize),
      'prevPage' => $page > 1 ? '?page=' . $page - 1 : '',
      'nextPage' => count($images) > $pageSize ? '?page=' . $page + 1 : '',
    ]);
  }

  function POST()
  {
    $array = Session::get('checked', []);

    if (isset($_POST['checked'])) {
      $array[] = $_POST['id'];
    } else if (($key = array_search($_POST['id'], $array)) !== false) {
      unset($array[$key]);
    }

    Session::set('checked', $array);

    return Response::text(count($array));
  }
}
