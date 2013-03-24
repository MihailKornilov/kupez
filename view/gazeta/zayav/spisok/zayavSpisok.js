G.category_spisok.unshift({uid:0, title:'����� ���������'});
G.category_spisok[1].title += "<div class=img_word></div>";
$("#category").infoLink({
    spisok:G.category_spisok,
    func:function (id) {
        G.spisok.print({category:id});
    }
});
$("#category .img_word:first")
    .click(function () {
        var gn = $("#gazeta_nomer").val();
        if (gn == 0) {
            $(this).vkHint({
                msg:'<span class=red>�� ������ ����� ������.</span>',
                indent:'right',
                top:-74,
                left:-19,
                show:1,
                remove:1
            });
        } else location.href = "/view/gazeta/zayav/spisok/PrintWord.php?gn=" + gn;
    })
    .vkHint({
        msg:'<span style=color:#444;>������� ������ ����������<br>' +
            '� �������� �������� � �������<br>' +
            'Microsoft Word.</span>',
        indent:'right',
        top:-97,
        left:-51
    });




$("#year").vkSel({
    width:147,
    title0:'��� �� ������',
    spisok: G.zayav.year,
    func:function(year){
        $("#vkSel_gazeta_nomer").remove();
        $("#gazeta_nomer").val(0);
        if(year > 0) gazetaNomerGet(year);
        G.spisok.print({year:year, gazeta_nomer:0});
    }
});
    
gazetaNomerGet((new Date).getFullYear());

$("#fastFind").topSearch({
    txt:'������� �����...',
    enter:1,
    func:function(inp) {
        if(inp) $("#nofast").hide();
        else $("#nofast").show();
        G.spisok.print({input:encodeURIComponent(inp)});
    }
});
$("#fastFind").vkHint({
    msg:'������� �������� � ������� <b>Enter</b>.<br>' +
        '����� ������������ �� ���� �����������,<br>' +
        '�� ���� ������ ��������� �� �����������.<br>' +
        '���� ������� ����� � ��� ��������� � �������<br>' +
        '������, �� ��� ������ ��������� ������ � ������.',
    ugol:'right',
    indent:10,
    top:-10,
    left:-316,
    delayShow:1500
});



$("#no_public").myCheck({
    title:'������������ ������',
    bottom:20,
    func:function (id) {
        $("#public")[id == 0 ? 'show' : 'hide']();
        G.spisok.print({no_public:id});
    }
});
$("#check_no_public").vkHint({
    msg:'������, ������� �� �������������<br>�� � ����� ������ ������.',
    indent:60,
    top:-70,
    left:-61
});

// ����� ������ ������� �� ����������� ���
function gazetaNomerGet(year) {
    $("#gazeta_nomer").vkSel({
        width:147,
        title0:'����� �� ������',
        spisok:G.zayav.gazeta_nomer_spisok[year],
        func:function (id) { G.spisok.print({gazeta_nomer:id}); }
    });
    $("#vkSel_gazeta_nomer").css('margin-top','4px');
}





G.spisok.unit = function (sp) {
    return "<H1><EM>" + sp.dtime + "</EM><A href='" + G.url + "&p=gazeta&d=zayav&d1=view&id="+sp.id+"'>" + G.category_ass[sp.category] + " �" + sp.id + "</A></H1>" +
        "<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>" +
            "<TABLE cellpadding=0 cellspacing=4>" +
            (sp.client_id ? "<TR><TD class=tdAbout>������:<TD><A HREF='" + G.url + "&p=gazeta&d=client&d1=info&id=" + sp.client_id + "'>" + sp.client_fio + "</A>" : '') +
            (sp.category == 1 ?
                "<TR><TD class=tdAbout>�������:<TD>" + G.rubrika_ass[sp.rubrika] + (sp.podrubrika > 0 ? "<SPAN class=ug>�</SPAN>" + G.podrubrika_ass[sp.podrubrika] : '') +
                "<TR><TD class=tdAbout valign=top>�����:<TD><DIV class=txt>" + sp.txt + "</DIV>" : '') +

            (sp.ob_dop ? "<TR><TD class=tdAbout>���. ��������:<TD>" + sp.ob_dop : '') +
            (sp.category == 2 ? "<TR><TD class=tdAbout>������:<TD>" + sp.size_x + " x " + sp.size_y + " = <b>" + sp.kv_sm + '</b> ��&sup2;' : '') +
//    if(sp.telefon) HTML+="<TR><TD class=tdAbout>�������:<TD>"+sp.telefon;
//    if(sp.adres) HTML+="<TR><TD class=tdAbout>�����:<TD>"+sp.adres;

            "<TR><TD class=tdAbout>���������:<TD><B>" + sp.summa + "</B> ���." +
                    (sp.summa_manual == 1 ? '<SPAN class=manual>(������� �������)</SPAN>' : '') +
            "</TABLE>" +

//    if(sp.file) HTML+="<TD class=image><IMG src=/files/images/"+sp.file+"s.jpg onclick=fotoShow('"+sp.file+"');>";

        "</TABLE>";
};

G.spisok.create({
    url:"/view/gazeta/zayav/spisok/AjaxZayavSpisok.php",
    view:$("#spisok"),
    limit:15,
    cache_spisok:spisok.ob,
    result_view:$("#findResult"),
    result:"�������� $count ����$ob",
    ends:{'$ob':['��', '��', '��']},
    next:"�������� ��� ������",
    nofind:"������ �� �������.",
    imgup:$("#findResult"),
//    a:1,
    values:{
        input:'',
        category:0,
        year:(new Date()).getFullYear(),
        gazeta_nomer:G.gn.first_active,
        no_public:0
    }
});
