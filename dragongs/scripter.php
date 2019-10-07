<?php

    $WaitTime = 15;    // タイマー値(秒)
    
    #------------------------------------
    # 初期処理
    #------------------------------------
    // Initial connect database
    syslog(LOG_INFO,"Initial connect database");
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=awdb;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);
    } catch (PDOException $e) {
        syslog(LOG_ERR,'データベース接続失敗。'.$e->getMessage());
    }

    // スクリプト読み込み
    $messages=array();
    $mess=getScriptMinMax($pdo);
        
    //echo $mess[40][3];

    // 自分の情報を取得
    $sth = $pdo->prepare("SELECT * FROM angels where id = :id");
    $sth->bindValue(':id', $argv[1], PDO::PARAM_INT);
    $sth->execute();
    
    if ($sth->rowCount()==0){
        echo "fin."."\r\n";
        syslog(LOG_ERR,'指定されたidは存在しません'.$argv[1]);
        exit(1);
    }
    
    $row = $sth->fetch(PDO::FETCH_ASSOC);
    $myid = $row['id'];
    $myname = $row['name'];
    $myteam = $row['team'];
    $mytF = strtoupper(substr($myteam,0,1));   //処理を簡略化するため
    $myfight = $row['fight'];
    $mymove = $row['move'];
    $myimpact = $row['impact'];
    $myx = $row['x'];
    $myy = $row['y'];
    $myscript = $row['script'];

    #------------------------------------
    # 降臨処理
    #------------------------------------
    // 現地の情報を取得する
    [$enemy,$r,$b] = check_here($pdo,$myteam,$myx,$myy);
    //echo $enemy."-".$r." ".$b."\r\n";
    $mode=0;
    
    if ($enemy > 0){
        // 3:会敵降臨
        $mode = 3;
    } elseif ($r + $b == 0) {
        // 0:無色降臨
        $mode = 0;
    } elseif ($mytF == "R"){
        //REDチーム
        if ($r < $b) {
            // 1:敵地降臨
            $mode = 1;
        } else {
            // 2:自陣降臨
            $mode = 2;
        }
    } else {
        //BLUEチーム
        if ($b < $r) {
            // 1:敵地降臨
            $mode = 1;
        } else {
            // 2:自陣降臨
            $mode = 2;
        }
    }
    
    // 降臨メッセージ取得
    $sc = getScript($mess,$mode);
    $script = str_replace('$name', $myname, $sc);
    //echo $script;
    
    // 降臨メッセージ書き込み
    $days = 1;
    $stmt = $pdo -> prepare("INSERT INTO history(id,day,story,x, y) VALUES (:id,:d,:s,:x,:y)");
    $stmt->bindValue(':id', $myid,   PDO::PARAM_INT);
    $stmt->bindValue(':d',  $days,   PDO::PARAM_INT);
    $stmt->bindValue(':s',  $script, PDO::PARAM_STR );
    $stmt->bindValue(':x',  $myx,    PDO::PARAM_INT);
    $stmt->bindValue(':y',  $myy,    PDO::PARAM_INT);
    $stmt->execute();    

        // 教化処理

        // 中心の教化
        $rate = 32;
        $x = $myx;
        $y = $myy;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 周囲の教化
        $rate = 12;

        // 上
        $x = $myx + 0;
        $y = $myy - 2;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 右上
        $x = $myx + 1;
        $y = $myy - 1;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 右下
        $x = $myx + 1;
        $y = $myy + 1;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 下
        $x = $myx + 0;
        $y = $myy + 2;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 左下
        $x = $myx - 1;
        $y = $myy + 1;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 左上
        $x = $myx - 1;
        $y = $myy - 1;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);


    #------------------------------------
    # ここより繰り返し処理
    #------------------------------------
    while(True){        // 無限ループに注意
    
        sleep($WaitTime);

        // 自分の情報を再取得　位置情報と移動能力
        $sth = $pdo->prepare("SELECT * FROM angels where id = :id");
        $sth->bindValue(':id', $argv[1], PDO::PARAM_INT);
        $sth->execute();
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        $mymove = $row['move'];
        $myx = $row['x'];
        $myy = $row['y'];

        $days++;
    
        // 教化処理

        // 中心の教化
        $rate = 32;
        $x = $myx;
        $y = $myy;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 周囲の教化
        $rate = 12;

        // 上
        $x = $myx + 0;
        $y = $myy - 2;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 右上
        $x = $myx + 1;
        $y = $myy - 1;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 右下
        $x = $myx + 1;
        $y = $myy + 1;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 下
        $x = $myx + 0;
        $y = $myy + 2;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 左下
        $x = $myx - 1;
        $y = $myy + 1;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);

        // 左上
        $x = $myx - 1;
        $y = $myy - 1;
        do_mission($pdo,$mytF,$x,$y,$myimpact,$rate);


        // 現地の情報を再取得する
        [$enemy,$r,$b] = check_here($pdo,$myteam,$myx,$myy);

        //echo $r,"-",$b,"-",$myimpact,"-",$enemy,"\r\n";
        // 会敵処理
        if ($enemy > 0){
            if ($mytF == "R") {
                $war = $r * $myimpact - $enemy * $b;
                echo $war."\r\n";
                if ($war == 0 ) {
                    // 22:会敵引き分け
                    $mode = 22;
                } elseif ($war > 0) {
                    // 20:会敵勝利
                    $mode = 20;
                } else {
                    // 21:会敵負け
                    $mode = 21;
                }
            } else {
                $war = $r * $enemy - $b * $myimpact;
                if ($war == 0 ) {
                    // 22:会敵引き分け
                    $mode = 22;
                } elseif ($war > 0) {
                    // 20:会敵勝利
                    $mode = 20;
                } else {
                    // 21:会敵負け
                    $mode = 21;
                }
            }
            // 戦闘メッセージ取得
            $sc = getScript($mess,$mode);
            $script = str_replace('$name', $myname, $sc);
            //echo $script;
            
            // 降臨メッセージ書き込み
            $stmt = $pdo -> prepare("INSERT INTO history(id,day,story,x, y) VALUES (:id,:d,:s,:x,:y)");
            $stmt->bindValue(':id', $myid,   PDO::PARAM_INT);
            $stmt->bindValue(':d',  $days,   PDO::PARAM_INT);
            $stmt->bindValue(':s',  $script, PDO::PARAM_STR );
            $stmt->bindValue(':x',  $myx,    PDO::PARAM_INT);
            $stmt->bindValue(':y',  $myy,    PDO::PARAM_INT);
            $stmt->execute();    

        }

        // 教化メッセージ格納
        if ($r + $b == 0) {
            // 10:無色教化
            $mode = 10;
        } elseif ($mytF == "R"){
            //REDチーム
            if ($r < $b) {
                // 12:敵陣教化
                $mode = 12;
            } else {
                // 11:自陣教化
                $mode = 11;
            }
        } else {
            //BLUEチーム
            if ($b < $r) {
                // 12:敵陣教化
                $mode = 12;
            } else {
                // 11:自陣教化
                $mode = 11;
            }
        }
    
        // 教化メッセージ取得
        $sc = getScript($mess,$mode);
        $script = str_replace('$name', $myname, $sc);
        //echo $script;
        
        // 教化メッセージ書き込み
        $stmt = $pdo -> prepare("INSERT INTO history(id,day,story,x, y) VALUES (:id,:d,:s,:x,:y)");
        $stmt->bindValue(':id', $myid,   PDO::PARAM_INT);
        $stmt->bindValue(':d',  $days,   PDO::PARAM_INT);
        $stmt->bindValue(':s',  $script, PDO::PARAM_STR );
        $stmt->bindValue(':x',  $myx,    PDO::PARAM_INT);
        $stmt->bindValue(':y',  $myy,    PDO::PARAM_INT);
        $stmt->execute();    


        // 移動処理
        if( $mymove == 0 ){
            // 帰還メッセージ取得  40:天昇
            $mode = 40; 
            $sc = getScript($mess,$mode);
            $script = str_replace('$name', $myname, $sc);
            //echo $script;
            
            // 天昇メッセージ書き込み
            $stmt = $pdo -> prepare("INSERT INTO history(id,day,story,x, y) VALUES (:id,:d,:s,:x,:y)");
            $stmt->bindValue(':id', $myid,   PDO::PARAM_INT);
            $stmt->bindValue(':d',  $days,   PDO::PARAM_INT);
            $stmt->bindValue(':s',  $script, PDO::PARAM_STR );
            $stmt->bindValue(':x',  $myx,    PDO::PARAM_INT);
            $stmt->bindValue(':y',  $myy,    PDO::PARAM_INT);
            $stmt->execute();
            
            break;    //終了
        }

        // 移動先の決定
        switch( rand(1,6)){
            case 1:
                // 上
                $x = $myx + 0;
                $y = $myy - 2;
                break;

            case 2:
                // 右上
                $x = $myx + 1;
                $y = $myy - 1;
                break;

            case 3:
                // 右下
                $x = $myx + 1;
                $y = $myy + 1;
                break;

            case 4:
                // 下
                $x = $myx + 0;
                $y = $myy + 2;
                break;

            case 5:
                // 左下
                $x = $myx - 1;
                $y = $myy + 1;
                break;

            case 6:
                // 左上
                $x = $myx - 1;
                $y = $myy - 1;

                break;
            
        }
        
        // 移動処理
        $m = $mymove - 1;
        $ustr = $pdo->prepare("update angels set move = :m , x =:x , y = :y where id = :id");
        $ustr->bindValue(':id', $argv[1], PDO::PARAM_INT);
        $ustr->bindValue(':m', $m, PDO::PARAM_INT);
        $ustr->bindValue(':x', $x, PDO::PARAM_INT);
        $ustr->bindValue(':y', $y, PDO::PARAM_INT);
        $ustr->execute();

        // 30:移動
        $mode = 30; 
        $sc = getScript($mess,$mode);
        $script = str_replace('$name', $myname, $sc);
        //echo $script;
        
        // 移動メッセージの格納
        $stmt = $pdo -> prepare("INSERT INTO history(id,day,story,x, y) VALUES (:id,:d,:s,:x,:y)");
        $stmt->bindValue(':id', $myid,   PDO::PARAM_INT);
        $stmt->bindValue(':d',  $days,   PDO::PARAM_INT);
        $stmt->bindValue(':s',  $script, PDO::PARAM_STR );
        $stmt->bindValue(':x',  $myx,    PDO::PARAM_INT);
        $stmt->bindValue(':y',  $myy,    PDO::PARAM_INT);
        $stmt->execute();

    }    

