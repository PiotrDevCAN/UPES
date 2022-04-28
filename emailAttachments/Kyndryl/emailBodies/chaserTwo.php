<?php
$pesEmail = 'Hello &&fullname&&,';
$pesEmail.= '<p>We have previously contacted you a couple of times regarding your &&accountname&& PES process. We require further information or documents to move forward with your clearance and we have not received a response.</p>';
$pesEmail.= '<p>If you are receiving this email, it IS because we did not have a reply to our latest email.  Please double check the afore mentioned (before responding) as this chaser would not have been sent if we had recieved the information requested.</p>';
$pesEmail.= '<p>Please can you reply at your earliest convenience or contact us with any questions you may have.</p>';

$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>&&accountname&& PES Team</h3>';

$pesEmailPattern = array('/&&fullname&&/','/&&accountname&&/');