<?php
namespace upes;

use itdq\DbTable;
use itdq\Loader;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use \DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\xls;
use itdq\AuditTable;

/*
 *
 *ALTER TABLE "UPES_UT"."ACCOUNT_PERSON" ALTER COLUMN "PES_RECHECK_DATE" SET DATA TYPE DATE;

 *
 */

class AccountPersonTable extends DbTable {

use xls;

protected $preparedStageUpdateStmts;
protected $preparedTrackerInsert;
protected $preparedGetPesCommentStmt;
protected $preparedProcessStatusUpdate;
protected $preparedGetProcessingStatusStmt;

const PES_TRACKER_RECORDS_ACTIVE       = 'Active';
const PES_TRACKER_RECORDS_ACTIVE_PLUS  = 'Active Plus';
const PES_TRACKER_RECORDS_NOT_ACTIVE   = 'Not Active';
const PES_TRACKER_RECORDS_ALL          = 'All';
const PES_TRACKER_RECORDS_ACTIVE_REQUESTED = 'Active Requested';
const PES_TRACKER_RECORDS_ACTIVE_PROVISIONAL = 'Active Provisional';



const PES_TRACKER_RETURN_RESULTS_AS_ARRAY      = 'array';
const PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET = 'resultSet';

const PES_TRACKER_STAGE_CONSENT        = 'Consent Form';
const PES_TRACKER_STAGE_WORK           = 'Right to Work';
const PES_TRACKER_STAGE_ID             = 'Proof of Id';
const PES_TRACKER_STAGE_RESIDENCY      = 'Residency';
const PES_TRACKER_STAGE_CREDIT         = 'Credit Check';
const PES_TRACKER_STAGE_SANCTIONS      = 'Financial Sanctions';
const PES_TRACKER_STAGE_CRIMINAL       = 'Criminal Records Check';
const PES_TRACKER_STAGE_ACTIVITY       = 'Activity';
const PES_TRACKER_STAGE_QUALIFICATIONS = 'Qualifications';
const PES_TRACKER_STAGE_DIRECTORS      = 'Directors';
const PES_TRACKER_STAGE_MEDIA          = 'Media';
const PES_TRACKER_STAGE_MEMBERSHIP     = 'Membership';




const PES_TRACKER_STAGES =  array('CONSENT','RIGHT_TO_WORK','PROOF_OF_ID','PROOF_OF_RESIDENCY','CREDIT_CHECK','FINANCIAL_SANCTIONS','CRIMINAL_RECORDS_CHECK','PROOF_OF_ACTIVITY','QUALIFICATIONS','DIRECTORS','MEDIA','MEMBERSHIP');