#------------------------------------
# do_mission
#------------------------------------
function do_mission($pdo,$t,$x,$y,$i,$rate){
    
    if (($x < 0 ) or ($y < 1) ) {
        return;
    }
    // 現地読み込み
    $sta = $pdo->prepare("SELECT * FROM advent where (x = :x and y = :y)");
    $sta->bindValue(':x', $x, PDO::PARAM_INT);
    $sta->bindValue(':y', $y, PDO::PARAM_INT);
    $sta->execute();
    $row = $sta->fetch(PDO::FETCH_ASSOC);
    $r = $row['r'];
    $b = $row['b'];

    // 教化実行
    if ($t == "B"){
        $b = $b + $i * $rate;
        $r = $r - $i * $rate;
    } else {
        $b = $b - $i * $rate;
        $r = $r + $i * $rate;
    }
    
    // 最大値、最小値補正
    if ($b>255) {
        $b=253;
    }
    if ($r>255){
        $r=253;    
    } 
    if ($b<0){
        $b=0;  
    } 
    if ($r<0){
        $r=0;
    }

    // 書き込み
    $sta = $pdo->prepare("update advent set r=:r , b=:b where (x = :x and y = :y)");
    $sta->bindValue(':r', $r, PDO::PARAM_INT);
    $sta->bindValue(':b', $b, PDO::PARAM_INT);
    $sta->bindValue(':x', $x, PDO::PARAM_INT);
    $sta->bindValue(':y', $y, PDO::PARAM_INT);
    $sta->execute();
}



