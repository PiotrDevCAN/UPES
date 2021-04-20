<?php
$emailBody = <<<endOfEmailBody
<p>Hello &&candidate_first_name&&</p>
<p>We believe you are about to engage/are engaged with a UKI account that has an additional staff vetting clause in their contract.  Therefore, IBM are contractually obliged to ensure that anyone engaging on this customer is vetted.  To allow us to process this requirement, please return the below required documents to us at your earliest convenience.</p>
<ul>
<li>Fully completed Application Form(s) :<br/>&&name_of_application_form&&</li>
<li>A Certified copy of the photo page of your Passport or ID Card along with your VISA/Work Permit if required</li>
<li>If your ID card does not contain your current address, please send a certified copy of a utility bill/Bank statement (less than 3 months old) - as evidence of your current address</li>
</ul>
<li>An email from your IBM Manager confirming your IBM Start Date <b>OR</b> a screen print from the relevant page of the 'Work Day' system.<br/>*<b>Further information and evidence</b> may be required *</li>
<p><b>The Certification MUST be done by an IBM'er<b>, to confirm that they have seen the original document. Either physically by another IBM’er or Virtually by your IBM Manager.<p>
The following statement should be added on each document, on the same side as the image, or an email can be sent directly to the PES team by your certifier to confirm which documents have been viewed.
.</p>
<h4><span style='color:red'><center>True & Certified Copy<br/>Name of certifier in BLOCK CAPITALS<br/>IBM Serial number of certifier<br/>Certification Date<br/>Signature of certifier</center></span></h4>
<p>If you have any questions, you do not have any of the listed documents or are unsure about the process please contact the PES Team.</p>
<p><b>Please Note</b></p>
<p>If you are unable to get your documents certified (physically or virtually), we do have a provisional clearance process and will accept documents without certification - however these documents will need to be certified as soon as the restrictions are lifted or as soon as you can.</p>
<p>This will not give you full PES clearance for the account, so if you can get them certified correctly (ie another IBM'er seeing the document and signing) please do so.</p>
<p>Many Thanks for your cooperation,</p>
<p>&&account_name&& &nbsp; PES Team</p>
<p>Rachele Smith - Global PES Compliance Officer<br/>Zoe O'Flaherty - Global PES compliance Officer<br/>Jean Dover - Global PES Team Leader<br/>Carra Booth - Global PES SME</p>
<p><small>Phone: 44-131 656 0870 | Tie-Line: 37 580870<br/>E-mail: <a href='mailto:&&pestaskid&&'>&&pestaskid&&</a></small></p>
<p>IBM UK PES Team<br/>Atria One, 5th Floor<br/>144 Morrison Street, Edinburgh, EH3 8EX</br>United Kingdom</br>**Please input IBM UK Ltd, as we share a building**</p>
endOfEmailBody;
