<?php

$TEXT_SIZES = [
  'sm' => '.75rem',
  'md' => '1rem',
  'lg' => '1.25rem',
  'xl' => '1.75rem',
  '2xl' => '2.5rem',
  '3xl' => '3.5rem',
  '4xl' => '5rem',
  '6xl' => '6rem',
  '7xl' => '7rem',
  '8xl' => '8rem',
];

Styler::addRule('text', function ($input) use ($TEXT_SIZES) {
  $value = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $value = trim($input, '[]');
  }

  if (isset($TEXT_SIZES[$input])) {
    $value = $TEXT_SIZES[$input];
  }

  return $value ? 'font-size:' . $value : false;
});

Styler::addRule('align', function ($input) {
  $value = $input;

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $value = trim($input, '[]');
  }

  return $value ? 'text-align:' . $value : false;
});
