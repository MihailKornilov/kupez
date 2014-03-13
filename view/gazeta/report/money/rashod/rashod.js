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

$("#expense_id").vkSel({
    width:140,
    title0:"Не выбрана",
    spisok: G.rashod_category_spisok,
    func:function (id) {
        if($("#periodCalendar").is(":hidden"))
            reportMonthGet();
        else G.spisok.print({category:id});
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
        var cat = '';
        if (sp.cat > 0) cat = '<b>' + G.rashod_category_ass[sp.cat] + (sp.txt ? ': ' : '') + '</b>';
        var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%><TR>" +
            "<TD class=sum><B>" + sp.sum + "</B>" +
            "<TD class=about>" + cat + sp.txt +
            "<TD class=data>" + sp.dtime_add +
            //"<BR><A href='http://vk.com/id" + sp.viewer_id + "'>" + G.vkusers[sp.viewer_id] + "</A>" +
            "</TABLE>";
        return html;
    };

    G.spisok.create({
        url:"/view/gazeta/report/money/rashod/AjaxRashodGet.php",
        limit:30,
        view:$("#spisok"),
        imgup:$("#summa"),
        nofind:"За выбранный период записей нет.",
        //a:1,
        values:{
            day_begin:$("#day_begin").val(),
            day_end:$("#day_end").val(),
            category:$("#expense_id").val()
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
        "&category=" + $("#expense_id").val()
    $.getJSON("/view/gazeta/report/money/rashod/AjaxRashodMonth.php?" + G.values + val, function (res) {
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


// Внесение расхода
function rashodAdd() {
    var html = "<TABLE cellpadding=0 cellspacing=10 id=rashod_add_tab>" +
        "<TR><TD class=tdAbout>Категория:<TD><a class=img_edit href='" + G.url + "&p=gazeta&d=setup&id=10'></a><INPUT type=hidden id=rashod_cat>" +
        "<TR><TD class=tdAbout>Описание:<TD><INPUT type=text id=rashod_txt maxlength=250>" +
        "<TR><TD class=tdAbout>Сумма:<TD><INPUT type=text id=rashod_sum maxlength=8> руб." +
        "<TR><TD class=tdAbout>Деньги взяты из кассы?:<TD><INPUT type=hidden id=rashod_kassa value='-1'>" +
        "</TABLE>";
    var dialog = $("#report_dialog").vkDialog({
        width:400,
        head:"Внесение расхода",
        content:html,
        submit:submit
    }).o;

    $("#rashod_cat").vkSel({
        width:190,
        title0:"Не выбрана",
        spisok: G.rashod_category_spisok
    });

    $("#rashod_kassa").vkRadio({
        display:'inline-block',
        right:15,
        spisok:[{uid:1, title:'да'},{uid:0, title:'нет'}],
    });

    $("#rashod_txt").focus();

    function submit() {
        var send = {
            cat:$("#rashod_cat").val(),
            txt:$("#rashod_txt").val(),
            sum:$("#rashod_sum").val(),
            kassa:$("#rashod_kassa").val()
        };

        var msg;
        if (!send.txt && send.cat == 0) { msg = "Выберите категорию или укажите описание."; $("#rashod_txt").focus(); }
        else if (!G.reg_sum.test(send.sum)) { msg = "Некорректно указана сумма."; $("#rashod_sum").focus(); }
        else if (send.kassa == -1) { msg = "Укажите, деньги взяты из кассы или нет."; }
        else {
            dialog.process();
            send.sum *= -1;
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
                left:115
            });
        }
    }
} // end rashodAdd
