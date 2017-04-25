<?php

namespace app\NL\Models\Employee;

use app\NL\database\Database;
use app\NL\validation\ValidationMysql;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

class EmployeeServiceMysql implements IEmployeeService
{
    /** 
     * @var $validation ValidationMysql 
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
     * EmployeeServiceMysql constructor.
     * @param ContainerInterface $containerInterface
     */
    public function __construct($containerInterface)
    {
        $this->validation = $containerInterface->get('ValidationMysql');
        $this->db = $containerInterface->get('Database');
        $this->logger = $containerInterface->get('Logger');
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAllEmployees()
    {
        $query = "SELECT * FROM scotchbox.employee";
        $statement = $this->validation->connectAndTryExecute($query, $execute=null);
        $result = $this->validation->ifTableNotEmptyFetchResult($statement);
        $this->logger->info('** Get list of ALL EMPLOYEES from MySQL database **');
        return $result;
    }

    /**
     * @param $id
     * @throws \Exception
     */
    public function deleteEmployee($id)
    {
        $this->validation->validateId($id);
        try {
            $query = "DELETE FROM employee WHERE id = :id";
            $execute = array(
                'id' => $id
            );
            $this->validation->connectAndTryExecute($query, $execute);
            echo 'ITEM WITH ID: ' . $id . ' SUCCESSFULLY DELETED FROM DATABASE!';
            $this->logger->info("** EMPLOYEE with id:{$id} successfully DELETED from MySql DB database**");
        } catch (\PDOException $ex) {
            echo '***** CAN\'T DELETE EMPLOYEE WITH ID: ' . $id . ' *****' . $ex->getMessage();
            $this->logger->error("***** CAN'T DELETE EMPLOYEE WITH ID:{$id} *****");
        }
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function getOneEmployee($id)
    {
        try {
            $oneEmployee = $this->validation->validateId($id);
            $this->logger->info("** Get one EMPLOYEE from MongoDB database with id:{$id} **");
            return $oneEmployee;
        } catch (\PDOException $ex) {
            echo '***** CAN\'T GET EMPLOYEE WITH ID: ' . $id . ' *****' . $ex->getMessage();
            $this->logger->error("***** CAN'T GET EMPLOYEE WITH ID:{$id} *****");
        }
    }

    /**
     * @throws \Exception
     */
    public function createEmployee()
    {
        try {
            if (isset($_POST['Submit']) && $_POST['first_name'] != '' && $_POST['last_name'] != '') {
                $query = "INSERT INTO scotchbox.employee (`id`, `first_name`, `last_name`, `email`, `job`) VALUES (:id, :first_name, :last_name, :email, :job)";
                $id = $this->validation->setIdIfNotAlready();
                $execute = array(
                    'id' => $_POST['id'] ? $_POST['id'] : $id,
                    'first_name' => $_POST['first_name'] ? $_POST['first_name'] : null,
                    'last_name' => $_POST['last_name'] ? $_POST['last_name'] : null,
                    'email' => $_POST['email'],
                    'job' => $_POST['job']
                );
                $this->validation->connectAndTryExecute($query, $execute);
                echo 'NEW EMPLOYEE CREATED SUCCESSFULLY!';
                $this->logger->info('** NEW EMPLOYEE successfully created MySQL database **');
            } else {
                echo '***** FIELDS "FIRST NAME" & "LAST NAME" MUST BE FILLED *****';
            }
        } catch (\PDOException $ex) {
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
        $this->validation->validateId($id);
        try {
            if (isset($_POST['Update'])) {
                $query = "UPDATE scotchbox.employee SET `first_name`=:first_name, `last_name`=:last_name, `email`=:email, `job`=:job WHERE `id`=:id";
                $execute = array(
                    'id' => $id,
                    'first_name' => $_POST['first_name'] ? $_POST['first_name'] : null,
                    'last_name' => $_POST['last_name'] ? $_POST['last_name'] : null,
                    'email' => $_POST['email'],
                    'job' => $_POST['job']
                );
                $this->validation->connectAndTryExecute($query, $execute);
                echo 'EMPLOYEE WITH ID: ' . $id . ' UPDATED SUCCESSFULLY!';
                $this->logger->info("** EMPLOYEE WITH ID:{$id} UPDATED SUCCESSFULLY **");
            }
        } catch (\PDOException $ex) {
            echo '***** CAN\'T UPDATE EMPLOYEE WITH ID: ' . $id . ' *****' . $ex->getMessage();
            $this->logger->error("***** CAN'T UPDATE EMPLOYEE WITH OBJECT ID: {$id} *****");
        }
    }
}