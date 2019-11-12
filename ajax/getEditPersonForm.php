<?php
use itdq\Trace;
use upes\PersonRecord;
use upes\PersonTable;
use upes\AllTables;
use itdq\FormClass;

Trace::pageOpening($_SERVER['PHP_SELF']);
ob_start();
$personTable = new PersonTable(AllTables::$PERSON);
$personRecord = new PersonRecord();
$personRecord->setFromArray(array('UPES_REF'=>$_POST['upesRef']));
$personData = $personTable->getRecord($personRecord);
$personRecord->setFromArray($personData);


ob_start();
$personRecord->displayForm(FormClass::$modeEDIT);
$form = ob_get_clean();

$messages = ob_get_clean();
$success = empty($messages);
$response = array('success'=>$success,'form' => $form, 'Messages'=>$messages,'country'=>$personData['COUNTRY'],'status'=>$personData['IBM_STATUS'],'cnum'=>$personData['CNUM']);

ob_clean();
echo json_encode($response);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);