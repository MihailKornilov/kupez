var linkSpisok = [
    {uid:1,title:'Информация'},
    {uid:2,title:'Редактировать'},
    {uid:3,title:'<b>Новая заявка</b>'},
    {uid:4,title:'Принять платёж'}
];
if (G.client.del == 0) linkSpisok.push({uid:5, title:'<span class=red>Удалить клиента</span>'});
$("#links").infoLink({
    spisok:linkSpisok,
    func:function (uid) {
        switch (uid) {
            case '2': clientAdd(function () { vkMsgOk("Данные клиента изменены."); location.reload(); }, G.client); break;
            case '3': location.href = G.url + "&p=gazeta&d=zayav&d1=add&client_id=" + G.client.id; break;
            case '4': oplataInsert(); break;
            case '5': clientDel(); break;
        }
    }
});


zayavShow($("#dopMenu A:first"));





function zayavShow(link) {
    $("#dopMenu A").attr('class', 'link')
    $(link).attr('class', 'linkSel')

    $("#money").html('');

    G.spisok.unit = function (sp) {
        return "<H1><EM>" + sp.dtime + "</EM><A href='" + G.url + "&p=gazeta&d=zayav&d1=view&id="+sp.id+"'>" + G.category_ass[sp.category] + " №" + sp.id + "</A></H1>" +
            "<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>" +
            "<TABLE cellpadding=0 cellspacing=4>" +
            (sp.client_id > 0 ? "<TR><TD class=tdAbout>Клиент:<TD><A HREF='" + G.url + "&p=gazeta&d=client&id=" + sp.client_id + "'>" + sp.client_fio + "</A>" : '') +
            (sp.category == 1 ?
                "<TR><TD class=tdAbout>Рубрика:<TD>" + G.rubrika_ass[sp.rubrika] + (sp.podrubrika > 0 ? "<SPAN class=ug>»</SPAN>" + G.podrubrika_ass[sp.podrubrika] : '') +
                    "<TR><TD class=tdAbout valign=top>Текст:<TD><DIV class=txt>" + sp.txt + "</DIV>" : '') +

            (sp.ob_dop ? "<TR><TD class=tdAbout>Доп. параметр:<TD>" + sp.ob_dop : '') +
            (sp.category == 2 ? "<TR><TD class=tdAbout>Размер:<TD>" + sp.size_x + " x " + sp.size_y + " = <b>" + sp.kv_sm + '</b> см&sup2;' : '') +
//    if(sp.telefon) HTML+="<TR><TD class=tdAbout>Телефон:<TD>"+sp.telefon;
//    if(sp.adres) HTML+="<TR><TD class=tdAbout>Адрес:<TD>"+sp.adres;

            "<TR><TD class=tdAbout>Стоимость:<TD><B>" + sp.summa + "</B> руб." + (sp.summa_manual == 1 ? '<SPAN class=manual>(указана вручную)</SPAN>' : '') +
            "</TABLE>" +

//    if(sp.file) HTML+="<TD class=image><IMG src=/files/images/"+sp.file+"s.jpg onclick=fotoShow('"+sp.file+"');>";

            "</TABLE>";
    };

    G.spisok.create({
        view:$("#zayav"),
        limit:15,
        json: G.client.zayav_spisok,
        result_view:$("#result"),
        result:"Показан$show $count заяв$zayav",
        ends:{'$show':['а', 'о'],'$zayav':['ка', 'ки', 'ок']},
        next:"Показать ещё заявки",
        nofind:"Заявок нет"
    });
}






function moneyShow(link) {
    $("#dopMenu A").attr('class', 'link')
    $(link).attr('class', 'linkSel')

    $("#zayav").html('');

    G.spisok.unit = function (sp) {
        var txt = sp.txt;
        if (sp.zayav_id > 0) { txt = "Оплата по заявке <A href='/index.php?" + G.values + "&p=gazeta&d=zayav&d1=view&id=" + sp.zayav_id + "'><EM>№</EM>" + sp.zayav_id + "</A>"; }
        var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%><TR>" +
            "<TD class=sum><B>" + sp.sum + "</B>" +
            "<TD class=about><b>" + G.money_type_ass[sp.type] + ":</b> " + txt +
            "<TD class=data>" + sp.dtime_add +
            "</TABLE>";
        return html;
    };

    G.spisok.create({
        view:$("#money"),
        limit:15,
        json: G.client.money_spisok,
        result_view:$("#result"),
        result:"Показан$show $count платеж$pay",
        ends:{'$show':['а', 'о'],'$pay':['', 'а', 'ей']},
        next:"Показать ещё...",
        nofind:"Платежей нет",
        callback:function (res) {
            if(res.length > 0) {
                var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%>" +
                    "<TR><TH class=sum>Сумма" +
                    "<TH class=about>Описание" +
                    "<TH class=data>Дата" +
                    "</TABLE>";
                $("#money").prepend(html);
            }
        }
    });

}








