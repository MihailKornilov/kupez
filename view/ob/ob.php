<?php
// Список объявлений
function obSpisok()
{
    global $VK, $vku;
    if (SA == VIEWER_ID) {
        $today = $VK->QRow("SELECT COUNT(`id`) FROM `visit` WHERE `dtime_add`>='".strftime("%Y-%m-%d",time())." 00:00:00'");
        $visit = "<BR><BR><BR><DIV class=findName>Посетители</DIV>"
                ."<A href='".URL."&p=ob&o=visit'>Сегодня: ".$today."</A>"
                ."<BR><BR><A id=cache_new>Очистить кэш</A>";
    }
    // Выборка количества объявлений каждой рубрике
    $rubrikaCount = $VK->ptpJson('SELECT
                                    `rubrika` AS `id`,
                                    COUNT(`id`) AS `ob_count`
                                  FROM `zayav` WHERE
                                    `status`=1 AND
                                    `category`=1 AND
                                    `active_day`>=DATE_FORMAT(NOW(), "%Y-%m-%d") GROUP BY `rubrika`');
    // Список городов, для которых есть объявления
    $cities = $VK->vkSelJson('SELECT DISTINCT(`city_id`),`city_name` FROM `zayav`
                              WHERE
                                `city_id`>0 AND
                                `city_name`!="" AND
                                `status`=1 AND
                                `category`=1 AND
                                `active_day`>=DATE_FORMAT(NOW(), "%Y-%m-%d") ORDER BY `city_name`');
?>
<DIV id=obSpisok>
  <TABLE cellpadding=0 cellspacing=0 id=tab1>
  <TR><TD><DIV id=vkFind></DIV>
      <TH><DIV class=vkButton><BUTTON onclick="location.href='<?=URL.'&p=ob&d=create'?>'";>Разместить объявление</BUTTON></DIV>
  </TABLE>
  <DIV id=findResult>&nbsp;</DIV>
  <TABLE cellpadding=0 cellspacing=0 width=100%>
    <TR>
    <TD id=spisok>&nbsp;
    <TD id=cond>
        <DIV class=findName>Регион</DIV><INPUT TYPE=hidden id=countries value=1><INPUT TYPE=hidden id=cities value=0><BR>

        <DIV class=findName>Рубрики</DIV><DIV id=rubrika></DIV>
        <DIV id=podrubrika></DIV>

        <DIV class=findName>Дополнительно</DIV>
        <INPUT TYPE=hidden id=foto_only value=0>
        <?=@$visit?>
  </TABLE>
</DIV>
    <?php
/*
    if($vkUser['app_setup'] == 0) {
        if(!$VK->QRow("select id from hint_no_show where hint_id=1 and viewer_id=".$_GET['viewer_id'])) {
            echo "
      $('#vk-ob').alertShow({
        otstup:160,
        delayShow:10000,
        delayHide:15000,
        ugol:'top',
        left:400,
        txt:hintTxt('".$VK->QRow("select txt from hint where id=1")."',1)
      });
      ";
        }
    } else {
        if($vkUser['menu_left_set'] == 0) {
            if(!$VK->QRow("select id from hint_no_show where hint_id=2 and viewer_id=".$_GET['viewer_id'])) {
                echo "
        $('#vk-ob').alertShow({
          otstup:160,
          delayShow:10000,
          delayHide:15000,
          ugol:'top',
          left:405,
          txt:hintTxt('".$VK->QRow("select txt from hint where id=2")."',2)
        });
        ";
            }
        }
    }*/
    ?>
<SCRIPT type="text/javascript">
var spisok = {
    enter:"<?=$vku['gazeta_worker'] == 1 ? "<A href='".URL."' onclick=setCookie('enter',1); class=enter>Войти в программу</A>" : ''?>",
    rubCount:<?=$rubrikaCount?>,
    cities:<?=$cities?>
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/spisok/obSpisok.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of obSpisok()

// Размещение объявления
function obCreate() {
?>
<DIV id=vk-create>
  <DIV class=headName>Создание нового объявления</DIV>
  <DIV class=info>
    <P>Пожалуйста, заполните все необходимые поля. После размещения объявление сразу становится доступно для других пользователей ВКонтакте.
    <P>Сотрудники приложения Купецъ оставляют за собой право изменять или запретить к показу объявление, если оно нарушает <A>правила</A>.
    <P>Объявление будет размещено сроком на 1 месяц, в дальнейшем Вы сможете продлить этот срок.
  </DIV>

  <TABLE cellpadding=0 cellspacing=10 class=tab>
  <TR><TD class=tdAbout>Рубрика:               <TD><INPUT TYPE=hidden id=rubrika value=0><TD><INPUT TYPE=hidden id=podrubrika value=0>
  <TR><TD class=tdAbout valign=top>Текст:      <TD colspan=2><TEXTAREA id=txt></TEXTAREA>
  <TR><TD class='tdAbout top5' valign=top>Загрузить изображение:<TD colspan=2 id=ms_images><INPUT TYPE=hidden id=images>
  <TR><TD class=tdAbout>Контактные телефоны:   <TD colspan=2><INPUT TYPE=text id=telefon maxlength=200>
  <TR><TD class='tdAbout top5' valign=top>Регион:    <TD colspan=2 id=ms_adres><INPUT TYPE=hidden id=countries value=1><INPUT TYPE=hidden id=cities value=0>
  <TR><TD class=tdAbout>Показывать имя из VK:  <TD colspan=2><INPUT TYPE=hidden id=viewer_id_show value=0>
  <TR><TD class=tdAbout>Платные сервисы:       <TD colspan=2><INPUT TYPE=hidden id=pay_service value=0>
  </TABLE>

  <TABLE cellpadding=0 cellspacing=8  id=payContent>
    <TR><TD class=tdAbout><TD><INPUT TYPE=hidden id=dop value=0>
    <TR><TD class=tdAbout>Поднять объявление:<TD valign=bottom><INPUT TYPE=hidden id=top><SPAN id=top_week>на <EM class=bok>&nbsp;</EM><EM class=a>-</EM><EM class=inp>1</EM><EM class=a>+</EM><EM class=bok>&nbsp;</EM> недел<EM class=end></EM></SPAN>
   </TABLE>

  <INPUT TYPE=hidden id=file>

  <DIV id=zMsg></DIV>
  <DIV id=butts>
    <DIV class=vkButton><BUTTON>Разместить объявление<SPAN></SPAN></BUTTON></DIV>
    <DIV class=vkCancel><BUTTON>Отмена</BUTTON></DIV>
  </DIV>

<DIV id=callbacks></DIV>

  <DIV class=headName>Предосмотр объявления</DIV>
  <DIV id=obSpisok></DIV>
</DIV>

<SCRIPT type="text/javascript">
        create = {
        back:"<?php echo isset($_GET['back']) ? $_GET['back'] : 'spisok'; ?>", // на какую страницу возвращаться при отмене
        order:{
            id:0,
            votes:0
        },
        top_week:1
};
</SCRIPT>
<SCRIPT type="text/javascript" src="/include/upload/upload.js?<?=JS_VERSION?>"></SCRIPT>
<SCRIPT type="text/javascript" src="/view/ob/create/create.js?<?=JS_VERSION?>"></SCRIPT>
<?php
} // end of obCreate()
?>