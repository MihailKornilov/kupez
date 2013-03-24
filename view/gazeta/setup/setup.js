function progressShow() { $(".headName:first").find("IMG").remove().end().append("<IMG src=/img/upload.gif>"); }
function progressHide() { $(".headName:first IMG").remove(); }


function setupSet(id) {
    $(".razdel").find(".help").remove();
    switch(id) {
        case '1':
        default: setupPerson(); break;
        case '2': setupRubrika(); break;
        case '7': setupPodRubrika(); break;
        case '3': setupGazNomer(); break;
        case '4': setupSmKvCost(); break;
        case '5': setupSkidka(); break;
        case '6': setupObDop(); break;
        case '8': setupAccess(); break;
        case '9': setupObLenght(); break;
        case '10': setupRashodCategory(); break;
        case '11': setupMoneyType(); break;
    }
} // end of setupSet()

// Заявители
function setupPerson() {
    $(".razdel")
        .find(".help").remove()
        .end().append("<DIV class=help>Типы заявителей используются для разделения клиентов на категории, такие как '<B>Частный клиент</B>', '<B>Организация</B>' и тп.</DIV>");
    var html="<DIV id=person>" +
        "<DIV class=headName>Настройки типов заявителей</div>" +
        "<A onclick=personAdd();>Добавить новый тип заявителя</A>" +
        "<DIV id=person_table></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/person/AjaxPersonGet.php?" + G.values, function(res){
        progressHide();
        if(res.length > 0) {
            $("#person_table").html("<IMG src=/img/upload.gif>");
            var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>" +
                "<TR><TH class=name>Наименование" +
                    "<TH class=colvo>Кол-во<BR>клиентов" +
                    "<TH class=set>Настройки" +
                "</TABLE>" +
                "<DL id=person_drag>";
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<DD id=" + sp.id + ">" +
                    "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>" +
                    "<TD class=name>" + sp.name +
                    "<TD class=colvo>" + (sp.col>0?sp.col:'') +
                    "<TD class=set><DIV class=img_edit onclick=personEdit("+sp.id+");></DIV>" +
                        "<DIV class=img_del onclick=personDel("+sp.id+");></DIV></TABLE>";
                }
            html += "</DL>";
            $("#person_table").html(html);
            $("#person_drag").sortable({axis:'y',update:function () {
                var DD=$("#person_drag DD");
                var LEN=DD.length;
                var VAL=DD.eq(0).attr('id');
                if(LEN>1) {
                    progressShow();
                    for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
                    $.getJSON("/view/gazeta/setup/person/AjaxPersonSort.php?" + G.values + "&val=" + VAL, progressHide);
                }
            }});
        } else $("#person_table").html("Список заявителей пуст.");
        frameBodyHeightSet();
    });
} // end of setupPerson()

// Добавление нового заявителя
function personAdd() {
    var html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=person_name style=width:200px;>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Внесение нового заявителя',
        content:html,
        focus:'#person_name',
        submit:function () {
            var send = {name:$("#person_name").val()}
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>Не указано наименование.</SPAN>', top:-47, left:94, indent:40, show:1, remove:1});
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/person/AjaxPersonAdd.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupPerson();
                    vkMsgOk("Новое наименование заявителя добавлено!");
                },'json');
            }
        }
    }).o;
} // end of personAdd()

// Редактирование заявителя
function personEdit(id) {
    var html="<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=person_name style=width:200px; value='"+$("#"+id+" .name").html()+"'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        top:100,
        head:'Редактирование наименования заявителя',
        butSubmit:'Сохранить',
        content:html,
        submit:function () {
            var send = {
                id:id,
                name:$("#person_name").val()
            };
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>Не указано наименование.</SPAN>', top:-47, left:94, indent:40, show:1, remove:1});
            }  else {
                dialog.process();
                $.post("/view/gazeta/setup/person/AjaxPersonEdit.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupPerson();
                    vkMsgOk("Наименование заявителя изменено!");
                },'json');
            }
        }
    }).o;
} // end of personEdit()

