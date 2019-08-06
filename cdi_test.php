<?php
use itdq\Trace;
use itdq\AuditTable;
use itdq\AuditRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

AuditTable::audit('hello',AuditTable::RECORD_TYPE_AUDIT);

Trace::pageLoadComplete($_SERVER['PHP_SELF']);