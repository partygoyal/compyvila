<?php
use App\Models\Admin;
return [
    'defaults' => ['guard' => 'admin', 'passwords' => 'admins'],
    'guards' => ['admin' => ['driver' => 'session', 'provider' => 'admins']],
    'providers' => ['admins' => ['driver' => 'eloquent', 'model' => Admin::class]],
    'passwords' => [],
    'password_timeout' => 3600,
];
