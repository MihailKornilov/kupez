var visit = {
  place:[]
};

$("#findRadio").myRadio({
  spisok:[
    {uid:1,title:'Все посетители'},
    {uid:2,title:'Заходили сегодня'},
    {uid:3,title:'В этом месяце'},
    {uid:4,title:'Размещали объявления'},
    {uid:5,title:'Установили приложение'},
    {uid:6,title:'Добавили в левое меню'}],
  bottom:7,
  func:visitSpisok
});

$("#with_ob").myCheck({name:"С объявлениями",func:visitSpisok});

visitSpisok();


function visitSpisok(OBJ) {
  var OBJ = $.extend({
    page:1,
    view:$("#spisok")
    },OBJ);

  $("#findResult").find('IMG').remove().end().append("<IMG src=/img/upload.gif>");

  var URL="&page="+OBJ.page;
  URL+="&radio="+$("#findRadio").val();
  $.getJSON("/vk/visit/user/AjaxVisitSpisok.php?" + G.values + URL,function(data){
    if(data[0].count > 0) {
      var HTML = '';
      var ENTER = '';
      visit.place = [];
      for(var n = 0; n < data.length; n++) {
        HTML += "<DIV class=unit>";
        HTML += "<TABLE cellspacing=0 cellpadding=0>";
        HTML += "<TR><TD class=img><A href='http://vk.com/id"+data[n].viewer_id+"' target=_vk><IMG src="+data[n].photo+"></A>";
        ENTER=''; if(data[n].count_day > 1) ENTER += "<SPAN>"+data[n].count_day+"x</SPAN>";
        HTML += "<TD valign=top><DIV class=time>"+ENTER+data[n].time+"</DIV><A href='http://vk.com/id"+data[n].viewer_id+"' target=_vk><B>"+data[n].last_name+" "+data[n].first_name+"</B></A>";
        HTML += "<DIV class=place></DIV>";
        if(data[n].ob_count > 0) HTML += "<DIV class=ob><A href='<?php echo $URL; ?>&my_page=vk-ob-user&id="+data[n].viewer_id+"'>Объявлений: "+data[n].ob_count+"</A></DIV>";
        HTML += "</TABLE></DIV>";
        visit.place.push({country:data[n].country,city:data[n].city});
      }
      if(data[0].page>0) HTML+="<DIV><DIV id=ajaxNext onclick=visitNext("+data[0].page+");>Показать ещё посетителей</DIV></DIV>";
      $("#findResult").html(data[0].result);
      OBJ.view.html(HTML);
/*
      VK.api('places.getCities',{country:1}, function(res) {
        var place = $("#vk-visit .place");
        for (var n = 0; n < visit.place.length; n++) {
          if (visit.place[n].country > 0) {
            
          }
        }
        alert(res.response[0].title)
      });
*/
    } else {
      $("#findResult").html("Запрос не дал результатов.");
      OBJ.view.html("<DIV class=findEmpty>Запрос не дал результатов.</DIV>");
    }
    frameBodyHeightSet();
  });
}

function visitNext(P) {
  $("#ajaxNext").html("<IMG SRC=/img/upload.gif>");
  visitSpisok({page:P,view:$("#ajaxNext").parent()});
}



