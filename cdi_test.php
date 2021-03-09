<?php

use itdq\slack;
use upes\PesStatusAuditTable;


// $slack = new slack();

// $slack->sendMessageToChannel("Test message from Rob.", slack::CHANNEL_UPES_AUDIT);


PesStatusAuditTable::insertRecord('123456', 'someone@uk.ibm.com', 'An Account','Cleared','2021-03-09');