// Удаление заявителя
function personDel(id) {
    var COLVO = $("#"+id+" .colvo").html();
    var html = "Удаление категории заявителей <B>"+$("#"+id+" .name").html()+"</B>.";
    if(COLVO) {
        html += "<TABLE cellspacing=0 cellpadding=0 style=margin-top:10px;>" +
            "<TR><TD style=padding-right:5px;>Переместить клиентов в<TD><INPUT type=hidden id=person_ost value=0></TABLE>";
        }
    var dialog = $("#setup_dialog").vkDialog({
        width:350,
        head:'Удаление',
        butSubmit:'Удалить',
        content:html,
        submit:function(){
            var send = {
                del:id,
                ost:0
            };
            if(COLVO) send.ost = $("#person_ost").val();
            if(COLVO && send.ost == 0) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>Выберите новую категорию заявителя.</SPAN>',top:-47,left:128, top:-47, left:94, indent:40, show:1, remove:1});
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/person/AjaxPersonDel.php?"+G.values, send, function () {
                    for(var n = 0; n < G.person_spisok.length; n++) {
                        if (G.person_spisok[n].uid == id) {
                            G.person_spisok.splice(n, 1);
                            break;
                        }
                    }
                    dialog.close();
                    setupPerson();
                    vkMsgOk("Удаление успешно произведено!");
                });
            }
        }
    }).o;
    if(COLVO) {
        var spisok = G.person_spisok;
        for(var n = 0; n < spisok.length; n++) {
            if (spisok[n].uid == id) {
                spisok.splice(n, 1);
                break;
            }
        }
        $("#person_ost").vkSel({
            width:180,
            title0:'Выберите категорию',
            spisok:spisok
        });
    }
} // end of personDel()









// Категории расходов
function setupRashodCategory() {
    var html="<DIV id=rashod>" +
        "<DIV class=headName>Настройки категорий расходов</div>" +
        "<A onclick=rashodCategoryAdd();>Добавить новую категорию</A>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/rashod_category/AjaxRashodGet.php?" + G.values, function(res){
        progressHide();
        if(res.length > 0) {
            var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>" +
                "<TR><TH class=name>Наименование" +
                "<TH class=set>Настройки" +
                "</TABLE>" +
                "<DL id=drag>";
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<DD id=" + sp.id + ">" +
                    "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>" +
                    "<TD class=name>" + sp.name +
                    "<TD class=set><DIV class=img_edit onclick=rashodCategoryEdit("+sp.id+");></DIV>" +
                    "<DIV class=img_del onclick=rashodCategoryDel("+sp.id+");></DIV></TABLE>";
            }
            html += "</DL>";
            $("#spisok").html(html);
            $("#drag").sortable({axis:'y',update:function () {
                var DD=$("#drag DD");
                var LEN=DD.length;
                var VAL=DD.eq(0).attr('id');
                if(LEN>1) {
                    progressShow();
                    for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
                    $.getJSON("/view/gazeta/setup/rashod_category/AjaxRashodSort.php?" + G.values + "&val=" + VAL, progressHide);
                }
            }});
        } else $("#spisok").html("Список пуст.");
        frameBodyHeightSet();
    });
} // end of setupRashodCategory()

function rashodCategoryAdd() {
    var html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=rashod_name style=width:200px;>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Внесение новой категории',
        content:html,
        focus:'#rashod_name',
        submit:function () {
            var send = {name:$("#rashod_name").val()}
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>Не указано наименование.</SPAN>', top:-47, left:94, indent:40, show:1, remove:1});
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/rashod_category/AjaxRashodAdd.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupRashodCategory();
                    vkMsgOk("Новая категория добавлена.");
                },'json');
            }
        }
    }).o;
} // end of rashodCategoryAdd()

function rashodCategoryEdit(id) {
    var html="<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=name style=width:200px; value='"+$("#"+id+" .name").html()+"'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Редактирование',
        butSubmit:'Сохранить',
        content:html,
        submit:function () {
            var send = {
                id:id,
                name:$("#name").val()
            };
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>Не указано наименование.</SPAN>', top:-47, left:94, indent:40, show:1, remove:1});
            }  else {
                dialog.process();
                $.post("/view/gazeta/setup/rashod_category/AjaxRashodEdit.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupRashodCategory();
                    vkMsgOk("Наименование категории изменено!");
                },'json');
            }
        }
    }).o;
} // end of rashodCategoryEdit()

function rashodCategoryDel(id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:300,
        head:'Удаление',
        butSubmit:'Удалить',
        content:"<CENTER>Подтвердите удаление категории '<B>"+$("#"+id+" .name").html()+"</B>'.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/rashod_category/AjaxRashodDel.php?" + G.values, {id:id}, function(res){
                dialog.close();
                setupRashodCategory();
                vkMsgOk("Удаление успешно произведено!");
            },'html');
        }
    }).o;
} // end of rashodCategoryDel()
















