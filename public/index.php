<?php

require_once '../vendor/autoload.php';
require_once '../app/NL/config/config.php';
require_once '../app/NL/bootstrap/bootstrap.php';

use app\NL\Models\Company\Company;
use app\NL\Models\Project\Project;

//***************************** CRUD START ****************************
//GET ALL Employees
$container->get('Klein')->respond('GET', '/', function () use ($container) {
    $container->get('Smarty')->assign('employeeArray', $container->get('EmployeeService')->getAllEmployees());
    return $container->get('Smarty')->display($container->get('getAllEmployeesView'));
});

//GET ONE Employee
$container->get('Klein')->respond('GET', '/[:id]', function ($request) use ($container) {
    $employee = $container->get('EmployeeService')->getOneEmployee($request->id);
    $container->get('Smarty')->assign('employee', $employee);
    return $container->get('Smarty')->display($container->get('getOneEmployeeView'));
});

//CREATE Employee
$container->get('Klein')->respond('POST', '/employee/create', function () use ($container) {
    $container->get('EmployeeService')->createEmployee();
    return $container->get('Smarty')->display($container->get('createEmployeeView'));
});

//UPDATE Employee
$container->get('Klein')->respond('POST', '/employee/update', function () use ($container) {
    $container->get('EmployeeService')->updateEmployee();
    return $container->get('Smarty')->display($container->get('updateEmployeeView'));
});

//DELETE Employee
$container->get('Klein')->respond('DELETE', '/[:id]', function ($request) use ($container) {
    $container->get('EmployeeService')->deleteEmployee($request->id);
});
//***************************** CRUD END ******************************

//****************************** OTHER ROUTES *******************************
//page with form for create new Employee
$container->get('Klein')->respond('GET', '/employee/create', function () use ($container) {
    return $container->get('Smarty')->display('templates/createEmployee.tpl');
});

//page with form for update existing Employee
$container->get('Klein')->respond('GET', '/employee/update', function () use ($container) {
    return $container->get('Smarty')->display('templates/updateEmployee.tpl');
});

$container->get('Klein')->respond('GET', '/about', function () use ($container) {
    $company = new Company('DevTech', array('Mihajla Pupina 12', 'Janka Cmelika 7'), 'Information Technology');
    $container->get('Smarty')->assign('companyDetails', $company->getCompanyDetails());
    return $container->get('Smarty')->display('templates/about.tpl');
});

$container->get('Klein')->respond('GET', '/project', function () use ($container) {
    $project = new Project('AppRiver', 'In Progress', 'Google', 'Street 01', 'IT');
    $container->get('Smarty')->assign('projectDetails', $project->getProject());
    return $container->get('Smarty')->display('templates/project.tpl');
});

//URL Exception
$container->get('Klein')->onHttpError(function () use ($container) {
    $current_uri = $_SERVER['REQUEST_URI'];
    $errorMessage = "Oooups...your URL: \"" . $current_uri . "\" does not exist!";
    $container->get('Smarty')->assign('errorMessage', $errorMessage);
    return $container->get('Smarty')->display('templates/404.tpl');
});

$container->get('Klein')->dispatch();
