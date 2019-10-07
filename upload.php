<?php
$from_arr = array( " " , "data:image/png;base64," );
$to_arr   = array( "+" , "" );
$icon    = base64_decode( str_replace( $from_arr , $to_arr , $_POST['ufile'] ) );

file_put_contents( "/home/ubuntu/public_html/test.png" , $icon );


?>