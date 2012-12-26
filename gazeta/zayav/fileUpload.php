<?php
function genFileName()
	{
	$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','1','2','3','4','5','6','7','8','9','0');
	for($i = 0;$i<10;$i++)
		$name.= $arr[rand(0,35)];
	return $name;
	}
function imResize($w,$imFun,$pName,$h=5000)
	{
	$width=imagesx($imFun);
	$height=imagesy($imFun);
	$x=$w;
	$y=round($x/$width*$height);
	
	if($y>$h)
		{
		$y=$h;
		$x=round($y/$height*$width);
		}

	$imNew=imagecreatetruecolor($x,$y);
	imagecopyresampled($imNew,$imFun,0,0,0,0,$x,$y,$width,$height);
	imagejpeg($imNew,$pName,80);
	imagedestroy($imNew);
	return $imFun;
	}

function getIM($name)
	{
	$im=imagecreatefromjpeg($name);
	if(!$im) $im=imagecreatefrompng($name);
	if(!$im) $im=imagecreatefromgif($name);
	return $im;
	}

require_once('../../include/AjaxHeader.php');

$fName=genFileName();					// задаём имя
$path="../../files/images/";				// указываем путь хранения изображений

ini_set('memory_limit','120M');

if($_FILES["file_name"]["type"]=="image/tiff")
	{
	$upName=genFileName()."_upload";
	if(move_uploaded_file($_FILES["file_name"]["tmp_name"],$PATH_FILES.$upName.".tif"))
		{
		exec("convert ".$PATH_FILES.$upName.".tif ".$PATH_FILES.$upName.".jpg");
		unlink($PATH_FILES.$upName.".tif");
		$im=getIM($PATH_FILES.$upName.".jpg");
		unlink($PATH_FILES.$upName.".jpg");
		}
	}

if(	$_FILES["file_name"]["type"]=="image/jpeg" or
	$_FILES["file_name"]["type"]=="image/png" or
	$_FILES["file_name"]["type"]=="image/gif") 
	$im=getIM($_FILES["file_name"]["tmp_name"]);



if(!$im) $fName="error";
else
	{
	$width=imagesx($im);
	if($width>600) $width=600;
	$imB=imResize($width,$im,$path.$fName."b.jpg",550);
	$imM=imResize(200,$imB,$path.$fName."m.jpg",320);
	imResize(80,$imM,$path.$fName."s.jpg",80);
	}

setcookie("upload",$fName,time()+3600,"/");
?>
