var myob = { spisok:[] };

$("#spisok").click(function (e) {
  var val = $(e.target).attr('val');
  if (val) {
    var arr = val.split('_');
    var sp = myob.spisok[arr[1]];
    switch (arr[0]) {
    case 'edit': G.obEdit(sp); break;
    case 'archiv': G.goArchiv(sp); break;
    case 'del': G.obDel(sp); break;
    case 'user': G.spisok.values.viewer_id_add = arr[1]; G.spisok.print({page:1}); break;
    }
  }
});


G.obEdit = function (sp) {
  var HTML = "<TABLE cellpadding=0 cellspacing=8 id=ob_edit>";
  HTML += "<TR><TD class=tdAbout>Рубрика:<TD><INPUT TYPE=hidden id=rubrika value=" + sp.rubrika + "><TD><INPUT TYPE=hidden id=podrubrika value=" + sp.podrubrika + ">";
  HTML += "<TR><TD class=tdAbout valign=top>Текст:<TD colspan=2><TEXTAREA id=txt>" + sp.txt.replace(/<BR>/g,"\n") + "</TEXTAREA>";
  HTML += "<TR><TD class='tdAbout top5' valign=top>Загрузить изображение:<TD colspan=2><INPUT TYPE=hidden id=images value='" + sp.file + "'>";
  HTML += "<TR><TD class=tdAbout>Контактные телефоны:<TD colspan=2><INPUT TYPE=text id=telefon maxlength=200 value='" + sp.telefon + "'>";
  HTML += "<TR><TD class='tdAbout top5' valign=top>Регион:<TD colspan=2 id=ms_adres><INPUT TYPE=hidden id=countries value=1><INPUT TYPE=hidden id=cities value=" + sp.city_id + ">";
  HTML += "<TR><TD class=tdAbout>Показывать имя из VK:<TD colspan=2><INPUT TYPE=hidden id=viewer_id_show value=" + sp.viewer_id_show + ">";
  HTML += "<TR id=active><TD colspan=3>Объявление будет размещено сроком на 1 месяц. <A>Отправить в архив</A>";
  HTML += "<TR id=archiv><TD colspan=3>Объявление будет отправлено в архив.<A>Сделать активным</A>";
  HTML += "</TABLE>";
  dialogShow({
    width:520,
    top:30,
    head:"Редактирование объявления",
    content:HTML,
    butSubmit:"Сохранить",
    submit:save
  });

  var active = sp.active;
  if (active == 1) {  $("#active").show(); } else { $("#archiv").show(); }

  // вывод списка рубрик и подрубрик
  $("#rubrika").vkSel({
    width:120,
    spisok:G.rubrika_spisok,
    func:function(uid){
      $("#podrubrika").val(0);
      $("#vkSel_podrubrika").remove();
      podrubPrint(uid);
    }
  });

  podrubPrint($("#rubrika").val());

  function podrubPrint(uid) {
    if(G.podrubrika_spisok[uid]) {
      $("#podrubrika").vkSel({
        width:201,
        title0:'Подрубрика не указана',
        spisok:G.podrubrika_spisok[uid]
      });
    }
  }

  // автоматическое расширение текствого поля
  $("#txt")
    .autosize({callback:frameBodyHeightSet})
    .focus();

  // загрузка изображений
  $("#images").imgUpload({max:4});

  // вывод стран
  $("#countries").vkSel({
    bottom:4,
    width:180,
    spisok:[{uid:1,title:'Россия'}]
  });

// список городов
  $("#cities").vkSel({
    ro:0,
    msg:'Город не указан',
    title0:'Город не указан',
    width:180,
    spisok:G.cities,
    vkfunc:keyupCity
  });

  function keyupCity(val) {
    if (val.length > 0) {
      VK.api('places.getCities', {country:$("#countries").val(), q:val}, function (data) {
        for(var n = 0; n < data.response.length; n++) {
          var c = data.response[n];
          c.uid = c.cid;
          c.content = c.title + (c.area?"<DIV class=pole2>" + c.area + "</DIV>" : '');
        }
		    $("#cities").val(0).vkSel({create:0, spisok:data.response});
		  });
    } else {
      $("#cities").val(0).vkSel({create:0, spisok:G.cities});
    }
  }

  // Показывать имя из VK
  $("#viewer_id_show").myCheck();

  $("#active A").click(function () {
    $("#active").hide();
    $("#archiv").show();
    active = 0;
  });

  $("#archiv A").click(function () {
    $("#active").show();
    $("#archiv").hide();
    active = 1;
  });


  function save() {
    var country_id = $("#countries").val();
    var country_name = $("#vkSel_countries INPUT:first").val();
    var city_id = $("#cities").val();
    var city_name = $("#vkSel_cities INPUT:first").val();

    var obj = {
      id:sp.id,
      rubrika:$("#rubrika").val(),
      podrubrika:$("#podrubrika").val(),
      txt:$("#txt").val(),
      telefon:$("#telefon").val(),
      file:$("#images").val(),
      country_id:country_id,
      country_name:country_id != '0' ? country_name : '',
      city_id:city_id,
      city_name:city_id != '0' ? city_name : '',
      viewer_id:sp.viewer_id,
      viewer_id_show:$("#viewer_id_show").val(),
      active:active
    };

    if (!obj.txt) {
      $("#dialog H3:first").alertShow({txt:"<DIV class=red>Введите текст объявления</DIV>", top:-45, left:180});
    } else {
      $(".vkButton BUTTON").butProcess();
        $.post("/vk/myOb/AjaxObEdit.php?" + G.values, obj, function (res) {
          for (var k in obj) { sp[k] = obj[k]; }
          sp.txt = sp.txt.replace(/\n/g,"<BR>");
          sp.day_last = res.dtime;
       //   sp.viewer_name = res.viewer_name;
          $("#unit_" + sp.id).html(G.spisok.unit(sp));
          dialogHide();
          frameBodyHeightSet();
        },'json');
    }
  }

};




