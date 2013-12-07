

// Номера выпусков газеты
function setupGazNomer() {
    $("#edit").html("<IMG src=/img/upload.gif>");
    var html="<DIV id=gazNomer>" +
        "<DIV class=headName>Управление номерами выпусков газеты</DIV>" +
        "<DIV id=dopMenu>";
        var FY = (new Date()).getFullYear();
        var year = G.setup.year;
        for(var y = year.begin; y <= year.end + 1; y++) {
            html += "<A class=link" + (y == FY ? 'Sel' : '') + " onclick=gazNomerGet(" + y + ");>" +
                "<I></I><B></B><DIV>" + y + "</DIV><B></B><I></I></A>";
        }
        html+="<DIV style=clear:both;></DIV></DIV>" +

            "<DIV id=spisok></DIV>" +
            "</DIV>";
        $("#edit").html(html);
        gazNomerGet(FY, 0);
} // end of setupGazNomer()

function gazNomerGet(year, id) {
    progressShow();
    var A = $("#dopMenu A");
    A.attr('class','link');
    for(var n = 0; n < A.length; n++)
        if(A.eq(n).find("DIV:first").html() == year)
            A.eq(n).attr('class','linkSel');
    $.getJSON("/view/gazeta/setup/gazeta_nomer/AjaxGNSpisokGet.php?"+G.values+"&year="+year+"&id="+id,function(res){
        progressHide();
        if(res.spisok.length > 0) {
            var html = "<A val=add_>Добавить новый номер</a><br /><br />" +
                "<TABLE cellpadding='0' cellspacing='0' class=tabSpisok><TR>" +
                "<TH>Номер<BR>выпуска" +
                //"<TH>Дни недели" +
                "<TH>День<BR>отправки<BR>в печать" +
                "<TH>День выхода" +
                "<TH>Заявки" +
                "<TH>Настройки";
            for(var n = 0; n < res.spisok.length; n++) {
                var sp = res.spisok[n];
                html += "<TR id=gn" + sp.general_nomer + " class='" + sp.grey + (id == sp.general_nomer ? ' yellow' : '') + "'>" +
                    "<TD align=center><B>" + sp.week_nomer + "</B> (<SPAN>" + sp.general_nomer + "</SPAN>)" +
                    //"<TD align=right>" + sp.day_txt +
                    "<TD align=right>" + sp.day_print +
                    "<TD align=right>" + sp.day_public +
                    "<TD align=center>" + (sp.zayav_count > 0 ? sp.zayav_count : '') +
                    "<TD class=set><DIV class=img_edit val=edit_" + n + "></DIV>" +
                                  "<DIV class=img_del val=del_" + n + "></DIV>";
            }
            html += "</TABLE>";
            $("#spisok")
                .html(html)
                .on('click', function (e) {
                    var val = $(e.target).attr('val')
                    if (val) {
                        val = val.split('_');
                        switch (val[0]) {
                            case 'add': gazNomerAdd(year); break;
                            case 'edit': gazNomerEdit(year, res.spisok[val[1]]); break;
                            case 'del': gazNomerDel(year, res.spisok[val[1]]); break;
                        }
                    }
                });
            $("#spisok .yellow").mouseover(function(){ $(this).removeClass('yellow'); });
        } else {
            html = "Номера газет, которые будут выходить в " + year + " году, не определены." +
                "<BR><BR><A onclick=gazNomerSpisokCreate(" + year + ");><B>Создать список</B>...</A>";
            $("#spisok").html(html);
        }
        frameBodyHeightSet();
    });
} // end of gazNomerGet()

