<?php
use itdq\AuditTable;
use itdq\Process;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$email = $_SESSION['ssoEmail'];
$type = isset($_POST['type']) ? $_POST['type'] : false;
if (!empty($type)) {

    $scriptsDirectory = '/var/www/html/batchJobs/';
    $processFile = 'downloadTrackerProcess.php';

    try {
        // exec('cd /patto/scripts && ./script.sh');
        chdir($scriptsDirectory);
        $cmd = 'php -f ' . $scriptsDirectory . $processFile . ' ' . $email . ' ' . str_replace(" ", "_", $type);
        $process = new Process($cmd);
        $pid = $process->getPid();
        $success = true;
        $messages = "<h4>Tracker Extract script has succeeded ".$pid."</h4>";
        $messages .= "<br/>PES Tracker type: <b>" . $type . "</b>";
        $messages .= "<br/>An extract file will be sent to: <b>" . $email . "</b> shortly.";
    } catch (Exception $exception) {
        $success = false;
        $messages = "Tracker Extract Script has failed";
    }
} else {
    $success = false;
    $messages = "Tracker type is mismatched";
}

$response = array('success'=>$success,'messages'=>$messages);
echo json_encode($response);