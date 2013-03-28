var zayav = {
    rubrika:null,
    gn:null,     // ����������� � �������� �����
    sending:0    // ������ ���� ���������� �� �������� ��� ���
};


// ������� �����������, ���� ������ �����������
function zayavAdd() {
    $("#category").vkSel({
        width:120,
        spisok: G.category_spisok,
        func:function (id) {
            // ������� ����������
            $("#for_ob").hide();
            zayav.rubrika.val(0);
            $("#vkSel_podrubrika").remove();
            $("#podrubrika").val(0);
            $("#txt").val('');
            calcSummaOb();
            $("#telefon").val('');
            $("#adres").val('');
            // ������� �������
            $("#for_rek").hide();
            $("#size_x").val('');
            $("#size_y").val('');
            $("#kv_sm").val('');

            fotoUpload();

            $("#skidka_tab").hide();
            zayav.skidka.val(0);

            $("#manual_tab").hide();
            $("#summa_manual").val(0);
            $("#summa").css('background-color','#FFF').attr('readonly',true);
            // ������� ������ ������� ������
            gnReload(id);

            switch (id) {
                case '1':
                    $("#for_ob").show();
                    $("#txt").focus();
                    $("#manual_tab").show();
                    break;
                case '2':
                    $("#for_rek").show();
                    $("#size_x").focus();
                    $("#manual_tab").show();
                    $("#skidka_tab").show();
                    break;
                default:
                    $("#summa_manual").val(1);
                    $("#summa").css('background-color','#FF8').removeAttr('readonly');
                    break;
            }
        }
    });

    $("#client_id").clientSel();

    rubrikaSet();

    // ��������� � ��������� ������ ��� ��������� �� ������
    $("#vkSel_rubrika .add:first").vkHint({
        msg:"������� � ��������� ������",
        indent:60,
        top:-58,
        left:-59,
        correct:0
    });

    $("#txt").autosize().focus().keyup(calcSummaOb);

    $("#size_x").keyup(calcSummaRek);
    $("#size_y").keyup(calcSummaRek);

    fotoUpload();

    zayav.skidka = $("#skidka").vkSel({
        width:60,
        title0:'���',
        spisok:G.skidka_spisok,
        func:function () { zayav.gn.cenaSet(); }
    }).o;

    gnReload(1);

    moneyCreate();

    $("#note").autosize({callback:frameBodyHeightSet});
} // end of zayavAdd()

function zayavEdit(category, client, gns) {
    if (client == 0) $("#client_id").clientSel();

    // ����� �����������
    var foto_link = $("#foto_link").val();
    if (foto_link) {
        $("#foto").html("<img src=" + foto_link + "s.jpg><div class=img_del />");
        $("#foto .img_del").click(fotoUpload);
        $("#foto img").click(function () {
            G.fotoView({spisok:[{link:foto_link}]});
        });
    } else fotoUpload();

    gnReload(category, gns);
    moneyCreate();
    switch (category) {
        case 1:
            rubrikaSet();
            podrubrikaSet(zayav.rubrika.val());
            $("#txt").autosize().focus().keyup(calcSummaOb);
            calcSummaOb();
            break;
        case 2:
            $("#size_x").keyup(calcSummaRek);
            $("#size_y").keyup(calcSummaRek);
            $("#skidka_tab").show();
            zayav.skidka = $("#skidka").vkSel({
                width:60,
                title0:'���',
                spisok:G.skidka_spisok,
                func:function () { zayav.gn.cenaSet(); }
            }).o;
            zayav.gn.cenaSet($("#kv_sm").val());
            break;
        default:
            $("#manual_tab").hide();
            $("#summa_manual").val(1);
            $("#summa").css('background-color','#FF8').removeAttr('readonly');
            break;
    }
} // end of zayavEdit()


function rubrikaSet() {
    zayav.rubrika = $("#rubrika").vkSel({
        display:'inline-block',
        width:120,
        title0:'�� �������',
        spisok:G.rubrika_spisok,
        funcAdd:function () { location.href = G.url + '&p=gazeta&d=setup&id=2'; },
        func:function (id) {
            $("#vkSel_podrubrika").remove();
            $("#podrubrika").val(0);
            podrubrikaSet(id);
        }
    }).o;
}