// Добавление номера газеты
function gazNomerAdd(year) {
    html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Номер выпуска:<TD>" +
        "<INPUT type=text id=week_nomer style=width:15px;text-align:right; maxlength=2'>&nbsp;" +
        "<INPUT type=text id=general_nomer style=width:20px;text-align:right; maxlength=3'>" +
        "<TR><TD class=tdAbout>День отправки в печать:<TD><INPUT type=hidden id=day_print>" +
        "<TR><TD class=tdAbout>День выхода:<TD><INPUT type=hidden id=day_public>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        width:340,
        head:'Добавление номера газеты',
        butSubmit:'Внести',
        content:html,
        submit:function () {
            var send = {
                week_nomer:$("#week_nomer").val(),
                general_nomer:$("#general_nomer").val(),
                day_print:$("#day_print").val(),
                day_public:$("#day_public").val()
            };
            var msg;
            if(!/^[0-9]+$/.test(send.week_nomer)) { msg = 1; $("#week_nomer").focus(); }
            else if(!/^[0-9]+$/.test(send.general_nomer)) { msg = 1; $("#general_nomer").focus(); }
            else {
                dialog.process();
                $.post("/view/gazeta/setup/gazeta_nomer/AjaxGNAdd.php?" + G.values, send, function (res) {
                    if (res.save == 0) {
                        dialog.process_cancel();
                        $("#setup_dialog .bottom:first").vkHint({
                            msg:'<SPAN class=red>Номер выпуска не может быть равным ' + send.general_nomer + ',<br />так как он занят другим номером газеты.</SPAN>',
                            top:-61,
                            left:85,
                            indent:40,
                            show:1,
                            remove:1
                        });
                    } else {
                        dialog.close();
                        gazNomerGet(year, send.general_nomer);
                        vkMsgOk("Новый номер внесён.");
                    }
                },'json');
            }
            if (msg) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не корректно введён номер выпуска.<BR>Используйте цифры.</SPAN>',
                    top:-61,
                    left:85,
                    indent:40,
                    show:1,
                    remove:1
                });
            }
        } // end of submit()
    }).o;
    $("#day_print").vkCalendar({lost:1});
    $("#day_public").vkCalendar({lost:1});
} // end of gazNomerAdd()

// Редактирование номера газеты
function gazNomerEdit(year, sp) {
    html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Номер выпуска:<TD>" +
            "<INPUT type=text id=week_nomer style=width:15px;text-align:right; maxlength=2 value='" + sp.week_nomer + "'>&nbsp;" +
            "<INPUT type=text id=general_nomer style=width:20px;text-align:right; maxlength=3 value='" + sp.general_nomer + "'>" +
        "<TR><TD class=tdAbout>День отправки в печать:<TD><INPUT type=hidden id=day_print value='" + sp.day_print_val + "'>" +
        "<TR><TD class=tdAbout>День выхода:<TD><INPUT type=hidden id=day_public value='" + sp.day_public_val + "'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        width:340,
        head:'Редактирование данных номера газеты',
        butSubmit:'Сохранить',
        content:html,
        submit:function () {
            var send = {
                week_nomer:$("#week_nomer").val(),
                general_nomer:$("#general_nomer").val(),
                general_nomer_prev:sp.general_nomer,
                day_print:$("#day_print").val(),
                day_public:$("#day_public").val()
            };
            var msg;
            if(!/^[0-9]+$/.test(send.week_nomer)) { msg = 1; $("#week_nomer").focus(); }
            else if(!/^[0-9]+$/.test(send.general_nomer)) { msg = 1; $("#general_nomer").focus(); }
            else {
                dialog.process();
                $.post("/view/gazeta/setup/gazeta_nomer/AjaxGNEdit.php?" + G.values, send, function (res) {
                     if (res.save == 0) {
                         dialog.process_cancel();
                         $("#setup_dialog .bottom:first").vkHint({
                             msg:'<SPAN class=red>Номер выпуска не может быть равным ' + send.general_nomer + ',<br />так как он занят другим номером газеты.</SPAN>',
                             top:-61,
                             left:85,
                             indent:40,
                             show:1,
                             remove:1
                         });
                     } else {
                         dialog.close();
                         gazNomerGet(year, send.general_nomer);
                         vkMsgOk("Данные изменены!");
                     }
                },'json');
            }
            if (msg) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не корректно введён номер выпуска.<BR>Используйте цифры.</SPAN>',
                    top:-61,
                    left:85,
                    indent:40,
                    show:1,
                    remove:1
                });
            }
        } // end of submit()
    }).o;
    $("#day_print").vkCalendar({lost:1});
    $("#day_public").vkCalendar({lost:1});
} // end of gazNomerEdit()

