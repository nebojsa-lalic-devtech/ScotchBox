<?php

namespace app\NL\validation;

use Mockery\CountValidator\Exception;

class ValidationMysql
{
    private $dbConnection;

    /**
     * ValidationMysql constructor.
     * @param $containerInterface
     */
    public function __construct($containerInterface)
    {
        $this->dbConnection = $containerInterface->get('Database')->getDatabase()->createConnection();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function validateId($id)
    {
        $statement = $this->dbConnection->prepare("SELECT * FROM employee WHERE id = :id LIMIT 1");
        $statement->execute(array(
            'id' => $id
        ));
        $employeeFromDb = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($employeeFromDb == true) {
            return $employeeFromDb;
        } else {
            throw new \UnexpectedValueException("***** ID: '$id', DOES NOT EXISTS IN DATABASE *****");
        }
    }

    /**
     * @param $query
     * @param $execute
     * @return mixed
     */
    public function connectAndTryExecute($query, $execute)
    {
        try {
            if (is_null($execute)) {
                $statement = $this->dbConnection->prepare($query);
                $statement->execute();
                return $statement;
            } else {
                $statement = $this->dbConnection->prepare($query);
                $statement->execute($execute);
                return $statement;
            }
        } catch (Exception $ex) {
            echo '***** CAN\'T EXECUTE QUERY TO DATABASE *****' . $ex->getMessage();
        }
    }

    /**
     * @param $statement
     * @return array
     * @throws \Exception
     */
    public function ifTableNotEmptyFetchResult($statement)
    {
        $rows = array();
        if (!($statement->rowCount() > 0)) {
            $this->logger->error("***** CAN'T GET TABLE CONTENT! EMPTY TABLE! *****");
            throw new \Exception("***** CAN'T GET TABLE CONTENT! EMPTY TABLE! *****");
        } else {
            while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    /**
     * @return mixed
     */
    public function setIdIfNotAlready()
    {
        return $this->dbConnection->lastInsertId();
    }
}