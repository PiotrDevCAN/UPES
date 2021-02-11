<?php

use itdq\slack;


$slack = new slack();

$slack->sendMessageToChannel("Test message from Rob.", slack::CHANNEL_UPES_AUDIT);
