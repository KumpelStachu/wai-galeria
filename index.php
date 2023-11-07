<?php
require_once 'lib/autoload.php';

DB::connect('db-mongo', 27017, 'root', 'mongo', 'galeria');

Router::basePath('/galeria');
Router::serveFiles('/static');
Router::serveFiles('/static');

Router::notFound(function () {
  return Response::render('404', ['title' => 'Nie znaleziono strony'])->status(404);
});

Router::registerHandler('/mime.types', 'GET', function () {
  $s = array();
  foreach (@explode("\n", @file_get_contents('http://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types')) as $x)
    if (isset($x[0]) && $x[0] !== '#' && preg_match_all('#([^\s]+)#', $x, $out) && isset($out[1]) && ($c = count($out[1])) > 1)
      for ($i = 1; $i < $c; $i++)
        $s[] = '\'' . $out[1][$i] . '\' => \'' . $out[1][0] . '\'';
  $result = @sort($s) ? '$mime_types = array(' . join(',', $s) . ');' : false;

  return Response::text($result);
});

Router::registerHandler('/info', 'GET', function () {
  phpinfo();
});

Router::registerHandler('/watermark.webp', 'GET', function () {
  return new Response(200, ['Content-Type' => 'image/webp'], function () {
    $source = imagecreatefromwebp('images/6549525a4da66ee6600f0483.webp');
    $watermark = Utils::watermark($source, '| test watermark | test watermark |');
    imagewebp($watermark);
  });
});

Router::registerHandler('/delete', 'GET', function () {
  Cookie::set('checked', '');
  return Response::redirect(Router::$basePath . '/');
});

Router::registerController(IndexController::class);
Router::registerController(RegisterController::class);
Router::registerController(LoginController::class);
Router::registerController(LogoutController::class);
Router::registerController(CreateController::class);
Router::registerController(SearchController::class);

Router::handle();
