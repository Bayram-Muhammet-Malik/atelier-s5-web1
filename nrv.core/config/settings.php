<?php
declare(strict_types=1);

return [
    'displayErrorDetails' => filter_var($_ENV['DISPLAY_ERROR_DETAILS'] ?? false, FILTER_VALIDATE_BOOLEAN),

    'db.driver'   => $_ENV['DB_DRIVER'],
    'db.host'     => $_ENV['DB_HOST'],
    'db.port'     => $_ENV['DB_PORT'],
    'db.database' => $_ENV['DB_NAME'],
    'db.username' => $_ENV['DB_USER'],
    'db.password' => $_ENV['DB_PASSWORD'],
];
