<?php

echo "start" , "\n";

$f = fopen("./debug.txt","a");

while(1){
    fwrite($f, "test\n");
    sleep(1);    
}


?>