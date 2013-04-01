$("#search").topSearch({
    txt:'Поиск',
    enter:1,
    func:function (val) {
        G.spisok.print({input:encodeURIComponent(val)});
    }
});

$("#findRadio").vkRadio({
    spisok:[
        {uid:1,title:'Все посетители'},
        {uid:2,title:'Заходили сегодня'},
        {uid:3,title:'В этом месяце'},
        {uid:4,title:'Размещали объявления'},
        {uid:5,title:'Установили приложение'},
        {uid:6,title:'Добавили в левое меню'}],
    bottom:7,
    func:function (id) {
        G.spisok.print({radio:id})
    }
});




G.spisok.unit = function (sp) {
    return "" +

    "<TABLE cellspacing=0 cellpadding=0>" +
        "<TR><TD class=img><A href='http://vk.com/id" + sp.viewer_id + "' target=_vk><IMG src=" + sp.photo + "></A>" +
            "<TD valign=top>" +
                "<DIV class=time>" + (sp.count_day > 1 ? "<SPAN>" + sp.count_day + "x</SPAN>" : '') + sp.time + "</DIV>" +
                "<A href='http://vk.com/id"+sp.viewer_id+"' target=_vk><B>" + sp.name + "</B></A>" +
                "<DIV class=place></DIV>" +
                (sp.ob_count > 0 ? "<DIV class=ob><A href='" + G.url + "&p=admin&d=ob&viewer_id_add=" + sp.viewer_id + "'>Объявлений: " + sp.ob_count + "</A></DIV>" : '') +
    "</TABLE>";
}

G.spisok.create({
    url:"/view/admin/visit/AjaxVisitSpisok.php",
    view:$("#left"),
    limit:50,
    result_view:$("#findResult"),
    result:"Показан$show $count посетител$user",
    ends:{'$show':['', 'о'], '$user':['ь', 'я', 'ей']},
    nofind:"Посетителей не найдено.",
    imgup:$("#findResult"),
    //a:1,
    values:{
        input:'',
        radio:2
    },
    callback:function () {
        G.spisok.result_view.append(" " + G.spisok.data.time);
    }
});


