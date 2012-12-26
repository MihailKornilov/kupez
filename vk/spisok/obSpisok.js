$("#vkFind").topSearch({
  txt:'Поиск объявлений: введите слово и нажмите Enter',
  enter:1,
  func:obSpisok,
  focus:1,
  width:300
});
  
$("#type_gaz").myCheck({name:"Газетный вариант",func:obSpisok});
$("#foto_only").myCheck({name:"Только с фото",func:obSpisok});

// перевод рубрик в ассоциативный массив
spisok.rubAss = objToArr(spisok.rubrika);

// перевод подрубрик в ассоциативный массив
spisok.podRubAss = [];
for (var n = 0; n < spisok.rubrika.length; n++) {
  var rub = spisok.podRubrika[spisok.rubrika[n].uid];
  if (rub) {
    for (var k = 0; k < rub.length; spisok.podRubAss[rub[k].uid] = rub[k].title, k++);
  }
}

// добавление количества объявлений к каждой рубрике
for (var n = 0; n < spisok.rubrika.length; n++) {
  sp = spisok.rubrika[n];
  sp.title += "<B>" + spisok.rubCount[sp.uid] + "</B>";
}
spisok.rubrika.unshift({uid:0,title:'Все объявления'});
$("#rubrika").infoLink({
  spisok:spisok.rubrika,
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
    spisok.cache_new = 1;
    obSpisok();
    spisok.cache_new = 0;
  });
} 


obSpisok();


function rubrikaSet(uid, podrub) {
    spisok.values.rub = uid;
    spisok.values.podrub = podrub || 0;
    if(spisok.podRubrika[uid]) {
      $("#podrubrika").html("<DIV class=findName>Подрубрика</DIV><INPUT TYPE=hidden id=podrub value=" + spisok.values.podrub + ">");
      $("#podrub").vkSel({
        width:150,
        title0:'Подрубрика не указана',
        spisok:spisok.podRubrika[uid],
        func:function (uid) { spisok.values.podrub = uid; obSpisok(); }
      });
    } else {
      $("#podrubrika").html('');
    }
    obSpisok();
  }


/*
<?php
  if($vkUser->app_setup == 0) {
    if(!$VK->QRow("select id from hint_no_show where hint_id=1 and viewer_id=".$_GET['viewer_id'])) {
      echo "
      $('#vk-ob').alertShow({
        otstup:160,
        delayShow:10000,
        delayHide:15000,
        ugol:'top',
        left:400,
        txt:hintTxt('".$VK->QRow("select txt from hint where id=1")."',1)
      });
      ";
    }
  } else {
    if($vkUser->menu_left_set == 0) {
      if(!$VK->QRow("select id from hint_no_show where hint_id=2 and viewer_id=".$_GET['viewer_id'])) {
        echo "
        $('#vk-ob').alertShow({
          otstup:160,
          delayShow:10000,
          delayHide:15000,
          ugol:'top',
          left:405,
          txt:hintTxt('".$VK->QRow("select txt from hint where id=2")."',2)
        });
        ";
      }
    }
  }
?>
*/


// перевод json-списка в ассоциативный массив
function objToArr(obj) {
  var arr = [];
  for (var n = 0; n < obj.length; arr[obj[n].uid] = obj[n].title, n++);
  return arr;
}


function obSpisok(OBJ) {
  var OBJ = $.extend({
    page:1,
    view:$("#spisok")
  },OBJ);

  $("#findResult").find('IMG').remove().end().append("<IMG src=/img/upload.gif>");

  var URL = "&page=" + OBJ.page;
  URL += "&rub=" + spisok.values.rub;
  URL += "&podrub=" + spisok.values.podrub;
  URL += "&foto_only=" + $("#foto_only").val();
  URL += "&cache_new=" + spisok.cache_new;
  var INP=$("#vkFind_input").val(); if(INP) URL+="&input="+encodeURIComponent(INP);

  if($("#type_gaz").val() == 0) {
    if (spisok.ob) {
      obSpisokPrint(OBJ, spisok.ob);
      spisok.ob = null;
    } else {
      $.getJSON("/vk/spisok/AjaxObSpisok.php?" + G.values + URL, function (data) { obSpisokPrint(OBJ, data); });
    }
  } else {
    $.getJSON("/vk/spisok/AjaxObSpisokGaz.php?"+ G.values + URL,function(data){
      $("#findResult").html(data.result);
      OBJ.view.html(data.html);
      frameBodyHeightSet();
    });
  }
}

function obSpisokPrint(OBJ, data) {
  var myOb = "<A href='/index.php?"+ G.values +"&my_page=vk-myOb' class=vk-ob-a>Мои объявления</A>" + spisok.enter + data.time;

  var len = data.spisok.length;
  var HTML = '';
  if (len > 0) {
    for(var n = 0; n < len; n++) {
      var sp = data.spisok[n];
      HTML+="<DIV class=unit>";
        HTML+="<DIV class='"+sp.dop+"'>";
        HTML+="<TABLE cellpadding=0 cellspacing=0 width=100%>";
        HTML+="<TR><TD class=txt>";
        HTML+="<A val=r_"+sp.rubrika+" class=aRub>" + spisok.rubAss[sp.rubrika] + "</A> » ";
        if (sp.podrubrika > 0) { HTML+="<A val=p_"+sp.rubrika+"_"+sp.podrubrika+" class=aRub>" + spisok.podRubAss[sp.podrubrika] + "</A> » "; }
        HTML+=sp.txt;
        if (sp.telefon) HTML+=" <B>Тел.: "+sp.telefon+"</B>";
        if (sp.adres) HTML+=" <B>Адрес: "+sp.adres+"</B>";
        if (sp.viewer_id > 0) HTML+="<A href='http://vk.com/id"+sp.viewer_id+"' target=_vk class=vk_name>"+sp.vk_name+"</A>";
        if (sp.file) HTML+="<TD width=80 align=center valign=top><IMG src=/files/images/"+sp.file+"s.jpg onclick=fotoShow('"+sp.file+"');>";
        HTML+="</TABLE>";
        HTML+="</DIV>";
      HTML+="</DIV>";
      }
    if(data.page > 0) HTML+="<DIV><DIV id=ajaxNext onclick=zayavNext("+data.page+");>Показать ещё объявления</DIV></DIV>";
  }
  $("#findResult").html(myOb + (len > 0 ? data.result : "Объявлений не найдено"));
  OBJ.view.html(len > 0 ? HTML : "<DIV class=findEmpty>Объявлений не найдено.</DIV>");
  frameBodyHeightSet();
}




function zayavNext(P) {
  $("#ajaxNext").css("padding","7px").html("<IMG SRC=/img/upload.gif>");
  obSpisok({page:P,view:$("#ajaxNext").parent()});
}

function enter() {
  setCookie('enter',1);
  location.href='/index.php?' + G.values;
}
