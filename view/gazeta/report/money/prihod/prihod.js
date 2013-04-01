$("#period_year").years({func:reportMonthGet});

$("#periodHead").on('click', function (e) {
    if ($(e.target).attr('class') == 'passive') {
        $(this).find('TD').attr('class', 'passive');
        $(e.target).attr('class', 'active');
    }
    switch($(e.target).attr('val')) {
        case 'calendar': reportCalendarGet(); break;
        case 'month': reportMonthGet(); break;
    }
});

$("#money_type").vkSel({
    width:140,
    title0:'Любые платежи',
    spisok:G.money_type_spisok,
    func:function (id) {
        if($("#periodCalendar").is(":hidden"))
            reportMonthGet();
        else G.spisok.print({type:id});
    }
});

reportCalendarGet();


// количество дней в месяце
function getDayCount(year, mon) {
    mon--;
    if (mon == 0) { mon = 12; year--; }
    return 32 - new Date(year, mon, 32).getDate();
}


function reportCalendarGet(month) {
    $("#periodCalendar").show();
    $("#periodMonth").hide();
    $("#periodHead TD").eq(0).attr('class', 'active');
    $("#periodHead TD").eq(1).attr('class', 'passive');
    $("#spisok").html('');
    if (month) {
        var year = $("#period_year").val();
        $("#day_begin").val(year + '-' + month + '-01');
        $("#day_end").val(year + '-' + month + '-' + getDayCount(year, month));
    }
    $("#day_begin").vkCalendar({lost:1, place:'left', func:function (data) { G.spisok.print({day_begin:data}); }});
    $("#day_end").vkCalendar({lost:1, place:'left', func:function (data) { G.spisok.print({day_end:data}); }});
    var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%>" +
        "<TR><TH class=sum>Сумма" +
        "<TH class=about>Описание" +
        "<TH class=data>Дата" +
        "</TABLE>";
    $("#spisokHead").html(html);
    G.spisok.unit = function (sp) {
        var txt = sp.txt;
        txt += txt ? ". " : '';
        if (sp.zayav_id > 0) { txt += "Оплата по заявке <A href='/index.php?" + G.values + "&p=gazeta&d=zayav&d1=view&id=" + sp.zayav_id + "'><EM>№</EM>" + sp.zayav_id + "</A>. "; }
        if (sp.client_id > 0) { txt += "Клиент: <A href='/index.php?" + G.values + "&p=gazeta&d=client&d1=info&id=" + sp.client_id + "'>" + sp.client_fio + "</A>."; }
        var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%><TR>" +
            "<TD class=sum><B>" + sp.sum + "</B>" +
            "<TD class=about><b>" + G.money_type_ass[sp.type] + (txt ? ': ' : '') + '</b>' + txt +
            "<TD class=data>" + sp.dtime_add +
            //"<BR><A href='http://vk.com/id" + sp.viewer_id + "'>" + G.vkusers[sp.viewer_id] + "</A>" +
            "</TABLE>";
        return html;
    };

    G.spisok.create({
        url:"/view/gazeta/report/money/prihod/AjaxPrihodGet.php",
        limit:30,
        view:$("#spisok"),
        imgup:$("#summa"),
        nofind:"За выбранный период платежей нет.",
        //a:1,
        values:{
            day_begin:$("#day_begin").val(),
            day_end:$("#day_end").val(),
            type:$("#money_type").val()
        },
        callback:function (res) { $("#itog").html(G.spisok.data.sum); }
    });
}




function reportMonthGet() {
    $("#periodCalendar").hide();
    $("#periodMonth").show();
    $("#spisokHead").html('');
    $("#spisok").html('<img src=/img/upload.gif>');
    var val = "&year=" + $("#period_year").val() +
              "&type=" + $("#money_type").val()
    $.getJSON("/view/gazeta/report/money/prihod/AjaxPrihodMonth.php?" + G.values + val, function (res) {
        $("#itog").html(res.sum);
        var html = "";
        if (res.spisok.length > 0) {
            html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok id=tab_month><TR><TH>Месяц<TH>Сумма";
            for (var n = 0; n < res.spisok.length; n++) {
                var sp = res.spisok[n];
                html += "<tr><td class=mon><a onclick=reportCalendarGet(" + sp.month + ");>" + G.months_ass[sp.month] + "</a><td class=sum>" + sp.sum;
            }
            html += "</TABLE>";
        }
        $("#spisok").html(html);
    });
}


