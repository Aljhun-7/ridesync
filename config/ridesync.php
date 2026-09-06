<?php

return [
    'mechanic_key' => env('RIDESYNC_MECHANIC_KEY', 'RIDESYNC-MECH-2026'),

    'admin' => [
        'name' => env('RIDESYNC_ADMIN_NAME', 'RideSync Admin'),
        'email' => env('RIDESYNC_ADMIN_EMAIL', 'admin@ridesync.test'),
        'password' => env('RIDESYNC_ADMIN_PASSWORD', 'Admin@123'),
    ],
];
