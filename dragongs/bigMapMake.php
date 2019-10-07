<?php

   
    $pdo = new PDO("mysql:host=localhost;dbname=awdb;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);


while(true)
{





    $img = imagecreate(960,500);
    $white = imagecolorallocate($img, 255, 255, 255);
    $black = imagecolorallocate($img, 0, 0, 0);
    $col = imagecolorallocate($img, 240, 248, 255); 
    $color = imagecolorallocate($img, 0, 255, 127);    //springgreen
     

    // Map Size 100*100 として
    //コマのサイズを決める
    $bw = intval((500-20)/50);

    $ss = 0;
    $sss = 0;
    $asth = $pdo->prepare("SELECT * FROM advent where x = :xq and y = :yq ");
    
    for( $i = 0 ; $i < 100 ; $i++){
        if ($i % 10 == 0 ) {
            imagestring($img, 5+20,$i*$bw+10+3+20, 10+$ss+3,  $i , $black);    
        }
        for ( $j = 0; $j < 50; $j+=1  ){
            $x1 = $i * $bw +10 ;
            $x2 = $i * $bw +10 +$bw;
            $y1 = $j * $bw +10 +$ss;
            $y2 = $j * $bw +10 +$bw +$ss;
            imagerectangle ( $img, $x1+20, $y1+20, $x2+20,  $y2+20 , $color );
    
            // 地面処理
            $asth->bindValue(':xq', $i,        PDO::PARAM_INT);
            $asth->bindValue(':yq', $j*2+$sss, PDO::PARAM_INT);
            $asth->execute();
    
    
            $ad = $asth->fetch(PDO::FETCH_ASSOC);
            $r = $ad['r'];
            $b = $ad['b'];

            //if ($r == 0 and $b == 0 ){
                
            //} else {
                $filcol = imagecolorexact($img, $r, 0, $b);
                if($filcol==-1) {
                  //color does not exist...
                  //test if we have used up palette
                  if(imagecolorstotal($img)>=255) {
                       //palette used up; pick closest assigned color
                       $filcol = imagecolorclosest($img, $r, 0, $b);
                  } else {
                       //palette NOT used up; assign new color
                       $filcol = imagecolorallocate($img, $r, 0, $b);
                  }
                }
    
                $result = ImageFilledRectangle($img, $x1+1+20,$y1+1+20, $x2-1+20,$y2-1+20, $filcol);
            //}         

            if ($i == 0 ){
                if ($j % 10 == 0 ){
                    imagestring($img, 5,$x1+3, $y1+16, $j*2+$sss , $black);    
                }
            }
            
        }

        
        if ($i % 10 == 0 ) {
            imagestring($img, 5+20,$i*$bw+10+3+20, 10+$ss+3,  $i , $black);    
        }
        if ($ss == 0) {
            $ss = intval($bw /2);
            $sss = 1;
        } else {
            $ss = 0;
            $sss = 0;
        }
    }

    //imagestring($img, 5, 10, $sy-20,$ix."/".$iy."/".$kx."/".$ky, $black); 


//header('Content-Type: image/png');
//    imagepng($img);
    imagepng($img, './public_html/bigmap.png');

    sleep(3);
    
}
?>
