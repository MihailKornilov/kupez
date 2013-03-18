<?php
$zayav=$VK->QueryObjectOne("select * from zayav where id=".(preg_match("|^[\d]+$|",$_GET['id'])?$_GET['id']:0));
if(!$zayav->id)  header("Location: ". $URL."&my_page=nopage&parent=zayav");

$gnMin=$VK->QRow("select min(general_nomer) from gazeta_nomer where day_print>='".strftime('%Y-%m-%d',time())."'");

$client=$VK->QRow("select fio from client where id=".$zayav->client_id);

if($_POST['zayav_id_rek'])
	{
	$VK->Query("delete from gazeta_nomer_pub where general_nomer>=".$gnMin." and zayav_id=".$_POST['zayav_id_rek']);

	$skRazmer=$VK->QRow("select razmer from setup_skidka where id=".$_POST['skidka']);

	if($_POST['gn_input'])
		{
		$arr=explode(',',$_POST['gn_input']);
		foreach($arr as $sp)
			{
			$gnpol=explode(':',$sp);
			$VK->Query("insert into gazeta_nomer_pub (general_nomer,polosa_id,zayav_id,summa,viewer_id_add) values (".$gnpol[0].",".$gnpol[1].",".$_POST['zayav_id_rek'].",'".$gnpol[2]."',".$_GET['viewer_id'].")");
			}
		}

	if($gnpol[0])
		$day_active=$VK->QRow("select day_end from gazeta_nomer where general_nomer=".$gnpol[0]." limit 1");
	else $day_active='0000-00-00';

	$summa=$VK->QRow("select sum(summa) from gazeta_nomer_pub where zayav_id=".$_POST['zayav_id_rek']);

	$VK->Query("update zayav set
size_x='".$_POST['size_x']."',
size_y='".$_POST['size_y']."',
skidka_id=".$_POST['skidka'].",
skidka_razmer=".($skRazmer?$skRazmer:0).",
summa='".$summa."',
active_day='".$day_active."',
file='".$_POST['file']."'
where id=".$_POST['zayav_id_rek']);

	setClientBalans($zayav->client_id);

	header("location:".$URL."&my_page=zayavView&id=".$_POST['zayav_id_rek']."&msg=zedit");
	}




$prevSum=round($VK->QRow("select ifnull(sum(summa),0) from gazeta_nomer_pub where general_nomer<".$gnMin." and zayav_id=".$zayav->id),2);
$prevSumShow="<INPUT type=".($prevSum>0?'text':'hidden')." id=prev_sum value='".round($prevSum,2)."' readonly>".($prevSum>0?' + ':'')."";

if($_POST['zayav_id_txt'])
	{
	$VK->Query("delete from gazeta_nomer_pub where zayav_id=".$_POST['zayav_id_txt']);

	if($_POST['gn_input'])
		{
		$arr=explode(',',$_POST['gn_input']);
		$dop;
		foreach($arr as $sp)
			{
			$gnpol=explode(':',$sp);
			$VK->Query("insert into gazeta_nomer_pub (general_nomer,ob_dop_id,zayav_id,summa,viewer_id_add) values (".$gnpol[0].",".$gnpol[1].",".$_POST['zayav_id_txt'].",'".$gnpol[2]."',".$_GET['viewer_id'].")");
			if($gnpol[1]>0) $dop=$gnpol[1];
			}
		if($dop)
			switch($dop)
				{
				case 1: $dop='ramka'; break;
				case 2: $dop='black'; break;
				case 3: $dop='bold'; break;
				}
		}
	
	if($gnpol[0])
		$day_active=$VK->QRow("select day_end from gazeta_nomer where general_nomer=".$gnpol[0]." limit 1");
	else $day_active='0000-00-00';

	$VK->Query("update zayav set
rubrika=".$_POST['rubrika'].",
podrubrika=".$_POST['podrubrika'].",
txt='".$_POST['txt']."',
telefon='".$_POST['telefon']."',
adres='".$_POST['adres']."',
summa_manual=".$_POST['summa_manual'].",
summa=".round($_POST['summa']+$prevSum,2).",
active_day='".$day_active."',
file='".$_POST['file']."',
dop='".$dop."'
where id=".$_POST['zayav_id_txt']);

	if($_POST['client_id'])
		{
		$VK->Query("update oplata set client_id=".$_POST['client_id']." where zayav_id=".$_POST['zayav_id_txt']);
		$VK->Query("update zayav set client_id=".$_POST['client_id']." where id=".$_POST['zayav_id_txt']);
		$zayav->client_id=$_POST['client_id'];
		}
	
	if($zayav->client_id>0) setClientBalans($zayav->client_id);

	header("location:".$URL."&my_page=zayavView&id=".$_POST['zayav_id_txt']."&msg=zedit");
	}








if($_POST['zayav_id_poz_st'])
	{
	$VK->Query("delete from gazeta_nomer_pub where zayav_id=".$_POST['zayav_id_poz_st']);

	if($_POST['gn_input'])
		{
		$arr=explode(',',$_POST['gn_input']);
		foreach($arr as $sp)
			{
			$gnpol=explode(':',$sp);
			$VK->Query("insert into gazeta_nomer_pub (general_nomer,zayav_id,summa,viewer_id_add) values (".$gnpol[0].",".$_POST['zayav_id_poz_st'].",'".$gnpol[2]."',".$_GET['viewer_id'].")");
			}
		}
	
	if($gnpol[0])
		$day_active=$VK->QRow("select day_end from gazeta_nomer where general_nomer=".$gnpol[0]." limit 1");
	else $day_active='0000-00-00';

	$VK->Query("update zayav set summa=".round($_POST['summa'],2).",active_day='".$day_active."',file='".$_POST['file']."' where id=".$_POST['zayav_id_poz_st']);

	setClientBalans($zayav->client_id);

	header("location:".$URL."&my_page=zayavView&id=".$_POST['zayav_id_poz_st']."&msg=zedit");
	}









include('incHeader.php');
$mLink2='Sel'; include 'gazeta/mainLinks.php';
$dLink2='Sel'; include 'gazeta/zayav/dopLinks.php';

echo "<DIV class=zayavEdit>";
$countViewed=$VK->QRow("select count(id) from gazeta_nomer_pub where general_nomer<".$gnMin." and zayav_id=".$zayav->id);
switch($zayav->category)
	{
	case 1: include 'gazeta/zayav/zayavEditOb.php'; break;
	case 2: include 'gazeta/zayav/zayavEditRek.php'; break;
	default: include 'gazeta/zayav/zayavEditPozSt.php'; break;
	}
echo "</DIV>";

include('incFooter.php');
?>
<SCRIPT type="text/javascript">
$(document).ready(function(){
	if($("#file").val())
		filePrint($("#file").val());
	else tdUploadSet();
	
	VK.callMethod('setLocation','zayavEdit_<?php echo $zayav->id; ?>');
	});


// «¿√–”« ¿ ‘¿…À¿
var timer=0;
function tdUploadSet()
	{
	$("#file").val('');
	clearInterval(timer);
	delCookie('upload');
	var HTML="<INPUT TYPE=file NAME=file_name id=file_name onchange=fileSelected();>";
	HTML+="<IFRAME src='' name=uploadFrame scrolling=yes frameborder=1 style=display:none;></IFRAME>";
	$("#tdUpload").html(HTML);
	frameBodyHeightSet();
	}

function fileSelected()
	{
	$("#file_name").after("<IMG src=/img/upload.gif class=upload><A href='javascript:' onclick=tdUploadSet();>ÓÚÏÂÌËÚ¸</A>");
	setCookie('upload','process');
	timer=setInterval("fileUploadStart();",500);
	document.FormZayav.submit();
	$("#file_name").attr('disabled','on');
	}

function fileUploadStart()
	{
	var COOKIE=getCookie("upload");
	if(COOKIE!='process')
		if(COOKIE!='error')
			{
			clearInterval(timer);
			$("#file").val(COOKIE);
			filePrint(COOKIE);
			delCookie("upload");
			}
	}

function filePrint(NAME)
	{
	var HTML="<TABLE cellpadding=0 cellspacing=0 id=fileTab>";
	HTML+="<TR><TD><IMG src=/files/images/"+NAME+"s.jpg onclick=fotoShow('"+NAME+"'); onload=frameBodyHeightSet();><TD valign=top><A href='javascript:' class=img_del onclick=tdUploadSet();></A>";
	HTML+="</TABLE>";
	$("#tdUpload").html(HTML);
	}


</SCRIPT>