// Виды платежей
function setupMoneyType() {
    var html="<DIV id=money_type>" +
        "<DIV class=headName>Настройки видов платежей</div>" +
        "<A onclick=moneyTypeAdd();>Добавить новый вид платежа</A>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/money_type/AjaxMoneyTypeGet.php?" + G.values, function(res){
        progressHide();
        if(res.length > 0) {
            var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>" +
                "<TR><TH class=name>Наименование" +
                "<TH class=set>Настройки" +
                "</TABLE>" +
                "<DL id=drag>";
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<DD id=" + sp.id + ">" +
                    "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>" +
                    "<TD class=name>" + sp.name +
                    "<TD class=set><DIV class=img_edit onclick=moneyTypeEdit("+sp.id+");></DIV>" +
                    "<DIV class=img_del onclick=moneyTypeDel("+sp.id+");></DIV></TABLE>";
            }
            html += "</DL>";
            $("#spisok").html(html);
            $("#drag").sortable({axis:'y',update:function () {
                var DD=$("#drag DD");
                var LEN=DD.length;
                var VAL=DD.eq(0).attr('id');
                if(LEN>1) {
                    progressShow();
                    for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
                    $.getJSON("/view/gazeta/setup/money_type/AjaxMoneyTypeSort.php?" + G.values + "&val=" + VAL, progressHide);
                }
            }});
        } else $("#spisok").html("Список пуст.");
        frameBodyHeightSet();
    });
} // end of setupMoneyType()

function moneyTypeAdd() {
    var html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=money_name style=width:200px;>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Внесение нового вида платежа',
        content:html,
        focus:'#money_name',
        submit:function () {
            var send = {name:$("#money_name").val()}
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>Не указано наименование.</SPAN>', top:-47, left:94, indent:40, show:1, remove:1});
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/money_type/AjaxMoneyTypeAdd.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupMoneyType();
                    vkMsgOk("Новый тип платежа дбавлен.");
                },'json');
            }
        }
    }).o;
} // end of moneyTypeAdd()

function moneyTypeEdit(id) {
    var html="<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=name style=width:200px; value='"+$("#"+id+" .name").html()+"'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Редактирование',
        butSubmit:'Сохранить',
        content:html,
        submit:function () {
            var send = {
                id:id,
                name:$("#name").val()
            };
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>Не указано наименование.</SPAN>', top:-47, left:94, indent:40, show:1, remove:1});
            }  else {
                dialog.process();
                $.post("/view/gazeta/setup/money_type/AjaxMoneyTypeEdit.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupMoneyType();
                    vkMsgOk("Наименование вида платежа изменено!");
                },'json');
            }
        }
    }).o;
} // end of moneyTypeEdit()

function moneyTypeDel(id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:300,
        head:'Удаление',
        butSubmit:'Удалить',
        content:"<CENTER>Подтвердите удаление вида платежа '<B>"+$("#"+id+" .name").html()+"</B>'.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/money_type/AjaxMoneyTypeDel.php?" + G.values, {id:id}, function(res){
                dialog.close();
                setupMoneyType();
                vkMsgOk("Удаление успешно произведено!");
            },'html');
        }
    }).o;
} // end of moneyTypeDel()


















// Список рубрик
function setupRubrika() {
    var html="<DIV id=rubrika>" +
        "<DIV class=headName>Настройка рубрик</DIV>" +
        "<A onclick=rubrikaAdd();>Добавить новую рубрику</A>" +
        "<DIV id=rubrika_table></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/rubrika/AjaxRubrikaGet.php?" + G.values, function (res) {
        progressHide();
        if(res.length > 0) {
            $("#rubrika_table").html("<IMG src=/img/upload.gif>");
            var html="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH class=name>Наименование<TH class=set>Настройки</TABLE>";
            html+="<DL id=rubrika_drag>";
            for(var n=0; n < res.length; n++) {
                var sp = res[n];
                html+="<DD id="+sp.id+"><TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>";
                html+="<TD class=name>"+sp.name;
                html+="<TD class=set><DIV class=img_edit onclick=rubrikaEdit("+sp.id+");></DIV><DIV class=img_del onclick=rubrikaDel("+sp.id+");></DIV></TABLE>";
            }
            html+="</DL>";
            $("#rubrika_table").html(html);
            $("#rubrika_drag").sortable({axis:'y',update:function(){
                var DD=$("#rubrika_drag DD");
                var LEN=DD.length;
                var VAL=DD.eq(0).attr('id');
                if(LEN > 1) {
                    progressShow();
                    for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
                    $.getJSON("/view/gazeta/setup/rubrika/AjaxRubrikaSort.php?"+G.values+"&val="+VAL, progressHide);
                }
            }});
        } else $("#rubrika_table").html("Рубрики не внесены.");
        frameBodyHeightSet();
    });
} // end of setupRubrika()