#------------------------------------
# Get Script
#------------------------------------
function getScript($mess,$mode){
    $n = rand(0,count($mess[$mode])-1);
    //echo "mode=",$mode," max=",count($mess[$mode])-1," rand=",$n," mess=",$mess[$mode][$n],"\r\n";
    return $mess[$mode][$n];
}
    
#------------------------------------
# Check here
#------------------------------------
function check_here($pdo,$t,$x,$y){
    
    // 陣地チェック
    $sta = $pdo->prepare("SELECT * FROM advent where (x = :x and y = :y)");
    $sta->bindValue(':x', $x, PDO::PARAM_INT);
    $sta->bindValue(':y', $y, PDO::PARAM_INT);
    $sta->execute();
    $row = $sta->fetch(PDO::FETCH_ASSOC);
    $r = $row['r'];
    $b = $row['b'];
    
    
    // 会敵チェック
    $sth = $pdo->prepare("SELECT * FROM angels where x = :x and y = :y and team != :t");
    
    $sth->bindValue(':t', $t, PDO::PARAM_STR);
    $sth->bindValue(':x', $x, PDO::PARAM_INT);
    $sth->bindValue(':y', $y, PDO::PARAM_INT);
    $sth->execute();
    if ($sth->rowCount()==0){
        $enemy = 0;
    }else{
        $ene = $sth->fetch(PDO::FETCH_ASSOC);
        $enemy = $ene['fight'];
    }
    
    //echo "Check here x = ",$x," y = ",$y," team = ",$t, " RED(",$r,") BLUE(",$b,") enemy=",$enemy,"\r\n";
    return[$enemy,$r,$b];    
}

#------------------------------------
# Scripts get
#------------------------------------
function  getScriptMinMax($pdo){
    $stmt = $pdo->query("select * from script order by mode,number");

    $i = 0;
    $j = 0;
    $messages=array();
    
    foreach($stmt->fetchAll() as $row){
        if ($i != $row['mode']){
            $i = $row['mode'];
            $j = $row['number'];
        }
        //echo "(".$i.",".$j.")".substr($row['message'],0,14)."\r\n";
        
        $messages[$i][$j]=$row['message'];
        $i = $row['mode'];
        $j++;
    }

    return $messages;
}

?>