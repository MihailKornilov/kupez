function progressShow() { $(".headName:first").find("IMG").remove().end().append("<IMG src=/img/upload.gif>"); }
function progressHide() { $(".headName:first IMG").remove(); }


function setupSet(id) {
    $(".razdel").find(".help").remove();
    switch(id) {
        case '3': setupGazNomer(); break;
        case '4': setupSmKvCost(); break;
        case '5': setupSkidka(); break;
        case '6': setupObDop(); break;
        case '8': setupAccess(); break;
        case '9': setupObLenght(); break;
        case '10': setupRashodCategory(); break;
    }
} // end of setupSet()





// ��������� ��������
function setupRashodCategory() {
    var html="<DIV id=rashod>" +
        "<DIV class=headName>��������� ��������� ��������</div>" +
        "<A onclick=rashodCategoryAdd();>�������� ����� ���������</A>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/rashod_category/AjaxRashodGet.php?" + G.values, function(res){
        progressHide();
        if(res.length > 0) {
            var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>" +
                "<TR><TH class=name>������������" +
                "<TH class=set>���������" +
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
        } else $("#spisok").html("������ ����.");
        frameBodyHeightSet();
    });
} // end of setupRashodCategory()

function rashodCategoryAdd() {
    var html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>������������:<TD><INPUT type=text id=rashod_name style=width:200px;>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'�������� ����� ���������',
        content:html,
        focus:'#rashod_name',
        submit:function () {
            var send = {name:$("#rashod_name").val()}
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>�� ������� ������������.</SPAN>', top:-47, left:94, indent:40, show:1, remove:1});
            } else {
                dialog.process();
                $.post("/view/gazeta/setup/rashod_category/AjaxRashodAdd.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupRashodCategory();
                    vkMsgOk("����� ��������� ���������.");
                },'json');
            }
        }
    }).o;
} // end of rashodCategoryAdd()

function rashodCategoryEdit(id) {
    var html="<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>������������:<TD><INPUT type=text id=name style=width:200px; value='"+$("#"+id+" .name").html()+"'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'��������������',
        butSubmit:'���������',
        content:html,
        submit:function () {
            var send = {
                id:id,
                name:$("#name").val()
            };
            if(!send.name) {
                $("#setup_dialog .bottom:first").vkHint({msg:'<SPAN class=red>�� ������� ������������.</SPAN>', top:-47, left:94, indent:40, show:1, remove:1});
            }  else {
                dialog.process();
                $.post("/view/gazeta/setup/rashod_category/AjaxRashodEdit.php?" + G.values, send, function (res) {
                    dialog.close();
                    setupRashodCategory();
                    vkMsgOk("������������ ��������� ��������!");
                },'json');
            }
        }
    }).o;
} // end of rashodCategoryEdit()

function rashodCategoryDel(id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:300,
        head:'��������',
        butSubmit:'�������',
        content:"<CENTER>����������� �������� ��������� '<B>"+$("#"+id+" .name").html()+"</B>'.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/rashod_category/AjaxRashodDel.php?" + G.values, {id:id}, function(res){
                dialog.close();
                setupRashodCategory();
                vkMsgOk("�������� ������� �����������!");
            },'html');
        }
    }).o;
} // end of rashodCategoryDel()




















// ������ �������� ������
function setupGazNomer() {
    $("#edit").html("<IMG src=/img/upload.gif>");
    var html="<DIV id=gazNomer>" +
        "<DIV class=headName>���������� �������� �������� ������</DIV>" +
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
            var html = "<A val=add_>�������� ����� �����</a><br /><br />" +
                "<TABLE cellpadding='0' cellspacing='0' class=tabSpisok><TR>" +
                "<TH>�����<BR>�������" +
                //"<TH>��� ������" +
                "<TH>����<BR>��������<BR>� ������" +
                "<TH>���� ������" +
                "<TH>������" +
                "<TH>���������";
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
            html = "������ �����, ������� ����� �������� � " + year + " ����, �� ����������." +
                "<BR><BR><A onclick=gazNomerSpisokCreate(" + year + ");><B>������� ������</B>...</A>";
            $("#spisok").html(html);
        }
        frameBodyHeightSet();
    });
} // end of gazNomerGet()

