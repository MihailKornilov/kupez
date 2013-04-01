$("BODY").append("<div id=dialog_my></div>");
var myob = { spisok:[] };

$("#obSpisok").click(function (e) {
    var val = $(e.target).attr('val');
    if (val) {
        var arr = val.split('_');
        var sp = myob.spisok[arr[1]];
        switch (arr[0]) {
            case 'edit':   G.obEdit(sp); break;
            case 'archiv': G.goArchiv(sp); break;
            case 'del':    G.obDel(sp); break;
            case 'user':   G.spisok.print({viewer_id:arr[1]}); break;
        }
    }
});


G.obEdit = function (sp) {
    var html = "<TABLE cellpadding=0 cellspacing=8 id=ob_edit>" +
    "<TR><TD class=tdAbout>Рубрика:<TD><INPUT TYPE=hidden id=rubrika value=" + sp.rubrika + ">" +
                                      "<INPUT TYPE=hidden id=podrubrika value=" + sp.podrubrika + ">" +
    "<TR><TD class=tdAbout valign=top>Текст:                <TD><TEXTAREA id=txt>" + sp.txt.replace(/<BR>/g,"\n") + "</TEXTAREA>" +
    "<TR><TD class=tdAbout valign=top><INPUT TYPE=hidden id=images value='" + sp.file + "'><TD id=upload>" +
    "<TR><TD class=tdAbout>Контактные телефоны:             <TD><INPUT TYPE=text id=telefon maxlength=200 value='" + sp.telefon + "'>" +
    "<TR><TD class=tdAbout valign=top>Регион:               <TD><INPUT TYPE=hidden id=countries value=" + sp.country_id + ">" +
                                                               "<INPUT TYPE=hidden id=cities value=" + sp.city_id + ">" +
    "<TR><TD class=tdAbout>Показывать имя из VK:            <TD><INPUT TYPE=hidden id=viewer_id_show value=" + sp.viewer_id_show + ">" +
    "<TR id=active><TD colspan=3>Объявление будет размещено сроком на 1 месяц. <A>Отправить в архив</A>" +
    "<TR id=archiv><TD colspan=3>Объявление будет отправлено в архив.<A>Сделать активным</A>" +
    "</TABLE>";
    var dialog = $("#dialog_my").vkDialog({
        width:520,
        top:30,
        head:"Редактирование объявления",
        content:html,
        butSubmit:"Сохранить",
        submit:save
    }).o;

    var active = sp.active;
    $("#" + (active == 1 ? 'active': 'archiv')).show();

    // вывод списка рубрик и подрубрик
    $("#rubrika").vkSel({
        width:120,
        display:'inline-block',
        spisok:G.rubrika_spisok,
        func:function(uid){
            $("#podrubrika").val(0);
            $("#vkSel_podrubrika").remove();
            podrubPrint(uid);
        }
    });

    podrubPrint($("#rubrika").val());

    function podrubPrint(uid) {
        if(G.podrubrika_spisok[uid]) {
            $("#podrubrika").vkSel({
                width:201,
                display:'inline-block',
                title0:'Подрубрика не указана',
                spisok:G.podrubrika_spisok[uid]
            });
        }
    }

    // автоматическое расширение текствого поля
    $("#txt")
        .autosize({callback:frameBodyHeightSet})
        .focus();

    if (sp.file) {
        $("#upload").html("<img src=" + sp.file + "s.jpg><div class=img_del />");
        $("#upload .img_del").click(fotoUpload);
    } else fotoUpload();

    // загрузка изображений
    function fotoUpload() {
        $("#images").val('');
        $("#upload").fotoUpload({
            func:function (res) {
                $("#images").val(res.link);
                $("#upload").html(res.img + "<div class=img_del />");
                $("#upload .img_del").click(fotoUpload);
            }
        });
    }

    // Вывод списка стран
    var country = $("#countries").vkSel({
        bottom:4,
        width:180,
        spisok:G.countries_spisok,
        func:citiesGet
    });

    // Вывод списка городов
    var city = $("#cities").vkSel({
        width:180,
        title0:'Город не указан',
        spisok:[],
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
        }
    }).o;

    citiesGet(sp.country_id);

    // Получение списка городов по стране
    function citiesGet(id) {
        city.process();
        VK.api('places.getCities',{country:id}, function (data) {
            var d = data.response;
            var cur = 0;
            for(var n = 0; n < d.length; n++) {
                d[n].uid = d[n].cid;
                if (sp.city_id > 0 && d[n].cid == sp.city_id)
                    cur = sp.city_id;
            }
            if (cur == 0 && sp.city_id > 0)
                d.unshift({uid:sp.city_id, title:sp.city_name})
            city.spisok(d);
            if (sp.city_id > 0)
                city.val(sp.city_id);
        });
    }

    // Показывать имя из VK
    $("#viewer_id_show").myCheck();

    $("#active A").click(function () {
        $("#active").hide();
        $("#archiv").show();
        active = 0;
    });

    $("#archiv A").click(function () {
        $("#active").show();
        $("#archiv").hide();
        active = 1;
    });


    function save() {
        var country_id = $("#countries").val();
        var country_name = $("#vkSel_countries INPUT:first").val();
        var city_id = $("#cities").val();
        var city_name = $("#vkSel_cities INPUT:first").val();

        var obj = {
            id:sp.id,
            rubrika:$("#rubrika").val(),
            podrubrika:$("#podrubrika").val(),
            txt:$("#txt").val(),
            telefon:$("#telefon").val(),
            file:$("#images").val(),
            country_id:country_id,
            country_name:country_id != '0' ? country_name : '',
            city_id:city_id,
            city_name:city_id != '0' ? city_name : '',
            viewer_id:sp.viewer_id,
            viewer_id_show:$("#viewer_id_show").val(),
            active:active
        };

        if (!obj.txt) {
            $("#dialog_my .bottom:first").vkHint({
                msg:"<DIV class=red>Введите текст объявления</DIV>",
                top:-48,
                left:164,
                indent:50,
                show:1,
                remove:1
            });
        } else {
            dialog.process();
            $.post("/view/ob/my/AjaxObEdit.php?" + G.values, obj, function (res) {
                for (var k in obj) { sp[k] = obj[k]; }
                sp.txt = sp.txt.replace(/\n/g,"<BR>");
                sp.day_last = res.dtime;
                //   sp.viewer_name = res.viewer_name;
                $("#unit_" + sp.id).html(G.spisok.unit(sp));
                dialog.close();
                frameBodyHeightSet();
            },'json');
        }
    }

};

