<?php

if( getenv( "VCAP_SERVICES" ) )
{
    # Get database details from the VCAP_SERVICES environment variable
    #
    # *This can only work if you have used the Bluemix dashboard to
    # create a connection from your dashDB service to your PHP App.
    #
    $details  = json_decode( getenv( "VCAP_SERVICES" ), true );
    $dsn      = $details [ "dashDB For Transactions" ][0][ "credentials" ][ "dsn" ];
    $ssl_dsn  = $details [ "dashDB For Transactions" ][0][ "credentials" ][ "ssldsn" ];

    # Build the connection string
    #
    $driver = "DRIVER={IBM DB2 ODBC DRIVER};";
    $conn_string = $driver . $dsn;     # Non-SSL
    $conn_string = $driver . $ssl_dsn; # SSL


    // $amendedConn_String = str_replace(";Security=", "!;Security=", $conn_string); // password needs to end with a !

    $amendedConn_String = '';
    $conn_array = explode(";", $conn_string);

    foreach ($conn_array as $conn_element){
        $conn_element =  substr($conn_element, 0,4)=='PWD=' ?  $conn_element . "!" : $conn_element;
        $amendedConn_String.=$conn_element . ";";
    }
    $amendedConn_String = substr($amendedConn_String,0,-1);
    $conn = db2_connect( $amendedConn_String, "", "" );

//   $_SESSION['ssoEmail'] = 'dummyUser';
    if( $conn )
    {
        $_SESSION['conn'] = $conn;
        db2_autocommit($conn, TRUE); // This is how it was on the Wintel Box - so the code has no/few commit points.
    }
    else
    {
        echo "<p>Connection failed.</p>";
        echo db2_conn_error();
        echo db2_conn_errormsg();
    }
}
else
{
    echo "<p>No credentials.</p>";
}




?>
