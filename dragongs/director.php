<?php
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=awdb;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
    } catch (PDOException $e) {
        exit('データベース接続失敗。'.$e->getMessage());
    }

    while(true){
        
        $sth = $pdo->prepare("SELECT * FROM angels where script = False ");
        $sth->execute();
        
        foreach($sth as $row) {
            $id = $row['id'];
            $ustr = $pdo->prepare("update angels set script = True where id = :id");
            $ustr->bindValue(':id', $id, PDO::PARAM_INT);
            $ustr->execute();

            $cmd = "/usr/bin/php /home/ubuntu/scripter.php " . $id;
            exec($cmd . " > /dev/null &");

        }
        sleep(1);
    }
?>