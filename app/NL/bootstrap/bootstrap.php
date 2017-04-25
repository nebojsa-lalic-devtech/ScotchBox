<?php

namespace app\NL\bootstrap;

use app\NL\database\MongoAdapter;
use app\NL\database\MysqlAdapter;
use DI\ContainerBuilder;
use Klein\Klein;
use MongoDB\Driver\BulkWrite;
use Smarty;
use app\NL\database\Database;
use app\NL\validation\ValidationMysql;
use app\NL\validation\ValidationMongodb;
use app\NL\Models\Employee\EmployeeServiceMysql;
use app\NL\Models\Employee\EmployeeServiceMongodb;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$builder = new ContainerBuilder();
$builder->addDefinitions([
    'Klein' => function () {
        return new Klein();
    },
    'Smarty' => function () {
        return new Smarty();
    },
    'MysqlAdapter' => function () {
        return new MysqlAdapter();
    },
    'MongoAdapter' => function () {
        return new MongoAdapter();
    },
    'ValidationMysql' => function ($container) {
        return new ValidationMysql($container);
    },
    'ValidationMongodb' => function ($container) {
        return new ValidationMongodb($container);
    },
    'Database' => function ($container) {
        return new Database($container);
    },
    'BulkWrite' => function () {
        return new BulkWrite();
    },
    'Logger' => function () {
        $logger = new Logger('Logger');
        return $logger->pushHandler(new StreamHandler('../app/NL/logger/loggerFile.log', Logger::DEBUG));
    }
]);
$container = $builder->build();

switch (CURRENT_DB) {
    case 'mysql':
        $container->set('EmployeeService', new EmployeeServiceMysql($container));
        $container->set('getAllEmployeesView', 'templates/indexMysql.tpl');
        $container->set('getOneEmployeeView', 'templates/employeeByIdMysql.tpl');
        $container->set('createEmployeeView', 'templates/createdEmployee.tpl');
        $container->set('updateEmployeeView', 'templates/updatedEmployee.tpl');
        break;
    case 'mongodb':
        $container->set('EmployeeService', new EmployeeServiceMongodb($container));
        $container->set('getAllEmployeesView', 'templates/indexMongodb.tpl');
        $container->set('getOneEmployeeView', 'templates/employeeByIdMongodb.tpl');
        $container->set('createEmployeeView', 'templates/createdEmployee.tpl');
        $container->set('updateEmployeeView', 'templates/updatedEmployee.tpl');
        break;
    default:
        break;
}