function clientDel() {
    var dialog = $("#dialog_client").vkDialog({
        width:250,
        head:"Удаление клиента",
        butSubmit:"Удалить",
        content:"<CENTER><B>Подтвердите удаление клиента</B></CENTER>",
        cancel:function () { $("#links").infoLinkSet(1); },
        submit:function () {
            dialog.process();
            $.getJSON("/view/gazeta/client/info/AjaxClientDel.php?" + G.values + "&id=" + G.client.id, function () {
                location.href = G.url + "&p=gazeta&d=client";
            }, 'json');
        }
    }).o;
} // end of clientDel()


/*
  $("#category").vkSel({
    width:146,
    title0:'Категория не указана',
    spisok:zayavCategoryVk,
    func:zayavSpisokGet
    });

  $.getJSON("/gazeta/client/AjaxClientEdit.php?"+G.values+"&id=<?php echo $client->id; ?>",getClient);

  zayavSpisokGet();
});


function clientEdit()
  {
  var HTML="<TABLE cellpadding=0 cellspacing=6 class=clientAdd>";
  HTML+="<TR><TD class=tdAbout>Заявитель:<TD><INPUT TYPE=hidden id=person value="+$("#edit_person_id").val()+">";
  HTML+="<TR><TD class=tdAbout>Название организации:<TD><INPUT TYPE=text id=org_name class=input value='"+$("#edit_org_name").html()+"'>";
  HTML+="<TR><TD class=tdAbout>Имя:<TD><INPUT TYPE=text id=fio class=input value='"+$("#edit_fio").html()+"'>";
  HTML+="<TR><TD class=tdAbout>Телефоны:<TD><INPUT TYPE=text id=telefon class=input value='"+$("#edit_telefon").html()+"'>";
  HTML+="<TR><TD class=tdAbout>Адрес:<TD id=ms><INPUT TYPE=text id=adres class=input value='"+$("#edit_adres").html()+"'>";
  HTML+="</TABLE>";
  dialogShow({
    width:440,
    top:60,
    butSubmit:'Сохранить',
    head:"Редактирование данных клиента",
    content:HTML,
    cancel:function () { $("#links").infoLinkSet(1); },
    submit:function () {
      if(!$("#fio").val() && !$("#org_name").val()) {
        $("#ms").alertShow({txt:"<DIV class=red>Необходимо указать имя клиента<BR>либо название организации.</DIV>",top:-3,left:-5});
        $("#org_name").focus();
      } else {
        $("#butDialog").butProcess();
        $.post("/gazeta/client/AjaxClientEdit.php?"+G.values+"&id=<?php echo $client->id; ?>",{
          person:$("#person").val(),
          org_name:$("#org_name").val(),
          fio:$("#fio").val(),
          telefon:$("#telefon").val(),
          adres:$("#adres").val()
          },function(res){
            $("#edit_person_name").html(res.person);
            getClient(res);
            dialogHide();
            vkMsgOk("Данные клиента изменены!");
            $("#links").infoLinkSet(1);
            },'json');
        }
      },
    });
  $("#person").vkSel({
    width:180,
    spisok:<?php echo vkSelGetJson("select id,name from setup_person order by sort"); ?>
    });
  }








// ПРОСМОТР ЗАЯВОК
function zayavShow(OBJ)
  {
  $("#spLinks .infoLinkSel").attr('class','infoLink');
  $(OBJ).attr('class','infoLinkSel');
  $("#zHead").html("<DIV id=zResult><IMG src=/img/upload.gif></DIV>Список заявок").show();
  $("#catDop").show();
  $("#zSpisok").html('');
  zayavSpisokGet();
  }

function zayavSpisokGet(OBJ)
  {
  var OBJ = $.extend({
    page:1,
    view:$("#zSpisok")
    },OBJ);

  var URL="&page="+OBJ.page;
  URL+="&category="+$("#category").val();

  $.getJSON("/gazeta/zayav/AjaxZayavSpisok.php?<?php echo $VALUES; ?>"+URL+"&client=<?php echo $client->id; ?>",function(data){
    if(data[0].count>0)
      {
      var HTML='';
      for(var n=1;n<data.length;n++)
        {
        HTML+="<DIV class=zayavUnit>";
          HTML+="<H1><EM>"+data[n].dtime+"</EM><A href='<?php echo $URL; ?>&my_page=zayavView&id="+data[n].id+"'>"+data[n].cat_name+" №"+data[n].id+"</A></H1>";
  
          HTML+="<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>";
  
            HTML+="<TABLE cellpadding=0 cellspacing=2>";
            if(data[n].cat_id==1) HTML+="<TR><TD class=tdAbout>Текст:<TD><DIV class=txt>"+data[n].txt+"</DIV>";
            if(data[n].cat_id==2) HTML+="<TR><TD class=tdAbout>Размер:<TD>"+data[n].size_x+" x "+data[n].size_y+" = "+data[n].kv_sm;
            HTML+="<TR><TD class=tdAbout>Стоимость:<TD><B>"+data[n].summa+"</B> руб.";
            HTML+="</TABLE>";

          if(data[n].file) HTML+="<TD class=image><IMG src=/files/images/"+data[n].file+"s.jpg>";

          HTML+="</TABLE>";
        HTML+="</DIV>";
        }
      if(data[0].page>0) HTML+="<DIV><DIV id=ajaxNext onclick=zayavNext("+data[0].page+");>Следующие 20 обьявлений</DIV></DIV>";
      $("#zResult").html(data[0].result);
      OBJ.view.html(HTML);
      }
    else
      {
      $("#zResult").html("Заявки не найдены.");
      OBJ.view.html('');
      }

    frameBodyHeightSet();
    });
  }





// ВНЕСЕНИЕ ПЛАТЕЖА
function oplataInsert() {
  var HTML="<TABLE cellpadding=0 cellspacing=6 id=oplataInsert>";
  HTML+="<TR><TD class=tdAbout>Вид платежа:<TD><INPUT TYPE=hidden id=oplata_tip value=1>";
  HTML+="<TR><TD class=tdAbout>Сумма:<TD id=pn><INPUT TYPE=text id=summa maxlength=6>";
  HTML+="<TR><TD class=tdAbout>Примечание:<TD><INPUT TYPE=text id=prim maxlength=250>";
  HTML+="</TABLE>";
  dialogShow({
    top:60,
    head:"Внесение платежа",
    content:HTML,
    cancel:function () { $("#links").infoLinkSet(1); },
    submit:function () {
      var SUMMA=$("#summa").val();
      if(!SUMMA) $("#pn").alertShow({txt:"<SPAN class=red>Необходимо ввести сумму</SPAN>",top:-42,left:-3});
      else
        {
        var reg = /^[0-9]*$/i;
        if(reg.exec(SUMMA)==null) $("#pn").alertShow({txt:"<SPAN class=red>Некорректно введена сумма</SPAN>",top:-42,left:-3});
        else
          {
          $("#butDialog").butProcess();
          $.post("/gazeta/client/AjaxOplataInsert.php?"+G.values,{
            client_id:<?php echo $client->id; ?>,
            tip:$("#oplata_tip").val(),
            summa:SUMMA,
            prim:$("#prim").val()
            },function(res){
              $("#balans").html("<B style=color:#"+(res.balans<0?'A00':'090')+">"+res.balans+"</B>");
              dialogHide();
              vkMsgOk("Новый платёж внесён!");
              $("#links").infoLinkSet(1);
              },'json');
          }
        }
      },
    focus:'#summa'
    });
  $("#oplata_tip").vkSel({
    width:120,
    spisok:[{uid:1,title:'Наличный'},{uid:2,title:'Безналичный'},{uid:3,title:'Взаимозачёт'}]
    });
  }



// ПРОСМОТР ПЛАТЕЖЕЙ
function oplataShow(OBJ) {
  $("#spLinks .infoLinkSel").attr('class','infoLink');
  $(OBJ).attr('class','infoLinkSel');
  $("#zHead").html("<DIV id=zResult><IMG src=/img/upload.gif></DIV>Список платежей").show();
  $("#catDop").hide();
  $("#zSpisok").html('');
  var URL="&page=1";
  $.getJSON("/gazeta/client/AjaxOplataSpisok.php?<?php echo $VALUES; ?>"+URL+"&client=<?php echo $client->id; ?>",function(data){
    if(data[0].count>0)
      {
      var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>";
      HTML+="<TR><TH>Сумма<TH>Примечание<TH>Дата<TH>Принял";
      for(var n=0;n<data[0].count;n++)
        HTML+="<TR><TD align=center width=40><B>"+data[n].summa+"</B><TD>"+data[n].prim+"<TD class=dtime>"+data[n].dtime+"<TD width=90>"+data[n].viewer_id;
      HTML+="</TABLE>";
      $("#zResult").html(data[0].result);
      $("#zSpisok").html(HTML);
      }
    else $("#zResult").html("Платежей нет");
    });
  }

// ПРОСМОТР ЗАМЕТОК
function commShow(OBJ)
  {
  $("#spLinks .infoLinkSel").attr('class','infoLink');
  $(OBJ).attr('class','infoLinkSel');
  $("#zHead").hide();
  $("#catDop").hide();
  $("#zSpisok").html('').vkComment({
    width:444,
    table_name:'client',
    table_id:<?php echo $client->id; ?>
    });
  }



*/