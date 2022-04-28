<?php
namespace itdq\Ocean;

use itdq\BluePagesSLAPHAPI;
use itdq\interfaces\BluePagesSLAPHAPISingleInterface;

/*
 *  Handles Blue Pages.
 */
class BluePagesSLAPHAPISingle extends BluePagesSLAPHAPI implements BluePagesSLAPHAPISingleInterface {
    
    static function getDetailsFromIntranetId($intranetId){
		$urlTemplate = self::getPersonTemplate();
	    $urlTemplate .= "(additional=*;" . $intranetId . ";*)(mail=*ocean*)";
		$urlTemplate .= self::getTemplateParams();
		echo $urlTemplate;
		exit;
		return self::getBPDetailsFromTemplate($urlTemplate);
	}

	static function getDetailsFromNotesId($notesId){
		// firstly get intranet id from notes id
		$intranetId = self::getIntranetIdFromNotesId($notesId);
		return self::getDetailsFromIntranetId($intranetId);
	}

	static function getDetailsFromCnum($cnum){
		$urlTemplate = self::getPersonTemplate();
	    $urlTemplate .= "(additional=*;" . $cnum . ";*)(mail=*ocean*)";
		$urlTemplate .= self::getTemplateParams();
		return self::getBPDetailsFromTemplate($urlTemplate);
	}
}
?>