// �������
$(".info A:first").click(function () {
    var html = "<DIV id=vk-create-rules>" +
        "<DIV class=headName>������������ ��� �������� ����������:</DIV>" +
        "<UL><LI>����� �������� ���������� ���� �����;" +
        "<LI>�� ����������� ���������� ����������, ����� ������� ������������� ����� ��������� ������� ���������� ��, ��� �� �����������;" +
        //" ���������� ��������� ��������� �� 4-� ����������� �� ���� ����������;" +
        "<LI>����������� ���������� �������� ����;" +
        "<LI>�� ��������� ���� � �� �� ���������� ��������, ��� ����� ���� ����������� ��������� ������� �������. ��������� ���������� ����� ���������;" +
        "<LI>�� ������ ���������� � ������� ��������;" +
        "<LI>���������� ����� ����������� �������� � ��������������� ����;" +
        "<LI>���� ���� ��������� ��� �� ���������, ������� ��� ��� ���������� � ����� � ������� \"��� ����������\".</UL>" +

        "<DIV class=headName>������, ������� ������� �� �����������:</DIV>" +
        "<UL><LI>�������, ������������ � (���) ���������� ������� ��������� ����������������� ���������� ���������;" +
        "<LI>������������� �������, ������������ ������� � �����������;" +
        "<LI>���������� ������� � ����������, �� ����������� ��������������� �������;" +
        "<LI>������� � (���) ������ �������� � �������� �������� �����-�������;" +
        "<LI>�������, ���������� ��������������� �����������, � ������ ���������� ����� �����������;" +
        "<LI>�������, ���������� ������������ ������������ ��� ����� ������������� ������������� ������������ ����������� ����������� �����������, � ������ ���������� ����� ������������ ��� ������������� ������ ������������;" +
        "<LI>������, �� ������������ � (���) ���������� ������� ��������� ��������� �������� ��� ���� ����������� ����������, � ������ ���������� ����� ����������.</UL></DIV>";
    var dialog = $("#dialog_obCreate").vkDialog({
        width:500,
        top:20,
        head:"������� ���������� ����������",
        content:html,
        butSubmit:'�������',
        butCancel:'',
        submit:function () { dialog.close(); }
    }).o;
});



fotoUpload();

// �������� �����������
function fotoUpload() {
    $("#images").val('');
    preview();
    $("#upload").fotoUpload({
        func:function (res) {
            $("#images").val(res.link);
            $("#upload").html(res.img + "<div class=img_del />");
            $("#upload .img_del").click(fotoUpload);
            preview();
        }
    });
}
/*
$("#images_upload").bind({
    mouseenter: function () {
        $("#ms_images").alertShow({
            txt:"�� ������ ��������� �� 4 �����������.",
            left:-3,
            top:-43,
            otstup:30,
            delayShow:500,
            delayHide:0
        });
    },
    mouseleave: function () { $("#alert").remove(); },
    click:function () { $("#alert").remove(); $(this).unbind(); }
});
*/



$("#telefon").keyup(preview);

G.cities_spisok = [{uid:1,title:"������"},{uid:2,title:"�����-���������"},{uid:35,title:"������� ��������"},{uid:10,title:"���������"},{uid:49,title:"������������"},{uid:60,title:"������"},{uid:61,title:"�����������"},{uid:72,title:"���������"},{uid:73,title:"����������"},{uid:87,title:"��������"},{uid:95,title:"������ ��������"},{uid:99,title:"�����������"},{uid:104,title:"����"},{uid:110,title:"�����"},{uid:119,title:"������-��-����"},{uid:123,title:"������"},{uid:125,title:"�������"},{uid:151,title:"���"},{uid:158,title:"���������"}];
for (var n = 0; n < 2; n++) { G.cities_spisok[n].content = "<B>" + G.cities_spisok[n].title + "</B>"; }

// ����� ������ �����
var country = $("#countries").vkSel({
    bottom:4,
    width:180,
    spisok:G.countries_spisok,
    func:function (id) { preview(); citiesGet(id); }
});

