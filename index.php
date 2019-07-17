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
		<h1 id='welcomeJumotron'><em>uPES</em> UKI Pre-Employment Screening</h1>
<!-- 	</div> -->
</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
 ?>
