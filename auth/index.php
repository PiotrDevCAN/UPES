<?php
//used to verify and process login

include realpath(dirname(__FILE__))."/../class/include.php";
error_log(__FILE__);
$auth = new Auth();
if($auth->verifyResponse($_GET))
{
    error_log("get state" . print_r($_GET,true));
    error_log("state" . print_r($_GET['state'],true));
    header("Access-Control-Allow-Origin: *");
    header("Location: ".$_GET['state']);
	exit();
}