G.goArchiv = function (sp) {
    $("#unit_" + sp.id + " A:first").html("<IMG src=/img/upload.gif>");
    $.getJSON("/view/ob/my/AjaxObGoArchiv.php?" + G.values + "&id=" + sp.id, function () {
        sp.active = 0;
        $("#unit_" + sp.id).html(G.spisok.unit(sp));
    });
};

G.obDel = function (sp) {
    var dialog = $("#dialog_my").vkDialog({
        width:300,
        head:"Удаление объявления",
        content:"<CENTER>После удаления объявления<BR>его невозможно будет восстановить.<BR><BR>Подтвердите удаление.</CENTER>",
        butSubmit:"Удалить",
        submit:function () {
            dialog.process();
            $.getJSON("/view/ob/my/AjaxObDel.php?" + G.values + "&id=" + sp.id, function () {
                $("#unit_" + sp.id).remove();
                dialog.close();
                vkMsgOk("Объявление удалено.");
                frameBodyHeightSet();
            });
        }
    }).o;
};


G.spisok.unit = function (sp) {
    var name = '';
    if (sp.viewer_id_show > 0) { name = "<A href='http://vk.com/id" + sp.viewer_id+"' target=_vk>" + sp.viewer_name + "</A>"; }
    var city = '';
    if (sp.city_name) { city = sp.country_name + ", " + sp.city_name; }


    return "<DIV class=head>" + sp.dtime + (sp.active == 1 ? "<TT>" + sp.day_last + "</TT>" : "<EM>В архиве</EM>") +

    (G.spisok.values.viewer_id_add == 0 ? "<A class=user onclick=goUser(" + sp.viewer_id + "," + sp.num + ");>" + sp.viewer_name + "</A>" : '') +

    "<H2><A style=visibility:" + (sp.active == 1 ? 'visible' : 'hidden') + "; val=archiv_" + sp.num + ">в архив</A>" +
    "<DIV class=img_edit val=edit_" + sp.num + "></DIV>" +
    "<DIV class=img_del val=del_" + sp.num + "></DIV></H2></DIV>" +

    "<DIV class='ob " + sp.dop + (sp.active == 0 ? ' archiv' : '') + "'>" +
    "<TABLE cellpadding=0 cellspacing=0 width=100%>" +
        "<TR><TD class=txt>" +
            "<SPAN class=aRub>" + G.rubrika_ass[sp.rubrika] + "</SPAN><U>»</U>" +
            (sp.podrubrika > 0 ? "<SPAN class=aRub>" + G.podrubrika_ass[sp.podrubrika] + "</SPAN><U>»</U>" : '') +
            sp.txt +
            (sp.telefon ? " <DIV class=tel>" + sp.telefon + "</DIV>" : '') +
            "<DIV class=adres>" + city + name + "</DIV>" +
            (sp.file ? "<TD class=foto><IMG src=" + sp.file.split('_')[0] + "s.jpg onclick=G.fotoView('" + sp.file + "');>" : '') +
    "</TABLE></DIV>";
}



