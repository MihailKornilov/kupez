$("#vkFind").topSearch({
  txt:'Поиск объявлений: введите слово и нажмите Enter',
  enter:1,
  func:function (val) { G.spisok.print({input:encodeURIComponent(val)}); },
  focus:1,
  width:300
});


// регион
$("#countries").vkSel({
  bottom:4,
  width:150,
  spisok:[{uid:1,title:'Россия'}]
});
$("#cities").vkSel({
  bottom:4,
  width:150,
  title0:'Город не указан',
  spisok:spisok.cities,
  func:function (uid) { G.spisok.print({city_id:uid}); }
});
$("#vkSel_cities").bind({
    mouseenter: function () {
      $("#ms_region").alertShow({
        txt:"Показываются города,<BR>для которых есть<BR>активные объявления.",
        ugol:'right',
        left:-150,
        top:22,
        otstup:0,
        delayShow:500,
        delayHide:0
      });
    },
    mouseleave: function () { $("#alert").remove(); }
  });


//$("#type_gaz").myCheck({name:"Газетный вариант",func:G.spisok.print});
$("#foto_only").myCheck({name:"Только с фото",func:function (id) { G.spisok.print({foto_only:$("#foto_only").val()}); }});

// добавление количества объявлений к каждой рубрике
for (var n = 0; n < G.rubrika_spisok.length; n++) {
  sp = G.rubrika_spisok[n];
  sp.title += "<B>" + spisok.rubCount[sp.uid] + "</B>";
}
G.rubrika_spisok.unshift({uid:0,title:'Все объявления'});
$("#rubrika").infoLink({
  spisok:G.rubrika_spisok,
  func:rubrikaSet
});



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

if ($("#cache_new").length > 0) {
  $("#cache_new").click(function () {
    G.spisok.print({cache_new:1});
    G.spisok.values.cache_new = 0;
  });
} 



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
  var HTML = "<DIV class='" + sp.dop + "'>";
  HTML += "<TABLE cellpadding=0 cellspacing=0 width=100%>";
  HTML += "<TR><TD class=txt>";
  HTML += "<A val=r_"+sp.rubrika+" class=aRub>" + G.rubrika_ass[sp.rubrika] + "</A><U>»</U>";
  if (sp.podrubrika > 0) { HTML+="<A val=p_"+sp.rubrika+"_"+sp.podrubrika+" class=aRub>" + G.podrubrika_ass[sp.podrubrika] + "</A><U>»</U>"; }
  HTML += sp.txt;
  if (sp.telefon) HTML += " <DIV class=tel>"+sp.telefon+"</DIV>";
  var name = '';
  if (sp.viewer_id > 0) { name = "<A href='http://vk.com/id"+sp.viewer_id+"' target=_vk>"+sp.viewer_name+"</A>"; }
  var city = '';
  if (sp.city_name) { city = sp.country_name + ", " + sp.city_name; }
  HTML += " <DIV class=adres>" + city + name + "</DIV>";
  if (sp.file) HTML += "<TD class=foto><IMG src=" + sp.file.split('_')[0] + "s.jpg onclick=fotoShow('" + sp.file + "');>";
  HTML += "</TABLE></DIV>";
  return HTML;
};

G.spisok.create({
  url:"/vk/spisok/AjaxObSpisok.php",
  view:$("#spisok"),
  limit:15,
  cache_spisok:spisok.ob,
  result_view:$("#findResult"),
  result:"Показано $count объявлени$ob",
  ends:{'$ob':['е', 'я', 'й']},
  next:"Показать ещё объявления",
  nofind:"Объявлений не найдено.",
  result_dop:"<A href='/index.php?"+ G.values +"&my_page=vk-myOb' class=vk-ob-a>Мои объявления</A>" + spisok.enter,
  imgup:"#rubrika .sel",
  values:{
    rub:0,
    podrub:0,
    foto_only:0,
    cache_new:0,
    city_id:0,
    input:''
  }
});






