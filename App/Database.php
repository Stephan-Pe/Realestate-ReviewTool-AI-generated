<?php

namespace App;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

class Database
{
    private static ?EntityManager $entityManager = null;

    public static function getEntityManager(): EntityManager
    {
        if (self::$entityManager === null) {
            // 1. Point Doctrine to where your Entity attributes/annotations are stored
            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths: [__DIR__ . '/Entities'],
                isDevMode: true
            );

            // 2. Define connection parameters (you can pull these from your App\Config class)
            $connectionParams = [
                'dbname'   => Config::DB_NAME,
                'user'     => Config::DB_USER,
                'password' => Config::DB_PASSWORD,
                'host'     => Config::DB_HOST,
                'driver'   => 'pdo_mysql',
            ];

            // 3. Create the connection and EntityManager
            $connection = DriverManager::getConnection($connectionParams, $config);
            self::$entityManager = new EntityManager($connection, $config);
        }

        return self::$entityManager;
    }
}