<?php
    header("Content-type: text/plain; charset=UTF-8");
    $pdo = new PDO("mysql:host=localhost;dbname=awdb;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
    
    
    $asth = $pdo->prepare("SELECT * FROM angels where id = :id ");
    $asth->bindValue(':id', $_POST['ino'], PDO::PARAM_INT);
    $asth->execute();
    $row = $asth->fetch(PDO::FETCH_ASSOC);
    print("名前:" .$row['name']   . '<br/>');
    print("戦闘:" .$row['fight']  . '<br/>');
    print("移動:" .$row['move']   . '<br/>');
    print("教化:" .$row['impact'] . '<br/>');
    print("位置:(".$row['x'] . "," . $row['y'] . ")" . '<br/>');
    
    $esth = $pdo->prepare("SELECT * FROM advent where x = :x and y = :y ");
    $esth->bindValue(':x', $row['x'], PDO::PARAM_INT);
    $esth->bindValue(':y', $row['y'], PDO::PARAM_INT);
    $esth->execute();
    $ent = $esth->fetch(PDO::FETCH_ASSOC);
    print("RED民 :" .$ent['r']   . '<br/>');
    print("BLUE民:" .$ent['b']  . '<br/>');

?>