// ���������� ������ ������
function gazNomerAdd(year) {
    html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>����� �������:<TD>" +
        "<INPUT type=text id=week_nomer style=width:15px;text-align:right; maxlength=2'>&nbsp;" +
        "<INPUT type=text id=general_nomer style=width:20px;text-align:right; maxlength=3'>" +
        "<TR><TD class=tdAbout>���� �������� � ������:<TD><INPUT type=hidden id=day_print>" +
        "<TR><TD class=tdAbout>���� ������:<TD><INPUT type=hidden id=day_public>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        width:340,
        head:'���������� ������ ������',
        butSubmit:'������',
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
                            msg:'<SPAN class=red>����� ������� �� ����� ���� ������ ' + send.general_nomer + ',<br />��� ��� �� ����� ������ ������� ������.</SPAN>',
                            top:-61,
                            left:85,
                            indent:40,
                            show:1,
                            remove:1
                        });
                    } else {
                        dialog.close();
                        gazNomerGet(year, send.general_nomer);
                        vkMsgOk("����� ����� �����.");
                    }
                },'json');
            }
            if (msg) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>�� ��������� ����� ����� �������.<BR>����������� �����.</SPAN>',
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

// �������������� ������ ������
function gazNomerEdit(year, sp) {
    html = "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>����� �������:<TD>" +
            "<INPUT type=text id=week_nomer style=width:15px;text-align:right; maxlength=2 value='" + sp.week_nomer + "'>&nbsp;" +
            "<INPUT type=text id=general_nomer style=width:20px;text-align:right; maxlength=3 value='" + sp.general_nomer + "'>" +
        "<TR><TD class=tdAbout>���� �������� � ������:<TD><INPUT type=hidden id=day_print value='" + sp.day_print_val + "'>" +
        "<TR><TD class=tdAbout>���� ������:<TD><INPUT type=hidden id=day_public value='" + sp.day_public_val + "'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        width:340,
        head:'�������������� ������ ������ ������',
        butSubmit:'���������',
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
                             msg:'<SPAN class=red>����� ������� �� ����� ���� ������ ' + send.general_nomer + ',<br />��� ��� �� ����� ������ ������� ������.</SPAN>',
                             top:-61,
                             left:85,
                             indent:40,
                             show:1,
                             remove:1
                         });
                     } else {
                         dialog.close();
                         gazNomerGet(year, send.general_nomer);
                         vkMsgOk("������ ��������!");
                     }
                },'json');
            }
            if (msg) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>�� ��������� ����� ����� �������.<BR>����������� �����.</SPAN>',
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
        head:'��������',
        butSubmit:'�������',
        content:"<CENTER>����������� ��������<BR>������ ������ <B>" + sp.week_nomer  + "</B> (" + sp.general_nomer + ").</CENTER>",
        submit:function () {
            dialog.process();
            $.post("/view/gazeta/setup/gazeta_nomer/AjaxGNDel.php?" + G.values, {general_nomer:sp.general_nomer}, function (res) {
                dialog.close();
                gazNomerGet(year, 0);
                vkMsgOk("�������� ������� �����������!");
                frameBodyHeightSet();
            },'json');
        }
    }).o;
} // end of gazNomerDel()

