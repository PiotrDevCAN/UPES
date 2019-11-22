<?php
$emailBody = <<<endOfEmailBody
<p>Hello &&candidate_first_name&&</p>
<p>We believe you are about to engage on a UKI FSS account.  IBM are contractually obliged to ensure that anyone engaging on any FSS customer accounts are PES cleared.  To allow us to process this requirement, please return the below required documents to us at your earliest convenience.</p>
<ul>
<li>Fully completed Application Form(s) :<br/>&&name_of_application_form&&</li>
<li>An email from your IBM Manager confirming your IBM Start Date <b>OR</b> a screen print from the relevant page of the 'Work Day' system.<br/>*<b>Further information and evidence</b> may be required *</li>
<li>A Certified copy of the photo page of your Passport along with your VISA/Work Permit if required</li>
<li>A Certified copy of your current full Driving Licence photocard or utility bill (not mobile)/Bank statement (less than 3 months old) - as evidence of your current address<br/>
</ul>
<p><b>The Certification MUST be done by another IBM'er</b>, to confirm that they have seen the original document.  The following statement should be <b>handwritten</b> on <b>each document</b>, on the <b>same side as the image</b>.</p>
<h4><span style='color:red'><center>True & Certified Copy<br/>Name of certifier in BLOCK CAPITALS<br/>IBM Serial number of certifier<br/>Certification Date<br/>Signature of certifier</center></span></h4>
<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team.</p>
<p>Many Thanks for your cooperation,</p>
<p>&&account_name&& &nbsp; PES Team</p>
<p>Rachele Smith - Global PES Compliance Officer<br/>Zoe O'Flaherty - Global PES compliance Officer<br/>Carra Booth - Global PES SME - Please note I work Monday - Friday 10 - 2:30</p>
<p><small>Phone: 44-131 656 0870 | Tie-Line: 37 580870<br/>E-mail: <a href='mailto:&&pestaskid&&'>&&pestaskid&&</a></small></p>
<p>IBM UK PES Team<br/>Atria One, 5th Floor<br/>144 Morrison Street, Edinburgh, EH3 8EX</br>United Kingdom</br>**Please input IBM UK Ltd, as we share a building**</p>
endOfEmailBody;
