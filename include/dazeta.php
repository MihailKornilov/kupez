<?php
// Основное горизонтальное меню
function main_links($g) {
    $name = array('Клиенты', 'Заявки', 'Отчёты', 'Настройки');
    $page = array('client',  'zayav',  'report', 'setup');

    $links = "<A style='float:right;margin:5px 5px 0px 15px;' onclick=\"setCookie('enter','0');\">Выход</A>";
    for ($n = 0; $n < count($page); $n++) {
        $links .=
            '<A HREF="'.URL.'&p=gazeta&g='.$page[$n].'" class="la'.($page[$n] == $g ? ' sel' : '').'">'.
                "<DIV class=l1></DIV>".
                "<DIV class=l2></DIV>".
                "<DIV class=l3>".$name[$n]."</DIV>".
            "</A>";
    }

    echo "<DIV id=main_links>".$links."</DIV>";
}
?>