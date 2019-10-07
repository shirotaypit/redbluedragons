<?php
    header("Content-type: text/plain; charset=UTF-8");
    $pdo = new PDO("mysql:host=localhost;dbname=awdb;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
    $sth = $pdo->prepare("SELECT * FROM history where id = :id ");
    $sth->bindValue(':id', $_POST['ino'], PDO::PARAM_INT);
    $sth->execute();
    foreach($sth as $sc) {
        print($sc['day']."日の後 ".$sc['story'].'<br/>'); 
    }
?>
