<?php

namespace app\NL\Models\Employee;

interface IEmployeeService
{
    public function getAllEmployees();
    public function deleteEmployee($id);
    public function getOneEmployee($id);
    public function createEmployee();
    public function updateEmployee();
}