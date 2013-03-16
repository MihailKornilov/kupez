<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
<LINK href="/include/globalStyle.css?<?php echo $G->script_style; ?>" rel="stylesheet" type="text/css">
<SCRIPT type="text/javascript" src="/include/jquery-1.7.1.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/jquery-ui-1.8.18.custom.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/xd_connection.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/globalScript.js?<?php echo $G->script_style; ?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/G_values.js?<?php echo $G->g_values; ?>"></SCRIPT>
<TITLE> Приложение 2881875 Газета Купецъ </TITLE>
</HEAD>

<BODY onclick=vkSelHide();>
<?php if ($_GET['viewer_id'] == 982006) { echo "<SCRIPT type='text/javascript' src='http://nyandoma".($_SERVER["SERVER_NAME"] == 'kupez' ? '' : '.ru')."/js/errors.js?".$G->script_style."'></SCRIPT>"; } ?>
<SCRIPT type="text/javascript">
if (document.domain == 'kupez') {
  for(var i in VK) {
    if (typeof VK[i] == 'function') {
      VK[i] = function () { return false; };
    }
  }
}

G.domen = "<?php echo $DOMEN; ?>";
G.values = "<?php echo $VALUES; ?>";
G.zayavMn = ['Объявления','Реклама','Поздравления','Статьи'];
G.vk = {
  viewer_id:<?php echo $vkUser['viewer_id']; ?>,
  first_name:"<?php echo $vkUser['first_name']; ?>",
  last_name:"<?php echo $vkUser['last_name']; ?>",
  city:"<?php echo $vkUser['city']; ?>"
};

var zayavCategory = ['Объявление','Реклама','Поздравление','Статья'];
var zayavCategoryVk = [
  {uid:1,title:'Объявление'},
  {uid:2,title:'Реклама'},
  {uid:3,title:'Поздравление'},
  {uid:4,title:'Статья'}
];
</SCRIPT>

<INPUT type=hidden id=VALUES value="<?php echo $VALUES; ?>">
<DIV id=frameBody>
