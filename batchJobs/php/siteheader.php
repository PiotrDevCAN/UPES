<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);
// session_start();

include "../php/w3config.php";
include "vendor/autoload.php";
include "splClassLoader.php";

include "connect.php";

$_SESSION['ssoEmail'] = 'Scheduled Job';
$GLOBALS['Db2Schema'] = strtoupper($_ENV['environment']);