    static function returnPesEventsTable($records='Active',$returnResultsAs='array'){

        switch (trim($records)){
            case self::PES_TRACKER_RECORDS_ACTIVE :
                $pesStatusPredicate = "  AP.PES_STATUS in('" . AccountPersonRecord::PES_STATUS_STARTER_REQUESTED . "','" . AccountPersonRecord::PES_STATUS_CANCEL_REQ . "','" . AccountPersonRecord::PES_STATUS_PES_PROGRESSING. "','" . AccountPersonRecord::PES_STATUS_PROVISIONAL. "') ";
                break;
            case self::PES_TRACKER_RECORDS_ACTIVE_PLUS :
                $pesStatusPredicate = "  AP.PES_STATUS in('" . AccountPersonRecord::PES_STATUS_STARTER_REQUESTED . "','" . AccountPersonRecord::PES_STATUS_CANCEL_REQ . "','" . AccountPersonRecord::PES_STATUS_CANCEL_CONFIRMED . "','" . AccountPersonRecord::PES_STATUS_PES_PROGRESSING. "','" . AccountPersonRecord::PES_STATUS_PROVISIONAL. "','" . AccountPersonRecord::PES_STATUS_RECHECK_REQ . "','" . AccountPersonRecord::PES_STATUS_REMOVED. "','" . AccountPersonRecord::PES_STATUS_CLEARED. "') ";
                break;
            case self::PES_TRACKER_RECORDS_ACTIVE_REQUESTED :
                $pesStatusPredicate = "  AP.PES_STATUS in('" . AccountPersonRecord::PES_STATUS_STARTER_REQUESTED . "','" . AccountPersonRecord::PES_STATUS_CANCEL_REQ . "','" . AccountPersonRecord::PES_STATUS_PES_PROGRESSING. "') ";
                break;
            case self::PES_TRACKER_RECORDS_ACTIVE_PROVISIONAL :
                $pesStatusPredicate = "  AP.PES_STATUS in('" . AccountPersonRecord::PES_STATUS_PROVISIONAL. "') ";
                break;
            case self::PES_TRACKER_RECORDS_NOT_ACTIVE :
                $pesStatusPredicate = " AP.PES_STATUS not in ('" . AccountPersonRecord::PES_STATUS_STARTER_REQUESTED . "','" . AccountPersonRecord::PES_STATUS_CANCEL_REQ . "','" . AccountPersonRecord::PES_STATUS_PES_PROGRESSING. "','" . AccountPersonRecord::PES_STATUS_PROVISIONAL. "')  ";
                $pesStatusPredicate.= " AND AP.PROCESSING_STATUS_CHANGED > current timestamp - 31 days  ";
                break;
            case self::PES_TRACKER_RECORDS_ALL :
                $pesStatusPredicate = "  ";
                break;
            default:
                $pesStatusPredicate = 'pass a parm muppet ';
                break;
        }

        $sql = " SELECT P.CNUM ";
        $sql.= ", P.UPES_REF ";
        $sql.= ", P.EMAIL_ADDRESS ";
        $sql.= ", A.ACCOUNT ";
        $sql.= ", A.ACCOUNT_ID ";
        $sql.= ", P.PASSPORT_FIRST_NAME ";
        $sql.= ", P.PASSPORT_LAST_NAME ";
        $sql.= ", case when P.PASSPORT_FIRST_NAME is null then P.FULL_NAME else P.PASSPORT_FIRST_NAME CONCAT ' ' CONCAT P.PASSPORT_LAST_NAME end as FULL_NAME  ";
        $sql.= ", P.COUNTRY ";
        $sql.= ", AP.PES_DATE_REQUESTED ";
        $sql.= ", AP.PES_REQUESTOR ";
        $sql.= ", AP.CONSENT ";
        $sql.= ", AP.RIGHT_TO_WORK ";
        $sql.= ", AP.PROOF_OF_ID ";
        $sql.= ", AP.PROOF_OF_RESIDENCY ";
        $sql.= ", AP.CREDIT_CHECK ";
        $sql.= ", AP.FINANCIAL_SANCTIONS ";
        $sql.= ", AP.CRIMINAL_RECORDS_CHECK ";
        $sql.= ", AP.PROOF_OF_ACTIVITY ";
        $sql.= ", AP.QUALIFICATIONS ";
        $sql.= ", AP.DIRECTORS ";
        $sql.= ", AP.MEDIA ";
        $sql.= ", AP.MEMBERSHIP ";
        $sql.= ", AP.PROCESSING_STATUS ";
        $sql.= ", AP.PROCESSING_STATUS_CHANGED ";
        $sql.= ", AP.DATE_LAST_CHASED ";
        $sql.= ", AP.PES_STATUS ";
        $sql.= ", AP.PES_STATUS_DETAILS ";
        $sql.= ", AP.COMMENT ";
        $sql.= ", AP.PRIORITY ";
        $sql.= ", AP.COUNTRY_OF_RESIDENCE ";
        $sql.= ", P.IBM_STATUS ";

        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . AllTables::$PERSON . " as P ";
        $sql.= " left join " . $_SESSION['Db2Schema'] . "." . AllTables::$ACCOUNT_PERSON . " as AP ";
        $sql.= " ON P.UPES_REF = AP.UPES_REF ";
        $sql.= " left join " . $_SESSION['Db2Schema'] . "." . AllTables::$ACCOUNT . " as A ";
        $sql.= " ON AP.ACCOUNT_ID = A.ACCOUNT_ID ";
        $sql.= " WHERE 1=1 ";
        $sql.= " and (AP.UPES_REF is not null or ( AP.UPES_REF is null  AND AP.PES_STATUS_DETAILS is null )) "; // it has a tracker record
        $sql.= " AND " . $pesStatusPredicate;

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception('Error in ' . __METHOD__ . " running $sql");
        }

