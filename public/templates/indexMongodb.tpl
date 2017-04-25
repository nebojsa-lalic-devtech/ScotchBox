<html>
<head>
    <title>Employee</title>
</head>
<body>
<form action="http://localhost:8000/employee/create">
    <input type="submit" value="Create New Employee"/>
</form>
<form action="http://localhost:8000/employee/update">
    <input type="submit" value="Update Employee"/>
</form>
<p>--------------------------------------------------------------------------------------------------------</p>
<h1>***** List - All Employees *****</h1>
<p>--------------------------------------------------------------------------------------------------------</p>
<div>
    <ul>
        {foreach from=$employeeArray key=key item=i}
            <li><p><b>Id: </b>{$i->_id}</p></li>
            <li><p><b>Name: </b>{$i->first_name} {$i->last_name}</p></li>
            <li><p><b>Email: </b>{$i->email}</p></li>
            <li><p><b>Job: </b>{$i->job}</p></li><br>
        {/foreach}
    </ul>
</div>
<p>--------------------------------------------------------------------------------------------------------</p>
</body>
</html>