// ����� ������ �������
var city = $("#cities").vkSel({
    width:180,
    title0:'����� �� ������',
    spisok:G.cities_spisok,
    ro:0,
    funcKeyup:function (val) {
        VK.api('places.getCities',{country:country.val(), q:val}, function (data) {
            for(var n = 0; n < data.response.length; n++) {
                var sp = data.response[n];
                sp.uid = sp.cid;
                sp.content = sp.title + (sp.area ? "<DIV class=pole2>" + sp.area + "</DIV>" : '');
            }
            if (val.length == 0) { data.response[0].content = "<B>" + data.response[0].title + "</B>"; }
            city.spisok(data.response);
        });
    },
    func:preview
}).o;

if (G.vk.country_id != 1) citiesGet(G.vk.country_id);

// ������� ������, � �������� ����� ������������
if (G.vk.city_id > 0) {
    var no_city = 1;
    for (var n = 0; n < G.cities_spisok.length; n++) {
        if (G.cities_spisok[n].uid == G.vk.city_id) {
            no_city = 0;
            city.val(G.vk.city_id);
            break;
        }
    }
    if (no_city == 1) {
        VK.api('places.getCityById', {cids:G.vk.city_id}, function (res) {
            G.cities_spisok.unshift({
                uid:res.response[0].cid,
                title:res.response[0].name,
                content:"<B>" + res.response[0].name + "</B>"
            });
            city.spisok(G.cities_spisok);
            city.val(G.vk.city_id);
        });
    }
}

// ��������� ������ ������� �� ������
function citiesGet(id) {
    city.process();
    VK.api('places.getCities',{country:id}, function (data) {
        var d = data.response;
        for(var n = 0; n < d.length; d[n].uid = d[n].cid, n++);
        d[0].content = "<B>" + d[0].title + "</B>";
        city.spisok(d);
    });
}

$("#vkSel_cities").vkHint({
    width:180,
    msg:"<div style=text-align:justify;>����������� ���������� �����, " +
        "���� ���� ���������� ������������� ������ �� ����, " +
        "����� ���������� ����� ������������ ������ � ����� ������.</div>",
    ugol:'left',
    top:-17,
    left:211,
    indent:15
});


$("#viewer_id_show").myCheck({func:preview});
$("#pay_service").myCheck({func:function (id) {
    $("#payContent").css('display', id == 1 ? 'block' : 'none');
    if (id == 0) {
        $("#dop").val(0);
        $("#top").val(0);
        $("#top_week").css('visibility', 'hidden');
    } else {
        // �������������� �������
        $("#dop").vkRadio({
            width:200,
            bottom:6,
            spisok:[
                {uid:0,title:'<SPAN style=color:#888;>�� ��������</SPAN>'},
                {uid:'ramka',title:'������� � �����'},
                {uid:'bold',title:'�������� ������ �������'},
                {uid:'black',title:'�� ������ ����'}
            ],
            func:function () { preview(); printButton(); }
        });
        // ������� ����������
        $("#top").myCheck({func:function (id) {
            $("#top_week").css('visibility', id == 1 ? 'visible' : 'hidden');
            if (id == 1) {
                create.top_week = 1;
                $("#top_week .inp").html(1);
                $("#top_week .end").html('�');
            }
            printButton();
        }});
    }
    preview();
    printButton();
    frameBodyHeightSet();
}});






$("#top_week .a").mousedown(function (e) {
    switch ($(e.target).html()) {
        case '-': if (create.top_week > 1) { create.top_week--; } break;
        case '+': if (create.top_week < 9) { create.top_week++; } break;
    }
    $("#top_week .inp").html(create.top_week);
    $("#top_week .end").html(G.end(create.top_week, '�', '�', '�'));
    printButton();
});

$("#butts .vkButton BUTTON")[0].onclick = vkCreateGo;



function printButton() {
    create.order.votes = 0;
    if ($("#dop").val() != '0') { create.order.votes++; }
    if ($("#top").val() > 0) { create.order.votes += create.top_week; }
    var v = create.order.votes;
    $(".vkButton SPAN").html(v > 0 ? " �� " + v + " �����" + G.end(v, ['', '�', '��']) : '');
}