function podrubrikaSet(id) {
    if (G.podrubrika_spisok[id]) {
        $("#podrubrika").vkSel({
            display:'inline-block',
            width:180,
            title0:'���������� �� �������',
            spisok:G.podrubrika_spisok[id],
            funcAdd:function () { location.href = G.url + '&p=gazeta&d=setup&id=7'; }
        });
        // ��������� � ��������� ��������� ��� ��������� �� ������
        $("#vkSel_podrubrika .add:first").vkHint({
            width:130,
            msg:"������� � ���������<br>���������",
            indent:60,
            top:-71,
            left:-59
        });
    }
} // end of podrubrikaSet


// �������� �����������
function fotoUpload() {
    $("#foto_link").val('');
    $("#foto").fotoUpload({
        func:function (res) {
            $("#foto_link").val(res.link);
            $("#foto")
                .html(res.img + "<div class=img_del />")
                .find("IMG:first").on('load', frameBodyHeightSet);
            $("#foto .img_del").click(fotoUpload);
            $("#foto img").click(function () {
                G.fotoView(res.link);
            });
            frameBodyHeightSet();
        }
    });
}

// ��������� ������ ������� � ����� �������
function gnReload(id, gns) {
    //if (!gns) var gns = null;
    zayav.gn = $("#gn_spisok").gnGet({
        category:id,
        gns:gns,
        manual:$("#summa_manual"),
        summa:$("#summa"),
        skidka:$("#skidka"),
        paid:$("#gn_paid").val()
    });

    $("#summa_manual").myCheck({func:function (id) {
        if(id == 1)
            $("#summa").css('background-color','#FF8').removeAttr('readonly').focus();
        else {
            $("#summa").css('background-color','#FFF').attr('readonly',true);
        }
        zayav.gn.cenaSet();
    }});
} // end of gnReload()


function moneyCreate() {
    $("#summa").keyup(function(){
        if(!G.reg_sum.test($(this).val()))
            $(this).vkHint({
                msg:"<SPAN class=red>�� ��������� ������� �����.<BR>����������� ����� � ����� ��� �����.</SPAN>",
                remove:1,
                indent:40,
                show:1,
                top:-70,
                left:0
            });
        else {
            $(this).prev().remove('.hint');
            zayav.gn.cenaSet();
        }
    });

    $("#oplata").vkRadio({
        display:'inline-block',
        right:15,
        spisok:[{uid:1, title:'��'},{uid:0, title:'���'}],
        func:function (id) {
            $("#money_tab")[id == 1 ? 'show' : 'hide']();
            var money = $("#summa").val();
            $("#money").val(money);
            frameBodyHeightSet();
        }
    });

    $("#money_type").vkSel({
        width:180,
        title0:'�� ������',
        spisok:G.money_type_spisok
    });

    $("#money_kassa").vkRadio({
        display:'inline-block',
        right:15,
        spisok:[{uid:1, title:'��'},{uid:0, title:'���'}],
    });
} // end of moneyCreate()

