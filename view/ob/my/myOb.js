$("#links").infoLink({
  spisok:[
    {uid:0,title:'��� ����������'},
    {uid:1,title:'��������'},
    {uid:2,title:'�����'}],
   func:function (uid) { G.spisok.print({menu:uid}); }
});



G.spisok.create({
    //a:1,
    view:$("#obSpisok"),
    limit:10,
    url:"/view/ob/my/AjaxObSpisok.php",
    result_view:$("#findResult"),
    result:"�������� $count ��������$ob",
    imgup:$("#findResult"),
    ends:{'$ob':['��', '��', '��']},
    nofind:"���������� �� �������.",
    values:{
        menu:0,
        viewer_id:G.vk.viewer_id
    },
    callback:function (data) {
        if (G.spisok.start == G.spisok.values.limit)
            myob.spisok = data;
        else
            for (var n = 0; n < data.length; n++)
                myob.spisok.push(data[n]);
        $(".unit").off();
        $(".unit").on({
            mouseenter:function () { $(this).find("H2").show(); },
            mouseleave:function () { $(this).find("H2").hide(); }
        });
    }
});


