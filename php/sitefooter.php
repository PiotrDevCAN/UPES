<?php
if ($GLOBALS['header_done']) {do_footer();}
//  include ('itdq/java/scripts.html');
// include ('vbac/java/scripts.html');
if ($helper->isCli()) {
    // $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
} else {
    include ('php/templates/interior.footer');
}