function rubrikaAdd() {
    html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=rubrika_name style=width:200px;>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Внесение новой рубрики',
        content:html,
        focus:'#rubrika_name',
        submit:function () {
            var send = {name:$("#rubrika_name").val()};
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не указано наименование.</SPAN>',
                    top:-47,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/rubrika/AjaxRubrikaAdd.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupRubrika();
                    vkMsgOk("Новая рубрика добавлена!");
                },'json');
            }
        }
    }).o;
} // end of rubrikaAdd()

function rubrikaEdit(id) {
    html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=rubrika_name style=width:200px; value='"+$("#"+id+" .name").html()+"'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Редактирование рубрики',
        butSubmit:'Сохранить',
        content:html,
        submit:function(){
            var send = {id:id,name:$("#rubrika_name").val()};
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не указано наименование.</SPAN>',
                    top:-47,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/rubrika/AjaxRubrikaEdit.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupRubrika();
                    vkMsgOk("Наименование рубрики изменено!");
                },'json');
            }
        }
    }).o;
} // end of rubrikaEdit()

function rubrikaDel(id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:300,
        head:'Удаление',
        butSubmit:'Удалить',
        content:"<CENTER>Подтвердите удаление рубрики '<B>"+$("#"+id+" .name").html()+"</B>'.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/rubrika/AjaxRubrikaDel.php?"+G.values,{id:id},function(res){
                dialog.close();
                setupRubrika();
                vkMsgOk("Удаление успешно произведено!");
            },'json');
        }
    }).o;
} // end of rubrikaDel()





















// Подрубрики
function setupPodRubrika() {
    var html="<DIV id=podrubrika>" +
        "<DIV class=headName>Настройка подрубрик</DIV>" +
        "<TABLE cellpadding=0 cellspacing=10>" +
            "<TR><TD id=rubrika_id>" +
            "<TD valign=top><div id=podRubLinkAdd></div><DIV id=podRub_table></DIV>" +
        "</TABLE></DIV>";
    $("#edit").html(html);
    $("#rubrika_id").infoLink({
        spisok:G.rubrika_spisok,
        func:podRubrikaGet
    });
    podRubrikaGet(G.rubrika_spisok[0].uid);
    frameBodyHeightSet();
} // end of setupPodRubrika()

function podRubrikaGet(id) {
    progressShow();
    var RUBNAME = $("#rubrika_id .sel:first").html();
    $("#podRubLinkAdd").html("<A onclick=\"podRubrikaAdd(" + id + ", '" + RUBNAME + "');\">Добавить новую подрубрику для рубрики <B>" + RUBNAME + "</B></A>");
    $.getJSON("/view/gazeta/setup/podrubrika/AjaxPodRubrikaGet.php?" + G.values + "&rubrika_id=" + id, function (res) {
        progressHide();
        if(res.length > 0) {
            $("#podRub_table").html("<IMG src=/img/upload.gif>");
            var html="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH class=name>Наименование<TH class=set>Настройки</TABLE>";
            html+="<DL id=podRub_drag>";
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<DD id=" + sp.id + "><TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>" +
                    "<TD class=name>" + sp.name +
                    "<TD class=set><DIV class=img_edit onclick=podRubrikaEdit(" + sp.id + "," + id + ");></DIV>" +
                    "<DIV class=img_del onclick=podRubrikaDel(" + sp.id + "," + id + ");></DIV></TABLE>";
                }
            html+="</DL>";
            $("#podRub_table").html(html);
            $("#podRub_drag").sortable({axis:'y',update:function(){
                var DD=$("#podRub_drag DD");
                var LEN=DD.length;
                var VAL=DD.eq(0).attr('id');
                if(LEN>1) {
                    progressShow();
                    for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
                    $.getJSON("/view/gazeta/setup/podrubrika/AjaxPodRubrikaSort.php?"+G.values+"&val="+VAL, progressHide);
                }
            }});
        }
        else $("#podRub_table").html("Подрубрик для <B>"+RUBNAME+"</B> нет.");
        frameBodyHeightSet();
    });
} // end of podRubrikaGet()

function podRubrikaAdd(id, RUBNAME) {
    html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Рубрика:<TD>"+RUBNAME +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=podrubrika_name style=width:200px;>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Внесение новой подрубрики для '+RUBNAME,
        content:html,
        focus:'#podrubrika_name',
        submit:function(){
            var send = {
                rubrika_id:id,
                name:$("#podrubrika_name").val()
            };
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не указано наименование.</SPAN>',
                    top:-47,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/podrubrika/AjaxPodRubrikaAdd.php?" + G.values, send, function (res) {
                    dialog.close();
                    podRubrikaGet(id);
                    vkMsgOk("Новая подрубрика добавлена!");
                },'json');
            }
        }
    }).o;
} // end of podRubrikaAdd()

