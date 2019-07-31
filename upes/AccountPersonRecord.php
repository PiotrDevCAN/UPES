<?php
namespace upes;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;

/*

DROP TABLE UPES_DEV.ACCOUNT_PERSON;


CREATE TABLE UPES_DEV.ACCOUNT_PERSON ( ACCOUNT_ID INTEGER NOT NULL, UPES_REF INTEGER NOT NULL, PES_LEVEL CHAR(5), PES_DATE_REQUESTED DATE NOT NULL WITH DEFAULT CURRENT DATE, PES_REQUESTOR CHAR(75) NOT NULL, PES_STATUS CHAR(50) NOT NULL WITH DEFAULT 'Request Created', PES_STATUS_DETAILS CLOB(1048576), PES_CLEARED_DATE DATE, PES_RECHECK_DATE CHAR(5)
                                       , CONSENT CHAR(10),RIGHT_TO_WORK CHAR(10),PROOF_OF_ID CHAR(10),PROOF_OF_RESIDENCY CHAR(10),CREDIT_CHECK CHAR(10),FINANCIAL_SANCTIONS CHAR(10),CRIMINAL_RECORDS_CHECK CHAR(10)
                                       , PROOF_OF_ACTIVITY CHAR(10),QUALIFICATIONS CHAR(10),DIRECTORS CHAR(10),MEDIA CHAR(10),MEMBERSHIP CHAR(10)
                                       , SYSTEM_START_TIME TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW BEGIN, SYSTEM_END_TIME TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW END, TRANS_ID TIMESTAMP(12) GENERATED ALWAYS AS TRANSACTION START ID, PERIOD SYSTEM_TIME(SYSTEM_START_TIME,SYSTEM_END_TIME) );
CREATE TABLE UPES_DEV.ACCOUNT_PERSON_HIST ( ACCOUNT_ID INTEGER NOT NULL, UPES_REF INTEGER NOT NULL , PES_LEVEL CHAR(5), PES_DATE_REQUESTED DATE NOT NULL, PES_REQUESTOR CHAR(75) NOT NULL,  PES_STATUS CHAR(50) NOT NULL, PES_STATUS_DETAILS CLOB(1048576), PES_CLEARED_DATE DATE, PES_RECHECK_DATE CHAR(5)
                                       , CONSENT CHAR(10),RIGHT_TO_WORK CHAR(10),PROOF_OF_ID CHAR(10),PROOF_OF_RESIDENCY CHAR(10),CREDIT_CHECK CHAR(10),FINANCIAL_SANCTIONS CHAR(10),CRIMINAL_RECORDS_CHECK CHAR(10)
                                       , PROOF_OF_ACTIVITY CHAR(10),QUALIFICATIONS CHAR(10),DIRECTORS CHAR(10),MEDIA CHAR(10),MEMBERSHIP CHAR(10)
                                       , SYSTEM_START_TIME TIMESTAMP(12) NOT NULL, SYSTEM_END_TIME TIMESTAMP(12) NOT NULL, TRANS_ID TIMESTAMP(12) );
ALTER TABLE UPES_DEV.ACCOUNT_PERSON ADD VERSIONING USE HISTORY TABLE UPES_DEV.ACCOUNT_PERSON_HIST;

ALTER TABLE "UPES_DEV"."ACCOUNT_PERSON" ADD CONSTRAINT "AccPer_PK" PRIMARY KEY ("ACCOUNT_ID","UPES_REF" ) ENFORCED;


 *
 */



class AccountPersonRecord extends DbRecord
{
    protected $ACCOUNT_ID;
    protected $UPES_REF;
    protected $PES_LEVEL;
    protected $PES_DATE_REQUESTED;
    protected $PES_REQUESTOR;
    protected $PES_STATUS;
    protected $PES_STATUS_DETAILS;
    protected $PES_CLEARED_DATE;
    protected $PES_RECHECK_DATE;


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

    const PES_EVENT_CONSENT        = 'Consent Form';
    const PES_EVENT_WORK           = 'Right to Work';
    const PES_EVENT_ID             = 'Proof of Id';
    const PES_EVENT_RESIDENCY      = 'Residency';
    const PES_EVENT_CREDIT         = 'Credit Check';
    const PES_EVENT_SANCTIONS      = 'Financial Sanctions';
    const PES_EVENT_CRIMINAL       = 'Criminal Records Check';
    const PES_EVENT_ACTIVITY       = 'Activity';
    const PES_EVENT_QUALIFICATIONS = 'Qualifications';
    const PES_EVENT_DIRECTORS      = 'Directors';
    const PES_EVENT_MEDIA          = 'Media';
    const PES_EVENT_MEMBERSHIP     = 'Membership';

    const PES_STATUS_CLEARED       = 'Cleared';
    const PES_STATUS_CLEARED_PERSONAL= 'Cleared - Personal Reference';
    const PES_STATUS_DECLINED      = 'Declined';
    const PES_STATUS_EXCEPTION     = 'Exception';
    const PES_STATUS_PROVISIONAL   = 'Provisional Clearance';
    const PES_STATUS_FAILED        = 'Failed';
    const PES_STATUS_PES_REQUESTED = 'Pes Requested';
    const PES_STATUS_EVI_REQUESTED = 'Evidence Requested';
    const PES_STATUS_REMOVED       = 'Removed';
    const PES_STATUS_CANCEL_REQ     = 'Cancel Requested';
    const PES_STATUS_CANCEL_CONFIRMED = 'Cancel Confirmed';
    const PES_STATUS_TBD           = 'TBD';



    static public $pesEvents = array('Consent Form','Right to Work','Proof of Id','Residency','Credit Check','Financial Sanctions','Criminal Records Check','Activity','Qualifications','Directors','Media','Membership');

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
            <label for='contract_id' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Contract'>Contract</label>
        	<div class='col-md-3'>
        		<select id='contract_id' class='form-group select2' name='contract_id' disabled >
        		<option value=''></option>
        		</select>
        		<input id='ACCOUNT_ID' name='ACCOUNT_ID' type='hidden'>
            </div>
        </div>
        <div class="form-group required " >
            <label for='PES_LEVEL' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Not applicable on all contracts'>PES Level</label>
        	<div class='col-md-3'>
        		<select id='PES_LEVEL' class='form-group select2' name='PES_LEVEL' <?=$notEditable?> data-placeholder='Select Pes Level' disabled >
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
    	<input id='PES_STATUS' name='PES_STATUS' type='hidden'  value='<?=AccountPersonRecord::PES_STATUS_PES_REQUESTED;?>'/>

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