<?php

$DISPLAY_SIZES = [
  'screen' => '100vh',
  'screen-d' => '100dvh',
  'screen-s' => '100svh',
];

Styler::addRule('minh', function ($input) use ($DISPLAY_SIZES) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else if (isset($DISPLAY_SIZES[$input])) {
    $result = $DISPLAY_SIZES[$input];
  }

  return $result ? 'min-height:' . $result : false;
});

Styler::addRule('w', function ($input) use ($DISPLAY_SIZES) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else if (isset($DISPLAY_SIZES[$input])) {
    $result = $DISPLAY_SIZES[$input];
  } else {
    $result = $input / 4 . 'rem';
  }

  return $result ? 'width:' . $result : false;
});

Styler::addRule('minw', function ($input) use ($DISPLAY_SIZES) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else if (isset($DISPLAY_SIZES[$input])) {
    $result = $DISPLAY_SIZES[$input];
  } else {
    $result = $input / 4 . 'rem';
  }

  return $result ? 'min-width:' . $result : false;
});