function gazNomerDel(year, sp) {
    var dialog = $("#setup_dialog").vkDialog({
        width:250,
        head:'Удаление',
        butSubmit:'Удалить',
        content:"<CENTER>Подтвердите удаление<BR>номера газеты <B>" + sp.week_nomer  + "</B> (" + sp.general_nomer + ").</CENTER>",
        submit:function () {
            dialog.process();
            $.post("/view/gazeta/setup/gazeta_nomer/AjaxGNDel.php?" + G.values, {general_nomer:sp.general_nomer}, function (res) {
                dialog.close();
                gazNomerGet(year, 0);
                vkMsgOk("Удаление успешно произведено!");
                frameBodyHeightSet();
            },'json');
        }
    }).o;
} // end of gazNomerDel()

function gazNomerSpisokCreate(year) {
    html = "<DIV class=gnInfo>Для создания списка номеров газет <B>" + year + "</B> года " +
            "укажите данные <B>первого номера</B>, " +
            "который будет выходить в этом году. " +
            "Все поля обязательны для заполнения.</DIV>" +
        "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Первый номер выпуска:<TD>" +
            "<INPUT type=text id=week_nomer style=width:15px;text-align:right; maxlength=2 value=1>&nbsp;" +
            "<INPUT type=text id=general_nomer style=width:20px;text-align:right; maxlength=3 value=" + (G.setup.gn_max + 1) + ">" +
        "<TR><TD class=tdAbout>Дни отправки в печать:<TD><INPUT type=hidden id=day_print>" +
        "<TR><TD class=tdAbout>Дни выхода:<TD><INPUT type=hidden id=day_public>" +
        "<TR><TD class=tdAbout>Первый день выхода:<TD><INPUT type=hidden id=first_day_public value='" + year + "-01-01'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        width:340,
        head:'Создание списка номеров газеты',
        butSubmit:'Создать',
        content:html,
        submit:function () {
            var send = {
                year:year,
                week_nomer:$("#week_nomer").val(),
                general_nomer:$("#general_nomer").val(),
                day_print:$("#day_print").val(),
                day_public:$("#day_public").val(),
                first_day_public:$("#first_day_public").val()
            };
            var msg;
            if(!/^[0-9]+$/.test(send.week_nomer)) { msg = 'Не корректно введён номер выпуска.'; $("#week_nomer").focus(); }
            else if(!/^[0-9]+$/.test(send.general_nomer)) { msg = 'Не корректно введён номер выпуска.'; $("#general_nomer").focus(); }
            else if(send.general_nomer <= G.setup.gn_max) { msg = 'Номер выпуска не может быть меньше ' + (G.setup.gn_max + 1); $("#general_nomer").focus(); }
            else {
                dialog.process();
                $.post("/view/gazeta/setup/gazeta_nomer/AjaxGNSpisokCreate.php?" + G.values, send, function (res) {
                    dialog.close();
                    gazNomerGet(year, send.general_nomer);
                    vkMsgOk("Список номеров создан.");
                }, 'json');
            }
            if (msg) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>' + msg + '</SPAN>',
                    top:-47,
                    left:83,
                    indent:40,
                    show:1,
                    remove:1
                });
            }
        }
    }).o;
    var weeks = [
        {uid:0,title:'Понедельник'},
        {uid:1,title:'Вторник'},
        {uid:2,title:'Среда'},
        {uid:3,title:'Четверг'},
        {uid:4,title:'Пятница'},
        {uid:5,title:'Суббота'},
        {uid:6,title:'Воскресенье'}
    ];
    $("#day_print").vkSel({width:100, value:1, spisok:weeks});
    $("#day_public").vkSel({width:100, value:4, spisok:weeks});
    $("#first_day_public").vkCalendar();
} // end of gazNomerSpisokCreate()



