// Внесение платежа
function moneyAdd(obj) {
    $("#dialog_prihod").remove();
    $("BODY").append("<div id=dialog_prihod></div>");

    var obj = $.extend({
        zayav_id:0,
        client_id:0,
        func:function () { location.reload(); }
    }, obj);

    var html = "<TABLE cellpadding=0 cellspacing=10 id=prihod_add_tab>" +
        "<TR><TD class=tdAbout>Вид:<TD><INPUT type=hidden id=prihod_type><a class=img_edit href='" + G.url + "&p=gazeta&d=setup&id=11'></a>" +
        "<TR><TD class=tdAbout>Сумма:<TD><INPUT type=text id=prihod_sum maxlength=8> руб." +
        "<TR><TD class=tdAbout>Комментарий:<TD><INPUT type=text id=prihod_txt maxlength=250>" +
        "<TR id=tr_kassa><TD class=tdAbout>Деньги поступили<br>в кассу?:<TD><INPUT type=hidden id=prihod_kassa value='-1'>" +
        "</TABLE>";
    var dialog = $("#dialog_prihod").vkDialog({
        top:50,
        width:420,
        head:"Внесение платежа",
        content:html,
        submit:submit,
        focus:"#prihod_sum"
    }).o;

    $("#prihod_type").vkSel({
        width:190,
        display:'inline-block',
        title0:'Не указан',
        spisok:G.money_type_spisok,
        func:function (id) {
            $("#tr_kassa")[id == 1 ? 'show' : 'hide']();
            $("#prihod_kassa").val(-1);
            $("#prihod_kassa").vkRadio({
                display:'inline-block',
                right:15,
                spisok:[{uid:1, title:'да'},{uid:0, title:'нет'}],
            });
        }
    });

    function submit() {
        var send = {
            zayav_id:obj.zayav_id,
            client_id:obj.client_id,
            type:$("#prihod_type").val(),
            txt:$("#prihod_txt").val(),
            sum:$("#prihod_sum").val(),
            kassa:$("#prihod_kassa").val()
        };

        var msg;
        if (send.type == 0) { msg = "Не указан вид платежа."; }
        else if (!G.reg_sum.test(send.sum)) { msg = "Некорректно указана сумма."; $("#prihod_sum").focus(); }
        else if (!send.txt && send.zayav_id == 0 && send.client_id == 0) { msg = "Укажите комментарий."; $("#prihod_txt").focus(); }
        else if (send.kassa == -1 && send.type == 1) { msg = "Укажите, деньги поступили в кассу или нет."; }
        else {
            if (send.kassa == -1) send.kassa = 0;
            dialog.process();
            $.post("/view/gazeta/report/money/AjaxPrihodRashodAdd.php?" + G.values, send, function (res) {
                dialog.close();
                vkMsgOk("Платёж успешно внесён.");
                obj.func();
            }, 'html');
        }
        if (msg) {
            $("#dialog_prihod .bottom:first").vkHint({
                msg:"<SPAN class=red>" + msg + "</SPAN>",
                remove:1,
                indent:40,
                show:1,
                top:-48,
                left:125
            });
        }
    }
} // end moneyAdd()

// Удаление платежа
function moneyDel(id) {
    $("#dialog_money").remove();
    $("BODY").append("<div id=dialog_money></div>");

    var dialog = $("#dialog_money").vkDialog({
        width:250,
        head:"Удаление платежа",
        butSubmit:"Удалить",
        content:"<CENTER><B>Подтвердите удаление платежа</B></CENTER>",
        submit:function () {
            dialog.process();
            $.getJSON("/view/gazeta/report/money/AjaxMoneyDel.php?" + G.values + "&id=" + id, function () {
                location.reload();
            }, 'json');
        }
    }).o;
} // end moneyDel()
