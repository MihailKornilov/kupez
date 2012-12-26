<?php
if($_GET['viewer_id'] == 142428863) header("location:".$URL."&my_page=vk-ob");

if(isset($_POST['vk-create'])) {
  $idLast = $VK->Query("insert into zayav (
category,
rubrika,
podrubrika,
txt,
telefon,
adres,
file,
viewer_id_add,
vk_viewer_id_show,
vk_srok,
vk_day_active
) values (
1,
".$_POST['rubrika'].",
".$_POST['podrubrika'].",
'".textFormat($_POST['txt'])."',
'".textFormat($_POST['telefon'])."',
'".textFormat($_POST['adres'])."',
'".$_POST['file']."',
".$_GET['viewer_id'].",
".$_POST['vk_viewer_id_show'].",
".$_POST['vk_srok'].",
date_add(current_timestamp,interval ".($_POST['vk_srok']*7)." day))");

  $ob_count = $VK->QRow("select count(id) from zayav where category=1 and vk_srok>0 and viewer_id_add=".$_GET['viewer_id']);
  $VK->Query("update vk_user set ob_count='".$ob_count."' where viewer_id=".$_GET['viewer_id']);

  rubrikaCountUpdate($_POST['rubrika']);

  header("location:".$URL."&my_page=vk-ob");
}

include('incHeader.php');
?>

<DIV id=vk-create>
  <DIV class=headName>Создание нового объявления</DIV>
  <DIV class=info>
    Пожалуйста, заполните все необходимые поля. После размещения объявление сразу становится доступно для других пользователей ВКонтакте.<BR>
    Сотрудники газеты Купецъ оставляют за собой право изменять или запретить к показу объявление<!-- , если оно нарушает <A href=''>правила</A> -->.
  </DIV>

  <FORM method=post action='/gazeta/zayav/fileUpload.php?<?php echo $VALUES; ?>' name=FormCreate enctype=multipart/form-data target=uploadFrame>
  <TABLE cellpadding=0 cellspacing=8 class=crTab>
  <TR><TD class=tdAbout>Рубрика:                   <TD><INPUT TYPE=hidden id=rubrika name=rubrika value=0><TD><INPUT TYPE=hidden NAME=podrubrika id=podrubrika value=0>
  <TR><TD class=tdAbout valign=top>Текст:      <TD colspan=2><TEXTAREA name=txt id=txt></TEXTAREA>
  <TR><TD class='tdAbout top5' valign=top>Загрузить изображение:<TD colspan=2 id=tdUpload>
  <TR><TD class=tdAbout>Контактные телефоны:  <TD colspan=2><INPUT TYPE=text NAME=telefon id=telefon maxlength=200>
  <TR><TD class=tdAbout>Адрес:                <TD colspan=2><INPUT TYPE=text NAME=adres id=adres maxlength=200>
  <TR><TD class=tdAbout>Показывать имя из VK:  <TD colspan=2><INPUT TYPE=hidden NAME=vk_viewer_id_show id=vk_viewer_id_show value=0>
  <TR><TD class=tdAbout>Срок:                  <TD colspan=2><INPUT TYPE=hidden NAME=vk_srok id=vk_srok value=1>
  </TABLE>
  <input type=hidden name=vk-create value=1>
  </FORM>

  <DIV id=zMsg></DIV>
  <DIV class=vkButton><BUTTON onclick=vkCreateGo();>Разместить объявление</BUTTON></DIV><DIV class=vkCancel><BUTTON onclick="location.href='<?php echo $URL."&my_page=".(isset($_GET['back']) ? $_GET['back'] : 'vk-ob'); ?>'">Отмена</BUTTON></DIV>

</DIV>

<SCRIPT type="text/javascript">
var rubrika = <?php echo xcache_get('rubrika'); ?>;
var podrubrika = <?php echo xcache_get('podRubrika'); ?>;
</SCRIPT>
<SCRIPT type="text/javascript" src="/vk/create/create.js?<?php echo $G->script_style; ?>"></SCRIPT>


<?php include('incFooter.php'); ?>



