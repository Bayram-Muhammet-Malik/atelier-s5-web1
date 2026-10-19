<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;

return [
    \PDO::class => function (ContainerInterface $c): \PDO {
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $c->get('db.driver'),
            $c->get('db.host'),
            $c->get('db.port'),
            $c->get('db.database')
        );

        return new \PDO($dsn, $c->get('db.username'), $c->get('db.password'), [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    },

    // Les repositories et services
];
