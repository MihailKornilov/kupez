$("#day_begin").vkCalendar({lost:1, place:'left', func:function (data) { G.spisok.print({day_begin:data}); }});
$("#day_end").vkCalendar({lost:1, place:'left', func:function (data) { G.spisok.print({day_end:data}); }});

G.spisok.unit = function (sp) {
    var txt = sp.txt;
    if (sp.zayav_id > 0) { txt = "Оплата по заявке <A href='/index.php?" + G.values + "&my_page=remZayavkiInfo&id=" + sp.zayav_id + "'><EM>№</EM>" + sp.zayav_id + "</A>"; }
    var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%><TR>" +
        "<TD class=sum><B>" + sp.sum + "</B>" +
        "<TD class=about>" + txt +
        "<TD class=data>" + sp.dtime_add +
        //"<BR><A href='http://vk.com/id" + sp.viewer_id + "'>" + G.vkusers[sp.viewer_id] + "</A>" +
        "</TABLE>";
    return html;
};

G.spisok.create({
    url:"/view/gazeta/report/money/kassa/AjaxKassaGet.php",
    limit:40,
    view:$("#spisok"),
    imgup:$("#summa"),
    nofind:"За выбранный период записей нет.",
    //a:1,
    values:{
        day_begin:$("#day_begin").val(),
        day_end:$("#day_end").val()
    },
    callback:function (res) { $("#itog").html(G.spisok.data.sum); }
});




// взять деньги из кассы
function kassaGet() {
    var k_sum = $("#itog").html();
    var html = "<TABLE cellpadding=0 cellspacing=8>" +
        "<TR><TD class=tdAbout>Сумма:<TD><INPUT type=text id=kassa_sum maxlength=8> (max: " + k_sum + ")" +
        "<TR><TD class=tdAbout>Комментарий:<TD><INPUT type=text id=kassa_txt>" +
        "</TABLE>";
    var dialog = $("#report_dialog").vkDialog({
        head:"Взятие денег из кассы",
        content:html,
        butSubmit:"Применить",
        submit:submit,
        focus:"#kassa_sum"
    }).o;

    function submit() {
        var send = {
            sum:$("#kassa_sum").val(),
            txt:$("#kassa_txt").val()
        };
        var msg;
        if (!/^[0-9]+$/.test(send.sum)) { msg = "Некорректно введена сумма."; }
        else if (send.sum > k_sum) { msg = "Введённая сумма превышает сумму в кассе."; }
        else if (!send.txt) { msg = "Не указан комментарий."; }
        else {
            send.sum *= -1;
            dialog.process();
            $.post("/view/gazeta/report/money/kassa/AjaxKassaInsert.php?" + G.values, send, function (res) {
                dialog.close();
                vkMsgOk("Операция выполнена.");
                G.spisok.print();
            }, 'html');
        }
        if (msg) {
            $("#report_dialog .bottom:first").vkHint({
                msg:"<SPAN class=red>" + msg + "</SPAN>",
                remove:1,
                indent:40,
                show:1,
                top:-48,
                left:96
            });
        }
    }
} // end kassaGet


// Положить деньги в кассу
function kassaPut() {
    var html = "<TABLE cellpadding=0 cellspacing=8>" +
        "<TR><TD class=tdAbout>Сумма:<TD><INPUT type=text id=kassa_sum maxlength=8> руб." +
        "<TR><TD class=tdAbout>Комментарий:<TD><INPUT type=text id=kassa_txt>" +
        "</TABLE>";
    var dialog = $("#report_dialog").vkDialog({
        head:"Внесение денег в кассу",
        content:html,
        butSubmit:"Применить",
        submit:submit,
        focus:"#kassa_sum"
    }).o;

    function submit() {
        var send = {
            sum:$("#kassa_sum").val(),
            txt:$("#kassa_txt").val()
        };
        var msg;
        if (!/^[0-9]+$/.test(send.sum)) { msg = "Некорректно введена сумма."; }
        else if (!send.txt) { msg = "Не указан комментарий."; }
        else {
            dialog.process();
            $.post("/view/gazeta/report/money/kassa/AjaxKassaInsert.php?" + G.values, send, function (res) {
                dialog.close();
                vkMsgOk("Операция выполнена.");
                G.spisok.print();
            }, 'html');
        }
        if (msg) {
            $("#report_dialog .bottom:first").vkHint({
                msg:"<SPAN class=red>" + msg + "</SPAN>",
                remove:1,
                indent:40,
                show:1,
                top:-48,
                left:96
            });
        }
    }
} // end kassaPut


// установка начального значения в кассе
function kassaSet(but) {
    var send = { summa:$("#kassa_set_sum").val() };
    if (!/^[\d]+$/.test(send.summa)) {
        $("#kassa_set").vkHint({
            msg:"<SPAN class=red>Некорректно введена сумма.</SPAN>",
            remove:1,
            indent:40,
            show:1,
            top:46,
            left:134,
            correct:0
        });
        $("#kassa_set_sum").focus();
    }
    else {
        $(but).butProcess();
        $.post("/view/gazeta/report/money/kassa/AjaxKassaSet.php?" + G.values, send, function (res) {
            location.reload();
        }, 'json');
    }
} // end kassaSet





