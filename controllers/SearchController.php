<?php

class SearchController
{
  public static string $path = '/search';

  function GET()
  {

    return Response::render('search', [
      'title' => 'Wyszukiwanie',
    ]);
  }

  function POST()
  {
    $user = Auth::getUser();
    $id = $user ? $user->username : '';
    $userWhere = "this.public != false || (this.author == '{$id}')";

    $search = $_POST['search'];
    $images = ImageModel::find(['$where' => $search ? "/$search/.test(this.title) && ($userWhere)" : $userWhere], ['limit' => 10, 'sort' => ['_id' => -1]]);

    $checked = explode(',', Cookie::get('checked') ?? '');
    array_walk($images, function (&$image) use ($checked) {
      if (in_array("{$image->_id}", $checked))
        $image->checked = 'checked';
    });

    return Response::render('_gallery', [
      'title' => 'Strona główna',
      'images' => $images,
    ]);
  }
}
