<?php
declare(strict_types=1);

return [
    'db.driver'   => $_ENV['DB_DRIVER'],
    'db.host'     => $_ENV['DB_HOST'],
    'db.database' => $_ENV['DB_NAME'],
    'db.username' => $_ENV['DB_USER'],
    'db.password' => $_ENV['DB_PASSWORD'],
    'jwt.secret'  => $_ENV['JWT_SECRET'],
];
