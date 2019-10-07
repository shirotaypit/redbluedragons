<?php
//新しく画像リソースを生成する場合
$img = imagecreate($_GET['width'],$_GET['height']);

$no = $_GET['id'];
$ix =  $_GET['x'];
$iy =  $_GET['y'];


$pdo = new PDO("mysql:host=localhost;dbname=awdb;charset=utf8","root","", [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING]);

$sth = $pdo->prepare("SELECT * FROM history where id = :id ");
$sth->bindValue(':id', $no, PDO::PARAM_INT);
$sth->execute();
$white = imagecolorallocate($img, 255, 255, 255);
$black = imagecolorallocate($img, 0, 0, 0);
//aliceblue（背景色となる）
$col = imagecolorallocate($img, 240, 248, 255); 
    
foreach($sth as $sc) {

}
$lx = $sc['x'];
$ly = $sc['y'];

$dx = abs($ix-$lx) * 2 + 2;
$dy = abs($iy-$ly) * 2 + 2;

if ($dx < 12) $dx = 12;
if ($dy < 9) $dy = 9;

//画像サイズを取得する
$sx = imagesx($img);
$sy = imagesy($img);

//コマのサイズを決める
$bw = intval($sx/$dx);

// 四角形の線の色を指定（springgreen）
$color = imagecolorallocate($img, 0, 255, 127);
 
// 画像リソースに四角形を描画
//imagerectangle ( $img, 50, 30, 450, 270, $color );

$kx = $ix - intval($dx /2);
$ky = $iy - intval($dy /2) - 2;

if ($ky & 1) {
    $k--;
}

$ss = 0;
$sss = 0;
$asth = $pdo->prepare("SELECT * FROM advent where x = :xq and y = :yq ");

for( $i = 0 ; $i < $dx-1 ; $i++){
    imagestring($img, 5,$i*$bw+10+3, 10+$ss+3,  $kx+$i , $white);    
    for ( $j = 0; $j < $dy -1; $j+=1  ){
        $x1 = $i * $bw +10 ;
        $x2 = $i * $bw +10 +$bw;
        $y1 = $j * $bw +10 +$ss;
        $y2 = $j * $bw +10 +$bw +$ss;
        imagerectangle ( $img, $x1, $y1, $x2,  $y2 , $color );

        // 地面処理
        $asth->bindValue(':xq', $kx+$i,        PDO::PARAM_INT);
        $asth->bindValue(':yq', $ky+$j*2+$sss, PDO::PARAM_INT);
        $asth->execute();

        $ad = $asth->fetch(PDO::FETCH_ASSOC);
        $r = $ad['r'];
        $b = $ad['b'];

        $filcol = ImageColorAllocate($img, $r, 0, $b);
        ImageFilledRectangle($img, $x1 + 1,$y1 +1, $x2 -1,$y2 -1, $filcol);

        if ($i == 0 ){
            imagestring($img, 5,$x1+3, $y1+16, $ky+$j*2+$sss , $white);    
        }
        
    }
    imagestring($img, 5,$i*$bw+10+3, 10+$ss+3,  $kx+$i , $white);    
    if ($ss == 0) {
        $ss = intval($bw /2);
        $sss = 1;
    } else {
        $ss = 0;
        $sss = 0;
    }
}

imagestring($img, 5, 10, $sy-20,$ix."/".$iy."/".$kx."/".$ky, $black); 


header('Content-Type: image/png');
imagepng($img);

?>