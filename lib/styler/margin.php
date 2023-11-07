<?php

Styler::addRule('m', function ($input) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else {
    $result = ($input / 4) . 'rem';
  }

  return $result ? 'margin:' . $result : false;
});

Styler::addRule('mb', function ($input) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else {
    $result = ($input / 4) . 'rem';
  }

  return $result ? 'margin-bottom:' . $result : false;
});

Styler::addRule('mt', function ($input) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else {
    $result = ($input / 4) . 'rem';
  }

  return $result ? 'margin-top:' . $result : false;
});

Styler::addRule('ml', function ($input) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else {
    $result = ($input / 4) . 'rem';
  }

  return $result ? 'margin-left:' . $result : false;
});

Styler::addRule('mr', function ($input) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else {
    $result = ($input / 4) . 'rem';
  }

  return $result ? 'margin-right:' . $result : false;
});

Styler::addRule('mx', function ($input) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else {
    $result = ($input / 4) . 'rem';
  }

  return $result ? 'margin-inline:' . $result : false;
});

Styler::addRule('my', function ($input) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else {
    $result = ($input / 4) . 'rem';
  }

  return $result ? 'margin-block:' . $result : false;
});
