<?php
// Заголовок Header
function _header($viewer)
{
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
<SCRIPT type="text/javascript" src="/include/jquery-1.7.1.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/jquery-ui-1.8.18.custom.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/xd_connection.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/globalScript.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/include/G_values.js?<?=G_VALUES_VERSION?>"></SCRIPT>
<LINK href="/include/globalStyle.css?<?=CSS_VERSION?>" rel="stylesheet" type="text/css">
<TITLE> Приложение 2881875 Газета Купецъ </TITLE>
</HEAD>
<BODY onclick=vkSelHide();>
<?=(SA == $_GET['viewer_id'])?'<SCRIPT type="text/javascript" src="http://nyandoma.ru/js/errors.js?0"></SCRIPT>':''?>
<SCRIPT type="text/javascript">
<?=($_SERVER['SERVER_NAME'] == 'kupez')?'for(var i in VK)if(typeof VK[i]=="function")VK[i]=function(){return false;};':''?>
G.domen = "<?=DOMAIN?>";
G.values = "<?=VALUES?>";
G.url = "<?=URL?>";
G.zayavMn = ['Объявления','Реклама','Поздравления','Статьи'];
G.vk = {
    viewer_id:<?=$viewer->viewer_id?>,
    first_name:"<?=$viewer->first_name?>",
    last_name:"<?=$viewer->last_name?>",
    city:"<?=$viewer->city?>"
};

var zayavCategory = ['Объявление','Реклама','Поздравление','Статья'];
var zayavCategoryVk = [
    {uid:1,title:'Объявление'},
    {uid:2,title:'Реклама'},
    {uid:3,title:'Поздравление'},
    {uid:4,title:'Статья'}
];
</SCRIPT>
<DIV id=frameBody>
<?
}

// Завершение страницы Footer
function _footer()
{
    if(SA == $_GET['viewer_id']) {
        $time = round(microtime(true) - TIME, 3);
?>
        <DIV id=admin>
            <A href=''>Admin</A> ::
            <A id=script_style>Стили и скрипты (<?=JS_VERSION?>)</A> ::
            php <?=$time?> ::
            js <span id=js_time></span>
        </DIV>
        <SCRIPT type='text/javascript'>
            $('#script_style').click(function () {
                $.getJSON('/superadmin/AjaxScriptStyleUp.php?' + G.values, function () { location.reload(); });
            });
            $("#js_time").html((new Date()).getTime() - G.js_time);
        </SCRIPT>
<?php
    }
?>
    </DIV>

    <SCRIPT type="text/javascript">
        VK.init(frameBodyHeightSet);
        VK.callMethod("setLocation","<?php echo $_GET['my_page'].(isset($_GET['id']) ? '_'.$_GET['id'] : '' ); ?>");
        VK.callMethod('scrollSubscribe');
        VK.addCallback('onScroll',function(top){ vkScroll = top; });
    </SCRIPT>

    </BODY></HTML>
<?php
}
?>