function podRubrikaEdit(id, rub_id) {
    var RUBNAME = $("#rubrika_id .sel:first").html();
    html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Рубрика:<TD>"+RUBNAME +
        "<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=podrubrika_name style=width:200px; value='"+$("#"+id+" .name").html()+"'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        top:100,
        head:'Редактирование подрубрики',
        butSubmit:'Сохранить',
        content:html,
        submit:function(){
            var send = {
                id:id,
                name:$("#podrubrika_name").val()
            };
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не указано наименование.</SPAN>',
                    top:-47,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/podrubrika/AjaxPodRubrikaEdit.php?" + G.values, send, function (res) {
                    dialog.close();
                    podRubrikaGet(rub_id);
                    vkMsgOk("Наименование подрубрики изменено!");
                },'json');
            }
        }
    }).o;
} // end of podRubrikaEdit()

function podRubrikaDel(id, rub_id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:300,
        head:'Удаление',
        butSubmit:'Удалить',
        content:"<CENTER>Подтвердите удаление подрубрики '<B>"+$("#"+id+" .name").html()+"</B>'.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/podrubrika/AjaxPodRubrikaDel.php?" + G.values, {id:id}, function (res) {
                dialog.close();
                podRubrikaGet(rub_id);
                vkMsgOk("Удаление успешно произведено!");
            },'json');
        }
    }).o;
} // end of podRubrikaDel()

















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
                    "<DIV class=img_del  val=del_" + n + "></DIV>";
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
//        "<TR><TD class=tdAbout>Дни недели:<TD>" +
//            "<INPUT type=hidden id=day_begin value='" + sp.day_begin_val + "'> - " +
//            "<INPUT type=hidden id=day_end value='" + sp.day_end_val + "'>" +
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
//                day_begin:$("#day_begin").val(),
//                day_end:$("#day_end").val(),
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
    //$("#day_begin").vkCalendar({lost:1});
    //$("#day_end").vkCalendar({lost:1});
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








// Стоимость см2 для каждой полосы
function setupSmKvCost() {
    var html = "<DIV id=smKvCost>" +
        "<DIV class=headName>Настройка стоимости см&sup2; рекламы для каждой полосы</DIV>" +
        "<A id=polosaAdd>Добавить новое название полосы</A>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $("#polosaAdd").click(setupPolosaAdd);

    $.getJSON("/view/gazeta/setup/polosa_cost/AjaxPolosaGet.php?" + G.values, function (res) {
        progressHide();
        var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>" +
            "<TR><TH class=name>Полоса" +
                "<TH class=cena>Цена за см&sup2;" +
                "<TH class=set>Настройки" +
            "</TABLE>";
        if(res.length > 0) {
            html += "<DL id=polosa_drag>";
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<DD id=" + sp.id + "><TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>" +
                    "<TD class=name>" + sp.name +
                    "<TD class=cena>" + sp.cena +
                    "<TD class=set><DIV class=img_edit onclick=polosaEdit(" + sp.id + ");></DIV>" +
                                  "<DIV class=img_del onclick=polosaDel(" + sp.id + ");></DIV>" +
                    "</TABLE>";
                }
            html+="</DL>";
        }
        $("#spisok").html(html);
        frameBodyHeightSet();
        $("#polosa_drag").sortable({axis:'y',update:function(){
            var DD=$("#polosa_drag DD");
            var LEN=DD.length;
            var VAL=DD.eq(0).attr('id');
            if(LEN > 1) {
                progressShow();
                for(var n=1;n<LEN;n++) VAL+=","+DD.eq(n).attr('id');
                $.getJSON("/view/gazeta/setup/polosa_cost/AjaxPolosaSort.php?" + G.values + "&val="+VAL, progressHide);
            }
        }});
    });
} // end of setupSmKvCost()

function setupPolosaAdd() {
    html = "<TABLE cellpadding=0 cellspacing=7>" +
        "<TR><TD class=tdAbout>Название:<TD><INPUT type=text id=name style=width:200px; maxlength=50>" +
        "<TR><TD class=tdAbout>Цена за см&sup2;:<TD id=sup><INPUT type=text id=cena style=width:40px; maxlength=6> руб." +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Внесение нового названия полосы',
        content:html,
        submit:function () {
            var send = {
                name:$("#name").val(),
                cena:$("#cena").val()
            };
            var msg;
            if(!send.name) { msg = 'Не указано название.'; $("#name").focus(); }
            else if (!/^[0-9.]+$/.test(send.cena)) { msg = 'Не корректно введено значение.'; $("#cena").focus(); }
            else {
                dialog.process();
                $.post("/view/gazeta/setup/polosa_cost/AjaxPolosaAdd.php?" + G.values, send, function () {
                    dialog.close();
                    setupSmKvCost();
                    vkMsgOk("Внесение успешно произведено!");
                },'json');
            }
            if (msg) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>' + msg + '</SPAN>',
                    top:-47,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
            }
        },
        focus:'#name'
    }).o;
} // end of setupPolosaAdd()

