<?php

class Styler
{
  private static bool $minify = true;
  private static array $rules = [];

  public static function extractClasses(string $html): array
  {
    preg_match_all('/class="(.*)"/', $html, $matches);

    return array_filter(array_unique(array_merge(...array_map(function ($s) {
      return preg_split('/\s+/', $s);
    }, $matches[1]))), function ($s) {
      return $s;
    });
  }

  public static function renderStyles(array $styles): string
  {
    return join(Styler::$minify ? '' : "\n\n\t\t", array_map(function ($class, $style) {
      return $style ? ".$class{{$style}}" : '';
    }, array_keys($styles), array_values($styles)));
  }

  public static function addRule(string $rule, string|Closure $value)
  {
    Styler::$rules[$rule] = $value;
  }

  public static function html(string $html): string
  {
    $classes = Styler::extractClasses($html);

    $pairs = array_map(function ($class) {
      preg_match('/(\w+)-?(.*)/', $class, $matches);
      $rule = $matches[1];

      if (!isset(Styler::$rules[$rule])) return [$class, false];

      $value = Styler::$rules[$rule];

      return [$class, gettype($value) === 'string' ? $value : $value($matches[2])];
    }, $classes);

    $styles = [];
    foreach ($pairs as [$key, $value]) {
      if ($value) $styles[Styler::escape($key)] = $value;
    }

    $s = Styler::$minify ? '' : "\n\t\t";
    return "<style>$s" . Styler::renderStyles($styles) . "$s</style>";
  }

  public static function escape(string $value)
  {
    foreach (str_split('[]%') as $char) {
      $value = str_replace($char, "\\$char", $value);
    }
    return $value;
  }
}

foreach (glob(join(DIRECTORY_SEPARATOR, [__DIR__, 'styler', '*.php'])) as $file) {
  include $file;
}
