var linkSpisok = [
    {uid:1,title:'����������'},
    {uid:2,title:'�������������'},
    {uid:3,title:'<b>����� ������</b>'},
    {uid:4,title:'������� �����'}
];
if (G.client.del == 0) linkSpisok.push({uid:5, title:'<span class=red>������� �������</span>'});
$("#links").infoLink({
    spisok:linkSpisok,
    func:function (uid) {
        switch (uid) {
            case '2': clientAdd(function () { vkMsgOk("������ ������� ��������."); location.reload(); }, G.client); break;
            case '3': location.href = G.url + "&p=gazeta&d=zayav&d1=add&client_id=" + G.client.id; break;
            case '4': moneyAdd({client_id: G.client.id}); break;
            case '5': clientDel(); break;
        }
    }
});


zayavShow($("#dopMenu A:first"));





function zayavShow(link) {
    $("#dopMenu A").attr('class', 'link')
    $(link).attr('class', 'linkSel')

    $("#money").html('');

    G.spisok.unit = function (sp) {
        return "<H1><EM>" + sp.dtime + "</EM><A href='" + G.url + "&p=gazeta&d=zayav&d1=view&id="+sp.id+"'>" + G.category_ass[sp.category] + " �" + sp.id + "</A></H1>" +
            "<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>" +
            "<TABLE cellpadding=0 cellspacing=4>" +
            (sp.client_id > 0 ? "<TR><TD class=tdAbout>������:<TD><A HREF='" + G.url + "&p=gazeta&d=client&id=" + sp.client_id + "'>" + sp.client_fio + "</A>" : '') +
            (sp.category == 1 ?
                "<TR><TD class=tdAbout>�������:<TD>" + G.rubrika_ass[sp.rubrika] + (sp.podrubrika > 0 ? "<SPAN class=ug>�</SPAN>" + G.podrubrika_ass[sp.podrubrika] : '') +
                    "<TR><TD class=tdAbout valign=top>�����:<TD><DIV class=txt>" + sp.txt + "</DIV>" : '') +

            (sp.ob_dop ? "<TR><TD class=tdAbout>���. ��������:<TD>" + sp.ob_dop : '') +
            (sp.category == 2 ? "<TR><TD class=tdAbout>������:<TD>" + sp.size_x + " x " + sp.size_y + " = <b>" + sp.kv_sm + '</b> ��&sup2;' : '') +
//    if(sp.telefon) HTML+="<TR><TD class=tdAbout>�������:<TD>"+sp.telefon;
//    if(sp.adres) HTML+="<TR><TD class=tdAbout>�����:<TD>"+sp.adres;

            "<TR><TD class=tdAbout>���������:<TD><B>" + sp.summa + "</B> ���." + (sp.summa_manual == 1 ? '<SPAN class=manual>(������� �������)</SPAN>' : '') +
            "</TABLE>" +

//    if(sp.file) HTML+="<TD class=image><IMG src=/files/images/"+sp.file+"s.jpg onclick=fotoShow('"+sp.file+"');>";

            "</TABLE>";
    };

    G.spisok.create({
        view:$("#zayav"),
        limit:15,
        json: G.client.zayav_spisok,
        result_view:$("#result"),
        result:"�������$show $count ����$zayav",
        ends:{'$show':['�', '�'],'$zayav':['��', '��', '��']},
        next:"�������� ��� ������",
        nofind:"������ ���"
    });
}


function moneyShow(link) {
    $("#dopMenu A").attr('class', 'link')
    $(link).attr('class', 'linkSel')

    $("#zayav").html('');

    G.spisok.unit = function (sp) {
        var txt = sp.txt;
        if (sp.zayav_id > 0) { txt = "������ �� ������ <A href='/index.php?" + G.values + "&p=gazeta&d=zayav&d1=view&id=" + sp.zayav_id + "'><EM>�</EM>" + sp.zayav_id + "</A>"; }
        var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%><TR>" +
            "<TD class=sum><B>" + sp.sum + "</B>" +
            "<TD class=about><b>" + G.money_type_ass[sp.type] + (txt ? ': ' : '') + '</b>' + txt +
            "<TD class=data>" + sp.dtime_add +
            "</TABLE>";
        return html;
    };

    G.spisok.create({
        view:$("#money"),
        limit:15,
        json: G.client.money_spisok,
        result_view:$("#result"),
        result:"�������$show $count ������$pay",
        ends:{'$show':['�', '�'],'$pay':['', '�', '��']},
        next:"�������� ���...",
        nofind:"�������� ���",
        callback:function (res) {
            if(res.length > 0) {
                var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%>" +
                    "<TR><TH class=sum>�����" +
                    "<TH class=about>��������" +
                    "<TH class=data>����" +
                    "</TABLE>";
                $("#money").prepend(html);
            }
        }
    });

}


function clientDel() {
    var dialog = $("#dialog_client").vkDialog({
        width:250,
        head:"�������� �������",
        butSubmit:"�������",
        content:"<CENTER><B>����������� �������� �������</B></CENTER>",
        cancel:function () { $("#links").infoLinkSet(1); },
        submit:function () {
            dialog.process();
            $.getJSON("/view/gazeta/client/info/AjaxClientDel.php?" + G.values + "&id=" + G.client.id, function () {
                location.href = G.url + "&p=gazeta&d=client";
            }, 'json');
        }
    }).o;
} // end of clientDel()

