<?php
namespace upes;

use itdq\DbTable;
use itdq\Loader;

/*
 *
 */

class AccountPersonTable extends DbTable
{
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







}