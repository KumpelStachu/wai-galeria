<?php

class Template
{
  private static array $paramGenerators = [];
  private static string $ext = '.wai';
  private static string $base = '_base';

  public static function addGenerator(Closure $generator)
  {
    Template::$paramGenerators[] = $generator;
  }

  public static function base(string $base)
  {
    Template::$base = $base;
  }

  public static function readBase()
  {
    $path = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'templates', Template::$base . Template::$ext]);
    $file = file_get_contents($path);

    if ($file === false) {
      throw 'readBase';
    }

    return $file;
  }

  public static function readFile(string $file): string
  {
    $path = join(DIRECTORY_SEPARATOR, [__DIR__, '..', 'templates', $file]);

    if (!file_exists($path) || is_dir($path)) {
      return '';
    }

    return file_get_contents($path);
  }

  public static function renderPage(string $filename, array $params): string
  {
    foreach (Template::$paramGenerators as $generator) {
      $generator($params);
    }

    $params['title'] = isset($params['title']) ? $params['title'] : $filename;
    $params['body'] = Template::renderFile($filename, $params);
    $params['head'] = Template::renderFile($filename . '.head', $params);

    $html = Template::render(Template::readBase(), $params);
    $styles = Styler::html($html);

    return preg_replace('/{{\s*(@style)\s*}}/', $styles, $html);
  }

  public static function renderFile(string $filename, array $params, bool $generate = false): string
  {
    $file = Template::readFile($filename . Template::$ext);

    if ($generate) {
      foreach (Template::$paramGenerators as $generator) {
        $generator($params);
      }
    }

    return Template::render($file, $params);
  }

  public static function render(string $template, array $params): string
  {
    while (preg_match('/{{\s*(#(?:include|if|each)|\$)/', $template, $matches) !== false && count($matches)) {
      switch (ltrim($matches[1], '#')) {
        case '$':
          Template::processVariable($template, $params);
          break;
        case 'include':
          Template::processInclude($template, $params);
          break;
        case 'if':
          Template::processIf($template, $params);
          break;
        case 'each':
          Template::processEach($template, $params);
          break;
        default:
          throw new Error("unknown directive '$matches[1]'");
      }
    }

    return $template;
  }

  public static function processInclude(string &$template, array $params)
  {
    // {{ #include(template) }}
    $template = preg_replace_callback('/{{\s*#include\((\S+)\)\s*}}/', function ($matched) use ($params) {
      return Template::renderFile($matched[1], $params);
    }, $template, 1);
  }

  public static function processVariable(string &$template, array $params)
  {
    // {{ $var }}
    $template = preg_replace_callback('/{{\s*(\${1,2})(\S+)\s*}}/', function ($matched) use ($params) {
      $silent = strlen($matched[1]) - 1;
      $value = $params;

      foreach (explode('.', $matched[2]) as $key) {
        if (gettype($value) === 'object') {
          $value = $silent ? @((object)$value)->{$key} : ((object)$value)->{$key};
        } else {
          $value = $silent ? @$value[$key] : $value[$key];
        }
      }

      return $value;
    }, $template, 1);
  }

  public static function processIf(string &$template, array $params)
  {
    $count = 0;

    // {{ #if($bool) }} ... {{ #else }} ... {{ /if }}
    $template = preg_replace_callback('/{{\s*#if(\[[^\]]+\])?\((.+)\)\s*}}((?:\n|.)*?){{\s*#else\1\s*}}((?:\n|.)*?){{\s*\/if\1\s*}}/', function ($matched) use ($params) {
      $if = $matched[2];
      foreach (array_keys($params) as $key) {
        $if = str_replace("$$key", "\$params['$key']", $if);
      }
      $res = false;
      eval("\$res = $if;");
      return $res ? $matched[3] : $matched[4];
    }, $template, 1, $count);

    if ($count) return;

    // {{ #if($bool) }} ... {{ /if }}
    $template = preg_replace_callback('/{{\s*#if(\[[^\]]+\])?\((.+)\)\s*}}((?:\n|.)*?){{\s*\/if\1\s*}}/', function ($matched) use ($params) {
      $if = $matched[2];
      foreach (array_keys($params) as $key) {
        $if = str_replace("$$key", "\$params['$key']", $if);
      }
      $res = false;
      eval("\$res = $if;");
      return $res ? $matched[3] : '';
    }, $template, 1);
  }

  public static function processEach(string &$template, array $params)
  {
    // {{ #each($array) }} ... {{ /each }}
    $template = preg_replace_callback('/{{\s*#each(\[[^\]]+\])?\(\$(\S+)\)\s*}}((?:\n|.)*?){{\s*\/each\1\s*}}/', function ($matched) use ($params) {
      $template = $matched[3];

      $array = array_map(function ($item) use ($params, $template) {
        return Template::render($template, ['_' => $item, ...$params]);
      }, $params[$matched[2]]);

      return join("\n", $array);
    }, $template, 1);
  }
}
