<?php
$pesEmail = 'Hello &&fullname&&,';
$pesEmail.= '<p>Thank you for your documents, I confirm that the only thing we are now waiting on is the completion of your criminal records check and we will let you know when this has happened.';
$pesEmail.= '<p>Please note that this can take up to 15 working days.</p>';
$pesEmail.= '<p>Many Thanks for your cooperation,</p>';
$pesEmail.= '<h3>&&accountname&& PES Team</h3>';

$pesEmailPattern = array('/&&fullname&&/','/&&accountname&&/');