<?php
    ini_set( "display_errors", true );
    define( "DB_USERNAME", "lubimcev" );
    define( "DB_PASSWORD", "neto1721" );
    define( "DB_NAME", "lubimcev" );
    define( "CHARSET", "utf8" );
    define( "DB_DSN", "mysql:host=localhost;dbname=" . DB_NAME . ";charset=" . CHARSET );

    function handleException( $exception ) {
        echo "Error";
        error_log($exception->getMessage(), 3, 'log.txt');
    }

    set_exception_handler( 'handleException' );