function gazNomerSpisokCreate(year) {
    html = "<DIV class=gnInfo>��� �������� ������ ������� ����� <B>" + year + "</B> ���� " +
            "������� ������ <B>������� ������</B>, " +
            "������� ����� �������� � ���� ����. " +
            "��� ���� ����������� ��� ����������.</DIV>" +
        "<TABLE cellpadding=0 cellspacing=10>" +
        "<TR><TD class=tdAbout>������ ����� �������:<TD>" +
            "<INPUT type=text id=week_nomer style=width:15px;text-align:right; maxlength=2 value=1>&nbsp;" +
            "<INPUT type=text id=general_nomer style=width:20px;text-align:right; maxlength=3 value=" + (G.setup.gn_max + 1) + ">" +
        "<TR><TD class=tdAbout>��� �������� � ������:<TD><INPUT type=hidden id=day_print>" +
        "<TR><TD class=tdAbout>��� ������:<TD><INPUT type=hidden id=day_public>" +
        "<TR><TD class=tdAbout>������ ���� ������:<TD><INPUT type=hidden id=first_day_public value='" + year + "-01-01'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        width:340,
        head:'�������� ������ ������� ������',
        butSubmit:'�������',
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
            if(!/^[0-9]+$/.test(send.week_nomer)) { msg = '�� ��������� ����� ����� �������.'; $("#week_nomer").focus(); }
            else if(!/^[0-9]+$/.test(send.general_nomer)) { msg = '�� ��������� ����� ����� �������.'; $("#general_nomer").focus(); }
            else if(send.general_nomer <= G.setup.gn_max) { msg = '����� ������� �� ����� ���� ������ ' + (G.setup.gn_max + 1); $("#general_nomer").focus(); }
            else {
                dialog.process();
                $.post("/view/gazeta/setup/gazeta_nomer/AjaxGNSpisokCreate.php?" + G.values, send, function (res) {
                    dialog.close();
                    gazNomerGet(year, send.general_nomer);
                    vkMsgOk("������ ������� ������.");
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
        {uid:0,title:'�����������'},
        {uid:1,title:'�������'},
        {uid:2,title:'�����'},
        {uid:3,title:'�������'},
        {uid:4,title:'�������'},
        {uid:5,title:'�������'},
        {uid:6,title:'�����������'}
    ];
    $("#day_print").vkSel({width:100, value:1, spisok:weeks});
    $("#day_public").vkSel({width:100, value:4, spisok:weeks});
    $("#first_day_public").vkCalendar();
} // end of gazNomerSpisokCreate()








// ��������� ��2 ��� ������ ������
function setupSmKvCost() {
    var html = "<DIV id=smKvCost>" +
        "<DIV class=headName>��������� ��������� ��&sup2; ������� ��� ������ ������</DIV>" +
        "<A id=polosaAdd>�������� ����� �������� ������</A>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $("#polosaAdd").click(setupPolosaAdd);

    $.getJSON("/view/gazeta/setup/polosa_cost/AjaxPolosaGet.php?" + G.values, function (res) {
        progressHide();
        var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>" +
            "<TR><TH class=name>������" +
                "<TH class=cena>���� �� ��&sup2;" +
                "<TH class=set>���������" +
            "</TABLE>";
        if(res.length > 0) {
            html += "<DL id=polosa_drag>";
            for(var n = 0; n < res.length; n++) {
                var sp = res[n];
                html += "<DD id=" + sp.id + "><TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>" +
                    "<TD class=name>" + sp.name +
                    "<TD class=cena>" + sp.cena +
                    "<TD class=set><DIV class=img_edit onclick=polosaEdit(" + sp.id + ");></DIV>" +
                                 // "<DIV class=img_del onclick=polosaDel(" + sp.id + ");></DIV>" +
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
        "<TR><TD class=tdAbout>��������:<TD><INPUT type=text id=name style=width:200px; maxlength=50>" +
        "<TR><TD class=tdAbout>���� �� ��&sup2;:<TD id=sup><INPUT type=text id=cena style=width:40px; maxlength=6> ���." +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'�������� ������ �������� ������',
        content:html,
        submit:function () {
            var send = {
                name:$("#name").val(),
                cena:$("#cena").val()
            };
            var msg;
            if(!send.name) { msg = '�� ������� ��������.'; $("#name").focus(); }
            else if (!/^[0-9.]+$/.test(send.cena)) { msg = '�� ��������� ������� ��������.'; $("#cena").focus(); }
            else {
                dialog.process();
                $.post("/view/gazeta/setup/polosa_cost/AjaxPolosaAdd.php?" + G.values, send, function () {
                    dialog.close();
                    setupSmKvCost();
                    vkMsgOk("�������� ������� �����������!");
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
        "<TR><TD class=tdAbout>��������:<TD><INPUT type=text id=name style=width:200px; maxlength=50 value='"+$("#"+id+" .name").html()+"'>" +
        "<TR><TD class=tdAbout>���� �� ��&sup2;:<TD id=sup><INPUT type=text id=cena style=width:40px; maxlength=6 value='"+$("#"+id+" .cena").html()+"'> ���." +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'�������������� ������ ������',
        butSubmit:'���������',
        content:html,
        submit:function () {
            var send = {
                id:id,
                name:$("#name").val(),
                cena:$("#cena").val()
            };
            var msg;
            if(!send.name) { msg = '�� ������� ��������.'; $("#name").focus(); }
            else if (!/^[0-9.]+$/.test(send.cena)) { msg = '�� ��������� ������� ��������.'; $("#cena").focus(); }
            else {
                dialog.process();
                $.post("/view/gazeta/setup/polosa_cost/AjaxPolosaEdit.php?"+G.values, send, function (res) {
                    dialog.close();
                    setupSmKvCost();
                    vkMsgOk("������ ��������!");
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
        head:'��������',
        butSubmit:'�������',
        content:"<CENTER>����������� �������� ������ ������ '<B>"+$("#"+id+" .name").html()+"</B>'.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/polosa_cost/AjaxPolosaDel.php?"+G.values,{id:id},function(res){
                dialog.close();
                setupSmKvCost();
                vkMsgOk("�������� ������� �����������!");
            },'json');
        }
    }).o;
} // end of polosaDel()










// ������
function setupSkidka() {
    var html="<DIV id=skidka>" +
        "<DIV class=headName>���������� ��������</DIV>" +
        "<A onclick=skidkaAdd();>�������� ����� ������</A>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/skidka/AjaxSkidkaGet.php?" + G.values, function (res) {
        progressHide();
        var html="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH>������ ������<TH>��������<TH>���������";
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
        "<TR><TD class=tdAbout>������ ������:<TD id=rz><INPUT type=text id=razmer style=width:30px; maxlength=3> %" +
        "<TR><TD class=tdAbout>��������:<TD><INPUT type=text id=about style=width:200px; maxlength=200>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'�������� ����� ������',
        content:html,
        submit:function () {
            var send = {
                about:$("#about").val(),
                razmer:$("#razmer").val()
            };
            if(!/^[0-9]+$/.test(send.razmer) || send.razmer < 1 || send.razmer > 100) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>�� ��������� ����� ������ ������.<BR>����������� �������� �� 1 �� 100.</SPAN>',
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
                    vkMsgOk("�������� ������� �����������!");
                },'json');
            }
        },
        focus:'#razmer'
    }).o;
} // end of skidkaAdd()

function skidkaEdit(id) {
    html = "<TABLE cellpadding=0 cellspacing=7>" +
        "<TR><TD class=tdAbout>������ ������:<TD id=rz><INPUT type=text id=razmer style=width:30px; maxlength=3 value='"+$("#raz"+id).html()+"'> %" +
        "<TR><TD class=tdAbout>��������:<TD><INPUT type=text id=about style=width:200px; maxlength=200 value='"+$("#ab"+id).html()+"'>" +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'�������������� ������ ������',
        butSubmit:'���������',
        content:html,
        submit:function () {
            var send = {
                id:id,
                about:$("#about").val(),
                razmer:$("#razmer").val()
            };
            if(!/^[0-9]+$/.test(send.razmer) || send.razmer < 1 || send.razmer > 100) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>�� ��������� ����� ������ ������.<BR>����������� �������� �� 1 �� 100.</SPAN>',
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
                    vkMsgOk("��������� ������� �����������!");
                },'json');
            }
        }
    }).o;
}

function skidkaDel(id) {
    var dialog = $("#setup_dialog").vkDialog({
        width:260,
        head:'��������',
        butSubmit:'�������',
        content:"<CENTER>����������� �������� ������ <B>"+$("#raz"+id).html()+"</B>%.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/skidka/AjaxSkidkaDel.php?"+G.values,{id:id},function(res){
                dialog.close();
                setupSkidka();
                vkMsgOk("�������� ������� �����������!");
            },'json');
        }
    }).o;
}











