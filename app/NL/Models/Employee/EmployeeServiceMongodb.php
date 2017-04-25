<?php

namespace app\NL\Models\Employee;

use app\NL\database\Database;
use app\NL\validation\ValidationMongodb;
use MongoDB\BSON\ObjectID;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Query;
use MongoDB\Driver\WriteConcern;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

class EmployeeServiceMongodb implements IEmployeeService
{
    /**
     * @var $bulk BulkWrite
     */
    private $bulk;
    /**
     * @var $validation ValidationMongodb
     */
    private $validation;
    /**
     * @var $db Database
     */
    private $db;
    /**
     * @var $logger Logger
     */
    private $logger;

    /**
     * EmployeeServiceMongodb constructor.
     * @param ContainerInterface $containerInterface
     */
    public function __construct($containerInterface)
    {
        $this->bulk = $containerInterface->get('BulkWrite');
        $this->validation = $containerInterface->get('ValidationMongodb');
        $this->db = $containerInterface->get('Database');
        $this->logger = $containerInterface->get('Logger');
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAllEmployees()
    {
        $query = new Query([]);
        $allEmployees = $this->db->getDatabase()->createConnection()->executeQuery(CURRENT_MONGO_TABLE, $query);
        $employeesArray = array();
        foreach ($allEmployees as $employee) {
            $employeesArray[] = $employee;
        }

        if (!(sizeof($employeesArray) > 0)) {
            $this->logger->error("***** CAN'T GET TABLE CONTENT! EMPTY TABLE! *****");
            throw new \Exception("***** CAN'T GET TABLE CONTENT! EMPTY TABLE! *****");
        }
        $this->logger->info('** Get list of ALL EMPLOYEES from MongoDB database **');
        return $employeesArray;
    }

    /**
     * @param $id
     */
    public function deleteEmployee($id)
    {
        $this->validation->validateId($id);
        try {
            $this->bulk->delete(["_id" => new ObjectID($id)], ['limit' => 1]);
            self::execute();
            echo 'ITEM WITH OBJECT ID: ' . $id . ' SUCCESSFULLY DELETED FROM DATABASE!';
            $this->logger->info("** EMPLOYEE with id:{$id} successfully DELETED from Mongo DB database**");
        } catch (\MongoException $ex) {
            echo '***** CAN\'T DELETE EMPLOYEE WITH ID: ' . $id . ' *****' . $ex->getMessage();
            $this->logger->error("***** CAN'T DELETE EMPLOYEE WITH ID:{$id} *****");
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getOneEmployee($id)
    {
        $employee = $this->validation->validateId($id);
        $this->logger->info("** Get one EMPLOYEE from MongoDB database with id:{$id} **");
        return $employee;
    }

    /**
     * @throws \Exception
     */
    public function createEmployee()
    {
        try {
            if (isset($_POST['Submit']) && $_POST['first_name'] != '' && $_POST['last_name'] != '') {
                $query = [
                    'first_name' => $_POST['first_name'],
                    'last_name' => $_POST['last_name'],
                    'email' => $_POST['email'] ? $_POST['email'] : null,
                    'job' => $_POST['job'] ? $_POST['job'] : null
                ];
                $this->bulk->insert($query);
                self::execute();
                echo 'NEW EMPLOYEE CREATED SUCCESSFULLY!';
                $this->logger->info('** NEW EMPLOYEE successfully created MongoDB database **');
            } else {
                echo '***** FIELDS "FIRST NAME" & "LAST NAME" MUST BE FILLED *****';
            }
        } catch (\MongoException $ex) {
            echo '***** CAN\'T CREATE EMPLOYEE! *****' . $ex->getMessage();
            $this->logger->error("***** CAN'T CREATE EMPLOYEE! *****");
        }
    }

    /**
     * @throws \Exception
     */
    public function updateEmployee()
    {
        $id = $_POST['id'];
        $employeeFromDb = $this->validation->validateId($id);
        $oldEmployee = (array)$employeeFromDb[0];
        try {
            if (isset($_POST['Update'])) {
                $newEmployee = [
                    'first_name' => $_POST['first_name'] ? $_POST['first_name'] : $oldEmployee['first_name'],
                    'last_name' => $_POST['last_name'] ? $_POST['last_name'] : $oldEmployee['last_name'],
                    'email' => $_POST['email'] ? $_POST['email'] : $oldEmployee['email'],
                    'job' => $_POST['job'] ? $_POST['job'] : $oldEmployee['job']
                ];
                $this->bulk->update($oldEmployee, $newEmployee);
                self::execute();
                echo 'EMPLOYEE WITH ID: ' . $id . ' UPDATED SUCCESSFULLY!';
                $this->logger->info("** EMPLOYEE WITH ID:{$id} UPDATED SUCCESSFULLY **");
            }
        } catch (\MongoException $ex) {
            echo '***** CAN\'T UPDATE EMPLOYEE WITH OBJECT ID: ' . $id . ' *****' . $ex->getMessage();
            $this->logger->error("***** CAN'T DELETE EMPLOYEE WITH ID:{$id} *****");
        }
    }

    /**
     * @throws \Exception
     */
    private function execute()
    {
        $writeConcern = new WriteConcern(WriteConcern::MAJORITY, 1000);
        $this->db->getDatabase()->createConnection()->executeBulkWrite(CURRENT_MONGO_TABLE, $this->bulk, $writeConcern);
    }
}