<?php

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=awdb;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
    } catch (PDOException $e) {
        exit('データベース接続失敗。'.$e->getMessage());
    }

    $stmt = $pdo->prepare('delete from advent');

    for($i = 0; $i < 1024 ; $i++){
        for ($j = 0; $j < 1024; $j++){
            $stmt = $pdo -> prepare("INSERT INTO advent(x, y, r, g, b) VALUES (:x,:y,0,0,0)");
            $stmt->bindValue(':x', $i, PDO::PARAM_INT);
            $stmt->bindValue(':y', $j, PDO::PARAM_INT);
            $stmt->execute();
        }
    }

?>