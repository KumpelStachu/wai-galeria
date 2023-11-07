<?php

class CreateController
{
  public static string $path = '/create';

  function GET()
  {
    return Response::render('create', [
      'title' => 'Dodaj zdjęcie',
    ]);
  }

  function POST()
  {
    $size = $_FILES['image']['size'];

    if ($size === 0 || $size > 1024 * 1024) {
      Template::addGenerator(function (&$params) use ($size) {
        $kmb = 'B';

        if ($size > 1024) {
          $size /= 1024;
          $kmb = 'KB';
        }

        if ($size > 1024) {
          $size /= 1024;
          $kmb = 'MB';
        }

        if ($size > 1024) {
          $size /= 1024;
          $kmb = 'GB';
        }

        $size = number_format($size, 2);

        $params['error'] = "Ten plik jest za duży! ($size $kmb)";
      });

      return CreateController::GET();
    }

    $id = ImageModel::fromArray([
      'title' => $_POST['title'],
      'author' => isset($_POST['author']) ? $_POST['author'] : Auth::getUser()->username,
      'public' => isset($_POST['author']) ? true : isset($_POST['public']),
      'createdAt' => time(),
    ])->save()->_id;

    $target = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'images', $id]);
    $fullTarget = "$target-full.webp";
    $thumbTarget = "$target-thumb.webp";
    $watermarkTarget = "$target.webp";

    if ($_FILES['image']['type'] === 'image/png') {
      $fullImage = imagecreatefrompng($_FILES['image']['tmp_name']);
    } else   if ($_FILES['image']['type'] === 'image/jpeg') {
      $fullImage = imagecreatefromjpeg($_FILES['image']['tmp_name']);
    } else {
      throw new Error('image type error????');
    }

    $scaledImage = Utils::scaleImage($fullImage, 200, 125);
    $watermarkImage = Utils::watermark($fullImage, $_POST['watermark']);

    imagewebp($fullImage, $fullTarget);
    imagewebp($scaledImage, $thumbTarget);
    imagewebp($watermarkImage, $watermarkTarget);

    return Response::redirect(Router::$basePath . '/');
  }
}
