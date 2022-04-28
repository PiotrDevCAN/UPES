<?php
namespace itdq\interfaces;

interface BluePagesSLAPHAPIMultiInterface
{
    static function getDetailsFromIntranetIdSlapMulti($intranetIdArray);
    static function getDetailsFromCnumSlapMulti($cnumArray);
    static function getDirectoryEntriesFromIntranetIdSlapMulti($intranetIdArray);
    static function getDirectoryEntriesFromCnumSlapMulti($cnumArray);

	public static function getAllDetailsFromIntranetIdsSlapMulti($batchOfIds, &$allDetails, $report);
    public static function getAllDetailsFromCnumSlapMulti($batchOfCnums, &$allDetails, $report);
}
?>