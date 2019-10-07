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
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

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

    <script>

    $(document).ready(function() {
        
        
        // スクリプトをリロードする
        function reScript(){
            console.log('sample');
            
            var data = {ino : $('#no').val()};
            
            $.ajax({
                type: "POST",
                url: "scriptQuery.php",
                data: data,
                success: function(data, dataType){
                    $('#result').html(data);
                    //console.log("data->" + data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    console.log("XMLHttpRequest : " + XMLHttpRequest.status);
                    console.log("textStatus : " + textStatus);
                    console.log("errorThrown : " + errorThrown);
                }
            });
        }
        setInterval(reScript, 3000);

        // ステータスをリロードする
        function reStatus(){

            var data = {ino : $('#no').val()};
            
            $.ajax({
                type: "POST",
                url: "statusQuery.php",
                data: data,
                success: function(data, dataType){
                    $('#status').html(data);
                    //console.log("data->" + data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown){
                    console.log("XMLHttpRequest : " + XMLHttpRequest.status);
                    console.log("textStatus : " + textStatus);
                    console.log("errorThrown : " + errorThrown);
                }
            });
        }
        setInterval(reStatus, 3000);


        // 大マップをリロードする
        function reBigMap(){

            var xhr = new XMLHttpRequest();
            xhr.responseType = 'blob';
            xhr.open('GET', 'bigmap.png', true);

            xhr.onload = function(e) {
              if (this.status == 200) {
                // get binary data as a response
                var blob = this.response;
                // URL.createObjectURL
                var img = document.getElementById("bigmap");
                var url = window.URL || window.webkitURL;
                img.src = url.createObjectURL(blob);
              }
            };
            xhr.send();
        }
        setInterval(reBigMap, 4000);


        // 小マップをリロードする
        function reMap(){

            var data2 = "ino=" + $('#no').val();
            console.log(data2);

            var xhr = new XMLHttpRequest();
            xhr.responseType = 'blob';
            xhr.open( 'POST', './mapQuery.php' ,true);
            xhr.setRequestHeader('content-type', 'application/x-www-form-urlencoded;charset=UTF-8');

            //xhr.open( 'GET', 'mapQuery.php/?ino=25' ,true);
            //xhr.open('GET', 'test.png', true);

            xhr.onload = function(e) {
              if (this.status == 200) {
                // get binary data as a response
                var blob = this.response;
                showImageByFileReader(blob);
                // URL.createObjectURL
                //var img = document.getElementById("minimap");
                //var url = window.URL || window.webkitURL;
                //img.src = url.createObjectURL(blob);
                //$("#minimap").src = this.response;
                //console.log(blob);
              }
            };

            xhr.send(data2);
            
        }
        setInterval(reMap, 3000);
        
        
        reStatus();
        reScript();
        reMap();
        reBigMap();
    });

    // FileReader
    var showImageByFileReader = function(blob) {
      var reader = new FileReader();
      reader.onloadend = function() {
        var img = document.getElementById("minimap");
        img.src = reader.result;
        //console.log(reader.result);
      }
      // DataURLとして読み込む
      reader.readAsDataURL(blob);
    }
    </script>
  </head>

  <body>
    <audio src="bgm2.mp3" autoplay loop></audio>
    <?php

    // セッションIDと名前、チームから検索する
    $sid = session_id();
    $sth = $pdo->prepare("select * from angels where sessionId = :sid and name = :name and team = :team");
    $sth->bindValue(':sid', $sid, PDO::PARAM_STR);
    $sth->bindValue(':team', $_POST['team'], PDO::PARAM_STR);
    $sth->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
    $sth->execute();

    $count = $sth->rowCount();

    if ($count == 0){
        $stmt = $pdo -> prepare("INSERT INTO angels (sessionId, name, team, fight, move, impact,x,y) VALUES (:s,:n,:t,:f,:m,:i,:x,:y)");
        $stmt->bindValue(':s', $sid, PDO::PARAM_STR );
        $stmt->bindValue(':n', $_POST['name'], PDO::PARAM_STR );
        $stmt->bindValue(':t', $_POST['team'], PDO::PARAM_STR );
        $stmt->bindValue(':f', $_POST['Fight'], PDO::PARAM_INT);
        $stmt->bindValue(':m', $_POST['Move'], PDO::PARAM_INT);
        $stmt->bindValue(':i', $_POST['Impact'], PDO::PARAM_INT);
        $stmt->bindValue(':x', rand(10,50)*2, PDO::PARAM_INT);    // 座標は偶数どうしか奇数どうしの組み合わせのみ
        $stmt->bindValue(':y', rand(10,50)*2, PDO::PARAM_INT);
        $stmt->execute();
    }

    $sid = session_id();
    $sth = $pdo->prepare("select * from angels where sessionId = :sid and name = :name and team = :team");
    $sth->bindValue(':sid', $sid, PDO::PARAM_STR);
    $sth->bindValue(':team', $_POST['team'], PDO::PARAM_STR);
    $sth->bindValue(':name', $_POST['name'], PDO::PARAM_STR);
    $sth->execute();
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    if($row['team']=='RED'){
        $fileName = 'DragonRed.png';
    } else {
        $fileName = 'DragonBlue.png';
    }
    $no = $row['id'];

    ?>
    <input type='hidden' id='no' value='<?php print($no) ?>'>
    <table border="1" width="100%">
        <tr>
            <td width="70%">
              <img id="bigmap" width="100%">
            </td>
            <td width="30%">
            <img id="minimap" width="100%">  
            <div id="status"></div>

            </td>
        </tr>
    </table>
    <table border="1" width="100%">
        <tr>
            <td width="20%">
                <img src="<?php print($fileName) ?>" width="100%">
                <form method="post" action="index.html">
                    <input type="submit" name="finish" value="back">
                </form>
            </td>
            <td width="80%">
                <div id="result"></div>
            </td>
        </tr>
    </table>
  </body>
</html>