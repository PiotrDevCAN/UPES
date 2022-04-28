<?php
namespace itdq\Both;

use itdq\interfaces\BluePagesSLAPHAPISingleInterface;
use itdq\BluePagesSLAPHAPI;
use itdq\IBM\BluePagesSLAPHAPISingle as IBMBluePagesSLAPHAPISingle;
use itdq\Ocean\BluePagesSLAPHAPISingle as OceanBluePagesSLAPHAPISingle;

/*
 *  Handles Blue Pages.
 */
class BluePagesSLAPHAPISingle extends BluePagesSLAPHAPI implements BluePagesSLAPHAPISingleInterface {
    
	// $intranetId may be either IBM or Ocean
    static function getDetailsFromIntranetId($intranetId){
		$sp = self::checkIfIsOceanId($intranetId);
		if($sp === FALSE){
			return IBMBluePagesSLAPHAPISingle::getDetailsFromIntranetId($intranetId);
		} else {
			return OceanBluePagesSLAPHAPISingle::getDetailsFromIntranetId($intranetId);
		}
	}

	// $notesId may be either IBM or Ocean
	static function getDetailsFromNotesId($notesId){
		$sp = self::checkIfIsOceanId($notesId);
		if($sp === FALSE){
			return IBMBluePagesSLAPHAPISingle::getDetailsFromNotesId($notesId);
		} else {
			return OceanBluePagesSLAPHAPISingle::getDetailsFromNotesId($notesId);
		}
	}

	// $cnum may be either IBM or Ocean
	static function getDetailsFromCnum($cnum){
		$allDetails = IBMBluePagesSLAPHAPISingle::getDetailsFromCnum($cnum);
		if (empty($allDetails)) {
			$allDetails = OceanBluePagesSLAPHAPISingle::getDetailsFromCnum($cnum);
		}
		return $allDetails;
	}
}
?>