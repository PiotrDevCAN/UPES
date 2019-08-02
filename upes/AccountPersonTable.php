<?php
namespace upes;

use itdq\DbTable;
use itdq\Loader;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use \DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\xls;

/*
 *
 */

class AccountPersonTable extends DbTable {

use xls;

protected $preparedStageUpdateStmts;
protected $preparedTrackerInsert;
protected $preparedGetPesCommentStmt;
protected $preparedProcessStatusUpdate;
protected $preparedGetProcessingStatusStmt;

const PES_TRACKER_RECORDS_ACTIVE     = 'Active';
const PES_TRACKER_RECORDS_NOT_ACTIVE = 'Not Active';
const PES_TRACKER_RECORDS_ALL        = 'All';
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
                $pesStatusPredicate = "  AP.PES_STATUS in('" . AccountPersonRecord::PES_STATUS_PES_REQUESTED . "','" . AccountPersonRecord::PES_STATUS_EVI_REQUESTED. "','" . AccountPersonRecord::PES_STATUS_PROVISIONAL. "') ";
                break;
            case self::PES_TRACKER_RECORDS_ACTIVE_REQUESTED :
                $pesStatusPredicate = "  AP.PES_STATUS in('" . AccountPersonRecord::PES_STATUS_PES_REQUESTED . "','" . AccountPersonRecord::PES_STATUS_EVI_REQUESTED. "') ";
                break;
            case self::PES_TRACKER_RECORDS_ACTIVE_PROVISIONAL :
                $pesStatusPredicate = "  AP.PES_STATUS in('" . AccountPersonRecord::PES_STATUS_PROVISIONAL. "') ";
                break;
            case self::PES_TRACKER_RECORDS_NOT_ACTIVE :
                $pesStatusPredicate = " AP.PES_STATUS not in ('" . AccountPersonRecord::PES_STATUS_REQUESTED . "','" . AccountPersonRecord::PES_STATUS_EVI_REQUESTED. "','" . AccountPersonRecord::PES_STATUS_PROVISIONAL. "')  ";
                $pesStatusPredicate.= " AND AP.PROCESSING_STATUS_CHANGED > current timestamp - 31 days AND AP.CNUM is not null ";
                break;
            case self::PES_TRACKER_RECORDS_ALL :
                $pesStatusPredicate = " AP.CNUM is not null ";
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

        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
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
		<tr class='' ><th>Email Address</th><th>Account</th><th>Requestor</th><th>Country</th>
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
		<tr class='searchingRow wrap'><td>Email Address</td><td>Requestor</td><td>Country</td>
		<td>Consent</td>
		<td>Right to Work</td>
		<td>ID</td>
		<td>Residency</td>
		<td>Credit Check</td>
		<td>Financial Sanctions</td>
		<td>Criminal Records Check</td>
		<td>Proof of Activity</td>
		<td>Process Status</td><td>PES Status</td><td>Comment</td></tr>
		</thead>
		<tbody>
		<?php

