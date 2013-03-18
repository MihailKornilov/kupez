<?php
$zayav=$VK->QueryObjectOne("select * from zayav where id=".(preg_match("|^[\d]+$|",$_GET['id'])?$_GET['id']:0));
if(!$zayav->id)  header("Location: ". $URL."&my_page=nopage&parent=zayav");

$client=$VK->QRow("select fio from client where id=".$zayav->client_id);
$gnMin=$VK->QRow("select min(general_nomer) from gazeta_nomer where day_print>='".strftime('%Y-%m-%d',time())."'");

$spisok=$VK->QueryRowArray("select
gazeta_nomer.day_public,
gazeta_nomer.week_nomer,
gazeta_nomer.general_nomer,
gazeta_nomer_pub.summa,
gazeta_nomer_pub.polosa_id,
gazeta_nomer_pub.ob_dop_id
from gazeta_nomer,gazeta_nomer_pub where gazeta_nomer_pub.general_nomer=gazeta_nomer.general_nomer and gazeta_nomer_pub.zayav_id=".$zayav->id." order by gazeta_nomer.general_nomer");
$nomer="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>";
switch($zayav->category)
	{
	case 1: $dopName='<TH>Дополнительно'; break;
	case 2: $dopName='<TH>Полоса'; break;
	}

$nomer.="<TR><TH>Номер<TH>Выход<TH>Цена".$dopName;
if(count($spisok)>0)
	{
	switch($zayav->category)
		{
		case 1: $dopVal=$VK->QueryPtPArray("select id,name from setup_ob_dop"); $p=5; break;
		case 2: $dopVal=$VK->QueryPtPArray("select id,name from setup_polosa_cost"); $p=4; break;
		}
	foreach($spisok as $sp)
		{
		$nomer.="<TR class='line".($sp[2]<$gnMin?' past':'')."'>";
		$nomer.="<TD align=right><B>".$sp[1]."</B><EM>(".$sp[2].")</EM>";
		$nomer.="<TD class=dtime>".FullData($sp[0],1,1);
		$nomer.="<TD align=right>".round($sp[3],2);
		if($dopVal) $nomer.="<TD class=dop>".$dopVal[$sp[$p]];
		}
	}
$nomer.="</TABLE>";

include('incHeader.php');
?>
<SCRIPT type="text/javascript">
$(document).ready(function(){
	$("#comm").vkComment({
		table_name:'zayav',
		table_id:<?php echo $zayav->id; ?>
		});

	VK.callMethod('setLocation','zayavView_<?php echo $zayav->id; ?>');
	});

function zayavDel(ID)
	{
	dialogShow({
		width:250,
		top:100,
		head:'Удаление',
		butSubmit:'Удалить',
		content:"<CENTER>Подтвердите удаление заявки.</CENTER>",
		submit:function(){
			$("#butDialog").butProcess();
			$.getJSON("/gazeta/zayav/AjaxZayavDel.php?"+$("#VALUES").val()+"&id="+ID,function(res){
				location.href="/index.php?"+$("#VALUES").val()+"&my_page=zayav";
				});
			}
		});
	}

// НАЧИСЛЕНИЕ
function accrualAdd()
	{
	var HTML="<TABLE cellpadding=0 cellspacing=10>";
	HTML+="<TR><TD class=tdAbout>Сумма:<TD><INPUT type=text id=summa style=width:50px; maxlength=8>";
	HTML+="<TR><TD class=tdAbout>Примечание:<TD id=pn><INPUT type=text id=prim style=width:210px; maxlength=250>";
	HTML+="</TABLE>";
	dialogShow({
		width:350,
		top:100,
		head:'Начисление за дополнительные услуги',
		content:HTML,
		submit:function(){
			var SUM=$("#summa").val();
			var reg=/^[0-9.]+$/;
			if(!reg.exec(SUM)) { $("#pn").alertShow({txt:"<SPAN class=red>Не корректно введена сумма.<BR>Используйте цифры и точку.</SPAN>",top:2,left:0}); $("#summa").focus(); }
			else
				{
				$("#butDialog").butProcess();
				$.post("/gazeta/zayav/AjaxAccrualAdd.php?"+$("#VALUES").val(),{zayav_id:<?php echo $zayav->id; ?>,summa:SUM,prim:$("#prim").val()},function(){
					location.href="/index.php?"+$("#VALUES").val()+"&my_page=zayavView&id=<?php echo $zayav->id?>&msg=acadd";
					});
				}
			},
		focus:'#summa'
		});
	}

// УДАЛЕНИЕ НАЧИСЛЕНИЕ
function accrualDel(ID)
	{
	$("#acc"+ID+" .del").html("<IMG src=/img/upload.gif>");
	$.getJSON("/gazeta/zayav/AjaxAccrualDel.php?"+$("#VALUES").val()+"&id="+ID,function(){ $("#acc"+ID).hide(); });
	}
</SCRIPT>
<?php
$count=$VK->QRow("select count(id) from gazeta_nomer_pub where general_nomer<".$gnMin." and zayav_id=".$zayav->id);
if($count>0) $zayavDel="<A href='".$URL."&my_page=zayavAdd&zayav_dub=".$zayav->id."' class=fr>Дублировать заявку</A>"; else $zayavDel="<A href='javascript:' class=fr onclick=zayavDel(".$zayav->id.");>Удалить заявку</A>";

$spisok=$VK->QueryObjectArray("select * from accrual where zayav_id=".$zayav->id." order by id");
if(count($spisok)>0)
	{
	$accrual="<DIV class=headBlue>Начисления</DIV>";
	$accrual.="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok id=accrual>";
	$accrual.="<TR><TH>Сумма<TH>Примечание<TH>Дата<TH>";
	foreach($spisok as $sp)
		$accrual.="<TR id=acc".$sp->id."><TD class=sum><B>".round($sp->summa,2)."</B><TD>".$sp->prim."<TD class=data>".FullData($sp->dtime_add)."<TD class=del><DIV class=img_del onclick=accrualDel(".$sp->id.");></DIV>";
	$accrual.="</TABLE>";
	}

$mLink2='Sel'; include 'gazeta/mainLinks.php';
$dLink1='Sel'; include 'gazeta/zayav/dopLinks.php';

switch($_GET['msg'])
	{
	case 'zedit': $msg="Данные изменены!"; break;
	case 'acadd': $msg="Начисление успешно произведено!"; break;
	}

echo "<INPUT type=hidden id=msg value='".$msg."'><DIV class=zayavView>";
switch($zayav->category)
	{
	case 1: include 'gazeta/zayav/zayavViewOb.php'; break;
	case 2: include 'gazeta/zayav/zayavViewRek.php'; break;
	default: include 'gazeta/zayav/zayavViewPozSt.php'; break;
	}
echo "</DIV>";

include('incFooter.php');
?>
