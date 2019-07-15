<?php
namespace cord;

/**
 * Provides a list of public static properties that define the specific table names used in the application.
 *
 * This removes the need to hardcode the table name in the app itself.
 *
 *
 */
class allTables
{
    public static $DB2_ERRORS         = 'DB2_ERRORS';

    public static $TRACE              = 'TRACE';
    public static $TRACE_CONTROL      = 'TRACE_CONTROL';
}