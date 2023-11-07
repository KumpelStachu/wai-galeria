<?php

class Response
{
  private int $status = 200;
  private array $headers = [];
  private Closure $response;

  public function __construct(int $status, array $headers = [], ?Closure $response = null)
  {
    $this->status = $status;
    $this->headers = $headers;
    $this->response = $response ?? Response::respondWith('');
  }

  public function status(int $status): Response
  {
    $this->status = $status;
    return $this;
  }

  public function __invoke(array ...$args)
  {
    http_response_code($this->status);

    foreach ($this->headers as $key => $value) {
      header("$key: $value");
    }

    $this->response->call($this, ...$args);
  }

  public static function text(string $value): Response
  {
    return new Response(200, ['content-type' => 'plain/text'], Response::respondWith($value));
  }

  public static function json(mixed $value): Response
  {
    return new Response(200, ['content-type' => 'application/json'], Response::respondWith(json_encode($value)));
  }

  public static function render(string $template, array $params = [])
  {
    return new Response(200, ['content-type' => 'text/html'], Response::respondWith(
      str_starts_with($template, '_') ?
        Template::renderFile($template, $params, true) :
        Template::renderPage($template, $params)
    ));
  }

  public static function file($filename)
  {
    $contentType = Mime::match($filename);

    return new Response(200, ['content-type' => $contentType], function () use ($filename) {
      echo file_get_contents($filename);
    });
  }

  public static function notFound(): Response
  {
    return new Response(404);
  }

  public static function redirect(string $path, bool $permanent = false): Response
  {
    return new Response($permanent ? 301 : 302, ['location' => $path]);
  }

  public static function respondWith($value)
  {
    return function () use ($value) {
      echo $value;
    };
  }
}
