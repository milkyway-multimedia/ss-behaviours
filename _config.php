<?php

if (!defined('SS_MWM_BEHAVIOURS_DIR')) {
    define('SS_MWM_BEHAVIOURS_DIR', basename(rtrim(__DIR__, DIRECTORY_SEPARATOR)));
}

// Silverstripe does not support loading traits (yet)
require_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/src/Traits/SilverstripeExtension.php');
require_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/src/Traits/Hashable.php');
require_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/src/Traits/Sluggable.php');
require_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/src/Traits/DefaultHashableFields.php');
require_once str_replace('/', DIRECTORY_SEPARATOR, __DIR__ . '/src/Traits/DefaultSluggableFields.php');

