<?php
use itdq\PlannedOutages;
use itdq\Navbar;
use itdq\NavbarMenu;
use itdq\NavbarOption;
use itdq\NavbarDivider;

include ('itdq/PlannedOutages.php');
include ('itdq/DbTable.php');
$plannedOutagesLabel = "Planned Outages";
$plannedOutages = new PlannedOutages();
include ('UserComms/responsiveOutages_V2.php');

$navBarImage = ""; //a small image to displayed at the top left of the nav bar
$navBarBrand = array(strtoupper($_SERVER['environment']),"index.php");
$navBarSearch = false;

$pageDetails = explode("/", $_SERVER['PHP_SELF']);
$page = isset($pageDetails[2]) ? $pageDetails[2] : $pageDetails[1];

$navbar = new Navbar($navBarImage, $navBarBrand,$navBarSearch);

$cdiAdmin       = new NavbarMenu("CDI Admin");
$trace          = new NavbarOption('View Trace','pi_trace.php','accessCdi');
$traceControl   = new NavbarOption('Trace Control','pi_traceControl.php','accessCdi');
$traceDelete    = new NavbarOption('Trace Deletion', 'pi_traceDelete.php','accessCdi');
$test           = new NavbarOption('Test', 'CDI_Test.php','accessCdi');
$claim          = new NavbarOption('Claim Upload', 'cdi_upload.php','accessCdi');
$cdiAdmin->addOption($trace);
$cdiAdmin->addOption($traceControl);
$cdiAdmin->addOption($traceDelete);
$cdiAdmin->addOption($test);
$cdiAdmin->addOption($claim);

$admin          = new NavBarMenu("Rates Admin");
$psRate         = new NavBarOption('P S Rate Table','pa_psRate.php','accessCdi accessResponder');
$dayRates       = new NavBarOption('Day Rates Table','pa_dayRates.php','accessCdi accessResponder');
$listRfs        = new NavbarOption('List RFS','p_listRfs.php','accessCdi accessResponder');
$admin->addOption($psRate);
$admin->addOption($dayRates);
$admin->addOption($listRfs);

$subcoAdmin     = new NavBarMenu("Subco Admin",'accessCdi accessSubcoAdmin' );
$subcoBoard     = new NavBarOption('Board','pa_subcoBoard.php','accessCdi accessSubcoAdmin');
$subcoList      = new NavBarOption('List','pa_subcoList.php','accessCdi accessSubcoAdmin');
$subcoAdmin->addOption($subcoBoard);
$subcoAdmin->addOption($subcoList);

$requestMenu      = new NavbarMenu('Rate Requests');
$requestRate      = new NavbarOption('Request Rate','p_requestRate.php','accessCdi accessRequestor');
$listRequests     = new NavbarOption('List Requests','p_listRateRequests.php','accessCdi accessResponder accessRequestor');
$determineRate    = new NavbarOption('Determine Rate','p_determineRate.php','accessCdi accessResponder');

$requestMenu->addOption($requestRate);
$requestMenu->addOption($listRequests);
$requestMenu->addOption($determineRate);

$reportMenu    = new NavbarMenu('Reports');
$resUtil       = new NavbarOption('RFS Rates Extract','pr_rfsRatesExtract.php', 'accessCdi accessResponder');
$reportMenu->addOption($resUtil);



$navbar->addMenu($cdiAdmin);
$navbar->addMenu($subcoAdmin);
$navbar->addMenu($admin);
$navbar->addMenu($requestMenu);
$navbar->addMenu($reportMenu);

$outages = new NavbarOption($plannedOutagesLabel, 'ppo_PlannedOutages.php','accessCdi accessResponder accessRequestor');
$navbar->addOption($outages);

$navbar->createNavbar($page);

$isCdi       = employee_in_group($_SESSION['cdiBg'],  $_SESSION['ssoEmail']) ? ".not('.accessCdi')" : null;
$isResponder = employee_in_group($_SESSION['responderBg'],  $_SESSION['ssoEmail']) ? ".not('.accessResponder')" : null;
$isRequestor = employee_in_group($_SESSION['requestorBg'],  $_SESSION['ssoEmail']) ? ".not('.accessRequestor')" : null;


$isCdi        = stripos($_SERVER['environment'], 'dev') ? ".not('.accessCdi')"        : $isCdi;
$isResponder  = stripos($_SERVER['environment'], 'dev')  ? ".not('.accessResponder')" : $isResponder;
//$isRequestor  = stripos($_SERVER['environment'], 'dev')  ? ".not('.accessRequestor')" : $isRequestor;


$_SESSION['isCdi']       = !empty($isCdi)  ? true : false;
$_SESSION['isResponder'] = !empty($isResponder) ? true : false;
$_SESSION['isRequestor'] = !empty($isRequestor) ? true : false;

$plannedOutagesId = str_replace(" ","_",$plannedOutagesLabel);

?>
<script>



$('.navbarMenuOption')<?=$isCdi?><?=$isResponder?><?=$isRequestor?>.remove();
$('.navbarMenu').not(':has(li)').remove();

$('li[data-pagename="<?=$page;?>"]').addClass('active').closest('li.dropdown').addClass('active');
<?php



if($page != "index.php" && substr($page,0,3)!='cdi'){
    ?>

    console.log('<?=$page;?>');

	var pageAllowed = $('li[data-pagename="<?=$page;?>"]').length;

    console.log('li[data-pagename="<?=$page;?>"]');
    console.log($('li[data-pagename="<?=$page;?>"]'));



	if(pageAllowed==0 ){
		window.location.replace('index.php');
		alert("You do not have access to:<?=$page?>");
	}
	<?php
}

?>

$(document).ready(function () {

    $('button.accessRestrict')<?=$isCdi?><?=$isRequestor?><?=$isResponder?>.remove();


    <?=!empty($isRequestor) ? '$("#userLevel").html("Requestor");console.log("Requestor");' : null;?>
    <?=!empty($isResponder) ? '$("#userLevel").html("Responder");console.log("Responder");' : null;?>
    <?=!empty($isCdi)       ? '$("#userLevel").html("CDI");console.log("CDI");' : null;?>

    var poContent = $('#<?=$plannedOutagesId?> a').html();
	var badgedContent = poContent + "&nbsp;" + "<?=$plannedOutages->getBadge();?>";
	$('#<?=$plannedOutagesId?> a').html(badgedContent);

});
</script>

