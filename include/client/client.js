function clientAdd(func, sp) {
    var sp = $.extend({
        id:0,
        person:0,
        fio:'',
        org_name:'',
        telefon:'',
        adres:'',
        inn:'',
        kpp:'',
        email:'',
        skidka:0
    }, sp);
    var html = "<TABLE cellpadding=0 cellspacing=8>" +
        "<TR><TD class=tdAbout>Заявитель:            <TD><a class=img_edit href='" + G.url + "&p=gazeta&d=setup&id=1'></a><INPUT TYPE=hidden id=person value=" + sp.person + ">" +
        "<TR><TD class=tdAbout>Контактное лицо (фио):<TD><INPUT TYPE=text id=client_fio maxlength=200 value='" + sp.fio + "'>" +
        "<TR><TD class=tdAbout>Название организации: <TD><INPUT TYPE=text id=org_name maxlength=200 value='" + sp.org_name + "'>" +
        "<TR><TD class=tdAbout>Телефоны:             <TD><INPUT TYPE=text id=client_telefon maxlength=300 value='" + sp.telefon + "'>" +
        "<TR><TD class=tdAbout>Адрес:                <TD><INPUT TYPE=text id=client_adres maxlength=200 value='" + sp.adres + "'>" +
        "<TR><TD class=tdAbout>ИНН:                  <TD><INPUT TYPE=text id=inn maxlength=100 value='" + sp.inn + "'>" +
        "<TR><TD class=tdAbout>КПП:                  <TD><INPUT TYPE=text id=kpp maxlength=100 value='" + sp.kpp + "'>" +
        "<TR><TD class=tdAbout>E-mail:               <TD><INPUT TYPE=text id=email maxlength=100 value='" + sp.email + "'>" +
        "<TR><TD class=tdAbout>Скидка:               <TD><INPUT TYPE=hidden id=client_skidka value=" + sp.skidka + ">" +
    "</TABLE>";

    $("#dialog_client_add").remove();
    $("BODY").append("<div id=dialog_client_add />");

    var dialog = $("#dialog_client_add").vkDialog({
        top:40,
        width:440,
        head:"Добавление нoвого клиента",
        content:html,
        focus:'#fio',
        submit:function () {
            var send = {
                id:sp.id,
                person:$("#person").val(),
                fio:$("#client_fio").val(),
                telefon:$("#client_telefon").val(),
                org_name:$("#org_name").val(),
                adres:$("#client_adres").val(),
                inn:$("#inn").val(),
                kpp:$("#kpp").val(),
                email:$("#email").val(),
                skidka:$("#client_skidka").val(),
            };
            var msg, top = 0;
            if (send.person == 0) { msg = "Не выбран заявитель."; }
            else if (!send.fio && !send.org_name) {
                msg = "Необходимо указать контактное лицо<BR>либо название организации.";
                $("#fio").focus();
                top = 13;
            } else {
                dialog.process();
                $.post("/include/client/AjaxClientAdd.php?" + G.values, send, function (res) {
                    send.uid = res.id;
                    func(send);
                    dialog.close();
                },'json');
            }
            if (msg) {
                $("#dialog_client_add .bottom:first").vkHint({
                    msg:"<SPAN class=red>" + msg + "</SPAN>",
                    remove:1,
                    indent:40,
                    show:1,
                    top:-48 - top,
                    left:133
                });
            }
        }
    }).o;
    $("#person").vkSel({
        width:180,
        title0:"Не выбран",
        spisok:G.person_spisok
    });

    $("#dialog_client_add .img_edit:first").vkHint({
        msg:"Перейти к настройкам заявителей",
        indent:110,
        top:-52,
        left:71
    });

    $("#client_skidka").vkSel({
        width:60,
        title0:"Нет",
        spisok:G.skidka_spisok
    });
}





$.fn.clientSel = function (obj) {
    var obj = $.extend({
        width:240
    }, obj);

    var client = $(this).vkSel({
        width:obj.width,
        title0:'Клиент не выбран',
        ro:0,
        funcKeyup:clientGet,
        funcAdd:function () {
            clientAdd(function (sp) {
                var arr = [];
                sp.title = sp.org_name ? sp.org_name : sp.fio;
                arr.push(sp);
                client.spisok(arr);
                client.val(sp.uid);
            });
        }
    }).o;

    // Установка выбранного клиента в списке SELECT
    var sel = $(this).val();
    if (!/^\d+$/.test(sel)) {
        sel = '';
    }

    clientGet('');

    function clientGet(val) {
        $.getJSON("/include/client/AjaxClientSpisok.php?" + G.values + "&input=" + encodeURIComponent(val) + "&sel=" + sel, function (res) {
            client.spisok(res.spisok);
            if (sel) {
                client.val(sel);
                sel = '';
            }
        });
    }
};