G.goArchiv = function (sp) {
  $("#unit_" + sp.id + " A:first").html("<IMG src=/img/upload.gif>");
  $.getJSON("/vk/myOb/AjaxObGoArchiv.php?" + G.values + "&id=" + sp.id, function () {
    sp.active = 0;
    $("#unit_" + sp.id).html(G.spisok.unit(sp));
  });
};


G.obDel = function (sp) {
  dialogShow({
    width:300,
    head:"Удаление объявления",
    content:"<CENTER>После удаления объявления<BR>его невозможно будет восстановить.<BR><BR>Подтвердите удаление.</CENTER>",
    butSubmit:"Удалить",
    submit:function () {
      $.getJSON("/vk/myOb/AjaxObDel.php?" + G.values + "&id=" + sp.id, function () {
        $("#unit_" + sp.id).remove();
        dialogHide();
        vkMsgOk("Объявление удалено.");
        frameBodyHeightSet();
      });
    }
  });
};











G.spisok.unit = function (sp) {
  var HTML = "<DIV class=head>" + sp.dtime + (sp.active == 1 ? "<TT>" + sp.day_last + "</TT>" : "<EM>В архиве</EM>");
  if (G.spisok.values.type == 'all') HTML += "<A class=user val=user_" + sp.viewer_id+">" + sp.viewer_name + "</A>";

  HTML += "<H2><A style=visibility:" + (sp.active == 1 ? 'visible' : 'hidden') + "; val=archiv_" + sp.num + ">в архив</A>";
  HTML += "<DIV class=img_edit val=edit_" + sp.num + "></DIV>";
  HTML += "<DIV class=img_del val=del_" + sp.num + "></DIV></H2></DIV>";

  HTML += "<DIV class='ob " + sp.dop + (sp.active == 0 ? ' archiv' : '') + "'>";
  HTML+="<TABLE cellpadding=0 cellspacing=0 width=100%>";
  HTML+="<TR><TD class=txt>";
  HTML+="<SPAN class=aRub>" + G.rubrika_ass[sp.rubrika] + "</SPAN><U>»</U>";
  if (sp.podrubrika > 0) { HTML+="<SPAN class=aRub>" + G.podrubrika_ass[sp.podrubrika] + "</SPAN><U>»</U>"; }
  HTML+=sp.txt;
  if (sp.telefon) HTML+=" <DIV class=tel>" + sp.telefon + "</DIV>";
  var name = '';
  if (sp.viewer_id_show > 0) { name = "<A href='http://vk.com/id" + sp.viewer_id+"' target=_vk>" + sp.viewer_name + "</A>"; }
  var city = '';
  if (sp.city_name) { city = sp.country_name + ", " + sp.city_name; }
  HTML += " <DIV class=adres>" + city + name + "</DIV>";
  if (sp.file) HTML+="<TD class=foto><IMG src=" + sp.file.split('_')[0] + "s.jpg onclick=fotoShow('" + sp.file + "');>";
  HTML+="</TABLE></DIV>";
  return HTML;
}



