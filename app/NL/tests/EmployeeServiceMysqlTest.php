<?php

namespace app\NL\tests;

use app\NL\Models\Employee\EmployeeServiceMysql;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class EmployeeServiceMysqlTest extends TestCase
{
    private $mockedContainer;
    private $mockedValidation;
    private $mockedDatabase;
    private $mockedLogger;
    private $mockedMysqlAdapter;
    private $mockedPdo;
    private $mockedPdoStatement;

    /**
     * SetUp Mocked Objects
     */
    public function setUp()
    {
        parent::setUp();
        $this->mockedContainer = m::mock('ContainerBuilder');
        $this->mockedValidation = m::mock('Validation');
        $this->mockedDatabase = m::mock('Database');
        $this->mockedLogger = m::mock('Logger');
        $this->mockedMysqlAdapter = m::mock('MysqlAdapter');
        $this->mockedPdo = m::mock('PDO');
        $this->mockedPdoStatement = m::mock('PDOStatement');
    }

    /**
     * Test for GetOneEmployee
     */
    public function testGetsOneEmployeeFromDatabase()
    {
        $returnEmployeeFromDb = array(
            'id' => 1,
            'first_name' => 'Nebojsa',
            'last_name' => 'Lalic',
            'email' => 'lalicnebojsa@gmail.com',
            'job' => 'developer'
        );
        $this->mockedContainer->shouldReceive('get')->times(2)->andReturn($this->mockedValidation, $this->mockedLogger);
        $this->mockedValidation->shouldReceive('validateId')->once()->with(1)->andReturn($returnEmployeeFromDb);
        $this->mockedLogger->shouldReceive('info')->once()->andReturn('** NEW EMPLOYEE successfully created MySQL database **');
        $expectedReturnEmployee = array(
            'id' => 1,
            'first_name' => 'Nebojsa',
            'last_name' => 'Lalic',
            'email' => 'lalicnebojsa@gmail.com',
            'job' => 'developer'
        );
        $employeeService = new EmployeeServiceMysql($this->mockedContainer);

        $this->assertEquals($expectedReturnEmployee, $employeeService->getOneEmployee(1));
    }

    /**
     * Test for GetAllEmployees
     */
    public function testGetsAllEmployeesFromDatabase()
    {
        $returnEmployeesArrayFromDb = array(
            '0' => array(
                'id' => 1,
                'first_name' => 'Nebojsa',
                'last_name' => 'Lalic',
                'email' => 'lalicnebojsa@gmail.com',
                'job' => 'developer'
            ),
            '1' => array(
                'id' => 2,
                'first_name' => 'Novak',
                'last_name' => 'Djokovic',
                'email' => 'novakdjokovic@gmail.com',
                'job' => 'manager'
            )
        );

        $expectedEmployeesArrayFromDb = array(
            '0' => array(
                'id' => 1,
                'first_name' => 'Nebojsa',
                'last_name' => 'Lalic',
                'email' => 'lalicnebojsa@gmail.com',
                'job' => 'developer'
            ),
            '1' => array(
                'id' => 2,
                'first_name' => 'Novak',
                'last_name' => 'Djokovic',
                'email' => 'novakdjokovic@gmail.com',
                'job' => 'manager'
            )
        );
        $this->mockedContainer->shouldReceive('get')->times(3)->andReturn($this->mockedValidation, $this->mockedLogger);
        $this->mockedValidation->shouldReceive('connectAndTryExecute')->once()->andReturn(true);
        $this->mockedValidation->shouldReceive('ifTableNotEmptyFetchResult')->once()->andReturn($returnEmployeesArrayFromDb);
        
        $this->mockedLogger->shouldReceive('info')->once()->andReturn('** Get list of ALL EMPLOYEES from MySQL database **');
        $this->mockedLogger->shouldReceive('error')->once()->andReturn('** Get list of ALL EMPLOYEES from MySQL database **');

        $employeeService = new EmployeeServiceMysql($this->mockedContainer);

        $this->assertEquals($expectedEmployeesArrayFromDb, $employeeService->getAllEmployees());
    }

    /**
     * Test for DeleteEmployee
     */
    public function testDeleteEmployeeFromDatabase()
    {
        $returnEmployeeFromDb = array(
            'id' => 1,
            'first_name' => 'Nebojsa',
            'last_name' => 'Lalic',
            'email' => 'lalicnebojsa@gmail.com',
            'job' => 'developer'
        );
        $this->mockedContainer->shouldReceive('get')->times(2)->andReturn($this->mockedValidation, $this->mockedLogger);
        $this->mockedValidation->shouldReceive('validateId')->once()->with(1)->andReturn($returnEmployeeFromDb);
        $this->mockedValidation->shouldReceive('connectAndTryExecute')->once()->andReturn(true);
        $this->mockedLogger->shouldReceive('info')->once()->andReturn('** EMPLOYEE successfully DELETED from MySql DB database**');
        $this->mockedLogger->shouldReceive('error')->once()->andReturn('** CAN\'T DELETE EMPLOYEE FROM DATABASE **');

        $employeeService = new EmployeeServiceMysql($this->mockedContainer);

        $this->assertNull($employeeService->deleteEmployee(1));
    }
}