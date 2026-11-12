<?php
/*
|--------------------------------------------------------------------------
| Employee Dashboard Redirect Shim
|--------------------------------------------------------------------------
|
| The login page redirects EMPLOYEE role users here.
| We forward them to the actual employee dashboard in the employee/ folder.
|
*/
header('Location: employee/employee-dashboard.php');
exit;
