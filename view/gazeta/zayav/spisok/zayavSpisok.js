G.category_spisok.unshift({uid:0, title:'Любая категория'});
G.category_spisok[1].title += "<div class=img_word></div>";
$("#category").infoLink({
    spisok:G.category_spisok,
    func:function (id) {
        G.spisok.print({category:id});
    }
}).infoLinkSet(G.zayav.category);

$("#category .img_word:first")
    .click(function () {
        var gn = $("#gazeta_nomer").val();
        if (gn == 0) {
            $(this).vkHint({
                msg:'<span class=red>Не выбран номер газеты.</span>',
                indent:'right',
                top:-74,
                left:-19,
                show:1,
                remove:1
            });
        } else location.href = "/view/gazeta/zayav/spisok/PrintWord.php?" + G.values + "&gn=" + gn;
    })
    .vkHint({
        width:145,
        msg:'<span style=color:#444;>Открыть список объявлений ' +
            'в газетном варианте в формате ' +
            'Microsoft Word.</span>',
        indent:'right',
        top:-110,
        left:-11
    });




$("#year").vkSel({
    width:147,
    title0:'Год не указан',
    spisok: G.zayav.years,
    func:function(year){
        $("#vkSel_gazeta_nomer").remove();
        $("#gazeta_nomer").val(0);
        if(year > 0) gazetaNomerGet(year);
        G.spisok.print({year:year, gazeta_nomer:0});
    }
});
    
gazetaNomerGet(G.zayav.year);

$("#fastFind").topSearch({
    txt:'Быстрый поиск...',
    enter:1,
    func:function(inp) {
        if(inp) $("#nofast").hide();
        else $("#nofast").show();
        G.spisok.print({input:encodeURIComponent(inp)});
    }
});


$("#no_public").myCheck({
    title:'Невыходившие заявки',
    bottom:20,
    func:function (id) {
        $("#public")[id == 0 ? 'show' : 'hide']();
        G.spisok.print({no_public:id});
    }
});
$("#check_no_public").vkHint({
    width:145,
    msg:'Заявки, которые не публиковались ни в одном номере газеты.',
    indent:60,
    top:-84,
    left:-61
});

// Вывод списка номеров за определённый год
function gazetaNomerGet(year) {
    $("#gazeta_nomer").vkSel({
        width:147,
        title0:'Номер не указан',
        spisok:G.zayav.gazeta_nomer_spisok[year],
        func:function (id) { G.spisok.print({gazeta_nomer:id}); }
    });
    $("#vkSel_gazeta_nomer").css('margin-top','4px');
}





G.spisok.unit = function (sp) {
    return "<H1><EM>" + sp.dtime + "</EM><A href='" + G.url + "&p=gazeta&d=zayav&d1=view&id="+sp.id+"'>" + G.category_ass[sp.category] + " №" + sp.id + "</A></H1>" +
        "<TABLE cellpadding=0 cellspacing=0>" +
        "<TR><TD valign=top>" +

            "<TABLE cellpadding=0 cellspacing=4>" +
            (sp.client_id ? "<TR><TD class=tdAbout>Клиент:<TD><A HREF='" + G.url + "&p=gazeta&d=client&d1=info&id=" + sp.client_id + "'>" + sp.client_fio + "</A>" : '') +
            (sp.category == 1 ?
                "<TR><TD class=tdAbout>Рубрика:<TD>" + G.rubrika_ass[sp.rubrika] + (sp.podrubrika > 0 ? "<SPAN class=ug>»</SPAN>" + G.podrubrika_ass[sp.podrubrika] : '') +
                "<TR><TD class=tdAbout valign=top>Текст:<TD><DIV class=txt>" + sp.txt + "</DIV>" : '') +

            (sp.ob_dop ? "<TR><TD class=tdAbout>Доп. параметр:<TD>" + sp.ob_dop : '') +
            (sp.category == 2 ? "<TR><TD class=tdAbout>Размер:<TD>" + sp.size_x + " x " + sp.size_y + " = <b>" + sp.kv_sm + '</b> см&sup2;' : '') +
            (sp.telefon ? "<TR><TD class=tdAbout>Телефон:<TD>" + sp.telefon : '') +
            (sp.adres ? "<TR><TD class=tdAbout>Адрес:<TD>" + sp.adres : '') +

            "<TR><TD class=tdAbout>Стоимость:<TD><B>" + sp.summa + "</B> руб." +
                    (sp.summa_manual == 1 ? '<SPAN class=manual>(указана вручную)</SPAN>' : '') +
            "</TABLE>" +

        (sp.file ? "<TD class=image><IMG src=" + sp.file + "s.jpg onclick=G.fotoView('" + sp.file + "');>" : '') +

        "</TABLE>";
};