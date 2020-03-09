<?php
use itdq\Trace;
use itdq\AuditTable;
use itdq\AuditRecord;
use itdq\BlueMail;
use upes\PesEmail;

Trace::pageOpening($_SERVER['PHP_SELF']);


$array1 = array('A'=>'aa','b'=>'b1','c'=>'c1');
$array2 = array('A'=>'aa','b'=>'b2','c'=>'c2');

$bigArray = array($array1,$array2);

echo "<pre>";
print_r($bigArray);
echo "</pre>";





Trace::pageLoadComplete($_SERVER['PHP_SELF']);