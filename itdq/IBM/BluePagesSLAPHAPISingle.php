<?php
namespace itdq\IBM;

use itdq\BluePagesSLAPHAPI;
use itdq\interfaces\BluePagesSLAPHAPISingleInterface;

/*
 *  Handles Blue Pages.
 */
class BluePagesSLAPHAPISingle extends BluePagesSLAPHAPI implements BluePagesSLAPHAPISingleInterface {
	
	static function getDetailsFromIntranetId($intranetId){
		$urlTemplate = self::getPersonTemplate();
	    $urlTemplate .= "(mail=" . $intranetId . ")";
		$urlTemplate .= self::getTemplateParams();
		return self::getBPDetailsFromTemplate($urlTemplate);
	}

	static function getDetailsFromNotesId($notesId){
		$urlTemplate = self::getPersonTemplate();
	    $urlTemplate .= "(notesemail=" . $notesId . ")";
		$urlTemplate .= self::getTemplateParams();
		return self::getBPDetailsFromTemplate($urlTemplate);
	}

	static function getDetailsFromCnum($cnum){
		$urlTemplate = self::getPersonTemplate();
	    $urlTemplate .= "(cnum=" . $cnum . ")";
		$urlTemplate .= self::getTemplateParams();
		return self::getBPDetailsFromTemplate($urlTemplate);
	}
}
?>