// ��������� ����� ����������
function setupObLenght() {
    var html="<DIV id=obLen>" +
        "<DIV class=headName>��������� ��������� ����� ����������</DIV>" +
        "<DIV id=table></DIV></DIV>";
    $("#edit").html(html);
    $.getJSON("/view/gazeta/setup/ob_len/AjaxObLenGet.php?" + G.values, function (res) {
        html="<TABLE cellpadding=0 cellspacing=8>" +
            "<TR><TD align=right>������ <INPUT type=text maxlength=3 value='"+res.txt_len_first+"' id=txt_len_first>&nbsp;&nbsp;��������:" +
                "<TD><INPUT type=text maxlength=3 value='"+res.txt_cena_first+"' id=txt_cena_first> ���." +
            "<TR><TD>����������� <INPUT type=text maxlength=3 value='"+res.txt_len_next+"' id=txt_len_next>&nbsp;&nbsp;��������:" +
                "<TD><INPUT type=text maxlength=3 value='"+res.txt_cena_next+"' id=txt_cena_next> ���." +
            "<TR><TD colspan=2 align=center id=info>" +
            "</TABLE>";
        $("#table").html(html);
        frameBodyHeightSet();
        $("#obLen INPUT").keyup(function () {
            $("#obLen #info").html('');
            if (!/^[0-9]+$/.test($(this).val()))
                $("#obLen #info").html("<SPAN class=red>������������ ���� ��������.<BR>����������� �����.</SPAN>");
        });
        $("#obLen INPUT").blur(function () {
            var send = {
                name:$(this).attr('id'),
                val:$(this).val()
            };
            var info = $("#obLen #info");
            if (!/^[0-9]+$/.test(send.val))
                info.html("<SPAN class=red>������������ ���� ��������.<BR>���������� ����������.</SPAN>");
            else if (res[send.name] != send.val) {
                info.html("<SPAN style=color:#AA0>����������... <IMG src=/img/upload.gif></SPAN>");
                $.post("/view/gazeta/setup/ob_len/AjaxObLenSave.php?" + G.values, send, function () {
                    res[send.name] = send.val;
                    info.html("<B style=color:#090>���������!</B>");
                    info.find("B").delay(2000).fadeOut(500);
                });
            }
        });
    });
} // setupObLenght()











