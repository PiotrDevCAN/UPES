<?php
namespace itdq\IBM;

use itdq\BluePagesSLAPHAPI;
use itdq\interfaces\BluePagesSLAPHAPIMultiInterface;

/*
 *  Handles Blue Pages.
 */
class BluePagesSLAPHAPIMulti extends BluePagesSLAPHAPI implements BluePagesSLAPHAPIMultiInterface {

	static function getDetailsFromIntranetIdSlapMulti($intranetIdArray){
		$startTime = microtime(true);
	    set_time_limit(120);
		$urlTemplate = self::getPersonTemplate();
		$urlTemplate .= "(|";

	    foreach ($intranetIdArray as $intranetId){
			if (filter_var($intranetId, FILTER_VALIDATE_EMAIL)) {
	        	$urlTemplate .= "(mail=" . trim($intranetId) . ")";
			}
	    }
// 	    $urlTemplate .= ")" . self::getTemplateParams() . "?&uid&managerSerialNumber&managerCountryCode&notesEmail&isManager&notesId&mail";
//	    $urlTemplate .= ")" . self::getTemplateParams() . "?&uid&dept&div&c&managerSerialNumber&managerCountryCode&notesEmail&isManager&notesId&mail&dept&callupname";
// 	    $urlTemplate .= ")" . self::getTemplateParams() . "?&uid&dept&div&c&managerSerialNumber&managerCountryCode&notesEmail&isManager&notesId&mail";
//	    $urlTemplate .= ")" . self::getTemplateParams() . "?&uid&callupName&dept&div&c&managerSerialNumber&managerCountryCode&jobResponsibilities&hrFirstname&ventureName&glTeamLead&notesEmail&notesShortName&notesMailDomain&mail&preferredFirstName&hrPreferredName&givenName&hrCompanyCode&workLoc";
		$urlTemplate .= ")" . self::getTemplateParams();

	    $ch = curl_init ( $urlTemplate );
	    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );

	    $curlReturn = curl_exec ( $ch );
	    $lookupTime = (float)(microtime(true) - $startTime);
	    $xml = simplexml_load_string($curlReturn);
	    return $xml;

		// this function requires modification 

	}

    static function getDetailsFromCnumSlapMulti($cnumArray){
		$startTime = microtime(true);
	    set_time_limit(120);
		$urlTemplate = self::getPersonTemplate();
		$urlTemplate .= "(|";

	    foreach ($cnumArray as $cnum){
	        $urlTemplate .= "(UID=" . trim($cnum) . ")";
	    }
// 	    $urlTemplate .= ")" . self::getTemplateParams() . "?&uid&managerSerialNumber&managerCountryCode&notesEmail&isManager&notesId&mail";
//	    $urlTemplate .= ")" . self::getTemplateParams() . "?&uid&dept&div&c&managerSerialNumber&managerCountryCode&notesEmail&isManager&notesId&mail&dept&callupname";
// 	    $urlTemplate .= ")" . self::getTemplateParams() . "?&uid&dept&div&c&managerSerialNumber&managerCountryCode&notesEmail&isManager&notesId&mail";
//	    $urlTemplate .= ")" . self::getTemplateParams() . "?&uid&callupName&dept&div&c&managerSerialNumber&managerCountryCode&jobResponsibilities&hrFirstname&ventureName&glTeamLead&notesEmail&notesShortName&notesMailDomain&mail&preferredFirstName&hrPreferredName&givenName&hrCompanyCode&workLoc";
		$urlTemplate .= ")" . self::getTemplateParams();

	    $ch = curl_init ( $urlTemplate );
	    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );

	    $curlReturn = curl_exec ( $ch );
	    $lookupTime = (float)(microtime(true) - $startTime);
	    $xml = simplexml_load_string($curlReturn);
	    return $xml;
		
		// this function requires modification 

	}

	static function getDirectoryEntriesFromIntranetIdSlapMulti($intranetIdArray){
	    $bpDetails = self::getDetailsFromIntranetIdSlapMulti($intranetIdArray);
	    $directoryEntries = $bpDetails->{'directory-entries'};
	    
	    return $directoryEntries;
	}

	static function getDirectoryEntriesFromCnumSlapMulti($cnumArray){
	    $bpDetails = self::getDetailsFromCnumSlapMulti($cnumArray);
		$directoryEntries = $bpDetails->{'directory-entries'};
	    
	    return $directoryEntries;
	}

	public static function getAllDetailsFromIntranetIdsSlapMulti($batchOfIds, &$allDetails, $report){
	    echo $report ?  "<h4>About to process a batch of " . count($batchOfIds) . " ids</h4>" : null;
	    $directoryEntries = self::getDirectoryEntriesFromIntranetIdSlapMulti($batchOfIds);
        
	    if($directoryEntries){
	        self::extractSpecificDetailsToAllDetailsByIntranetId($directoryEntries, $allDetails);
	    }
	}

    public static function getAllDetailsFromCnumSlapMulti($batchOfCnums, &$allDetails, $report){
	    echo $report ?  "<h4>About to process a batch of " . count($batchOfCnums) . " cnums</h4>" : null;
	    $directoryEntries = self::getDirectoryEntriesFromCnumSlapMulti($batchOfCnums);
        
	    if($directoryEntries){
	        self::extractSpecificDetailsToAllDetails($directoryEntries, $allDetails);
	    }
	}
}
?>