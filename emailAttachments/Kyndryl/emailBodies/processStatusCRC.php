<?php
$pesEmail = 'Hello &&fullname&&,';
$pesEmail.= '<p>Thank you for your documents, I confirm that the only thing we are now waiting on is the completion of your credit and/or criminal records check and we will let you know when this has happened.';
$pesEmail.= '<p>Please note that this can take up to 3 weeks.  We cannot expedite this part of the process.</p>';
$pesEmail.= '<p>Many thanks for your cooperation,</p>';
$pesEmail.= '<h3>&&accountname&& PES Team</h3>';

$pesEmailPattern = array('/&&fullname&&/','/&&accountname&&/');