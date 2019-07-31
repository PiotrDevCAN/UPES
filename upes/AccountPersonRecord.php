<?php
namespace upes;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;

/*

CREATE TABLE UPES_DEV.ACCOUNT_PERSON ( ACCOUNT_ID INTEGER NOT NULL, UPES_REF INTEGER NOT NULL, CNUM CHAR(9), EMAIL_ADDRESS CHAR(50), FULL_NAME VARCHAR(75) NOT NULL WITH DEFAULT, PASSPORT_FIRST_NAME CHAR(50), PASSPORT_LAST_NAME CHAR(50), COUNTRY CHAR(2) NOT NULL WITH DEFAULT, PES_DATE_REQUESTED DATE NOT NULL WITH DEFAULT CURRENT DATE, PES_REQUESTOR CHAR(75) NOT NULL, PES_STATUS CHAR(50) NOT NULL WITH DEFAULT 'Request Created', PES_STATUS_DETAILS CLOB(1048576), PES_CLEARED_DATE DATE, PES_RECHECK_DATE CHAR(5), PES_LEVEL CHAR(5)
                                       , CONSENT CHAR(10),RIGHT_TO_WORK CHAR(10),PROOF_OF_ID CHAR(10),PROOF_OF_RESIDENCY CHAR(10),CREDIT_CHECK CHAR(10),FINANCIAL_SANCTIONS CHAR(10),CRIMINAL_RECORDS_CHECK CHAR(10)
                                       , PROOF_OF_ACTIVITY CHAR(10),QUALIFICATIONS CHAR(10),DIRECTORS CHAR(10),MEDIA CHAR(10),MEMBERSHIP CHAR(10)
                                       , SYSTEM_START_TIME TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW BEGIN, SYSTEM_END_TIME TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW END, TRANS_ID TIMESTAMP(12) GENERATED ALWAYS AS TRANSACTION START ID, PERIOD SYSTEM_TIME(SYSTEM_START_TIME,SYSTEM_END_TIME) );
CREATE TABLE UPES_DEV.ACCOUNT_PERSON_HIST ( ACCOUNT_ID INTEGER NOT NULL, UPES_REF INTEGER NOT NULL, CNUM CHAR(9), EMAIL_ADDRESS CHAR(50), FULL_NAME VARCHAR(75) NOT NULL, PASSPORT_FIRST_NAME CHAR(50), PASSPORT_LAST_NAME CHAR(50), COUNTRY CHAR(2) NOT NULL, PES_DATE_REQUESTED DATE NOT NULL, PES_REQUESTOR CHAR(75) NOT NULL,  PES_STATUS CHAR(50) NOT NULL, PES_STATUS_DETAILS CLOB(1048576), PES_CLEARED_DATE DATE, PES_RECHECK_DATE CHAR(5), PES_LEVEL CHAR(5)
                                       , CONSENT CHAR(10),RIGHT_TO_WORK CHAR(10),PROOF_OF_ID CHAR(10),PROOF_OF_RESIDENCY CHAR(10),CREDIT_CHECK CHAR(10),FINANCIAL_SANCTIONS CHAR(10),CRIMINAL_RECORDS_CHECK CHAR(10)
                                       , PROOF_OF_ACTIVITY CHAR(10),QUALIFICATIONS CHAR(10),DIRECTORS CHAR(10),MEDIA CHAR(10),MEMBERSHIP CHAR(10)
                                       , SYSTEM_START_TIME TIMESTAMP(12) NOT NULL, SYSTEM_END_TIME TIMESTAMP(12) NOT NULL, TRANS_ID TIMESTAMP(12) );
ALTER TABLE UPES_DEV.ACCOUNT_PERSON ADD VERSIONING USE HISTORY TABLE UPES_DEV.ACCOUNT_PERSON_HIST;

 *
 */



class AccountPersonRecord extends DbRecord
{
    protected $ACCOUNT_ID;
    protected $UPES_REF;
    protected $PES_DATE_REQUESTED;
    protected $PES_REQUESTOR;
    protected $PES_STATUS;
    protected $PES_STATUS_DETAILS;
    protected $PES_CLEARED_DATE;
    protected $PES_RECHECK_DATE;
    protected $PES_LEVEL;

    protected $CONSENT;
    protected $RIGHT_TO_WORK;
    protected $PROOF_OF_ID;
    protected $PROOF_OF_RESIDENCY;
    protected $CREDIT_CHECK;
    protected $FINANCIAL_SANCTIONS;
    protected $CRIMINAL_RECORDS_CHECK;
    protected $PROOF_OF_ACTIVITY;
    protected $QUALIFICATIONS;
    protected $DIRECTORS;
    protected $MEDIA;
    protected $MEMBERSHIP;

    protected $PROCESSING_STATUS;
    protected $PROCESSING_STATUS_CHANGED;
    protected $DATE_LAST_CHASED;
    protected $COMMENT;
    protected $PRIORITY;

    function displayForm($mode)
    {
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
        $loader = new Loader();
        $allEmail = $loader->loadIndexed('EMAIL_ADDRESS','UPES_REF',AllTables::$PERSON);
        ?>
        <form id='accountPersonForm' class="form-horizontal" method='post'>
        <div class="form-group" >
            <label for='UPES_REF' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Email Address'>Email Address</label>
        	<div class='col-md-3'>
			<select id='UPES_REF' class='form-group select2' name='UPES_REF'  >
        		<option value=''></option>
        		<?php
        		foreach ($allEmail as $upesRef => $emailAddress) {
        		    ?><option value='<?=$upesRef?>' data-email='<?=$emailAddress?>'><?=$emailAddress?></option><?php
        		}
        		?>
        		</select>
            </div>
        </div>

        <div class="form-group required">
            <label for='CONTRACT_ID' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Contract'>Contract</label>
        	<div class='col-md-3'>
        		<select id='CONTRACT_ID' class='form-group select2' name='CONTRACT_ID' <?=$notEditable?> >
        		<option value=''></option>
        		</select>
            </div>
        </div>
        <div class="form-group required " >
            <label for='PES_LEVEL' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Not applicable on all contracts'>PES Level</label>
        	<div class='col-md-3'>
        		<select id='PES_LEVEL' class='form-group select2' name='PES_LEVEL' <?=$notEditable?> data-placeholder='Select Pes Level'>
        		<option value=''></option>

        		</select>
            </div>
        </div>


        <div class="form-group required " >
            <label for='FULL_NAME' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Full Name'>Full Name</label>
        	<div class='col-md-3'>
				<input id='FULL_NAME' name='FULL_NAME' class='form-control' disabled />
            </div>
        </div>

    	<input id='PES_REQUESTOR' name='PES_REQUESTOR' type='hidden'  value='<?=$_SESSION['ssoEmail']?>'/>

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