function preview() {
    var txt = $("#txt").val();
    txt = txt.replace(/\n/g,"<BR>");

    var sp = {
        rubrika:rubrika.val() > 0 ? "<EM class=aRub>" + $("#vkSel_rubrika INPUT:first").val() + "</EM><U>�</U>" : '',
        podrubrika:parseInt($("#podrubrika").val()) ? "<EM class=aRub>" + $("#vkSel_podrubrika INPUT:first").val() + "</EM><U>�</U>" : '',
        txt:txt,
        telefon:$("#telefon").val(),
        file:$("#images").val(),
        dop:$("#dop").val()
    };

    var name = '';
    if (parseInt($("#viewer_id_show").val())) name = "<A href='http://vk.com/id" + G.vk.viewer_id+"' target=_vk>" + G.vk.first_name + " " + G.vk.last_name + "</A>";
    var city = '';
    if ($("#cities").val() > 0) { city = $("#vkSel_countries INPUT:first").val() + ", " + $("#vkSel_cities INPUT:first").val(); }

    var html = "<DIV class=unit>" +
        "<DIV class='" + sp.dop + "'>" +
        "<TABLE cellpadding=0 cellspacing=0 width=100%>" +
        "<TR><TD class=txt>" + sp.rubrika + sp.podrubrika + sp.txt +
        (sp.telefon ? " <DIV class=tel>"+sp.telefon+"</DIV>" : '') +
        " <DIV class=adres>" + city + name + "</DIV>" +
        (sp.file ? "<TD class=foto><IMG src=" + sp.file.split('_')[0] + "s.jpg onclick=G.fotoView('" + sp.file + "');>" : '') +
        "</TABLE></DIV></DIV>";

    $("#obSpisok").html(html);
    frameBodyHeightSet();
}



var process = 0;
function vkCreateGo() {
    if (process == 1) return;
    var dop = $("#dop").val();
    var country_id = $("#countries").val();
    var country_name = $("#vkSel_countries INPUT:first").val();
    var city_id = $("#cities").val();
    var city_name = $("#vkSel_cities INPUT:first").val();
    var top_week = 0;
    if ($("#top_week").css('visibility') == 'visible') { top_week = create.top_week; }

    var obj = {
        rubrika:$("#rubrika").val(),
        podrubrika:$("#podrubrika").val(),
        txt:$("#txt").val(),
        telefon:$("#telefon").val(),
        adres:$("#adres").val(),
        file:$("#images").val(),
        dop:dop != '0' ? dop : '',
        country_id:country_id,
        country_name:country_id != '0' ? country_name : '',
        city_id:city_id,
        city_name:city_id != '0' ? city_name : '',
        viewer_id_show:$("#viewer_id_show").val(),
        top_day:top_week * 7,
        order_id:create.order.id,
        order_votes:create.order.votes
    };


    var msg;
    if (obj.rubrika == 0) { msg = "�� ������� �������"; }
    else if (!obj.txt) { msg = "������� ����� ����������"; }
    else {
        if (create.order.votes > 0 && create.order.id == 0) {
            VK.callMethod('showOrderBox', {type:'item', item:'votes_' + create.order.votes});
        } else {
            process = 1;
            $(".vkButton BUTTON").butProcess();
            $.post("/view/ob/create/AjaxObCreate.php?" + G.values, obj, function (res) {
                wallPost(obj);
                location.href = "/index.php?" + G.values + "&p=ob";
            },'json');
        }
    }

    if (msg) {
        $("#butts").vkHint({
            msg:"<DIV class=red>" + msg + "</DIV>",
            top:-37,
            left:157,
            indent:50,
            show:1,
            remove:1
        });
    }
}

VK.addCallback('onOrderSuccess', function(order_id) {
    create.order.id = order_id;
    vkCreateGo();
});

function wallPost(obj) {
    var msg = $("#vkSel_rubrika INPUT:first").val() + ' � ' +
        (obj.podrubrika > 0 ? $("#vkSel_podrubrika INPUT:first").val() + ' � ' : '') +
        obj.txt +
        (obj.telefon ? '\n���.: ' + obj.telefon : '') +
        '\n\n\n&#128221; vk.com/kupezz';
    var send = {
        message:msg
    };
    VK.api('wall.post', send, function(data) {
        vkMsgOk('���������� ��������� �� ����� ����� ��������.');
    });
}
