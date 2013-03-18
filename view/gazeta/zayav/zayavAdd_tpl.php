<?php
/*
// ВНЕСЕНИЕ ПЛАТЕЖЕЙ ВСЕМ ЗАЯВКАМ, У КОТОРЫХ СУММА > 0
$spisok=$VK->QueryObjectArray("select * from zayav where summa>0 order by id");
foreach($spisok as $sp)
	$VK->Query("insert into oplata (zayav_id,summa,tip,viewer_id_add,dtime_add) values (".$sp->id.",'".$sp->summa."',1,".$sp->viewer_id_add.",'".$sp->dtime_add."')");

// УСТАНОВКА ВСЕМ ЗАЯВКАМ ПОСЛЕДНИЙ АКТИВНЫЙ ДЕНЬ
if($_GET['viewer_id']==982006)
	{
	$gnDay=$VK->QueryPtPArray("select general_nomer,day_end from gazeta_nomer order by id");
	$spisok=$VK->QueryObjectArray("select id from zayav order by id");
	foreach($spisok as $sp)
		{
		$gnLast=$VK->QRow("select general_nomer from gazeta_nomer_pub where zayav_id=".$sp->id." order by general_nomer desc limit 1");
		if($gnDay[$gnLast]) $VK->Query("update zayav set active_day='".$gnDay[$gnLast]."' where id=".$sp->id);
		echo $sp->id." = ".$gnDay[$gnLast]."<BR>";
		}
	}

// УСТАНОВКА ПАРАМЕТРА dop ВСЕМ ЗАЯВКАМ
$spisok=$VK->QueryObjectArray("select id from zayav where category=1 order by id limit 0,1000");
foreach($spisok as $sp)
	{
	$dop=$VK->QRow("select ob_dop_id from gazeta_nomer_pub where zayav_id=".$sp->id." order by id desc limit 1");
	if($dop>0)
		{
		switch($dop)
			{
			case 1: $dop='ramka'; break;
			case 2: $dop='black'; break;
			case 3: $dop='bold'; break;
			}
		$VK->Query("update zayav set dop='".$dop."' where id=".$sp->id);
		}
	echo $sp->id." - ".$dop."<BR>";
	}
*/









if($_GET['id']) $_GET['client_id']=$_GET['id'];

$txtLen=$VK->QueryObjectOne("select txt_len_first,txt_len_next,txt_cena_first,txt_cena_next from setup_global limit 1");

if($_POST['zayavAdd'])
	{
	if(!$_POST['client_id']) $_POST['client_id']=0;
	if(!$_POST['rubrika']) $_POST['rubrika']=0;
	if(!$_POST['podrubrika']) $_POST['podrubrika']=0;
	if(!$_POST['skidka']) $_POST['skidka']=0;
	$skidka=0; $skRazmer=0;
	if($_POST['skidka']>0)
		{
		$skidka=$skRazmer/100;
		$skRazmer=$VK->QRow("select razmer from setup_skidka where id=".$_POST['skidka']);
		}
	$idLast=$VK->Query("insert into zayav (
client_id,
category,

rubrika,
podrubrika,
txt,
telefon,
adres,

size_x,
size_y,

summa,
summa_manual,
skidka_id,
skidka_razmer,
skidka_sum,
file,
whence,
viewer_id_add
) values (
".$_POST['client_id'].",
".$_POST['category'].",
".$_POST['rubrika'].",
".$_POST['podrubrika'].",
'".textFormat($_POST['txt'])."',
'".$_POST['telefon']."',
'".$_POST['adres']."',

'".($_POST['size_x']?$_POST['size_x']:0)."',
'".($_POST['size_y']?$_POST['size_y']:0)."',

'".$_POST['summa']."',
".$_POST['summa_manual'].",
".$_POST['skidka'].",
".$skRazmer.",
'".$_POST['skidka_sum']."',
'".$_POST['file']."',
'kupez',
".$_GET['viewer_id'].")");
	
	$gnInputArr=explode(',',$_POST['gn_input']);
	switch($_POST['category'])
		{
		case 1:
			$dop=0;
			foreach($gnInputArr as $sp)
				{
				$gnpol=explode(':',$sp);
				$VK->Query("insert into gazeta_nomer_pub (general_nomer,ob_dop_id,zayav_id,summa,viewer_id_add) values (".$gnpol[0].",".$gnpol[1].",".$idLast.",'".$gnpol[2]."',".$_GET['viewer_id'].")");
				if($gnpol[1]>0) $dop=$gnpol[1];
				}
			if($dop>0)
				{
				switch($dop)
					{
					case 1: $dop='ramka'; break;
					case 2: $dop='black'; break;
					case 3: $dop='bold'; break;
					}
				$VK->Query("update zayav set dop='".$dop."' where id=".$idLast);
				}
			break;
		case 2:
			foreach($gnInputArr as $sp)
				{
				$gnpol=explode(':',$sp);
				$VK->Query("insert into gazeta_nomer_pub (general_nomer,polosa_id,zayav_id,summa,viewer_id_add) values (".$gnpol[0].",".$gnpol[1].",".$idLast.",'".$gnpol[2]."',".$_GET['viewer_id'].")");
				}
			break;
		default:
			foreach($gnInputArr as $sp)
				{
				$gnpol=explode(':',$sp);
				$VK->Query("insert into gazeta_nomer_pub (general_nomer,zayav_id,summa,viewer_id_add) values (".$gnpol[0].",".$idLast.",'".$gnpol[2]."',".$_GET['viewer_id'].")");
				}
			break;
		}


	if($_POST['oplata']==1) $VK->Query("insert into oplata (client_id,zayav_id,summa,tip,viewer_id_add) values (".$_POST['client_id'].",".$idLast.",'".$_POST['summa']."',1,".$_GET['viewer_id'].")");

	if($_POST['client_id']>0)
		{
		$count=$VK->QRow("select count(id) from zayav where client_id=".$_POST['client_id']);
		$VK->Query("update client set zayav_count=".$count." where id=".$_POST['client_id']);
		setClientBalans($_POST['client_id']);
		}

	if($_POST['note']) $VK->Query("insert into vk_comment (table_name,table_id,txt,viewer_id_add) values ('zayav',".$idLast.",'".textFormat($_POST['note'])."',".$_GET['viewer_id'].")");

	$day_active=$VK->QRow("select day_end from gazeta_nomer where general_nomer=".$gnpol[0]." limit 1");
	$VK->Query("update zayav set active_day='".$day_active."' where id=".$idLast);

	header("Location:".$URL."&my_page=zayavView&id=".$idLast);
	}

