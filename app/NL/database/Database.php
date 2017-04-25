<?php

namespace app\NL\database;

use Psr\Container\ContainerInterface;

class Database
{
    private $mysqlAdapter;
    private $mongodbAdapter;

    /**
     * Database constructor.
     * @param ContainerInterface $containerInterface
     */
    public function __construct($containerInterface)
    {
        $this->mysqlAdapter = $containerInterface->get('MysqlAdapter');
        $this->mongodbAdapter = $containerInterface->get('MongoAdapter');
    }

    /**
     * @return MongoAdapter|MysqlAdapter
     * @throws \Exception
     */
    public function getDatabase()
    {
        switch (CURRENT_DB) {
            case 'mysql':
                $connection = $this->mysqlAdapter;
                return $connection;
                break;
            case 'mongodb':
                $connection = $this->mongodbAdapter;
                return $connection;
                break;
            default:
                throw new \Exception('***** BASE YOU SPECIFY DO NOT EXIST *****');
                break;
        }
    }
}