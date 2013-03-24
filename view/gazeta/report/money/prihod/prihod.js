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
        if (sp.zayav_id > 0) { txt = "Оплата по заявке <A href='/index.php?" + G.values + "&p=gazeta&d=zayav&d1=view&id=" + sp.zayav_id + "'><EM>№</EM>" + sp.zayav_id + "</A>"; }
        var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%><TR>" +
            "<TD class=sum><B>" + sp.sum + "</B>" +
            "<TD class=about><b>" + G.money_type_ass[sp.type] + ":</b> " + txt +
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


// Внесение прихода
function prihodAdd() {
    var html = "<TABLE cellpadding=0 cellspacing=10 id=prihod_add_tab>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=prihod_txt maxlength=250>" +
        "<TR><TD class=tdAbout>Сумма:<TD><INPUT type=text id=prihod_sum maxlength=8> руб." +
        "<TR><TD class=tdAbout>Деньги поступили в кассу?:<TD><INPUT type=hidden id=prihod_kassa value='-1'>" +
        "</TABLE>";
    var dialog = $("#report_dialog").vkDialog({
        width:420,
        head:"Внесение поступления средств",
        content:html,
        submit:submit
    }).o;

    $("#prihod_kassa").vkRadio({
        display:'inline-block',
        right:15,
        spisok:[{uid:1, title:'да'},{uid:0, title:'нет'}],
    });

    $("#prihod_txt").focus();

    function submit() {
        var send = {
            txt:$("#prihod_txt").val(),
            sum:$("#prihod_sum").val(),
            kassa:$("#prihod_kassa").val()
        };

        var msg;
        if (!send.txt) { msg = "Не указано наименование."; $("#prihod_txt").focus(); }
        else if (!/^(\d+)(.{1}\d{1,2})?$/.test(send.sum)) { msg = "Некорректно указана сумма."; $("#prihod_sum").focus(); }
        else if (send.kassa == -1) { msg = "Укажите, деньги поступили в кассу или нет."; }
        else {
            dialog.process();
            $.post("/view/gazeta/report/money/AjaxPrihodRashodAdd.php?" + G.values, send, function (res) {
                dialog.close();
                vkMsgOk("Новое поступление внесено.");
                reportCalendarGet();
            }, 'html');
        }
        if (msg) {
            $("#report_dialog .bottom:first").vkHint({
                msg:"<SPAN class=red>" + msg + "</SPAN>",
                remove:1,
                indent:40,
                show:1,
                top:-48,
                left:125
            });
        }
    }
} // end prihodAdd








