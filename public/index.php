<?php

use App\Kernel;

date_default_timezone_set('UTC');
set_time_limit(200);
require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
