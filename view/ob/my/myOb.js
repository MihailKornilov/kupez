G.spisok.create({
    //a:1,
    view:$("#obSpisok"),
    limit:10,
    url:"/view/ob/my/AjaxObSpisok.php",
    result_view:$("#findResult"),
    result:"Показано $count объявлен$ob",
    imgup:$("#findResult"),
    ends:{'$ob':['ие', 'ия', 'ий']},
    nofind:"Объявлений не найдено.",
    values:{
        menu:0,
        viewer_id_add:G.vk.viewer_id
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


