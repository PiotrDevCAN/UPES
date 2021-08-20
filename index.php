<?php
use itdq\Trace;

Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<style type="text/css" class="init">
body {
	background: url('./images/splash.png')
		no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
}
</style>


<div class="container">
<!-- 	<div class="jumbotron"> -->
		<?php
			$ibmerLabel = '<em>uPES</em> UKI Pre-Employment Screening';
			$kyndrylerLabel = '<em>uPES</em> Pre-Employment Screening Tracked - For Kyndryl Employees ONLY';
			$label = stripos($_ENV['environment'], 'newco') ? $kyndrylerLabel : $ibmerLabel;
		?>
		<h1 id='welcomeJumotron'><p><?=$label?></p></h1>
		</div> -->
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
 ?>
