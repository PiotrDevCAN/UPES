<?php
namespace upes;

use itdq\DbRecord;
use itdq\Loader;
use itdq\FormClass;
use upes\AccountPersonTable;
use itdq\AuditTable;

/*

CREATE TABLE "UPES".PES_STATUS_AUDIT ( CNUM CHAR(9) NOT NULL ,EMAIL_ADDRESS CHAR(50) NOT NULL, ACCOUNT CHAR(125) NOT NULL ,PES_STATUS CHAR(50) NOT NULL
                                     ,PES_CLEARED_DATE DATE NOT NULL ,UPDATER CHAR(50) ,UPDATED TIMESTAMP(6)  )
           ;

ALTER TABLE "UPES_UT".PES_STATUS_AUDIT
		ADD CONSTRAINT PES_STATUS_AUDIT_PK
           PRIMARY KEY ( CNUM, ACCOUNT, PES_STATUS, PES_CLEARED_DATE );
           

GRANT CONTROL, INDEX, SELECT, UPDATE, INSERT, ALTER, DELETE, REFERENCES  
         ON TABLE "UPES".PES_STATUS_AUDIT TO voJchMHqPNqo3mVk ;


 */



class PesStatusAuditRecord extends DbRecord
{
    protected $CNUM;  
    protected $EMAIL_ADDRESS;
    protected $ACCOUNT;
    protected $PES_STATUS;
    protected $PES_CLEARED_DATE;
    protected $UPDATER;
    protected $UPDATED;


}