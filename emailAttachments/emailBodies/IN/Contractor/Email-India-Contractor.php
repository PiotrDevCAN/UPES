<?php

$pesEmail = 'Hello &&firstName&&,';
$pesEmail.= '<p>We believe you are about to engage on the IBM/Lloyds account.  IBM are contractually obliged to ensure that anyone engaging on the Lloyds account is PES cleared.  To allow us to process this requirement, please return the below required documents to us <b>as one attachment</b> at your earliest convenience.</p>';
$pesEmail.= '<ul>';
$pesEmail.= '<li>Fully completed Application Form <b>(Attached)</b><br/><span style="color:red">Omissions & inaccuracies in your application form may prevent your PES clearance.</span></li>';
$pesEmail.= '<li>Fully completed ODC application form<b>(Attached)</b>, with full 5 years address history (please return this as <b>one separate .pdf document with a physical signature</b> - Please do not have this document certified by another IBM\'er)<br/><span style="color:red">Please note that your names on the ODC & Global Application form must align to your passport and evidence documents.</span></li>';
$pesEmail.= '<li>A Certified copy of the photo page of your Passport along with your VISA/Work Permit if required</li>';
$pesEmail.= '<li>A Certified copy of <b>one</b> utility bill (not mobile) or <b>one</b> Bank/Credit Card Statement less than 3 months old (in your name, showing your current address)</li>';
$pesEmail.= '<li>An email from your IBM Manager confirming your IBM Start Date <b>OR</b> a screen print of the relevant page of the "About You" system.<br/><b>Further information and evidence</b> will be required if it is <b>less</b> than 5 years ago (ie certified copies of your service certificate/relieving certificate or education transfer certificate).</li>';
$pesEmail.= '</ul>';
$pesEmail.= '<p style="text-align:center"><b>The Certification MUST be done by another IBM\'er</b>, to confirm that they have seen the original document.  The following statement should be <b>handwritten</b> on <b>each document</b>, on the <b>same side as the image</b>.</span></p>';
$pesEmail.= '<p style="text-align:center;color:red">True & Certified Copy<br/>Name of certifier in BLOCK CAPITALS<br/>IBM Serial number of certifier<br/>Certification Date</br>Signature of certifier</span></p>';
$pesEmail.= '<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team on <a href=\'mailto:LBGVETPR@uk.ibm.com\'>LBGVETPR@uk.ibm.com</a></p>';
$pesEmail.= '<p>Many Thanks for your cooperation</p>';
$pesEmail.= '<h3>Lloyds PES Team</h3>';

$pesEmailPattern = array('/&&firstName&&/');