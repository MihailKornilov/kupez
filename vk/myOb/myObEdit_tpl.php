<?php
if($_POST['vk-edit'])	{
	if($_POST['act']==1)
		$srokSet="vk_srok=".$_POST['vk_srok'].",vk_day_active=date_add(current_timestamp,interval ".($_POST['vk_srok']*7)." day),";
	else "vk_day_active='0000-00-00'";

	$VK->Query("update zayav set
rubrika=".$_POST['rubrika'].",
podrubrika=".$_POST['podrubrika'].",
txt='".textFormat($_POST['txt'])."',
telefon='".$_POST['telefon']."',
adres='".$_POST['adres']."',
file='".$_POST['file']."',
".$srokSet."
vk_viewer_id_show=".$_POST['vk_viewer_id_show']."
where id=".$_POST['vk-edit']);

  rubrikaCountUpdate($_POST['rubrika']);

	header("location:".$URL."&my_page=vk-myOb");
	}

$ob = $VK->QueryObjectOne("select * from zayav where category=1 and status=1 and id=".(preg_match("|^[\d]+$|",$_GET['id'])?$_GET['id']:0));
if(!$ob->id or $ob->viewer_id_add!=$_GET['viewer_id'])  header("Location: ". $URL."&my_page=vk-myOb");

$act=strtotime($ob->vk_day_active)-time()<0?0:1;

include('incHeader.php');
?>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
	$("#rubrika").vkSel({
		width:120,
		spisok:<?php echo vkSelGetJson("select id,name from setup_rubrika order by sort"); ?>,
		func:function(ID){
			$("#podrubrika").val(0);
			$("#vkSel_podrubrika").remove();
			if(ID>0) podrubGet();
			}
		});
	
	podrubGet();

	$("#txt").textareaResize({minH:50});

	if($("#file").val())
		filePrint($("#file").val());
	else tdUploadSet();

	$("#vk_viewer_id_show").myCheck();

	$("#vk_srok").vkSel({
		width:90,
		spisok:[{uid:1,title:'1 неделя'},{uid:2,title:'2 недели'},{uid:3,title:'3 недели'}]
		});

	if($("#act").val()==0) goAcrhiv();

	VK.callMethod('setLocation','vk-create');
	});



// ВЫВОД СПИСКА ПОДРУБРИК
function podrubGet()
	{
	$.getJSON("/gazeta/zayav/AjaxPodRubrikaGet.php?"+$("#VALUES").val()+"&rubrika_id="+$("#rubrika").val(),function(res){
		if(res!=null)
		$("#podrubrika").vkSel({
			width:200,
			title0:'Подрубрика не указана',
			msg:'Подрубрика не указана',
			spisok:res
			});
		});
	}



// ЗАГРУЗКА ФАЙЛА
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
	$("#file_name").after("<IMG src=/img/upload.gif class=upload><A href='javascript:' onclick=tdUploadSet();>отменить</A>");
	setCookie('upload','process');
	timer=setInterval("fileUploadStart();",500);
	document.FormCreate.submit();
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








function goAcrhiv()
	{
	$("#active").hide();
	$("#archiv").show();
	$("#act").val(0);
	}

function goActive()
	{
	$("#active").show();
	$("#archiv").hide();
	$("#act").val(1);
	}







function vkCreateGo()
	{
	var MSG='',GO=1;

	if($("#rubrika").val()==0) { MSG="Не указана рубрика"; GO=0; }
		else
			if(!$("#txt").val()) { MSG="Введите текст объявления"; GO=0; }
	
	
	if(GO==0) $("#zMsg").alertShow({txt:"<DIV class=red>"+MSG+"</DIV>",top:-43,left:220});
	else
		{
		document.FormCreate.action="<?php echo $URL; ?>&my_page=vk-myObEdit";
		document.FormCreate.enctype='';
		document.FormCreate.target='';
		document.FormCreate.submit();
		}	
	}
</SCRIPT>

<DIV id=vk-create>
	<DIV class=headName>Редактирование объявления</DIV>

	<FORM method=post action='/gazeta/zayav/fileUpload.php?<?php echo $VALUES; ?>' name=FormCreate enctype=multipart/form-data target=uploadFrame>
	<TABLE cellpadding=0 cellspacing=8 class=crTab>
	<TR><TD class=tdAbout>Рубрика:							<TD><INPUT TYPE=hidden id=rubrika name=rubrika value=<?php echo $ob->rubrika; ?>>
																						<TD><INPUT TYPE=hidden NAME=podrubrika id=podrubrika value=<?php echo $ob->podrubrika; ?>>
	<TR><TD class=tdAbout valign=top>Текст:				<TD colspan=2><TEXTAREA name=txt id=txt><?php echo textUnFormat($ob->txt); ?></TEXTAREA>
	<TR><TD class='tdAbout top5' valign=top>Загрузить изображение:<TD colspan=2 id=tdUpload>
	<TR><TD class=tdAbout>Контактные телефоны:	<TD colspan=2><INPUT TYPE=text NAME=telefon id=telefon maxlength=200 value='<?php echo $ob->telefon; ?>'>
	<TR><TD class=tdAbout>Адрес:								<TD colspan=2><INPUT TYPE=text NAME=adres id=adres maxlength=200 value='<?php echo $ob->adres; ?>'>
	<TR><TD class=tdAbout>Показывать имя из VK:	<TD colspan=2><INPUT TYPE=hidden NAME=vk_viewer_id_show id=vk_viewer_id_show value=<?php echo $ob->vk_viewer_id_show; ?>>
	<TR id=active><TD class=tdAbout>Срок:		<TD colspan=2><A href='javascript:' class=goarchiv onclick=goAcrhiv();>Отправить в архив</A><INPUT TYPE=hidden NAME=vk_srok id=vk_srok value=<?php echo $ob->vk_srok; ?>>
	<TR id=archiv><TD class=tdAbout>&nbsp;		<TD colspan=2><SPAN>Объявление находится в архиве.</SPAN><A href='javascript:' onclick=goActive();>Сделать активным</A>

	</TABLE>
	<INPUT TYPE=hidden NAME=file id=file value='<?php echo $ob->file; ?>'>
	<INPUT TYPE=hidden name=act id=act value=<?php echo $act; ?>>
	<INPUT TYPE=hidden name=vk-edit value=<?php echo $ob->id; ?>>
	</FORM>

	<DIV id=zMsg></DIV>
	<DIV class=vkButton><BUTTON onclick=vkCreateGo();>Сохранить</BUTTON></DIV><DIV class=vkCancel><BUTTON onclick="location.href='<?php echo $URL."&my_page=vk-myOb"; ?>'">Отмена</BUTTON></DIV>

</DIV>

<?php include('incFooter.php'); ?>



