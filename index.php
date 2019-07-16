<?php
use itdq\Trace;

Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<style type="text/css" class="init">
body {
	background: url('./images/SIM_IMG_503987.jpg')
		no-repeat center center fixed;
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
}
</style>


<div class="container">
	<div class="jumbotron">
		<h5 id='welcomeJumotron'><small></small><em>uPES</em> UKI Pre-Employment Screening</small></h5>
	</div>
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
 ?>
