<?php
namespace itdq\interfaces;

interface BluePagesSLAPHAPISingleInterface
{
    static function getDetailsFromIntranetId($intranetId);
	static function getDetailsFromNotesId($notesId);
	static function getDetailsFromCnum($cnum);
}
?>