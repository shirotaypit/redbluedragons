<?php
    session_start();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <META http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Set Parameters</title>
    <style>
        body {
          background: url(back.jpg) center center / cover no-repeat fixed;
        }
        .example {
            margin: 10px auto;
            width:400px;
            color: #FFFFFF;
        }
    </style>

  </head>
  <body>
      <audio src="bgm1.mp3" autoplay loop></audio>
</br>
</br>
</br>
</br>
</br>

<div class="example">
    <?php 
    console.log('test');
    if(isset($_POST['Redsubmit'])){
	    $team="RED";
        if(empty($_POST['name'])){
            $name = "紅龍";
            $file = "DragonRed.png";
        } else {
            $name = $_POST['name'];
            $file = "DragonRed.png";
        }
    }elseif(isset($_POST['Bluesubmit'])){
	    $team="BLUE";
        if(empty($_POST['name'])){
            $name = "蒼龍";
            $file = "DragonBlue.png";
        } else {
            $name = $_POST['name'];
            $file = "DragonBlue.png";
        }
    }else{
    	http_response_code( 301 ) ;
	    header( "Location: ./index.html" ) ;
	    exit ;
    }
    ?>

    <h1>Welcome to this world</h1>
    Your Dragon Name <?php echo($name); ?> <br/> 


    <br/>

    <h1>Input Parameter</h1>
    <form method="post" action="index3.php">
        戦闘力
        <input type="text" name="Fight" value="5">
        <br>
        移動力
        <input type="text" name="Move" value="3">
        <br>
        教化力
        <input type="text" name="Impact" value="8">
</br>
</br>
        <br>
        <input type="submit" name="advent" value=" Go to the fight">
        <input type="text" name="sess" value="<?php print(session_id()); ?>">
        <input type="hidden" name="team" value="<?php print($team); ?>">
        <input type="hidden" name="name" value="<?php print($name); ?>" >

    </form>
</div>
  </body>
</html>