<?php
$pesEmail = 'Hello &&fullname&&,';
$pesEmail.= '<p>We have contacted you a number of times regarding your &&accountname&& PES process. We require further information or documents to move forward with your clearance and we have not received a response.</p>';
$pesEmail.= '<p>If you are receiving this email, it IS because we did not have a reply to our latest email.  Please double check the afore mentioned (before responding) as this chaser would not have been sent if we had recieved the information requested.</p>';
$pesEmail.= '<p>If we do not have your documents/information within the next 4 working days, we will have to remove you from this process.<p>';
$pesEmail.= '<p>If you have questions or concerns please contact us to discuss before any action is taken - as we can help you move forward with this.</p>';

$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>&&accountname&& PES Team</h3>';

$pesEmailPattern = array('/&&fullname&&/','/&&accountname&&/');