function polosaEdit(id) {
    html="<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>Название:<TD><INPUT type=text id=name style=width:200px; maxlength=50 value='"+$("#"+id+" .name").html()+"'>" +
        "<TR><TD class=tdAbout>Цена за см&sup2;:<TD id=sup><INPUT type=text id=cena style=width:40px; maxlength=6 value='"+$("#"+id+" .cena").html()+"'> руб." +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Редактирование данных полосы',
        butSubmit:'Сохранить',
        content:html,
        submit:function () {
            var send = {
                id:id,
                name:$("#name").val(),
                cena:$("#cena").val()
            };
            var msg;
            if(!send.name) { msg = 'Не указано название.'; $("#name").focus(); }
            else if (!/^[0-9.]+$/.test(send.cena)) { msg = 'Не корректно введено значение.'; $("#cena").focus(); }
            else {
                dialog.process();
                $.post("/view/gazeta/setup/polosa_cost/AjaxPolosaEdit.php?"+G.values, send, function (res) {
                    dialog.close();
                    setupSmKvCost();
                    vkMsgOk("Данные изменены!");
                },'json');
            }
            if (msg) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>' + msg + '</SPAN>',
                    top:-47,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
            }
        }
    }).o;
} // end of polosaEdit()

function polosaDel(id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:270,
        head:'Удаление',
        butSubmit:'Удалить',
        content:"<CENTER>Подтвердите удаление данных полосы '<B>"+$("#"+id+" .name").html()+"</B>'.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/polosa_cost/AjaxPolosaDel.php?"+G.values,{id:id},function(res){
                dialog.close();
                setupSmKvCost();
                vkMsgOk("Удаление успешно произведено!");
            },'json');
        }
    }).o;
} // end of polosaDel()










// СКИДКИ
function setupSkidka() {
    var html="<DIV id=skidka>" +
        "<DIV class=headName>Управление скидками</DIV>" +
        "<A onclick=skidkaAdd();>Добавить новую скидку</A>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/skidka/AjaxSkidkaGet.php?" + G.values, function (res) {
        progressHide();
        var html="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH>Размер скидки<TH>Описание<TH>Настройки";
        if (res.length > 0)
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<TR>" +
                    "<TD align=center><B id=raz"+sp.id+">"+sp.razmer+"</B>%" +
                    "<TD id=ab" + sp.id + ">" + sp.about +
                    "<TD align=center>" +
                        "<DIV class=img_edit onclick=skidkaEdit("+sp.id+");>" +
                        "</DIV><DIV class=img_del onclick=skidkaDel("+sp.id+");>";
            }
        html += "</TABLE>";
        $("#spisok").html(html);
        frameBodyHeightSet();
    });
}

function skidkaAdd() {
    html="<TABLE cellpadding=0 cellspacing=7>" +
        "<TR><TD class=tdAbout>Размер скидки:<TD id=rz><INPUT type=text id=razmer style=width:30px; maxlength=3> %" +
        "<TR><TD class=tdAbout>Описание:<TD><INPUT type=text id=about style=width:200px; maxlength=200>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Внесение новой скидки',
        content:html,
        submit:function () {
            var send = {
                about:$("#about").val(),
                razmer:$("#razmer").val()
            };
            if(!/^[0-9]+$/.test(send.razmer) || send.razmer < 1 || send.razmer > 100) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не корректно введён размер скидки.<BR>Используйте значение от 1 до 100.</SPAN>',
                    top:-60,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/skidka/AjaxSkidkaAdd.php?" + G.values, send, function () {
                    dialog.close();
                    setupSkidka();
                    vkMsgOk("Внесение успешно произведено!");
                },'json');
            }
        },
        focus:'#razmer'
    }).o;
} // end of skidkaAdd()

