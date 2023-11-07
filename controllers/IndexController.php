<?php

class IndexController
{
  public static string $path = '/';

  function GET()
  {
    $user = Auth::getUser();
    $id = $user ? $user->username : '';
    $userWhere = "this.public != false || (this.author == '{$id}')";

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $pageSize = 12;

    $images = ImageModel::find(['$where' => $userWhere], ['limit' => $pageSize + 1, 'skip' => $pageSize * ($page - 1), 'sort' => ['createdAt' => -1]]);

    $checked = explode(',', Cookie::get('checked') ?? '');
    array_walk($images, function (&$image) use ($checked) {
      if (in_array("{$image->_id}", $checked))
        $image->checked = 'checked';
    });

    return Response::render('index', [
      'title' => 'Strona główna',
      'images' => array_slice($images, 0, $pageSize),
      'prevPage' => $page > 1 ? '?page=' . $page - 1 : '',
      'nextPage' => count($images) > $pageSize ? '?page=' . $page + 1 : '',
    ]);
  }

  function POST()
  {
    $array = explode(',', Cookie::get('checked') ?? '');

    if (isset($_POST['checked'])) {
      $array[] = $_POST['id'];
    } else if (($key = array_search($_POST['id'], $array)) !== false) {
      unset($array[$key]);
    }

    Cookie::set('checked', join(',', $array));

    return Response::json($array);
  }
}