// �������������� ��������� ����������
function setupObDop() {
    var html="<DIV id=obDop>" +
        "<DIV class=headName>��������� �������������� ���������� ����������</DIV>" +
        "<DIV id=spisok></DIV>" +
        "</DIV>";
    $("#edit").html(html);
    progressShow();
    $.getJSON("/view/gazeta/setup/ob_dop/AjaxObDopGet.php?" + G.values, function (res) {
        progressHide();
        var html="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR><TH>������������<TH>���������<TH>���������";
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
        "<TR><TD class=tdAbout>������������:<TD><b>"+$("#name"+id).html()+"</b>" +
        "<TR><TD class=tdAbout>���������:<TD><INPUT type=text id=cena style=width:30px; maxlength=3 value='"+$("#cena"+id).html()+"'> ���." +
        "</TABLE>";
    var dialog = $("#setup_dialog").vkDialog({
        head:'�������������� ���������',
        butSubmit:'���������',
        content:html,
        submit:function () {
            var send = {
                id:id,
                name:$("#name").val(),
                cena:$("#cena").val()
            };
            if(!/^[0-9]+$/.test(send.cena)) {
                $("#setup_dialog .bottom:first").vkHint({
                    msg:'<SPAN class=red>�� ��������� ������� ���������.</SPAN>',
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
                    vkMsgOk("�������������� ������� �����������!");
                },'html');
            }
        }
    }).o;
}















// ���������� ������� �����������
function setupAccess() {
    var html="<DIV id=access>" +
        "<DIV class=headName>���������� ������ ����������</DIV>" +
        "<TABLE cellpadding=0 cellspacing=7><TR><TD class=tdAbout>������ �� ��������<BR>������������ ��������� ��� ��� id:" +
        "<TD><INPUT type=text id=find_input>" +
        "<TD><DIV class=vkButton><BUTTON onclick=accessUserGet(this);>�����</BUTTON></DIV></TABLE>" +
        "<DIV id=userFind></DIV>" +

        "<DIV class='headName top30'>������ ����������� ������</DIV>" +
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
                    (sp.admin == 1 ? "<DIV class=admin>�������������</DIV>" : '') +
                    "<DIV class=dtime>" + sp.dtime_add + "</DIV>" +
                    (sp.admin ==0 ? "<TD><A onclick=accessUserDel("+sp.viewer_id+");>�������</A>" : '') +
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
            "<TD>�� ������������� �������� � ���������� <br />" +
                "������������ <A href='http://vk.com/id"+res.uid+"' target=_blank>"+res.first_name+" "+res.last_name+"</A> ?" +
            "<DIV class=buttons><DIV class=vkButton><BUTTON id=vk_add>��������</BUTTON></DIV>&nbsp;&nbsp;" +
            "<DIV class=vkCancel><BUTTON id=vk_cancel>������</BUTTON></DIV></DIV>" +
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
        head:'�������� ����������',
        butSubmit:'�������',
        content:"<CENTER>����������� �������� ���������� <B>"+$("#name"+id).html()+"</B>.</CENTER>",
        submit:function(){
            dialog.process();
            $.post("/view/gazeta/setup/access/AjaxWorkerDel.php?" + G.values, {viewer_id:id}, function (res) {
                dialog.close();
                vkMsgOk("�������� ������� �����������!");
                $("#user"+id).remove();
                frameBodyHeightSet();
            },'json');
        }
    }).o;
}