function skidkaEdit(id) {
    html = "<TABLE cellpadding=0 cellspacing=7>" +
        "<TR><TD class=tdAbout>Размер скидки:<TD id=rz><INPUT type=text id=razmer style=width:30px; maxlength=3 value='"+$("#raz"+id).html()+"'> %" +
        "<TR><TD class=tdAbout>Описание:<TD><INPUT type=text id=about style=width:200px; maxlength=200 value='"+$("#ab"+id).html()+"'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Редактирование данных скидки',
        butSubmit:'Сохранить',
        content:html,
        submit:function () {
            var send = {
                id:id,
                about:$("#about").val(),
                razmer:$("#razmer").val()
            };
            if(!/^[0-9]+$/.test(send.razmer) || send.razmer < 1 || send.razmer > 100) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не корректно введён размер скидки.<BR>Используйте значение от 1 до 100.</SPAN>',
                    top:-60,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/skidka/AjaxSkidkaEdit.php?" + G.values, send, function () {
                    dialog.close();
                    setupSkidka();
                    vkMsgOk("Изменение успешно произведено!");
                },'json');
            }
        }
    }).o;
}

function skidkaDel(id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:260,
        head:'Удаление',
        butSubmit:'Удалить',
        content:"<CENTER>Подтвердите удаление скидки <B>"+$("#raz"+id).html()+"</B>%.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/skidka/AjaxSkidkaDel.php?"+G.values,{id:id},function(res){
                dialog.close();
                setupSkidka();
                vkMsgOk("Удаление успешно произведено!");
            },'json');
        }
    }).o;
}











// СТОИМОСТЬ ДЛИНЫ ОБЪЯВЛЕНИЯ
function setupObLenght() {
    var html="<DIV id=obLen>" +
        "<DIV class=headName>Настройка стоимости длины объявления</DIV>" +
        "<DIV id=table></DIV></DIV>";
    $("#edit").html(html);
    $.getJSON("/view/gazeta/setup/ob_len/AjaxObLenGet.php?" + G.values, function (res) {
        html="<TABLE cellpadding=0 cellspacing=8>" +
            "<TR><TD align=right>Первые <INPUT type=text maxlength=3 value='"+res.txt_len_first+"' id=txt_len_first>&nbsp;&nbsp;символов:" +
                "<TD><INPUT type=text maxlength=3 value='"+res.txt_cena_first+"' id=txt_cena_first> руб." +
            "<TR><TD>Последующие <INPUT type=text maxlength=3 value='"+res.txt_len_next+"' id=txt_len_next>&nbsp;&nbsp;символов:" +
                "<TD><INPUT type=text maxlength=3 value='"+res.txt_cena_next+"' id=txt_cena_next> руб." +
            "<TR><TD colspan=2 align=center id=info>" +
            "</TABLE>";
        $("#table").html(html);
        $("#obLen INPUT").keyup(function () {
            $("#obLen #info").html('');
            if (!/^[0-9]+$/.test($(this).val()))
                $("#obLen #info").html("<SPAN class=red>Некорректрый ввод значения.<BR>Используйте цифры.</SPAN>");
        });
        $("#obLen INPUT").blur(function () {
            var send = {
                name:$(this).attr('id'),
                val:$(this).val()
            };
            var info = $("#obLen #info");
            if (!/^[0-9]+$/.test(send.val))
                info.html("<SPAN class=red>Некорректрый ввод значения.<BR>Сохранение невозможно.</SPAN>");
            else {
                info.html("<SPAN style=color:#AA0>Сохранение... <IMG src=/img/upload.gif></SPAN>");
                $.post("/view/gazeta/setup/ob_len/AjaxObLenSave.php?" + G.values, send, function () {
                    info.html("<B style=color:#090>Сохранено!</B>");
                    info.find("B").delay(2000).fadeOut(500);
                });
            }
        });
    });
} // setupObLenght()











// ДОПОЛНИТЕЛЬНЫЕ ПАРАМЕТРЫ ОБЪЯВЛЕНИЯ
function setupObDop() {
    var html="<DIV id=obDop>" +
        "<DIV class=headName>Настройка дополнительных параметров объявлений</DIV>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/ob_dop/AjaxObDopGet.php?" + G.values, function (res) {
        progressHide();
        var html="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH>Наименование<TH>Стоимость<TH>Настройки";
        if(res.length > 0)
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<TR><TD id=name"+sp.id+">" + sp.name +
                    "<TD align=center id=cena" + sp.id + ">"+sp.cena +
                    "<TD align=center><DIV class=img_edit onclick=paramEdit("+sp.id+");></DIV>";
                }
        html += "</TABLE>";
        $("#spisok").html(html);
        frameBodyHeightSet();
    });
}

