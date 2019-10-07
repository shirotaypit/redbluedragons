<?php
    session_start();

    try {
        $pdo = new PDO("mysql:host=localhost;dbname=awdb;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
    } catch (PDOException $e) {
        exit('データベース接続失敗。'.$e->getMessage());
    }
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <META http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Dragons war Status Report</title>
    <style>
        body {
          background: url(back2.jpg) center center / cover no-repeat fixed;
            color: #FFFFFF;
        }
        .body {
            margin: 10px auto;
            width:400px;
            color: #FFFFFF;
        }
    </style>
  </head>
  <body>

    </br>
    </br>
    <center><h1> Dragons WAR status report</h1> </center>

    <table  border="1" align="center" width="60%">
        <tr>
            <th></th>
            <th>RED</th>
            <th>BLUE</th>
        </tr>
        <tr>
            <td>ドラゴン数</td>
            <?php
                $sth = $pdo->query("select count(*) from angels where team = 'RED'");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "</td>";
            ?>
            <?php
                $sth = $pdo->query("select count(*) from angels where team = 'BLUE'");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "</td>";
            ?>
        </tr>
        <tr>
            <td>最大戦闘力</td>
            <?php
                $sth = $pdo->query(" select fight,name from angels where team = 'RED' order by fight desc, name desc limit 1;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "   ( ", $row[1] , " ) " , "</td>";
            ?>
            <?php
                $sth = $pdo->query(" select fight,name from angels where team = 'BLUE' order by fight desc, name desc limit 1;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "   ( ", $row[1] , " ) " , "</td>";
            ?>
        </tr>
        <tr>
            <td>最大移動力</td>
            <?php
                $sth = $pdo->query("  select max(day),name from angels left join history on angels.id = history.id  where team = 'RED' group by day desc, name desc limit 1;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "   ( ", $row[1] , " ) " , "</td>";
            ?>
            <?php
                $sth = $pdo->query("  select max(day),name from angels left join history on angels.id = history.id  where team = 'BLUE' group by day desc, name desc limit 1;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "   ( ", $row[1] , " ) " , "</td>";
            ?>
        </tr>
        <tr>
            <td>最大教化力</td>
            <?php
                $sth = $pdo->query(" select impact,name from angels where team = 'RED' order by fight desc, name desc limit 1;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "   ( ", $row[1] , " ) " , "</td>";
            ?>
            <?php
                $sth = $pdo->query(" select impact,name from angels where team = 'BLUE' order by fight desc, name desc limit 1;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "   ( ", $row[1] , " ) " , "</td>";
            ?>
        </tr>
        <tr>
            <td>全教化値（総合合計）</td>
            <?php
                $sth = $pdo->query(" select sum(r) from advent;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "</td>";
            ?>
            <?php
                $sth = $pdo->query(" select sum(b) from advent;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "</td>";
            ?>
        </tr>
        <tr>
            <td>全教化値（周辺地域）</td>
            <?php
                //$sth = $pdo->query("select  sum(r), sum(b) from advent left join history on history.x = advent.x and advent.y = history.y where history.id is NULL;");
                $sth = $pdo->query("select  sum(r), sum(b) from advent left join history on history.x = advent.x and advent.y = history.y where (advent.r >0 or advent.b>0) and history.id is NULL;");
                $row = $sth->fetch();
                echo "<td>" ,$row[0] , "</td>";
                echo "<td>" ,$row[1] , "</td>";
            ?>
        </tr>
        
    </table>

  </body>
</html>