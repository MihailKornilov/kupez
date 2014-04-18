fotoUpload();

// загрузка изображений
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
            txt:"Вы можете загрузить до 4 изображений.",
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


$("#pay_service").myCheck({func:function (id) {
    $("#payContent").css('display', id == 1 ? 'block' : 'none');
    if (id == 0) {
        $("#dop").val(0);
        $("#top").val(0);
        $("#top_week").css('visibility', 'hidden');
    } else {
        // дополнительные сервисы
        $("#dop").vkRadio({
            width:200,
            bottom:6,
            spisok:[
                {uid:0,title:'<SPAN style=color:#888;>Не выделять</SPAN>'},
                {uid:'ramka',title:'Обвести в рамку'},
                {uid:'bold',title:'Выделить жирным шрифтом'},
                {uid:'black',title:'На чёрном фоне'}
            ],
            func:function () { preview(); printButton(); }
        });
        // поднять объявление
        $("#top").myCheck({func:function (id) {
            $("#top_week").css('visibility', id == 1 ? 'visible' : 'hidden');
            if (id == 1) {
                create.top_week = 1;
                $("#top_week .inp").html(1);
                $("#top_week .end").html('ю');
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
    $("#top_week .end").html(G.end(create.top_week, 'ю', 'и', 'ь'));
    printButton();
});

$("#butts .vkButton BUTTON")[0].onclick = vkCreateGo;

function printButton() {
    create.order.votes = 0;
    if ($("#dop").val() != '0') { create.order.votes++; }
    if ($("#top").val() > 0) { create.order.votes += create.top_week; }
    var v = create.order.votes;
    $(".vkButton SPAN").html(v > 0 ? " за " + v + " голос" + G.end(v, ['', 'а', 'ов']) : '');
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
    if (obj.rubrika == 0) { msg = "Не указана рубрика"; }
    else if (!obj.txt) { msg = "Введите текст объявления"; }
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
    var msg = $("#vkSel_rubrika INPUT:first").val() + ' » ' +
        (obj.podrubrika > 0 ? $("#vkSel_podrubrika INPUT:first").val() + ' » ' : '') +
        obj.txt +
        (obj.telefon ? '\nТел.: ' + obj.telefon : '') +
        '\n\n\n&#128221; vk.com/kupezz';
    var send = {
        message:msg
    };
    VK.api('wall.post', send, function(data) {
        vkMsgOk('Объявление размещено на стене Вашей страницы.');
    });
}
