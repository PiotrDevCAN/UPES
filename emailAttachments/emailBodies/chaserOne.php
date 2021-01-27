<?php
$pesEmail = 'Hello &&fullname&&,';
$pesEmail.= '<p>A short while ago we contacted you regarding your &&accountname&& PES process. We required further information or documents, however, we do not appear to have received a response.</p>';
$pesEmail.= '<p>Please can you reply at your earliest convenience or contact us with any questions you may have.</p>';

$pesEmail.= '<p><b>Please Note</b></p>';
$pesEmail.= "<p>If you are unable to get your documents certified (physically or virtually), we do have a provisional clearance process and will accept documents without certification - however these documents will need to be certified as soon as the restrictions are lifted or as soon as you can.</p>";
$pesEmail.= "<p>This will not give you full PES clearance for the account, so if you can get them certified correctly (ie another IBM'er seeing the document and signing) please do so.</p>";

$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>&&accountname&&  PES Team</h3>';

$pesEmailPattern = array('/&&fullname&&/','/&&accountname&&/');