        switch ($returnResultsAs) {
            case self::PES_TRACKER_RETURN_RESULTS_AS_ARRAY:
                $report = array();
                while(($row=db2_fetch_assoc($rs))==true){
                    $report[] = $row;
                }
                return $report;
            break;
            case self::PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET:
                return $rs;
            default:
                return false;
                break;
           }
        }


    function buildTable($records='Active'){
        $allRows = self::returnPesEventsTable($records,self::PES_TRACKER_RETURN_RESULTS_AS_ARRAY);
        ob_start();
        ?>
        <table id='pesTrackerTable' class='table table-striped table-bordered table-condensed '  style='width:100%'>
		<thead>
		<tr class='' ><th>Person Details</th><th>Account</th><th>Requestor</th>
		<th width="5px">Consent Form</th>
		<th width="5px">Proof or Right to Work</th>
		<th width="5px">Proof of ID</th>
		<th width="5px">Proof of Residency</th>
		<th width="5px">Credit Check</th>
		<th width="5px">Financial Sanctions</th>
		<th width="5px">Criminal Records Check</th>
		<th width="5px">Proof of Activity</th>
		<th width="5px">Qualifications</th>
		<th width="5px">Directors</th>
		<th width="5px">Media</th>
		<th width="5px">Membership</th>
		<th>Process Status</th><th>PES Status</th><th>Comment</th></tr>
		<tr class='searchingRow wrap'>
		<td>Email Address</td>
		<td>Account</td>
		<td>Requestor</td>
		<td>Country</td>
		<td>Consent</td>
		<td>Right to Work</td>
		<td>ID</td>
		<td>Residency</td>
		<td>Credit Check</td>
		<td>Financial Sanctions</td>
		<td>Criminal Records Check</td>
		<td>Proof of Activity</td>
		<td>Qualifications</td>
		<td>Directors</td>
		<td>Media</td>
		<td>Membership</td>
		<td>Process Status</td><td>PES Status</td><td>Comment</td></tr>
		</thead>
		<tbody>
		<?php

        foreach ($allRows as $row){
            $today = new \DateTime();
            $date = DateTime::createFromFormat('Y-m-d', $row['PES_DATE_REQUESTED']);
            $age  = !empty($row['PES_DATE_REQUESTED']) ?  $date->diff($today)->format('%R%a days') : null ;
            // $age = !empty($row['PES_DATE_REQUESTED']) ? $interval->format('%R%a days') : null;
            $upesref = trim($row['UPES_REF']);
            $accountId = trim($row['ACCOUNT_ID']);
            $account = trim($row['ACCOUNT']);
            $fullName = trim($row['FULL_NAME']);
            $emailaddress = trim($row['EMAIL_ADDRESS']);
            $requestor = trim($row['PES_REQUESTOR']);

            $formattedIdentityField = self::formatEmailFieldOnTracker($row);

            ?>
            <tr class='<?=$upesref;?> personDetails' data-upesref='<?=$upesref;?>' data-accountid='<?=$accountId;?>' data-account='<?=$account;?>' data-fullname='<?=$fullName;?>' data-emailaddress='<?=$emailaddress;?>'  data-requestor='<?=$requestor;?>'   >
            <td class='formattedEmailTd'>
            <div class='formattedEmailDiv'><?=$formattedIdentityField;?></div>
            </td>
            <td><?=$row['ACCOUNT']?></td>
            <td><?=$row['PES_REQUESTOR']?><br/><small><?=$row['PES_DATE_REQUESTED']?><br/><?=$age?></small></td>

            <?php
            foreach (self::PES_TRACKER_STAGES as $stage) {
                $stageValue         = !empty($row[$stage]) ? trim($row[$stage]) : 'TBD';
                $stageAlertValue    = self::getAlertClassForPesStage($stageValue);
                ?>
                <td class='nonSearchable'>
            	<?=self::getButtonsForPesStage($stageValue, $stageAlertValue, $stage, $upesref, $accountId);?>
                </td>
                <?php
            }
            ?>
            <td class='nonSearchable'>
            <div class='alert alert-info text-center pesProcessStatusDisplay' role='alert' ><?=self::formatProcessingStatusCell($row);?></div>
            <div class='text-center'>
            <span style='white-space:nowrap' >
            <a class="btn btn-xs btn-info  btnProcessStatusChange accessPes accessCdi" 		data-processstatus='PES' data-toggle="tooltip" data-placement="top" title="With PES Team" ><i class="fas fa-users"></i></a>
            <a class="btn btn-xs btn-info  btnProcessStatusChange accessPes accessCdi" 		data-processstatus='User' data-toggle="tooltip" data-placement="top" title="With Applicant" ><i class="fas fa-user"></i></a>
            <a class="btn btn-xs btn-info   btnProcessStatusChange accessPes accessCdi" 	data-processstatus='CRC' data-toggle="tooltip" data-placement="top" title="Awaiting CRC"><i class="fas fa-gavel"></i></a>
            <button class='btn btn-info btn-xs  btnProcessStatusChange accessPes accessCdi' data-processstatus='Unknown' data-toggle="tooltip"  title="Unknown"><span class="glyphicon glyphicon-erase" ></span></button>
            </span>
            <?php
            $dateLastChased = !empty($row['DATE_LAST_CHASED']) ? DateTime::createFromFormat('Y-m-d', $row['DATE_LAST_CHASED']) : null;
            $dateLastChasedFormatted = !empty($row['DATE_LAST_CHASED']) ? $dateLastChased->format('d M Y') : null;
            $alertClass = !empty($row['DATE_LAST_CHASED']) ? self::getAlertClassForPesChasedDate($row['DATE_LAST_CHASED']) : 'alert-info';
            ?>
            <div class='alert <?=$alertClass;?>'>
            <input class="form-control input-sm pesDateLastChased" value="<?=$dateLastChasedFormatted?>" type="text" placeholder='Last Chased' data-toggle='tooltip' title='PES Date Last Chased' data-upesref='<?=$upesref?>'  data-accountid='<?=$accountId?>'  data-account='<?=$account?>'>
            </div>
            <span style='white-space:nowrap' >
            <a class="btn btn-xs btn-info  btnChaser accessPes accessCdi" data-chaser='One'  data-toggle="tooltip" data-placement="top" title="Chaser One" ><i>1</i></a>
            <a class="btn btn-xs btn-info  btnChaser accessPes accessCdi" data-chaser='Two'  data-toggle="tooltip" data-placement="top" title="Chaser Two" ><i>2</i></a>
            <a class="btn btn-xs btn-info  btnChaser accessPes accessCdi" data-chaser='Three' data-toggle="tooltip" data-placement="top" title="Chaser Three"><i>3</i></a>
            </span>
            </div>
            </td>
            <td class='nonSearchable'><?=AccountPersonRecord::getPesStatusWithButtons($row)?></td>
            <td class='pesCommentsTd'><textarea rows="3" cols="20"  data-upesref='<?=$upesref?>' data-accountid='<?=$accountId?>'></textarea><br/>
            <button class='btn btn-default btn-xs btnPesSaveComment accessPes accessCdi' data-setpesto='Yes' data-toggle="tooltip" data-placement="top" title="Save Comment" ><span class="glyphicon glyphicon-save" ></span></button>
            <div class='pesComments' data-upesref='<?=$upesref?>'><small><?=$row['COMMENT']?></small></div>
            </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
		</table>
		<?php
		$table = ob_get_clean();
		return $table;
    }




    function displayTable($records='Active Initiated'){
        ?>
        <div class='container-fluid' >
        <div class='col-sm-8 col-sm-offset-1'>
          <form class="form-horizontal">
  			<div class="form-group">
    			<label class="control-label col-sm-1" for="pesTrackerTableSearch">Table Search:</label>
    			<div class="col-sm-3" >
      			<input type="text" id="pesTrackerTableSearch" placeholder="Search"  onkeyup=searchTable()  />
      			<br/>

				</div>

    			<label class="control-label col-sm-1" for="pesRecordFilter">Records:</label>
    			<div class="col-sm-4" >
    			<div class="btn-group" role="group" aria-label="Record Selection">
  					<button type="button" role='button' name='pesRecordFilter' class="btn btn-sm btn-info btnRecordSelection active" data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_REQUESTED?>'    data-toggle='tooltip'  title='Active Record in Initiated or Requested status'     >Requested</button>
					<button type="button" role='button' name='pesRecordFilter' class="btn btn-sm btn-info btnRecordSelection "       data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_PROVISIONAL?>'  data-toggle='tooltip'  title='Active Records in Provisional Clearance status' >Provisional</button>
  					<button type="button" role='button' name='pesRecordFilter' class="btn btn-sm btn-info btnRecordSelection "       data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE?>'              data-toggle='tooltip'  title='Active Records'     >Active</button>
  					<button type="button" role='button' name='pesRecordFilter' class="btn btn-sm btn-info btnRecordSelection "       data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_PLUS?>'         data-toggle='tooltip'  title='Active+ Records'     >Active+</button>
  					<button type="button" role='button' name='pesRecordFilter' class="btn btn-sm btn-info btnRecordSelection"        data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_NOT_ACTIVE?>'          data-toggle='tooltip'  title='Recently Closed'  >Recent</button>
				</div>
				</div>

    			<label class="control-label col-sm-1" for="pesPriorityFilter">Filters:</label>
    			<div class="col-sm-2" >
  				<span style='white-space:nowrap' id='pesPriorityFilter' >
  				<button class='btn btn-sm btn-danger  btnSelectPriority accessPes accessCdi' data-pespriority='1'  data-toggle='tooltip'  title='Filter on High'  type='button' onclick='return false;'><span class='glyphicon glyphicon-king' ></span></button>
            	<button class='btn btn-sm btn-warning btnSelectPriority accessPes accessCdi' data-pespriority='2'  data-toggle='tooltip'  title='Filter on Medium' type='button' onclick='return false;'><span class='glyphicon glyphicon-knight' ></span></button>
            	<button class='btn btn-sm btn-success btnSelectPriority accessPes accessCdi' data-pespriority='3'  data-toggle='tooltip'  title='Filter on Low' type='button' onclick='return false;'><span class='glyphicon glyphicon-pawn' ></span></button>
            	<button class='btn btn-sm btn-info    btnSelectPriority accessPes accessCdi' data-pespriority='0'  data-toggle='tooltip'  title='Filter off' type='button' onclick='return false;'><span class='glyphicon glyphicon-ban-circle' ></span></button>
            	<br/><br/>
            	<a class="btn btn-sm btn-info  btnSelectProcess accessPes accessCdi" 		data-pesprocess='PES' data-toggle="tooltip" data-placement="top" title="With PES Team" ><i class="fas fa-users"></i></a>
                <a class="btn btn-sm btn-info  btnSelectProcess accessPes accessCdi" 		data-pesprocess='User' data-toggle="tooltip" data-placement="top" title="With Applicant" ><i class="fas fa-user"></i></a>
                <a class="btn btn-sm btn-info   btnSelectProcess accessPes accessCdi" 	    data-pesprocess='CRC' data-toggle="tooltip" data-placement="top" title="Awaiting CRC"><i class="fas fa-gavel"></i></a>
                <button class='btn btn-info btn-sm  btnSelectProcess accessPes accessCdi'   data-pesprocess='Unknown' data-toggle="tooltip"  title="Status Unknown" type='button' onclick='return false;'><span class="glyphicon glyphicon-erase" ></span></button>
              	</span>
              	</div>
              	<div class="col-sm-1"  >
              	<span style='white-space:nowrap' id='pesDownload' >
				<a class='btn btn-sm btn-link accessBasedBtn accessPes accessCdi' href='/dn_pesTracker.php'><i class="glyphicon glyphicon-download-alt"></i> PES Tracker</a>
				<a class='btn btn-sm btn-link accessBasedBtn accessPes accessCdi' href='/dn_pesTrackerRecent.php'><i class="glyphicon glyphicon-download-alt"></i> PES Tracker(Recent)</a>
				<a class='btn btn-sm btn-link accessBasedBtn accessPes accessCdi' href='/dn_pesTrackerActivePlus.php'><i class="glyphicon glyphicon-download-alt"></i> PES Tracker(Active+)</a>

				</span>
            	</div>
  			</div>
		  </form>
		  </div>
		</div>

		<div id='pesTrackerTableDiv' class='center-block' width='100%'>
		</div>
		<?php
    }



    function setPesStageValue($upesref,$account_id, $stage,$stageValue){
        $preparedStmt = $this->prepareStageUpdate($stage);
        $data = array($stageValue,$account_id, $upesref);

        $rs = db2_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update PES Stage: $stage to $stageValue for $upesref on $account_id ");
        }
        return true;
    }

    function prepareStageUpdate($stage){
//         if(!empty($_SESSION['preparedStageUpdateStmts'][strtoupper(db2_escape_string($stage))] )) {
//             return $_SESSION['preparedStageUpdateStmts'][strtoupper(db2_escape_string($stage))];
//         }
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET " . strtoupper(db2_escape_string($stage)) . " = ? ";
        $sql.= " WHERE ACCOUNT_ID= ? and UPES_REF=? ";

        $this->preparedSelectSQL = $sql;

        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);

        if($preparedStmt){
            $_SESSION['preparedStageUpdateStmts'][strtoupper(db2_escape_string($stage))] = $preparedStmt;
        }
        return $preparedStmt;
    }


    function savePesComment($upesref, $account_id,$comment){
        $existingComment = $this->getPesComment($upesref, $account_id);
        $now = new \DateTime();

        $newComment = trim($comment) . "<br/><small>" . $_SESSION['ssoEmail'] . ":" . $now->format('Y-m-d H:i:s') . "</small><br/>" . $existingComment;


        $commentFieldSize = (int)$this->getColumnLength('COMMENT');

        if(strlen($newComment)>$commentFieldSize){
            AuditTable::audit("PES Tracker Comment too long. Will be truncated.<b>Old:</b>$existingComment <br>New:$comment");
            $newComment = substr($newComment,0,$commentFieldSize-20);
        }


        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET COMMENT='" . db2_escape_string($newComment) . "' ";
        $sql.= " WHERE UPES_REF='" . db2_escape_string($upesref) . "' AND ACCOUNT_ID='" . db2_escape_string($account_id) . "' ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception("Failed to update PES Comment for $upesref Account: $account_id. Comment was " . $comment);
        }

        return $newComment;
    }

    function prepareGetPesCommentStmt(){
//         if(!empty($_SESSION['preparedGetPesCommentStmt'])){
//             return $_SESSION['preparedGetPesCommentStmt'];
//         }

        $sql = " SELECT COMMENT FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE UPES_REF=?  and ACCOUNT_ID = ? ";

        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);

        if($preparedStmt){
            $_SESSION['preparedGetPesCommentStmt'] = $preparedStmt;
            return $preparedStmt;
        }

        throw new \Exception('Unable to prepare GetPesComment');
    }


    function getPesComment($uposref, $account_id){
        $preparedStmt = $this->prepareGetPesCommentStmt();
        $data = array($uposref, $account_id);

        $rs = db2_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'Prepared Stmt');
            throw new \Exception('Unable to getPesComment for ' . $uposref . ":" . $account_id );
        }

        $row = db2_fetch_assoc($preparedStmt);
        return $row['COMMENT'];
    }


    function savePesPriority($upesRef, $accountId,$pesPriority=null){

        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PRIORITY=";
        $sql.= !empty($pesPriority) ? "'" . db2_escape_string($pesPriority) . "' " : " null, ";
        $sql.= " WHERE UPES_REF='" . db2_escape_string($upesRef) . "' and ACCOUNT_ID='" . db2_escape_string($accountId) . "' ";

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update Pes Priority: $pesPriority for $upesRef");
        }

        return true;
    }

    static function formatProcessingStatusCell($row){
        $processingStatus = empty($row['PROCESSING_STATUS']) ? 'Unknown' : trim($row['PROCESSING_STATUS']) ;
        $today = new \DateTime();
        $date = DateTime::createFromFormat('Y-m-d H:i:s', substr($row['PROCESSING_STATUS_CHANGED'],0,19));
        $age  = !empty($row['PROCESSING_STATUS_CHANGED']) ?  $date->diff($today)->format('%R%a days') : null ;

        echo $processingStatus;?><br/><small><?=substr(trim($row['PROCESSING_STATUS_CHANGED']),0,10);?><br/><?=$age?></small><?php
    }

    static function formatEmailFieldOnTracker($row){

        $priority = !empty($row['PRIORITY']) ? ucfirst(trim($row['PRIORITY'])) : 'TBD';

        switch (trim($row['PRIORITY'])){
            case 'High':
            case 1:
                $alertClass='alert-danger';
                break;
            case 'Medium':
            case 2:
                $alertClass='alert-warning';
                break;
            case 'Low':
            case 3:
                $alertClass='alert-success';
                break;
            default:
                $alertClass='alert-info';
                break;
        }

        $formattedField = trim($row['EMAIL_ADDRESS']) . "<br/><small>";
        $formattedField.= "<i>" . trim($row['PASSPORT_FIRST_NAME']) . "&nbsp;<b>" . trim($row['PASSPORT_LAST_NAME']) . "</b></i><br/>";
        $formattedField.= trim($row['FULL_NAME']) . "</b></small><br/>" . trim($row['UPES_REF']);
        $formattedField.= "<br/>" . trim($row['IBM_STATUS']) . ":" . trim($row['COUNTRY']);
        $formattedField.= "<br/>Resides:&nbsp;" . trim($row['COUNTRY_OF_RESIDENCE']);
        $formattedField.= "<div class='alert $alertClass priorityDiv'>Priority:" . $priority . "</div>";

        $formattedField.="<span style='white-space:nowrap' >
            <button class='btn btn-xs btn-danger  btnPesPriority accessPes accessCdi' data-pespriority='1'  data-upesref='" . $row['UPES_REF'] ."' data-accountid='" . $row['ACCOUNT_ID'] . "' data-toggle='tooltip'  title='High' ><span class='glyphicon glyphicon-king' ></button>
            <button class='btn btn-xs btn-warning btnPesPriority accessPes accessCdi' data-pespriority='2' data-upesref='" . $row['UPES_REF'] ."' data-accountid='" . $row['ACCOUNT_ID'] . "' data-toggle='tooltip'  title='Medium' ><span class='glyphicon glyphicon-knight' ></button>
            <button class='btn btn-xs btn-success btnPesPriority accessPes accessCdi' data-pespriority='3' data-upesref='" . $row['UPES_REF'] ."' data-accountid='" . $row['ACCOUNT_ID'] . "' data-toggle='tooltip'  title='Low'><span class='glyphicon glyphicon-pawn' ></button>
            <button class='btn btn-xs btn-info    btnPesPriority accessPes accessCdi' data-pespriority='99'    data-upesref='" . $row['UPES_REF'] ."' data-accountid='" . $row['ACCOUNT_ID'] . "' data-toggle='tooltip'  title='Unknown'><span class='glyphicon glyphicon-erase' ></button>
            </span>";


        return $formattedField;
    }


    static function getAlertClassForPesStage($pesStageValue=null){
        switch ($pesStageValue) {
            case 'Yes':
                $alertClass = ' alert-success ';
                break;
            case 'Prov':
                $alertClass = ' alert-warning ';
                break;
            case 'N/A':
                $alertClass = ' alert-secondary ';
                break;
            default:
                $alertClass = ' alert-info ';
                break;
        }
        return $alertClass;
    }

    static function getAlertClassForPesChasedDate($pesChasedDate){
        $today = new \DateTime();
        $date = DateTime::createFromFormat('Y-m-d', $pesChasedDate);
        $age  = $date->diff($today)->d;

        switch (true) {
            case $age < 7 :
                $alertClass = ' alert-success ';
                break;
            case $age < 14:
                $alertClass = ' alert-warning ';
                break;
            default:
                $alertClass = ' alert-danger ';
                break;
        }
        return $alertClass;
    }

    static function getButtonsForPesStage($value, $alertClass, $stage, $upesref, $accountid){
        ?>
        <div class='alert <?=$alertClass;?> text-center pesStageDisplay' role='alert' ><?=$value;?></div>
        <div class='text-center columnDetails' data-pescolumn='<?=$stage?>' >
        <span style='white-space:nowrap' >
        <button class='btn btn-success btn-xs btnPesStageValueChange accessPes accessCdi'  data-setpesto='Yes' data-toggle="tooltip" data-placement="top" title="Cleared" ><span class="glyphicon glyphicon-ok-sign" ></span></button>
  		<button class='btn btn-warning btn-xs btnPesStageValueChange accessPes accessCdi'  data-setpesto='Prov' data-toggle="tooltip"  title="Stage Cleared Provisionally"><span class="glyphicon glyphicon-alert" ></span></button>
	  	<br/>
	  	<button class='btn btn-default btn-xs btnPesStageValueChange accessPes accessCdi' data-setpesto='N/A' data-toggle="tooltip"  title="Not applicable"><span class="glyphicon glyphicon-remove-sign" ></span></button>
	  	<button class='btn btn-info btn-xs btnPesStageValueChange accessPes accessCdi'    data-setpesto='TBD'data-toggle="tooltip"  title="Clear Field"><span class="glyphicon glyphicon-erase" ></span></button>
	  	</span>
	  	</div>
        <?php
    }

    function prepareProcessStatusUpdate(){
        if(!empty($_SESSION['preparedProcessStatusUpdate'] )) {
            return $_SESSION['prepareProcessStatusUpdate'];
        }
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PROCESSING_STATUS =?, PROCESSING_STATUS_CHANGED = current timestamp ";
        $sql.= " WHERE UPES_REF=? AND ACCOUNT_ID=?";

        $this->preparedSelectSQL = $sql;

        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);

        if($preparedStmt){
            $_SESSION['prepareProcessStatusUpdate'] = $preparedStmt;
        }

        return $preparedStmt;
    }


    function setPesProcessStatus($upesref, $accountid,$processStatus){
        $preparedStmt = $this->prepareProcessStatusUpdate();
        $data = array($processStatus,$upesref,$accountid);

        $rs = db2_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update PES Process Status $processStatus for $cnum");
        }

        return true;
    }


    function setPesDateLastChased($upesref, $accountId, $dateLastChased){

        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET DATE_LAST_CHASED=DATE('" . db2_escape_string($dateLastChased) . "') ";
        $sql.= " WHERE UPES_REF='" . db2_escape_string($upesref) . "' AND ACCOUNT_ID='" . db2_escape_string($accountId)  . "' ";

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update Date Last Chased to : $dateLastChased for $upesref / $accountId");
        }

        return true;
    }

    function setPesStatus($upesref=null,$accountid= null, $status=null,$requestor=null, $pesStatusDetails=null){

        $db2AutoCommit = db2_autocommit($_SESSION['conn']);
        db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);


        if(!$upesref or !$accountid or !$status){
            throw new \Exception('One or more of UPESREF/ACCOUNTID/STATUS not provided in ' . __METHOD__);
        }

        $requestor = empty($requestor) ? $_SESSION['ssoEmail'] : $requestor;

        switch ($status) {
            case AccountPersonRecord::PES_STATUS_STARTER_REQUESTED:
            case AccountPersonRecord::PES_STATUS_RECHECK_REQ:
                $requestor = empty($requestor) ? 'Unknown' : $requestor;
                $dateField = 'PES_DATE_REQUESTED';
                break;
            case AccountPersonRecord::PES_STATUS_PES_PROGRESSING:
                $dateField = 'PES_EVIDENCE_DATE';
                break;
            case AccountPersonRecord::PES_STATUS_CLEARED:
//            case AccountPersonRecord::PES_STATUS_CLEARED_PERSONAL:
                $dateField = 'PES_CLEARED_DATE';
                self::setPesRescheckDate($upesref,$accountid, $requestor);
                break;
            case AccountPersonRecord::PES_STATUS_PROVISIONAL:
            default:
                $dateField = 'PES_DATE_RESPONDED';
                break;
        }
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET $dateField = current date, PES_STATUS='" . db2_escape_string($status)  . "' ";
        $sql .= !empty($pesStatusDetails) ? " AND PES_STATUS_DETAILS='" . db2_escape_string($pesStatusDetails) . "' " : null;
        $sql .= trim($status)==AccountPersonRecord::PES_STATUS_STARTER_REQUESTED ? ", PES_REQUESTOR='" . db2_escape_string($requestor) . "' " : null;
        $sql .= " WHERE UPES_REF='" . db2_escape_string($upesref) . "' and ACCOUNT_ID='" . db2_escape_string($accountid)  . "' ";

        $result = db2_exec($_SESSION['conn'], $sql);

        if(!$result){
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $pesTracker = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
        $pesTracker->savePesComment($upesref, $accountid,  "PES_STATUS set to :" . $status );

        AuditTable::audit("PES Status set for:" . $upesref . "/" . $accountid ." To : " . $status . " By:" . $requestor,AuditTable::RECORD_TYPE_AUDIT);


        db2_commit($_SESSION['conn']);
        db2_autocommit($_SESSION['conn'],$db2AutoCommit);


        return true;
    }

    function setPesRescheckDate($upesref=null,$accountid=null, $requestor=null){
        if(!$upesref or !$accountid){
            throw new \Exception('No UPES_REF/ACCOUNTID provided in ' . __METHOD__);
        }

        $requestor = empty($requestor) ? $_SESSION['ssoEmail'] : $requestor;

        $loader = new Loader();
        $predicate = " UPES_REF='" . db2_escape_string(trim($upesref)) . "' AND ACCOUNT_ID = '" . db2_escape_string($accountid) . "' ";
        $pesLevels = $loader->loadIndexed('PES_LEVEL','UPES_REF',AllTables::$ACCOUNT_PERSON,$predicate);
        $pesRecheckPeriods = $loader->loadIndexed('RECHECK_YEARS','PES_LEVEL_REF',AllTables::$PES_LEVELS);

        $pesRecheckPeriod = 99; // default in case we don't find the actual value for this PES_LEVEL_REF

        if(isset($pesLevels[trim($upesref)])){
            $pesLevel = $pesLevels[trim($upesref)];
            if(isset($pesRecheckPeriods[$pesLevel])){
                $pesRecheckPeriod = $pesRecheckPeriods[$pesLevel];
            }
        }

        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET PES_RECHECK_DATE = current date + " . $pesRecheckPeriod . " years " ;
        $sql .= " WHERE UPES_REF='" . db2_escape_string($upesref) . "' AND ACCOUNT_ID='" . db2_escape_string($accountid) . "' ";

        $result = db2_exec($_SESSION['conn'], $sql);

        if(!$result){
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $sql  = " SELECT PES_RECHECK_DATE FROM  " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " WHERE UPES_REF='" . db2_escape_string($upesref) . "' AND ACCOUNT_ID='" . db2_escape_string($accountid) . "' ";

        $res = db2_exec($_SESSION['conn'], $sql);

        if(!$res){
            DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($res);

        $pesTracker = new AccountPersonTable(AllTables::$ACCOUNT_PERSON);
        $pesTracker->savePesComment($upesref, $accountid, "PES_RECHECK_DATE set to :" .  $row['PES_RECHECK_DATE'] );

        AuditTable::audit("PES_RECHECK_DATE set to :  "  . $row['PES_RECHECK_DATE'] . " by " . $requestor,AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }


    function getTracker($records=self::PES_TRACKER_RECORDS_ACTIVE, Spreadsheet $spreadsheet){
        $sheet = 1;

        $rs = self::returnPesEventsTable($records, self::PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET);

        if($rs){
            set_time_limit(62);
            $recordsFound = static::writeResultSetToXls($rs, $spreadsheet);
            if($recordsFound){
                static::autoFilter($spreadsheet);
                static::autoSizeColumns($spreadsheet);
                static::setRowColor($spreadsheet,'105abd19',1);
            }
        }

        if(!$recordsFound){
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Warning");
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2,"No records found");
        }
        // Rename worksheet & create next.

        $spreadsheet->getActiveSheet()->setTitle('Record ' . $records);
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($sheet++);

        return true;
    }

    static function addButtonsForPeopleReport($row){
        $account = $row['ACCOUNT'];
        $accountId = $row['ACCOUNT_ID'];
        $upesref = $row['UPES_REF'];
        $email = $row['EMAIL_ADDRESS'];
        $fullname = $row['FULL_NAME'];
        $countryOfResidence = $row['COUNTRY_OF_RESIDENCE'];

        $row['ACCOUNT'] = array('display'=>"$account<br/><small>($countryOfResidence)<small>", 'sort'=>$account);

        $row['ACTION'] = '';

        switch ($row['PES_STATUS']) {
            case AccountPersonRecord::PES_STATUS_CLEARED:
            case AccountPersonRecord::PES_STATUS_CANCEL_REQ:
            case AccountPersonRecord::PES_STATUS_CANCEL_CONFIRMED:
                $row['ACTION'].= "<button type='button' class='btn btn-primary btn-xs editPerson ' aria-label='Left Align' data-upesref='" . $upesref . "' data-toggle='tooltip' title='Edit Person' >
                                  <span class='glyphicon glyphicon-edit editPerson'  aria-hidden='true' data-upesref='" . $upesref . "'  ></span>
                                </button>";
                break;

             default:
                 $row['ACTION'].= "<button type='button' class='btn btn-primary btn-xs editPerson ' aria-label='Left Align' data-upesref='" . $upesref . "' data-toggle='tooltip' title='Edit Person' >
                                  <span class='glyphicon glyphicon-edit editPerson'  aria-hidden='true' data-upesref='" . $upesref . "'  ></span>
                                </button>";
                $row['ACTION'].= "&nbsp;";
                $row['ACTION'].= "<button type='button' class='btn btn-primary btn-xs cancelPesRequest ' aria-label='Left Align' data-accountid='" .$accountId . "' data-account='" . $account . "' data-upesref='" . $upesref . "' data-email='" . $email . "'  data-name='" . $fullname . "'data-toggle='tooltip' title='Cancel PES Request' >
              <span class='glyphicon glyphicon-ban-circle cancelPesRequest'  aria-hidden='true' data-accountid='" .$accountId . "' data-account='" . $account . "'  data-upesref='" . $upesref . "'  ></span>
              </button>";


            break;
        }

        $requestor = $row['PES_REQUESTOR'];
        $requested = $row['PES_DATE_REQUESTED'];


        $row['REQUESTED'] = "<small>" .  $requestor . "<br/>" . $requested . "</small>";

        $pesLevel = $row['PES_LEVEL'];
        $pesLevelRef = $row['PES_LEVEL_REF'];
        $row['PES_LEVEL']= "<button type='button' class='btn btn-primary btn-xs editPesLevel ' aria-label='Left Align' data-plEmailAddress='" . $email . "' data-plFullName='" . $fullname . "' data-plAccount='" . $account . "'data-plAccountId='" . $accountId . "' data-plPesLevelRef='" . $pesLevelRef . "'  data-plCountry='" . $countryOfResidence . "' data-toggle='tooltip' title='Edit Person' >
                          <span class='glyphicon glyphicon-edit aria-hidden='true' ></span>
                          </button>&nbsp;" . $pesLevel;

        return $row;

    }


    static function cancelPesRequest( $accountId=null, $upesref=null){

        db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_OFF);

        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . AllTables::$ACCOUNT_PERSON;
        $sql.= " SET PES_STATUS='" . AccountPersonRecord::PES_STATUS_CANCEL_REQ . "' ";
        $sql.= " WHERE ACCOUNT_ID='" . db2_escape_string($accountId) . "' ";
        $sql.= " AND UPES_REF='" . db2_escape_string($upesref) . "' ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $sql = " SELECT * ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . AllTables::$ACCOUNT_PERSON;
        $sql.= " WHERE ACCOUNT_ID='" . db2_escape_string($accountId) . "' ";
        $sql.= " AND UPES_REF='" . db2_escape_string($upesref) . "' ";


        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $accountPersonData = db2_fetch_assoc($rs);


        $accountPersonRecord = new AccountPersonRecord();
        $accountPersonData['PES_STATUS'] = AccountPersonRecord::PES_STATUS_CANCEL_REQ; // Because DB2 isn't a commited change
        $accountPersonRecord->setFromArray($accountPersonData);

        $accountPersonRecord->sendPesStatusChangedEmail();

        db2_commit($_SESSION['conn']);
        db2_autocommit($_SESSION['conn'],DB2_AUTOCOMMIT_ON);


    }



}