// Показ изображения
function imageView() {
    G.fotoView({spisok:[{link:G.zayav.image}]});
};


if ($("#del").length > 0) {
    $("#del").vkHint({
        msg:'Чтобы удалить эту заявку<br>' +
            'привяжите её к клиенту.<br>' +
            'Таким образом все платежи<br>' +
            'этой заявки вернутся клиенту<br>' +
            'на баланс после удаления.',
        ugol:'top',
        indent:120,
        top:17,
        left:426
    });
}

if ($("#delete").length > 0) {
    $("#delete").click(function () {
        var dialog = $("#dialog_zayav").vkDialog({
            width:250,
            head:"Удаление заявки",
            butSubmit:"Удалить",
            content:"<CENTER><B>Подтвердите удаление заявки</B></CENTER>",
            submit:function () {
                dialog.process();
                var url = "&id=" + G.zayav.id +
                          "&category=" + G.zayav.category +
                          "&client_id=" + G.zayav.client_id;
                $.getJSON("/view/gazeta/zayav/view/AjaxZayavDel.php?" + G.values + url, function () {
                    location.href = G.url + "&p=gazeta&d=zayav";
                }, 'json');
            }
        }).o;
    });
}