function paramEdit(id) {
    html="<TABLE cellpadding=0 cellspacing=7>" +
        "<TR><TD class=tdAbout>Наименование:<TD><b>"+$("#name"+id).html()+"</b>" +
        "<TR><TD class=tdAbout>Стоимость:<TD><INPUT type=text id=cena style=width:30px; maxlength=3 value='"+$("#cena"+id).html()+"'> руб." +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'Редактирование параметра',
        butSubmit:'Сохранить',
        content:html,
        submit:function () {
            var send = {
                id:id,
                name:$("#name").val(),
                cena:$("#cena").val()
            };
            if(!/^[0-9]+$/.test(send.cena)) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>Не корректно введена стоимость.</SPAN>',
                    top:-47,
                    left:94,
                    indent:40,
                    show:1,
                    remove:1
                });
                $("#cena").focus();
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/ob_dop/AjaxObDopEdit.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupObDop();
                    vkMsgOk("Редактирование успешно произведено!");
                },'html');
            }
        }
    }).o;
}















// УПРАВЛЕНИЕ ПРАВАМИ СОТРУДНИКОВ
function setupAccess() {
    var html="<DIV id=access>" +
        "<DIV class=headName>Добавление нового сотрудника</DIV>" +
        "<TABLE cellpadding=0 cellspacing=7><TR><TD class=tdAbout>Ссылка на страницу<BR>пользователя Вконтакте или его id:" +
        "<TD><INPUT type=text id=find_input>" +
        "<TD><DIV class=vkButton><BUTTON onclick=accessUserGet(this);>Поиск</BUTTON></DIV></TABLE>" +
        "<DIV id=userFind></DIV>" +

        "<DIV class='headName top30'>Список сотрудников газеты</DIV>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    $("#find_input").focus();
    frameBodyHeightSet();
    accessUserSpisok();
}

function accessUserSpisok() {
    progressShow();
    $.getJSON("/view/gazeta/setup/access/AjaxWorkerSpisok.php?" + G.values, function (res) {
        progressHide();
        var html='';
        if(res.length > 0)
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<DIV class=userShow id=user"+sp.viewer_id+"><TABLE cellpadding=0 cellspacing=8>" +
                    "<TR><TD><A href='http://vk.com/id"+sp.viewer_id+"' target=_blank><IMG src="+sp.photo+"></A>" +
                    "<TD width=330><A href='http://vk.com/id"+sp.viewer_id+"' target=_blank id=name"+sp.viewer_id+">" + sp.full_name + "</A>" +
                    (sp.admin == 1 ? "<DIV class=admin>Администратор</DIV>" : '') +
                    "<DIV class=dtime>" + sp.dtime_add + "</DIV>" +
                    (sp.admin ==0 ? "<TD><A onclick=accessUserDel("+sp.viewer_id+");>Удалить</A>" : '') +
                    "</TABLE>" +
                "</DIV>";
            }
        $("#spisok").html(html);
        frameBodyHeightSet();
    });
}

function accessUserGet(but) {
    var user = $("#find_input").val();
    if (user) {
        var host = user.split('http://vk.com/')[1];
        if (host) user = host;
        progressShow();
        VK.api('users.get',{uids:user, fields:'photo,sex'}, function (data) {
            progressHide();
            var res = data.response[0];
            var html="<DIV class=userShow><TABLE cellpadding=0 cellspacing=8>" +
            "<TR><TD><A href='http://vk.com/id"+res.uid+"' target=_blank><IMG src="+res.photo+"></A>" +
            "<TD>Вы действительно добавить в сотрудники <br />" +
                "пользователя <A href='http://vk.com/id"+res.uid+"' target=_blank>"+res.first_name+" "+res.last_name+"</A> ?" +
            "<DIV class=buttons><DIV class=vkButton><BUTTON id=vk_add>Добавить</BUTTON></DIV>&nbsp;&nbsp;" +
            "<DIV class=vkCancel><BUTTON id=vk_cancel>Отмена</BUTTON></DIV></DIV>" +
            "</TABLE>" +
            "<INPUT type=hidden id=uid value="+res.uid+">" +
            "</DIV>";
            $("#userFind").html(html);
            $("#vk_add").on('click', function () {
                progressShow();
                $.post("/view/gazeta/setup/access/AjaxWorkerAdd.php?" + G.values, res, function () {
                    $('#userFind').html('');
                    $("#find_input").val('');
                    accessUserSpisok();
                });
            });
            $("#vk_cancel").on('click', function () { $('#userFind .userShow').slideUp(200,frameBodyHeightSet); });
            frameBodyHeightSet();
        });
    }
}







function accessUserDel(id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:240,
        head:'Удаление сотрудника',
        butSubmit:'Удалить',
        content:"<CENTER>Подтвердите удаление сотрудника <B>"+$("#name"+id).html()+"</B>.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/access/AjaxWorkerDel.php?" + G.values, {viewer_id:id}, function (res) {
                dialog.close();
                vkMsgOk("Удаление успешно произведено!");
                $("#user"+id).remove();
                frameBodyHeightSet();
            },'json');
        }
    }).o;
}