include('incHeader.php');

if(preg_match("|^[\d]+$|",$_GET['zayav_dub'])) $dub=$VK->QueryObjectOne("select * from zayav where id=".$_GET['zayav_dub']);
?>
<SCRIPT type="text/javascript" src="/gazeta/zayav/zayavAEV.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/clientAdd/clientAdd.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript">
var skidkaSpisok=<?php echo vkSelGetJson("select id,concat(razmer,'%') from setup_skidka order by razmer"); ?>;
$(document).ready(function(){
	$("#client_id").clientSel({func:function(res){
			var DD=$("#vkSel_client_id .vkSelRes DD");
			var LEN=DD.length;
			for(var n=0;n<LEN;n++)
				if(DD.eq(n).attr('val')==res)
					$("#telefon").val(DD.eq(n).find('SPAN:first').html());
			}
		});

	$("#category").vkSel({
		width:120,
		spisok:zayavCategoryVk,
		func:setZayav
		});

	$("#summa").keyup(function(){
		var reg=/^[0-9.]+$/;
		if(!reg.exec($(this).val()))
			$("#zMsg").alertShow({txt:"<DIV class=red>Не корректно введено сумма.<BR>Используйте цифры и точку для дроби.</DIV>",top:-175,left:200});
		else 
			switch($("#category").val())
				{
				case '1': calcSummaOb(); break;
				case '2': calcSummaRek(); break;
				default: calcSummaPozSt(); break;
				}
		});

	$("#summa_manual").myCheck({func:function(ID){
		if($("#"+ID).val()==1)
			$("#summa").css('background-color','#FF8').removeAttr('readonly').focus();
		else
			{
			$("#summa").css('background-color','#FFF').attr('readonly',true);
			switch($("#category").val())
				{
				case '1': calcSummaOb(); break;
				case '2': calcSummaRek(); break;
				}
			}
		}});

	$("#note").autosize({callback:frameBodyHeightSet});

	$("#oplata").myRadio({spisok:[{uid:1,title:'да'},{uid:0,title:'нет'}]});

	setZayav();

	VK.callMethod('setLocation','zayavAdd_<?php echo $_GET['client_id']; ?>');
	});

