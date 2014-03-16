$("#vkFind").topSearch({
    txt:'Поиск объявлений: кто ищет, тот найдёт!',
    enter:1,
    func:function (val) { G.spisok.print({input:encodeURIComponent(val)}); },
    focus:1,
    width:300
});




// Клик по списку объявлений
$("#spisok").mousedown(function (e) {
    var val = $(e.target).attr('val');
    if (val) {
        var arr = val.split(/_/);
        switch(arr[0]) {
            case 'r':
                $("#rubrika").infoLinkSet(arr[1]);
                rubrikaSet(arr[1]);
                break;
            case 'p':
                $("#rubrika").infoLinkSet(arr[1]);
                rubrikaSet(arr[1], arr[2]);
                break;
        }
    }
});



function rubrikaSet(uid, podrub) {
    var podrub = podrub || 0;
    if(G.podrubrika_spisok[uid]) {
        $("#podrubrika").html("<DIV class=findName>Подрубрика</DIV><INPUT TYPE=hidden id=podrub value=" + podrub + ">");
        $("#podrub").vkSel({
            width:150,
            title0:'Подрубрика не указана',
            spisok:G.podrubrika_spisok[uid],
            func:function (pid) { G.spisok.print({podrub:pid}); }
        });
    } else {
        $("#podrubrika").html('');
    }
    G.spisok.print({rub:uid, podrub:podrub});
}




G.spisok.unit = function (sp) {
    var name = (sp.viewer_id > 0 ? "<A href='http://vk.com/id"+sp.viewer_id+"' target='_blank'>" + sp.viewer_name + "</A>" : '');
    var city = (sp.city_name ? sp.country_name + ", " + sp.city_name : '');
    return "<DIV class='" + sp.dop + "'>" +
            "<TABLE cellpadding=0 cellspacing=0 width=100%>" +
            "<TR><TD class=txt>" +
            "<A val=r_"+sp.rubrika+" class=aRub>" + G.rubrika_ass[sp.rubrika] + "</A><U>»</U>" +
            (sp.podrubrika > 0 ? "<A val=p_" + sp.rubrika + "_" + sp.podrubrika + " class=aRub>" + G.podrubrika_ass[sp.podrubrika] + "</A><U>»</U>" : '') +
            sp.txt +
            (sp.telefon ? "<DIV class=tel>" + sp.telefon + "</DIV>" : '') +
            " <DIV class=adres>" + city + name + "</DIV>" +
            (sp.file ? "<TD class=foto><IMG src=" + sp.file.split('_')[0] + "s.jpg onclick=G.fotoView('" + sp.file + "');>" : '') +
          "</TABLE></DIV>";
};

G.spisok.create({
    url:"/view/ob/spisok/AjaxObSpisok.php",
    view:$("#spisok"),
    limit:15,
    result_view:$("#findResult"),
    result:"Показано $count объявлени$ob",
    ends:{'$ob':['е', 'я', 'й']},
    next:"Показать ещё объявления",
    nofind:"Объявлений не найдено.",
    result_dop:'<A href="/index.php?' + G.values + '&p=ob&d=my" style="float:right;">Мои объявления</A>' + spisok.enter,
    imgup:"#rubrika .sel",
    //a:1,
    values:{
        rub:0,
        podrub:0,
        foto_only:0,
        city_id:0,
        input:''
    },
    callback:function () {
        if (G.vk.viewer_id == 982006)
            G.spisok.result_view.append(" " + G.spisok.data.time);
    }
});






