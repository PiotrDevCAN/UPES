<?php
namespace upes;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;
use upes\AccountPersonTable;

/*

DROP TABLE UPES_DEV.ACCOUNT_PERSON;


CREATE TABLE UPES_DEV.ACCOUNT_PERSON ( ACCOUNT_ID INTEGER NOT NULL, UPES_REF INTEGER NOT NULL, PES_LEVEL CHAR(5), PES_DATE_REQUESTED DATE NOT NULL WITH DEFAULT CURRENT DATE, PES_REQUESTOR CHAR(75) NOT NULL, PES_STATUS CHAR(50) NOT NULL WITH DEFAULT 'Request Created', PES_STATUS_DETAILS CLOB(1048576), PES_CLEARED_DATE DATE, PES_RECHECK_DATE CHAR(5)
                                       , CONSENT CHAR(10),RIGHT_TO_WORK CHAR(10),PROOF_OF_ID CHAR(10),PROOF_OF_RESIDENCY CHAR(10),CREDIT_CHECK CHAR(10),FINANCIAL_SANCTIONS CHAR(10),CRIMINAL_RECORDS_CHECK CHAR(10)
                                       , PROOF_OF_ACTIVITY CHAR(10),QUALIFICATIONS CHAR(10),DIRECTORS CHAR(10),MEDIA CHAR(10),MEMBERSHIP CHAR(10)
                                       , PROCESSING_STATUS CHAR(20), PROCESSING_STATUS_CHANGED TIMESTAMP
                                       , DATE_LAST_CHASED DATE, COMMENT VARCHAR(8192), PRIORITY CHAR(10)
                                       , SYSTEM_START_TIME TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW BEGIN, SYSTEM_END_TIME TIMESTAMP(12) NOT NULL GENERATED ALWAYS AS ROW END, TRANS_ID TIMESTAMP(12) GENERATED ALWAYS AS TRANSACTION START ID, PERIOD SYSTEM_TIME(SYSTEM_START_TIME,SYSTEM_END_TIME) );
CREATE TABLE UPES_DEV.ACCOUNT_PERSON_HIST ( ACCOUNT_ID INTEGER NOT NULL, UPES_REF INTEGER NOT NULL , PES_LEVEL CHAR(5), PES_DATE_REQUESTED DATE NOT NULL, PES_REQUESTOR CHAR(75) NOT NULL,  PES_STATUS CHAR(50) NOT NULL, PES_STATUS_DETAILS CLOB(1048576), PES_CLEARED_DATE DATE, PES_RECHECK_DATE CHAR(5)
                                       , CONSENT CHAR(10),RIGHT_TO_WORK CHAR(10),PROOF_OF_ID CHAR(10),PROOF_OF_RESIDENCY CHAR(10),CREDIT_CHECK CHAR(10),FINANCIAL_SANCTIONS CHAR(10),CRIMINAL_RECORDS_CHECK CHAR(10)
                                       , PROOF_OF_ACTIVITY CHAR(10),QUALIFICATIONS CHAR(10),DIRECTORS CHAR(10),MEDIA CHAR(10),MEMBERSHIP CHAR(10)
                                       , PROCESSING_STATUS CHAR(20), PROCESSING_STATUS_CHANGED TIMESTAMP
                                       , DATE_LAST_CHASED DATE, COMMENT VARCHAR(8192), PRIORITY CHAR(10)
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



    static function getPesStatusWithButtons($row){
        $email   = trim($row['EMAIL_ADDRESS']);
        $upesRef = trim($row['UPES_REF']);
        $status  = trim($row['PES_STATUS']);
        $boarder = stripos(trim($row['PES_STATUS_DETAILS']),'Boarded as')!== false ;
        $passportFirst   = array_key_exists('PASSPORT_FIRST_NAME', $row) ? $row['PASSPORT_FIRST_NAME'] : null;
        $passportLastname = array_key_exists('PASSPORT_LAST_NAME', $row)    ? $row['PASSPORT_LAST_NAME'] : null;

        $pesStatusWithButton = '';
        $pesStatusWithButton.= "<span class='pesStatusField' data-upesref='" . $upesRef . "'>" .  $status . "</span><br/>";
        switch (true) {
            case $boarder:
                // Don't add buttons if this is a boarded - pre-boarder record.
                break;
            case $status == AccountPersonRecord::PES_STATUS_TBD && !$_SESSION['isPesTeam']:
                $pesStatusWithButton.= "<button type='button' class='btn btn-default btn-xs btnPesInitiate accessRestrict accessPmo accessFm' ";
                $pesStatusWithButton.= "aria-label='Left Align' ";
                $pesStatusWithButton.= " data-upesref='" .$upesRef . "' ";
                $pesStatusWithButton.= " data-pesstatus='$status' ";
                $pesStatusWithButton.= " data-toggle='tooltip' data-placement='top' title='Initiate PES Request'";
                $pesStatusWithButton.= " > ";
                $pesStatusWithButton.= "<span class='glyPesInitiate glyphicon glyphicon-plane ' aria-hidden='true'></span>";
                $pesStatusWithButton.= "</button>&nbsp;";
                break;
            case $status == AccountPersonRecord::PES_STATUS_PES_REQUESTED && $_SESSION['isPesTeam'] ;
            $emailAddress = trim($row['EMAIL_ADDRESS']);
            $fullName    = trim($row['FULL_NAME']);
            $country      = trim($row['COUNTRY']);
            $cnum         = trim($row['CNUM']);

            $missing = !empty($emailAddress) ? '' : ' Email Address';
            $missing.= !empty($fullName) ? '' : ' Full Name';
            $missing.= !empty($country) ? '' : ' Country';

            $valid = empty(trim($missing));

            $disabled = $valid ? '' : 'disabled';
            $tooltip = $valid ? 'Confirm PES Email details' : "Missing $missing";


            $pesStatusWithButton.= "<button type='button' class='btn btn-default btn-xs btnSendPesEmail accessRestrict accessPmo accessFm' ";
            $pesStatusWithButton.= "aria-label='Left Align' ";
            $pesStatusWithButton.= " data-emailaddress='$emailAddress' ";
            $pesStatusWithButton.= " data-fullnamee='$fullName' ";
            $pesStatusWithButton.= " data-country='$country' ";
            $pesStatusWithButton.= " data-upesref='$upesRef' ";
            $pesStatusWithButton.= " data-toggle='tooltip' data-placement='top' title='$tooltip'";
            $pesStatusWithButton.= " $disabled  ";
            $pesStatusWithButton.= " > ";
            $pesStatusWithButton.= "<span class='glyphicon glyphicon-send ' aria-hidden='true' ></span>";

            $pesStatusWithButton.= "</button>&nbsp;";
            case $status == AccountPersonRecord::PES_STATUS_REQUESTED && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_CLEARED_PERSONAL && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_CLEARED && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_EXCEPTION && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_DECLINED && $_SESSION['isPesTeam'] ;
            case $status == AccountPersonRecord::PES_STATUS_FAILED && $_SESSION['isPesTeam'] ;
            case $status == AccountPersonRecord::PES_STATUS_REMOVED && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_REVOKED && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_LEFT_IBM && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_PROVISIONAL && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_TBD && $_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_RECHECK_REQ && $_SESSION['isPesTeam'] :
                $pesStatusWithButton.= "<button type='button' class='btn btn-default btn-xs btnPesStatus' aria-label='Left Align' ";
                $pesStatusWithButton.= " data-upesref='" .$upesRef . "' ";
                $pesStatusWithButton.= " data-email='" . $email . "' ";
                $pesStatusWithButton.= " data-pesdaterequested='" .trim($row['PES_DATE_REQUESTED']) . "' ";
                $pesStatusWithButton.= " data-pesrequestor='" .trim($row['PES_REQUESTOR']) . "' ";
                $pesStatusWithButton.= " data-pesstatus='" .$status . "' ";
                $pesStatusWithButton.= array_key_exists('PASSPORT_FIRST_NAME', $row) ?  " data-passportfirst='" .$passportFirst . "' " : null;
                $pesStatusWithButton.= array_key_exists('PASSPORT_LAST_NAME', $row) ? " data-passportlastname='" .$passportLastname . "' " : null;
                $pesStatusWithButton.= " data-toggle='tooltip' data-placement='top' title='Amend PES Status'";
                $pesStatusWithButton.= " > ";
                $pesStatusWithButton.= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $pesStatusWithButton.= "</button>";
                break;
            case $status == AccountPersonRecord::PES_STATUS_EVI_REQUESTED && !$_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_RECHECK_REQ && !$_SESSION['isPesTeam'] :
            case $status == AccountPersonRecord::PES_STATUS_PES_REQUESTED && !$_SESSION['isPesTeam'] ;
            $pesStatusWithButton.= "<button type='button' class='btn btn-default btn-xs btnPesCancel accessRestrict accessFm' aria-label='Left Align' ";
            $pesStatusWithButton.= " data-upesref='" .$upesRef . "' ";
            $pesStatusWithButton.= " data-email='" . $email . "' ";
            $pesStatusWithButton.= " data-pesdaterequested='" .trim($row['PES_DATE_REQUESTED']) . "' ";
            $pesStatusWithButton.= " data-pesrequestor='" .trim($row['PES_REQUESTOR']) . "' ";
            $pesStatusWithButton.= " data-pesstatus='" .$status . "' ";
            $pesStatusWithButton.= array_key_exists('PASSPORT_FIRST_NAME', $row) ?  " data-passportfirst='" .$passportFirst . "' " : null;
            $pesStatusWithButton.= array_key_exists('PASSPORT_LAST_NAME', $row) ? " data-passportlastname='" .$passportLastname . "' " : null;
            $pesStatusWithButton.= " data-toggle='tooltip' data-placement='top' title='Cancel PES Request'";
            $pesStatusWithButton.= " > ";
            $pesStatusWithButton.= "<span class='glyphicon glyphicon-erase ' aria-hidden='true' ></span>";
            $pesStatusWithButton.= "</button>";
            break;
            case $status == AccountPersonRecord::PES_STATUS_CANCEL_CONFIRMED && $_SESSION['isPesTeam'] :
            default:
                break;
        }

        if(isset($row['PROCESSING_STATUS']) && ( $row['PES_STATUS']== AccountPersonRecord::PES_STATUS_EVI_REQUESTED || $row['PES_STATUS']==AccountPersonRecord::PES_STATUS_PES_REQUESTED || $row['PES_STATUS']==AccountPersonRecord::PES_STATUS_RECHECK_REQ ) ){
            $pesStatusWithButton .= "&nbsp;<button type='button' class='btn btn-default btn-xs btnTogglePesTrackerStatusDetails' aria-label='Left Align' data-toggle='tooltip' data-placement='top' title='See PES Tracker Status' >";
            $pesStatusWithButton .= !empty($row['PROCESSING_STATUS']) ? "&nbsp;<small>" . $row['PROCESSING_STATUS'] . "</small>&nbsp;" : null;
            $pesStatusWithButton .= "<span class='glyphicon glyphicon-search  ' aria-hidden='true' ></span>";
            $pesStatusWithButton .= "</button>";

            $pesStatusWithButton .= "<div class='alert alert-info text-center pesProcessStatusDisplay' role='alert' style='display:none' >";
            ob_start();
            pesTrackerTable::formatProcessingStatusCell($row);
            $pesStatusWithButton .= ob_get_clean();
            $pesStatusWithButton .= "</div>";
        }

        return $pesStatusWithButton;

    }



}