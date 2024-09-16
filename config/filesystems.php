<?php

return [
    'default' => 'local',
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => $_SERVER['HOME'] . '/.deployer-cli',
        ],
        'current' => [
            'driver' => 'local',
            'root' => getcwd(),
        ],
    ],
];
