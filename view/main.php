<?php
// Заголовок Header
function _header($vku)
{
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
<HEAD>
<meta http-equiv="content-type" content="text/html; charset=windows-1251">
<?=(SA==VIEWER_ID)?'<SCRIPT type="text/javascript" src="http://nyandoma.ru/js/errors.js?'.JS_VERSION.'"></SCRIPT>':''?>
<SCRIPT type="text/javascript" src="/js/jquery-1.9.1.min.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/js/xd_connection.js"></SCRIPT>
<SCRIPT type="text/javascript" src="/js/global.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/js/G_values.js?<?=G_VALUES_VERSION?>"></SCRIPT>
<LINK href="/css/global.css?<?=CSS_VERSION?>" rel="stylesheet" type="text/css">
<TITLE> Приложение КупецЪ </TITLE>
</HEAD>
<BODY>
<SCRIPT type="text/javascript">
<?=(DOMAIN == 'kupez')?'for(var i in VK)if(typeof VK[i]=="function")VK[i]=function(){return false;};':''?>
G.domen = "<?=DOMAIN?>";
G.values = "<?=VALUES?>";
G.url = "<?=URL?>";
G.vk = {
    viewer_id:<?=$vku['viewer_id']?>,
    first_name:"<?=$vku['first_name']?>",
    last_name:"<?=$vku['last_name']?>",
    city:"<?=$vku['city']?>"
};
</SCRIPT>
<DIV id=frameBody>
<?
} // end of _header()

// Завершение страницы Footer
function _footer()
{
    if(SA == VIEWER_ID) {
?>      <DIV id=admin>
            <A href='<?=URL?>'>Admin</A> ::
            <A id=script_style>Стили и скрипты (<?=JS_VERSION?>)</A> ::
            php <?=(round(microtime(true) - TIME, 3))?> ::
            js <span id=js_time></span>
        </DIV>
        <SCRIPT type='text/javascript'>
            $('#script_style').click(function () {
                $.getJSON('/superadmin/AjaxScriptStyleUp.php?' + G.values, function () { location.reload(); });
            });
            $("#js_time").html(((new Date()).getTime() - G.T)/1000);
        </SCRIPT>
<?php } ?>
    </DIV>
    <SCRIPT type="text/javascript">
        //VK.init(frameBodyHeightSet);
        VK.callMethod("setLocation","");
        VK.callMethod('scrollSubscribe');
        VK.addCallback('onScroll',function(top){ vkScroll = top; });
    </SCRIPT>
    </BODY></HTML>
<?php
}  // end of _footer()

// Проверка пользователя на наличие в базе. Также обновление при первом входе в Контакт
function vkUserCheck($vku, $update = false)
{
    if ($update or !isset($vku['viewer_id'])) {
        require_once('include/vkapi.class.php');
        $VKAPI = new vkapi(API_ID, API_SECRET);
        $res = $VKAPI->api('users.get',array('uids' => VIEWER_ID, 'fields' => 'photo,sex,country,city'));
        $vku = array(
            'viewer_id' => VIEWER_ID,
            'first_name' => win1251($res['response'][0]['first_name']),
            'last_name' => win1251($res['response'][0]['last_name']),
            'sex' => $res['response'][0]['sex'],
            'photo' => $res['response'][0]['photo'],
            'country' => isset($res['response'][0]['country']) ? $res['response'][0]['country'] : 0,
            'city' => isset($res['response'][0]['city']) ? $res['response'][0]['city'] : 0,
            'menu_left_set' => 0,
            'enter_last' => curTime()
        );
        // установил ли приложение
        $app = $VKAPI->api('isAppUser',array('uid'=>VIEWER_ID));
        $vku['app_setup'] = $app['response'];
        // поместил ли в левое меню
        $mls = $VKAPI->api('getUserSettings',array('uid'=>VIEWER_ID));
        $vku['menu_left_set'] = ($mls['response']&256) > 0 ? 1 : 0;
        global $VK;
        $VK->Query('INSERT INTO `vk_user` (
                    `viewer_id`,
                    `first_name`,
                    `last_name`,
                    `sex`,
                    `photo`,
                    `app_setup`,
                    `menu_left_set`,
                    `country`,
                    `city`,
                    `enter_last`
                    ) values (
                    '.VIEWER_ID.',
                    "'.$vku['first_name'].'",
                    "'.$vku['last_name'].'",
                    '.$vku['sex'].',
                    "'.$vku['photo'].'",
                    '.$vku['app_setup'].',
                    '.$vku['menu_left_set'].',
                    '.$vku['country'].',
                    '.$vku['city'].',
                    current_timestamp)
                    ON DUPLICATE KEY UPDATE
                    `first_name`="'.$vku['first_name'].'",
                    `last_name`="'.$vku['last_name'].'",
                    `sex`='.$vku['sex'].',
                    `photo`="'.$vku['photo'].'",
                    `app_setup`='.$vku['app_setup'].',
                    `menu_left_set`='.$vku['menu_left_set'].',
                    `country`='.$vku['country'].',
                    `city`='.$vku['city'].',
                    `enter_last`=current_timestamp
                    ');

        // сброс счётчика объявлений
        if($vku['menu_left_set'] == 1) {
            $VKAPI->api('secure.setCounter', array('counter'=>0, 'uid'=>VIEWER_ID, 'timestamp'=>time(), 'random'=>rand(1,1000)));
        }
        // счётчик посетителей
        $id = $VK->QRow('SELECT `id` FROM `visit` WHERE `viewer_id`='.VIEWER_ID.' AND `dtime_add`>="'.strftime("%Y-%m-%d").' 00:00:00" LIMIT 1');
        $VK->Query('INSERT INTO `visit` (`id`,`viewer_id`)
                                 VALUES ('.($id ? $id : 0).','.VIEWER_ID.')
                                 ON DUPLICATE KEY UPDATE `count_day`=`count_day`+1,`dtime_add`=current_timestamp');
        $VK->Query('UPDATE `vk_user` SET
                           `count_day`='.($id ? '`count_day`+1' : 1).',
                           `enter_last`=current_timestamp where viewer_id='.VIEWER_ID);
    }
    return $vku;
}
?>