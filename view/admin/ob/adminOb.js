$("#menu").vkRadio({
    top:4,
    light:1,
    spisok:[
        {uid:0,title:'¬се объ€влени€'},
        {uid:1,title:'јктивные'},
        {uid:2,title:'јрхив'}],
    func:function (uid) { G.spisok.print({menu:uid}); }
});

G.spisok.create({
    //a:1,
    view:$(".left"),
    limit:10,
    url:"/view/ob/my/AjaxObSpisok.php",
    result_view:$("#findResult"),
    result:"ѕоказано $count объ€влен$ob",
    imgup:$("#findResult"),
    ends:{'$ob':['ие', 'и€', 'ий']},
    nofind:"ќбъ€влений не найдено.",
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

// ѕереход к просмотру объ€влений выбранного пользовател€
function goUser(id, n) {
    var sp = myob.spisok[n];
    var html = sp.viewer_name + "<br />" +
        "<a href='http://vk.com/id" + sp.viewer_id + "' target=_blank><img src='" + sp.viewer_photo + "' /></a>" +
        "<a onclick=hideUser();>—крыть</a>";
    $("#viewer").html(html);
    G.spisok.print({viewer_id_add:id});
}

function hideUser() {
    $("#viewer").html('');
    G.spisok.print({viewer_id_add:0});
}