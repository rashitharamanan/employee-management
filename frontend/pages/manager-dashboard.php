<?php
/*
|--------------------------------------------------------------------------
| Manager Dashboard Redirect Shim
|--------------------------------------------------------------------------
|
| The login page or direct navigation forwards MANAGER role users here.
| We redirect them to the dedicated manager dashboard in the manager/ folder.
|
*/
header('Location: manager/manager-dashboard.php');
exit;
