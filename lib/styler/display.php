<?php

Styler::addRule('relative', 'position:relative');
Styler::addRule('absolute', 'position:absolute');

Styler::addRule('inline', 'display:inline');
Styler::addRule('flex', 'display:flex');
Styler::addRule('grid', 'display:grid');
Styler::addRule('hidden', 'display:none');


Styler::addRule('items', function ($input) {
  $value = $input;

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $value = trim($input, '[]');
  }

  return 'place-items:' . $value;
});

Styler::addRule('justify', function ($input) {
  $value = $input;

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $value = trim($input, '[]');
  }

  return 'justify-content:' . $value;
});

Styler::addRule('gap', function ($input) {
  $result = '';

  if (str_starts_with($input, '[') && str_ends_with($input, ']')) {
    $result = trim($input, '[]');
  } else {
    $result = $input / 4;
  }

  return $result ? 'gap:' . $result . 'rem' : false;
});