        foreach ($allRows as $row){
            $today = new \DateTime();
            $date = DateTime::createFromFormat('Y-m-d', $row['PES_DATE_REQUESTED']);
            $age  = !empty($row['PES_DATE_REQUESTED']) ?  $date->diff($today)->format('%R%a days') : null ;
            // $age = !empty($row['PES_DATE_REQUESTED']) ? $interval->format('%R%a days') : null;
            $upesref = $row['UPES_REF'];
            $accountId = $row['ACCOUNT_ID'];
            $fullName = trim($row['FULL_NAME']);
            $emailaddress = trim($row['EMAIL_ADDRESS']);
            $requestor = trim($row['PES_REQUESTOR']);

            $formattedIdentityField = self::formatEmailFieldOnTracker($row);

            ?>
            <tr class='<?=$upesref;?>'>
            <td class='formattedEmailTd'>
            <div class='formattedEmailDiv'><?=$formattedIdentityField;?></div>
            </td>
            <td><?=$row['ACCOUNT']?></td>
            <td><?=$row['PES_REQUESTOR']?><br/><small><?=$row['PES_DATE_REQUESTED']?><br/><?=$age?></small></td>
            <td><?=trim($row['COUNTRY'])?></td>

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
            <div class='text-center personDetails '   data-upesref='<?=$upesref;?>' data-accountid='<?=$accountId;?>' data-fullname='<?=$fullName;?>' data-emailaddress='<?=$emailaddress;?>'  data-requestor='<?=$requestor;?>'   >
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
            <input class="form-control input-sm pesDateLastChased" value="<?=$dateLastChasedFormatted?>" type="text" placeholder='Last Chased' data-toggle='tooltip' title='PES Date Last Chased' data-upesref='<?=$upesref?>'>
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
  					<button type="button" role='button'  class="btn btn-info btnRecordSelection active" data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_REQUESTED?>'    data-toggle='tooltip'  title='Active Record in Initiated or Requested status'     >Requested</button>
					<button type="button" role='button'  class="btn btn-info btnRecordSelection "       data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE_PROVISIONAL?>'  data-toggle='tooltip'  title='Active Records in Provisional Clearance status' >Provisional</button>
  					<button type="button" role='button'  class="btn btn-info btnRecordSelection "       data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_ACTIVE?>'              data-toggle='tooltip'  title='Active Records'     >Active</button>
  					<button type="button" role='button'  class="btn btn-info btnRecordSelection"        data-pesrecords='<?=AccountPersonTable::PES_TRACKER_RECORDS_NOT_ACTIVE?>'          data-toggle='tooltip'  title='Recently Closed'  >Recent</button>
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


    function savePesComment($uposref, $account_id,$comment){
        $existingComment = $this->getPesComment($uposref, $account_id);
        $now = new \DateTime();

        $newComment = trim($comment) . "<br/><small>" . $_SESSION['ssoEmail'] . ":" . $now->format('Y-m-d H:i:s') . "</small><br/>" . $existingComment;


        $commentFieldSize = (int)$this->getColumnLength('COMMENT');

        if(strlen($newComment)>$commentFieldSize){
            AuditTable::audit("PES Tracker Comment too long. Will be truncated.<b>Old:</b>$existingComment <br>New:$comment");
            $newComment = substr($newComment,0,$commentFieldSize-20);
        }


        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET COMMENT='" . db2_escape_string($newComment) . "' ";
        $sql.= " WHERE UPES_REF='" . db2_escape_string($uposref) . "' AND ACCOUNT_ID='" . db2_escape_string($account_id) . "' ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception("Failed to update PES Comment for $cnum. Comment was " . $comment);
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
        $sql.= " WHERE UPES_REF='" . db2_escape_string($upesRef) . "', ACCOUNT_ID='" . db2_escape_string($accountId) . "' ";

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
        $formattedField.= trim($row['FULL_NAME']) . "</b></small><br/>" . trim($row['CNUM']);
        $formattedField.= "<div class='alert $alertClass priorityDiv'>Priority:" . $priority . "</div>";

        $formattedField.="<span style='white-space:nowrap' >
            <button class='btn btn-xs btn-danger  btnPesPriority accessPes accessCdi' data-pespriority='1'  data-upesref='" . $row['UPES_REF'] ."' data-accountid='" . $row['ACCOUNT_ID'] . "' data-toggle='tooltip'  title='High' ><span class='glyphicon glyphicon-king' ></button>
            <button class='btn btn-xs btn-warning  btnPesPriority accessPes accessCdi' data-pespriority='2' data-upesref='" . $row['UPES_REF'] ."' data-accountid='" . $row['ACCOUNT_ID'] . "' data-toggle='tooltip'  title='Medium' ><span class='glyphicon glyphicon-knight' ></button>
            <button class='btn btn-xs btn-success  btnPesPriority accessPes accessCdi' data-pespriority='3' data-upesref='" . $row['UPES_REF'] ."' data-accountid='" . $row['ACCOUNT_ID'] . "' data-toggle='tooltip'  title='Low'><span class='glyphicon glyphicon-pawn' ></button>
            <button class='btn btn-xs btn-info btnPesPriority accessPes accessCdi' data-pespriority='99'    data-upesref='" . $row['UPES_REF'] ."' data-accountid='" . $row['ACCOUNT_ID'] . "' data-toggle='tooltip'  title='Unknown'><span class='glyphicon glyphicon-erase' ></button>
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

    static function getButtonsForPesStage($value, $alertClass, $stage, $upesref, $accountid){
        ?>
        <div class='alert <?=$alertClass;?> text-center pesStageDisplay' role='alert' ><?=$value;?></div>
        <div class='text-center' data-pescolumn='<?=$stage?>' data-upesref='<?=$upesref?>' data-accountid='<?=$accountid?>'>
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





}