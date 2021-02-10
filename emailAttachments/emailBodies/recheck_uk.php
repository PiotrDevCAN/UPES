<?php
$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>You have PES clearance for a UKI IBM FSS account that has a revalidation requirement.  It is within 8 weeks of your revalidation date.</p>';
$pesEmail.= '<p>If you would like to retain your PES Clearance, please see the below requirements.</p>';
$pesEmail.= 'Please return the following documents:';
$pesEmail.= '<ul>';
$pesEmail.= '<li>Fully completed Application Form		 <Global Application Form></li>';
$pesEmail.= '<li>A Certified copy of the photopage of your Passport or Full Current ID Card/Photocard Driving Licence.</li>';
$pesEmail.= '<li>A Certified copy of a utility bill (not mobile)/Bank/credit Card statement (less than 3 months old) - as evidence of your current address</li>';
$pesEmail.= '</ul>';
$pesEmail.= '<p>If you require clarifiation or instructions regarding certification please let us know.</p>';
$pesEmail.= '<p>If you have any other questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team.</p>';
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>UKI FSS PES Team</h3>';
$pesEmailPattern = array('/&&firstName&&/');