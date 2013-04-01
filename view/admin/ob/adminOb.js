$("#menu").vkRadio({
    top:4,
    light:1,
    spisok:[
        {uid:0,title:'��� ����������'},
        {uid:1,title:'��������'},
        {uid:2,title:'�����'}],
    func:function (uid) { G.spisok.print({menu:uid}); }
});

G.spisok.create({
    //a:1,
    view:$(".left"),
    limit:10,
    url:"/view/ob/my/AjaxObSpisok.php",
    result_view:$("#findResult"),
    result:"�������� $count ��������$ob",
    imgup:$("#findResult"),
    ends:{'$ob':['��', '��', '��']},
    nofind:"���������� �� �������.",
    values:{
        menu:0,
        viewer_id_add:G.viewer.id
    },
    callback:function (data) {
        if (data.length == 0)
            myob.spisok = [];
        else if (data[0].num == 0)
            myob.spisok = [];
        for (var n = 0; n < data.length; n++)
            myob.spisok.push(data[n]);

        $(".unit").off();
        $(".unit").on({
            mouseenter:function () { $(this).find("H2").show(); },
            mouseleave:function () { $(this).find("H2").hide(); }
        });
    }
});

// ������� � ��������� ���������� ���������� ������������
function goUser(id, n) {
    var sp = myob.spisok[n];
    var html = sp.viewer_name + "<br />" +
        "<a href='http://vk.com/id" + sp.viewer_id + "' target=_blank><img src='" + sp.viewer_photo + "' /></a>" +
        "<a onclick=hideUser();>������</a>";
    $("#viewer").html(html);
    G.spisok.print({viewer_id_add:id});
}

function hideUser() {
    $("#viewer").html('');
    G.spisok.print({viewer_id_add:0});
}