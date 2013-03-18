$("#category").vkSel({
    width:147,
    title0:'Категория не указана',
    spisok:G.category_spisok,
    func:function(id){
        if(id == 1) {
            //$("#type_gaz").myCheck({name:"Газетный вариант",func:''});
            $("#type_gaz").after("<A class=word>Скачать в формате Word</A>");
            //$(".word").click(function(){ location.href="/gazeta/zayav/PrintWordOb.php?<?php echo $VALUES; ?>&gn="+$("#gazeta_nomer").val(); });
        } else {
            //$("#check_type_gaz").remove();
            $(".word").remove();
        }
        G.spisok.print({category:id});
    }

});

$("#vkSel_category").css('margin-bottom','5px');

$("#year").vkSel({
    width:147,
    title0:'Год не указан',
    spisok:Zayav.year,
    func:function(year){
        $("#vkSel_gazeta_nomer").remove();
        $("#gazeta_nomer").val(0);
        if(year > 0) gazetaNomerGet(year);
        G.spisok.print({year:year,gazeta_nomer:0});
    }
});
    
if($("#year").val() > 0) gazetaNomerGet($("#year").val());

$("#fastFind").topSearch({
    txt:'Быстрый поиск...',
    enter:1,
    func:function(inp) {
        if(inp) $("#nofast").hide();
        else $("#nofast").show();
        G.spisok.print({input:encodeURIComponent(inp)});
    }
});


function gazetaNomerGet(year) {
    $("#gazeta_nomer").vkSel({
        width:147,
        title0:'Номер не указан',
        spisok:G.gazeta_nomer_spisok[year],
        func:function (id) { G.spisok.print({gazeta_nomer:id}); }
    });
    $("#vkSel_gazeta_nomer").css('margin-top','4px');
}





G.spisok.unit = function (sp) {
    return "<H1><EM>" + sp.dtime + "</EM><A href='" + G.url + "&p=gazeta&d=zayav&d1=view&id="+sp.id+"'>" + G.category_ass[sp.category] + " №" + sp.id + "</A></H1>" +
        "<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>" +
            "<TABLE cellpadding=0 cellspacing=4>" +
            (sp.client_id > 0 ? "<TR><TD class=tdAbout>Клиент:<TD><A HREF='" + G.url + "&p=gazeta&d=client&id=" + sp.client_id + "'>" + sp.client_fio + "</A>" : '') +
            (sp.category == 1 ?
                "<TR><TD class=tdAbout>Рубрика:<TD>" + G.rubrika_ass[sp.rubrika] + (sp.podrubrika > 0 ? "<SPAN class=ug>»</SPAN>" + G.podrubrika_ass[sp.podrubrika] : '') +
                "<TR><TD class=tdAbout valign=top>Текст:<TD><DIV class=txt>" + sp.txt + "</DIV>" : '') +

            (sp.ob_dop ? "<TR><TD class=tdAbout>Доп. параметр:<TD>" + sp.ob_dop : '') +
            (sp.category == 2 ? "<TR><TD class=tdAbout>Размер:<TD>" + sp.size_x + " x " + sp.size_y + " = " + sp.kv_sm : '') +
//    if(sp.telefon) HTML+="<TR><TD class=tdAbout>Телефон:<TD>"+sp.telefon;
//    if(sp.adres) HTML+="<TR><TD class=tdAbout>Адрес:<TD>"+sp.adres;

            "<TR><TD class=tdAbout>Стоимость:<TD><B>" + sp.summa + "</B> руб." + (sp.summa_manual == 1 ? '<SPAN class=manual>(указана вручную)</SPAN>' : '') +
            "</TABLE>" +

//    if(sp.file) HTML+="<TD class=image><IMG src=/files/images/"+sp.file+"s.jpg onclick=fotoShow('"+sp.file+"');>";

        "</TABLE>";
};

G.spisok.create({
    url:"/view/gazeta/zayav/spisok/AjaxZayavSpisok.php",
    view:$("#spisok"),
    limit:15,
    cache_spisok:spisok.ob,
    result_view:$("#findResult"),
    result:"Показано $count заяв$ob",
    ends:{'$ob':['ка', 'ки', 'ок']},
    next:"Показать ещё заявки",
    nofind:"Заявок не найдено.",
    imgup:$("#findResult"),
    a:1,
    values:{
        input:'',
        category:0,
        year:(new Date()).getFullYear(),
        gazeta_nomer:Zayav.gazeta_nomer
    }
});

function obSpisokGet() {
    var URL="&gn="+$("#gazeta_nomer").val();
    $.ajax({
        url:"/gazeta/zayav/AjaxObSpisok.php?<?php echo $VALUES; ?>"+URL,
        dataType:'json',
        success:function(data){
            $("#findResult").html(data.result);
            $("#spisok").html("<DIV id=obSpisok>"+data.html+"</DIV>");
            frameBodyHeightSet();
            }
        });

    }
