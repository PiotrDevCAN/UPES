<?php
$pesEmail = '<h3>PES Processing for  &&fullname&&</h3>';
$pesEmail.= '<p>This is to inform you that the PES Team have had to request further information from the individual</p>';
$pesEmail.= '<p>Processing of their PES Clearance can\'t progress until that has been received.</p>';
$pesEmail.= '<h3>Regards,</h3>';
$pesEmail.= '<h3>&&accountname&& PES Team</h3>';

$pesEmailPattern = array('/&&fullname&&/','/&&accountname&&/');