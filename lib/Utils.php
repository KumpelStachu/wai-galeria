<?php

class Utils
{
  public static function replaceStart(string $haystack, string $needle, string $replace)
  {
    $pos = strpos($haystack, $needle);
    if ($pos === 0) {
      return substr_replace($haystack, $replace, $pos, strlen($needle));
    }
    return $haystack;
  }

  public static function removeStart(string $haystack, string $needle)
  {
    return Utils::replaceStart($haystack, $needle, '');
  }

  public static function randomId(int $length = 32)
  {
    return substr(str_shuffle(MD5(microtime())), 0, $length);
  }

  public static function copyImage(GdImage $source): GdImage
  {
    $width = imagesx($source);
    $height = imagesy($source);

    $copy = imagecreatetruecolor($width, $height);
    imageAlphaBlending($copy, false);
    imageSaveAlpha($copy, true);

    $transparent = imagecolorallocatealpha($copy, 0, 0, 0, 127);
    imagefilledrectangle($copy, 0, 0, $width - 1, $height - 1, $transparent);

    imagecopy($copy, $source, 0, 0, 0, 0, $width, $height);

    return $copy;
  }

  public static function scaleImage(GdImage $source, int $targetWidth, int $targetHeight): GdImage
  {
    return imagescale($source, $targetWidth, $targetHeight);
  }

  public static function watermark(GdImage $source, string $watermark): GdImage
  {
    $image = Utils::copyImage($source);

    $width = imagesx($image);
    $height = imagesy($image);

    $font = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'static', 'watermark.ttf']);
    $fontSize = min($width, $height) / 30;
    $margin = $fontSize;

    $white = imagecolorallocate($image, 255, 255, 255);
    imagettftext($image, $fontSize, 0, $margin, $height - $margin, $white, $font, $watermark);

    return $image;
  }
}
