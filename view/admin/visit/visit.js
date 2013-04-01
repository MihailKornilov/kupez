$("#search").topSearch({
    txt:'�����',
    enter:1,
    func:function (val) {
        G.spisok.print({input:encodeURIComponent(val)});
    }
});

$("#findRadio").vkRadio({
    spisok:[
        {uid:1,title:'��� ����������'},
        {uid:2,title:'�������� �������'},
        {uid:3,title:'� ���� ������'},
        {uid:4,title:'��������� ����������'},
        {uid:5,title:'���������� ����������'},
        {uid:6,title:'�������� � ����� ����'}],
    bottom:7,
    func:function (id) {
        G.spisok.print({radio:id})
    }
});




G.spisok.unit = function (sp) {
    return "" +

    "<TABLE cellspacing=0 cellpadding=0>" +
        "<TR><TD class=img><A href='http://vk.com/id" + sp.viewer_id + "' target=_vk><IMG src=" + sp.photo + "></A>" +
            "<TD valign=top>" +
                "<DIV class=time>" + (sp.count_day > 1 ? "<SPAN>" + sp.count_day + "x</SPAN>" : '') + sp.time + "</DIV>" +
                "<A href='http://vk.com/id"+sp.viewer_id+"' target=_vk><B>" + sp.name + "</B></A>" +
                "<DIV class=place></DIV>" +
                (sp.ob_count > 0 ? "<DIV class=ob><A href='" + G.url + "&p=admin&d=ob&viewer_id_add=" + sp.viewer_id + "'>����������: " + sp.ob_count + "</A></DIV>" : '') +
    "</TABLE>";
}

G.spisok.create({
    url:"/view/admin/visit/AjaxVisitSpisok.php",
    view:$("#left"),
    limit:50,
    result_view:$("#findResult"),
    result:"�������$show $count ���������$user",
    ends:{'$show':['', '�'], '$user':['�', '�', '��']},
    nofind:"����������� �� �������.",
    imgup:$("#findResult"),
    //a:1,
    values:{
        input:'',
        radio:2
    },
    callback:function () {
        G.spisok.result_view.append(" " + G.spisok.data.time);
    }
});