function zayavAddGo()
	{
	var MSG='',GO=1;

	switch($("#category").val())
		{
		case '1':
			if($("#rubrika").val()==0) { MSG="Не указана рубрика"; GO=0; }
			else
				if(!$("#txt").val()) { MSG="Введите текст объявления"; GO=0; }
				else
					if(!$("#telefon").val() && !$("#adres").val()) { MSG="Укажите контактный телефон или адрес клиента"; GO=0; }
					else
						if(!$("#gn_input").val()) { MSG="Необходимо выбрать минимум один номер выпуска"; GO=0; }
			break;
				
		case '2':
			if(document.FormZayav.client_id.value==0) { MSG="Не выбран клиент"; GO=0; }
			else
				if(!$("#kv_sm").val()) { MSG="Не указан размер изображения"; GO=0; }
				else
					{
					var GNI=$("#gn_input").val();
					if(GNI)
						{
						var arr=GNI.split(/,/);
						for(var n=0;n<arr.length;n++)
							{
							var gnArr=arr[n].split(/:/);
							if(gnArr[1]==0) GO=0;
							}
						if(GO==0) MSG="Необходимо указать полосу у всех выбранных номеров";
						}
					else { MSG="Необходимо выбрать минимум один номер выпуска"; GO=0; }
					}
			break;

		default:
			if(document.FormZayav.client_id.value==0) { MSG="Не выбран клиент"; GO=0; }
			else
				if(!$("#gn_input").val()) { MSG="Необходимо выбрать минимум один номер выпуска"; GO=0; }
			break;
		}

	if(GO==0) $("#zMsg").alertShow({txt:"<DIV class=red>"+MSG+"</DIV>",top:-43,left:200});
	else
		{
		var reg=/^[0-9.]+$/;
		if(!reg.exec(document.FormZayav.summa.value)) $("#zMsg").alertShow({txt:"<DIV class=red>Не корректно введена сумма.<BR>Используйте цифры и точку для дроби.</DIV>",top:-56,left:200});
		else
			{
			if(document.FormZayav.oplata.value.length==0) $("#zMsg").alertShow({txt:"<DIV class=red>Укажите, оплачена заявка или нет.</DIV>",top:-43,left:200});
			else
				{
				document.FormZayav.action="<?php echo $URL; ?>&my_page=zayavAdd";
				document.FormZayav.enctype='';
				document.FormZayav.target='';
				document.FormZayav.submit();
				}
			}
		}
	}
</SCRIPT>

<?php $mLink2='Sel'; include 'gazeta/mainLinks.php'; ?>

<INPUT type=hidden id=dub_rubrika value=<?php echo $dub->rubrika?$dub->rubrika:0; ?>>
<INPUT type=hidden id=dub_podrubrika value=<?php echo $dub->podrubrika?$dub->podrubrika:0; ?>>
<INPUT type=hidden id=dub_txt value="<?php echo $dub->txt; ?>">
<INPUT type=hidden id=dub_telefon value="<?php echo $dub->telefon; ?>">
<INPUT type=hidden id=dub_adres value="<?php echo $dub->adres; ?>">
<INPUT type=hidden id=dub_size_x value="<?php echo round($dub->size_x,1); ?>">
<INPUT type=hidden id=dub_size_y value="<?php echo round($dub->size_y,1); ?>">
<INPUT type=hidden id=dub_kv_sm value="<?php echo round($dub->size_x*$dub->size_y,2); ?>">

<INPUT type=hidden id=txt_len_first value="<?php echo $txtLen->txt_len_first; ?>">
<INPUT type=hidden id=txt_cena_first value="<?php echo $txtLen->txt_cena_first; ?>">
<INPUT type=hidden id=txt_len_next value="<?php echo $txtLen->txt_len_next; ?>">
<INPUT type=hidden id=txt_cena_next value="<?php echo $txtLen->txt_cena_next; ?>">

<DIV id=zayavAdd>
	<DIV class=headName>Внесение новой заявки</DIV>

	<FORM method=post action='' name=FormZayav>
	<TABLE cellpadding=0 cellspacing=8>
	<TR><TD class=tdAbout>Клиент:							<TD><INPUT TYPE=hidden id=client_id name=client_id value="<?php echo  $dub->client_id?$dub->client_id:$_GET['client_id']; ?>">
	<TR><TD class=tdAbout>Категория:					<TD><INPUT TYPE=hidden NAME=category id=category value=<?php echo $dub->category?$dub->category:1; ?>>
	</TABLE>
	
	<DIV id=content></DIV>

	<TABLE cellpadding=0 cellspacing=8><TR><TD class=tdAbout>Номера выпуска:<TD></TABLE>
	<DIV id=nomer></DIV>

	<DIV id=skidkaContent></DIV>

	<TABLE cellpadding=0 cellspacing=8 id=manual_tab>
		<TR><TD class=tdAbout>Указать стоимость вручную:<TD><INPUT TYPE=hidden id=summa_manual name=summa_manual value=0>
	</TABLE>

	<TABLE cellpadding=0 cellspacing=8>
	<TR><TD class=tdAbout>Итоговая стоимость:	<TD><INPUT TYPE=text NAME=summa id=summa readonly value=0> руб.
																							<SPAN id=sumSkidka>Сумма скидки: <B></B> руб.</SPAN><INPUT TYPE=hidden NAME=skidka_sum id=skidka_sum value=0>
	<TR><TD class=tdAbout>Заявка оплачена?:			<TD><INPUT TYPE=hidden name=oplata id=oplata>
	<TR><TD class=tdAbout valign=top>Заметка:<TD><TEXTAREA name=note id=note></TEXTAREA>
	</TABLE>


	<input type=hidden name=zayavAdd value=1>
	</FORM>
	<DIV id=zMsg></DIV>
	<DIV class=vkButton><BUTTON onclick=zayavAddGo();>Внести</BUTTON></DIV><DIV class=vkCancel><BUTTON onclick="location.href='<?php echo $URL; ?>&my_page=zayav'">Отмена</BUTTON></DIV>
</DIV>





<?php include('incFooter.php'); ?>


