<?php
namespace upes;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;

/*
CREATE TABLE UPES_DEV.PERSON      ( UPES_REF INTEGER NOT NULL GENERATED BY DEFAULT AS IDENTITY ( START WITH 10000 INCREMENT BY 100 NO CYCLE ), CNUM CHAR(9), EMAIL_ADDRESS CHAR(50), FULL_NAME VARCHAR(75) NOT NULL WITH DEFAULT, PASSPORT_FIRST_NAME CHAR(50), PASSPORT_LAST_NAME CHAR(50), COUNTRY CHAR(2) NOT NULL WITH DEFAULT, PES_DATE_ADDED DATE NOT NULL WITH DEFAULT CURRENT DATE, PES_ADDER CHAR(75) NOT NULL,  SYSTEM_START_TIME TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW BEGIN, SYSTEM_END_TIME TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW END, TRANS_ID TIMESTAMP(12) GENERATED ALWAYS AS TRANSACTION START ID, PERIOD SYSTEM_TIME(SYSTEM_START_TIME,SYSTEM_END_TIME) );
CREATE TABLE UPES_DEV.PERSON_HIST ( UPES_REF INTEGER NOT NULL, CNUM CHAR(9), EMAIL_ADDRESS CHAR(50), FULL_NAME VARCHAR(75) NOT NULL, PASSPORT_FIRST_NAME CHAR(50), PASSPORT_LAST_NAME CHAR(50), COUNTRY CHAR(2) NOT NULL, PES_DATE_ADDED DATE NOT NULL, PES_ADDER CHAR(75) NOT NULL , SYSTEM_START_TIME TIMESTAMP(12) NOT NULL, SYSTEM_END_TIME TIMESTAMP(12) NOT NULL, TRANS_ID TIMESTAMP(12) );
ALTER TABLE  UPES_DEV.PERSON ADD VERSIONING USE HISTORY TABLE UPES_DEV.PERSON_HIST;

 *
 */



class PersonRecord extends DbRecord
{
    protected $UPES_REF;
    protected $CNUM;
    protected $EMAIL_ADDRESS;
    protected $FULL_NAME;
    protected $PASSPORT_FIRST_NAME;
    protected $PASSPORT_LAST_NAME;
    protected $COUNTRY;
    protected $PES_DATE_ADDED;
    protected $PES_ADDER;
//     protected $PES_STATUS;
//     protected $PES_STATUS_DETAILS;
//     protected $PES_CLEARED_DATE;
//     protected $PES_RECHECK_DATE;
//     protected $PES_LEVEL;

  function displayForm($mode)
    {
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
        ?>
        <form id='personForm' class="form-horizontal" method='post'>
         <div class="form-group" >
            <label for='CNUM' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='IBM CNUM if applicable'>Lookup IBMer</label>
        	<div class='col-md-3'>
				<input id='ibmer' name='ibmer' class='form-control typeahead' <?=$notEditable;?> />
				<input id='CNUM' name='CNUM' class='form-control' type='hidden' <?=$notEditable;?> />
            </div>
        </div>
        <div class="form-group" >
            <label for='EMAIL_ADDRESS' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Email Address'>Email Address</label>
        	<div class='col-md-3'>
				<input id='EMAIL_ADDRESS' name='EMAIL_ADDRESS' class='form-control' <?=$notEditable;?> />
            </div>
        </div>
        <div class="form-group required " >
            <label for='FULL_NAME' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Full Name'>Full Name</label>
        	<div class='col-md-3'>
				<input id='FULL_NAME' name='FULL_NAME' class='form-control' <?=$notEditable;?> />
            </div>
        </div>
        <div class="form-group required " >
            <label for='COUNTRY' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Country of Residence'>Country</label>
        	<div class='col-md-3'>
        		<select id='COUNTRY' class='form-group select2' name='COUNTRY' <?=$notEditable?> >
        		<option value=''></option>
        		</select>
            </div>
        </div>
        <input id='PES_ADDER' name='PES_ADDER' type='hidden'  value='<?=$_SESSION['ssoEmail']?>'/>




   		<div class='form-group'>
   		<div class='col-sm-offset-2 -col-md-3'>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');
        $allButtons = array();
   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updatePerson',null,'Update') :  $this->formButton('submit','Submit','savePerson',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetPersonForm',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		$this->formBlueButtons($allButtons);
  		?>
  		</div>
  		</div>
	</form>
    <?php
    }
}