<?php
namespace upes;

use itdq\DbTable;
use itdq\Loader;
use upes\pesTrackerRecord;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use \DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\xls;
use itdq\AuditTable;

class PesTrackerTable extends DbTable{

    use xls;

    protected $preparedStageUpdateStmts;
    protected $preparedTrackerInsert;
    protected $preparedGetPesCommentStmt;
    protected $preparedProcessStatusUpdate;
    protected $preparedGetProcessingStatusStmt;

    static function preProcessRowForWriteToXls($row){
        $breaks = array("<br/>","</br/>");
        $comment = str_ireplace($breaks, "\r\n", $row['COMMENT']);
        $row['COMMENT'] = strip_tags($comment);
        return $row;
    }



    function getProcessingStatusCell($cnum){
        $preparedStmt = $this->preparedGetProcessingStatusStmt();

        $data = array($cnum);
        $rs = db2_execute($preparedStmt,$data);

        if($rs){
            $row = db2_fetch_assoc($preparedStmt);
            ob_start();
            self::formatProcessingStatusCell($row);
            $cellContents = ob_get_clean();
            return $cellContents;
        }
        return false;
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

    function prepareGetProcessingStatusStmt(){
        if(isset($this->preparedGetProcessingStatusStmt)){
            return $this->preparedGetProcessingStatusStmt;
        }

        $sql = " SELECT PROCESSING_STATUS, PROCESSING_STATUS_CHANGED ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE CNUM=? ";

        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);

        $this->preparedGetProcessingStatusStmt = $preparedStmt ? $preparedStmt : false;
        return $this->preparedGetProcessingStatusStmt;
    }


    function prepareProcessStatusUpdate(){
        if(isset($this->preparedProcessStatusUpdate )) {
            return $this->prepareProcessStatusUpdate;
        }
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PROCESSING_STATUS =?, PROCESSING_STATUS_CHANGED = current timestamp ";
        $sql.= " WHERE CNUM=? ";

        $this->preparedSelectSQL = $sql;

        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);

        if($preparedStmt){
            $this->prepareProcessStatusUpdate = $preparedStmt;
        }

        return $preparedStmt;
    }

    function prepareTrackerInsert(){
        if(isset($this->preparedTrackerInsert )) {
            return $this->preparedTrackerInsert;
        }
        $sql = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " ( CNUM ) VALUES (?) ";
        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);

        if($preparedStmt){
            $this->preparedTrackerInsert = $preparedStmt;
            return $preparedStmt;
        }

        return false;

    }

    function createNewTrackerRecord($cnum){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));

        if (!$this->existsInDb($trackerRecord)) {

            $preparedStmt = $this->prepareTrackerInsert();
            $data = array($cnum);

            $rs = db2_execute($preparedStmt,$data);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
                throw new \Exception('Unable to create blank Tracker record for ' . $cnum);
            }

            return;

        }
        return false;

    }


    function setPesStageValue($cnum,$stage,$stageValue){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));

        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }
        $preparedStmt = $this->prepareStageUpdate($stage);
        $data = array($stageValue,$cnum);

        $rs = db2_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update PES Stage: $stage to $stageValue for $cnum");
        }

       return true;
    }

    function setPesProcessStatus($cnum,$processStatus){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));

        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }
        $preparedStmt = $this->prepareProcessStatusUpdate();
        $data = array($processStatus,$cnum);

        $rs = db2_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update PES Process Status $processStatus for $cnum");
        }

        return true;
    }

    function setPesPassportNames($cnum,$passportFirstname=null,$passportSurname=null){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));

        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }

        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PASSPORT_FIRST_NAME=";
        $sql.= !empty($passportFirstname) ? "'" . db2_escape_string($passportFirstname) . "', " : " null, ";
        $sql.= " PASSPORT_LAST_NAME=";
        $sql.= !empty($passportSurname) ? "'" . db2_escape_string($passportSurname) . "'  " : " null ";
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update Passport Names: $passportFirstname  / $passportSurname for $cnum");
        }

        return true;
    }

    function setPesDateLastChased($cnum,$dateLastChased){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));

        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }

        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET DATE_LAST_CHASED=DATE('" . db2_escape_string($dateLastChased) . "') ";
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update Date Last Chased to : $dateLastChased for $cnum");
        }

        return true;
    }

    function getTracker($records=self::PES_TRACKER_RECORDS_ACTIVE, Spreadsheet $spreadsheet){
        $sheet = 1;

        $rs = self::returnPesEventsTable($records, PesTrackerTable::PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET);

        if($rs){
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


    function changeCnum($fromCnum,$toCnum){
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET CNUM='" . db2_escape_string(trim($toCnum)) . "' ";
        $sql.= " WHERE CNUM='" . db2_escape_string(trim($fromCnum)) . "' ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }

        db2_commit($_SESSION['conn']);

//         $sql = " DELETE FROM  " . $_SESSION['Db2Schema'] . "." . $this->tableName;
//         $sql.= " WHERE CNUM='" . db2_escape_string(trim($fromCnum)) . "' ";

//         $rs = db2_exec($_SESSION['conn'], $sql);

//         if(!$rs){
//             DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
//             return false;
//         }

        $loader = new Loader();
        $emailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',AllTables::$PERSON," CNUM in('" . db2_escape_string(trim($fromCnum)) . "','" . db2_escape_string(trim($toCnum)) . "') ");

        $this->savePesComment($toCnum, "Serial Number changed from $fromCnum to $toCnum");
        $this->savePesComment($toCnum, "Email Address changed from $emailAddress[$fromCnum] to $emailAddress[$toCnum] ");

        return true;
     }

}
