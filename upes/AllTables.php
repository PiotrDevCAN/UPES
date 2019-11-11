<?php
namespace upes;


/**
 * Provides a list of public static properties that define the specific table names used in the application.
 *
 * This removes the need to hardcode the table name in the app itself.
 *
 *
 */
class AllTables
{
    public static $ACCOUNT            = 'ACCOUNT';
    public static $ACCOUNT_PERSON     = 'ACCOUNT_PERSON';

    public static $CONTRACT           = 'CONTRACT';
    public static $COUNTRY            = 'COUNTRY';

    public static $DB2_ERRORS         = 'DB2_ERRORS';

    public static $PERSON             = 'PERSON';

    public static $PES_LEVELS         = 'PES_LEVELS';

    public static $TRACE              = 'TRACE';
    public static $TRACE_CONTROL      = 'TRACE_CONTROL';
}