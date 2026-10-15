<?php

return [
    'enabled' => env('GA_ENABLED', env('APP_ENV', 'production') === 'production'),
    'measurement_id' => env('GA_MEASUREMENT_ID', 'G-0E51GY5RYX'),
];