// ���������� ��������� ����������
function calcSummaOb() {
    var txt_sum = 0; // ����� ������ �� �����
    var txt = $("#txt").val();
    txt = txt.replace(/\./g,'');     // �����
    txt = txt.replace(/,/g,'');      // �������
    txt = txt.replace(/\//g,'');     // ���� /
    txt = txt.replace(/\"/g,'');     // ������� �������
    txt = txt.replace(/( +)/g,' ');  // ������ �������
    txt = txt.replace( /^\s+/g, ''); // ������� � ������
    txt = txt.replace( /\s+$/g, ''); // ������� � �����
    if (txt.length == 0) {
        $("#txtCount").html('');
    } else {
        txt_sum += G.txt_cena_first * 1;
        var podr_about = ''; // ��������� ������������ ����� ����������
        if (txt.length>G.txt_len_first) {
            podr_about = ' = ';
            var CEIL = Math.ceil((txt.length - G.txt_len_first) / G.txt_len_next);
            podr_about += G.txt_len_first;
            var LAST = txt.length - G.txt_len_first - (CEIL - 1) * G.txt_len_next;
            txt_sum += CEIL*G.txt_cena_next;
            if (G.txt_len_next == LAST) CEIL++;
            if (CEIL > 1) podr_about += " + " + G.txt_len_next;
            if (CEIL > 2) podr_about += "x" + (CEIL - 1);
            if (G.txt_len_next > LAST) podr_about += " + " + LAST;
        }
        var html = "�����: <B>" + txt.length + "</B>" + podr_about + "<BR>" +
            "����: <B>" + txt_sum + "</B> ���.<SPAN>(��� ����� ���. ����������)</SPAN>";
        $("#txtCount").html(html);
    }
    zayav.gn.cenaSet(txt_sum);
} // end of calcSummaOb()

// ���������� ��������� �������
function calcSummaRek() {
    val = $(this).val();
    var id = $(this).attr('id');
    $("#kv_sm").val('');
    kv_sm = 0;
    if (!G.reg_sum.test(val)) {
        $("#for_rek").vkHint({
            msg:"<SPAN class=red>�� ��������� ������� ��������.</SPAN>",
            remove:1,
            indent:40,
            show:1,
            top:-49,
            left:144 + (id == 'size_y' ? 63 : 0)
        });
    } else {
        $("#for_rek").prev().remove('.hint');
        var val_x = $("#size_x").val();
        var val_y = $("#size_y").val();
        var x = G.reg_sum.test(val_x) ? val_x : 0;
        var y = G.reg_sum.test(val_y) ? val_y : 0;
        kv_sm = Math.round((x * y) * 100) / 100;
        if (kv_sm > 0) $("#kv_sm").val(kv_sm);
    }
    zayav.gn.cenaSet(kv_sm);
} // end of calcSummaRek()

function zayavAddGo(but, id) {
    if (zayav.sending == 1) return;
    var send = {
        id:id,
        client_id:$("#client_id").val(),
        category:$("#category").val(),
        rubrika:$("#rubrika").val() || 0,
        podrubrika:$("#podrubrika").val() || 0,
        txt:$("#txt").val() || '',
        telefon:$("#telefon").val() || '',
        adres:$("#adres").val() || '',
        size_x:$("#size_x").val() || 0,
        size_y:$("#size_y").val() || 0,
        kv_sm:$("#kv_sm").val() || 0,

        file:$("#foto_link").val(),
        gn_first:G.gn.first_active,
        gns:zayav.gn.gnSelected(),

        skidka:$("#skidka").val() || 0,
        skidka_sum:$("#skidka_sum").val() || 0,
        summa_manual:$("#summa_manual").val(),
        summa:$("#summa").val(),
        oplata:$("#oplata").val(),
        money:$("#money").val(),
        money_type:$("#money_type").val(),
        money_kassa:$("#money_kassa").val(),
        note:$("#note").val() || ''
    };

    var msg;
    if (send.category == 1 && send.rubrika == 0) { msg = "�� ������� �������"; }
    else if (send.category == 1 && !send.txt) { msg = "������� ����� ����������"; }
    else if (send.category == 1 && !send.telefon && !send.adres) { msg = "������� ���������� ������� ��� ����� �������"; }
    else if (send.category > 1 && send.client_id == 0) { msg = "�� ������ ������"; }
    else if (send.category == 2 && !send.kv_sm) { msg = "�� ������ ������ �����������"; }
//    else if (!send.gns) { msg = "���������� ������� ������� ���� ����� �������"; }
    else if (send.gns == 'no_polosa') { msg = "���������� ������� ������ � ���� �������"; }
    else if (!G.reg_sum.test(send.summa)) { msg = "����������� ������� �������� ���������"; }
    else if (send.summa > 0 && send.oplata == -1) { msg = "�������, ������ �������� ��� ���"; }
    else if (send.oplata == 1 && (!G.reg_sum.test(send.money) || send.money ==0)) { msg = "����������� ������� ����� ������"; }
    else if (send.oplata == 1 && send.money_type == 0) { msg = "�� ������ ��� �������"; }
    else if (send.oplata == 1 && send.money_kassa == -1) { msg = "�������, ��������� ������ � ����� ��� ���"; }
    else {
        zayav.sending = 1;
        $(but).butProcess();
        $.post("/view/gazeta/zayav/add/AjaxZayavAdd.php?" + G.values, send, function (res) {
            //$(".headName").html(res)
            location.href = G.url + "&p=gazeta&d=zayav&d1=view&id=" + res.id;
        }, 'json');
    }
    if (msg) {
        $(but).vkHint({
            msg:"<SPAN class=red>" + msg + "</SPAN>",
            remove:1,
            indent:40,
            show:1,
            top:-58,
            left:-14
        });
    }
} // end of zayavAddGo()
