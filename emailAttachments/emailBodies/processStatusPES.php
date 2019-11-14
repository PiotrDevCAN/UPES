<?php
$pesEmail = 'Hello &&fullname&&,';
$pesEmail.= '<p>I confirm that the PES team have received your documents/information and we are working on your case. We will get back to you within 7 days with an update.</p>';
$pesEmail.= '<p>Many Thanks for your cooperation,</p>';
$pesEmail.= '<h3>&&accountname&& PES Team</h3>';

$pesEmailPattern = array('/&&fullname&&/','/&&accountname&&/');