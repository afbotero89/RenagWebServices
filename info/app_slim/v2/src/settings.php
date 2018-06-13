<?php

namespace App\v2\src;

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // folders
        'folders' => [
            'middlewares',
            'routes',
            'models',
            'models/general',
            'models/pressure',
            'models/asthmapp',
            'models/vrapp',
            'libs',
            'libs/fpdf',
            'libs/excel',          
            'libs/asthmapp'
        ],

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'app-slim',
            'path' => __DIR__ . '/../logs/app.log',
        ],

        // database settings
        'db' => [
            'host' => "localhost",
            'user' => "tqgibicc_wserv51",
            'pass' => "Pw123456$",
            'dbname' => "tqgibicc_wservices",
        ],

        // JWT Auth
        'jwt' => [
            'secret' => "8AhUPyjE5K0U"
        ],

        'security' => [
            'pwd' => "8AhUPyjE5K0U"
        ],

        'asthmapp' => [
            'files_path' => 'asthmapp_files/',
        ],
    ],
];
