<?php
$client=$VK->QueryObjectOne("select * from client where id=".(preg_match("|^[\d]+$|",$_GET['id'])?$_GET['id']:0));
if(!$client->id)  header("Location: ". $URL."&my_page=nopage&parent=client");

$zc=$VK->QRow("select count(id) from zayav where client_id=".$client->id); if($zc>0) $zayav_count="<EM>".$zc."</EM>";
$oc=$VK->QRow("select count(id) from oplata where client_id=".$client->id); if($oc>0) $oplata_count="<EM>".$oc."</EM>";
$mc=$VK->QRow("select count(id) from vk_comment where table_name='client' and table_id=".$client->id); if($mc>0) $msg_count="<EM>".$mc."</EM>";

switch($_GET['msg']){ case 'zdel': $msg="Заявка удалена!"; break; }
include('incHeader.php');
?>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
  $("#links").infoLink({
    spisok:[
      {uid:1,title:'Информация'},
      {uid:2,title:'Редактировать'},
      {uid:3,title:'Новая заявка'},
      {uid:4,title:'Принять платёж'}
      <?php if($zc==0 and $oc==0) echo ",{uid:5,title:'Удалить клиента'}"; ?>
    ],
    func:function (uid) {
      switch (uid) {
      case '2': clientEdit(); break;
      case '3': location.href="/index.php?" + G.values + "&my_page=zayavAdd&client_id=<?php echo $client->id; ?>"; break;
      case '4': oplataInsert(); break;
      case '5': clientDel(); break;
      }
    }
  });

  $("#spLinks").infoLink({
    spisok:[
      {uid:0,title:'<?php echo $zayav_count; ?>Заявки'},
      {uid:1,title:'<?php echo $oplata_count; ?>Платежи'},
      {uid:2,title:'<?php echo $msg_count; ?>Заметки'}],
    func:function (uid) {
      switch (uid) {
      case '0': zayavShow(this); break;
      case '1': oplataShow(this); break;
      case '2': commShow(this); break;
      }      
    }
  });



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




function getClient(res) {
  var HTML="<TABLE cellpadding=0 cellspacing=4>";
  HTML+="<TR><TD class=tdAbout>Заявитель:<INPUT type=hidden id=edit_person_id value="+res.person_id +"><TD class=person_name>"+res.person_name;
  HTML+="<TR"+(res.org_name?'':' style=display:none')+">  <TD class=tdAbout>Организация:    <TD><B id=edit_org_name>"+res.org_name+"</B>";
  HTML+="<TR"+(res.fio?'':' style=display:none')+">        <TD class=tdAbout>Имя:          <TD"+(!res.org_name?' style=font-weight:bold':'')+"  id=edit_fio>"+res.fio+"</TD>";
  HTML+="<TR"+(res.telefon?'':' style=display:none')+">    <TD class=tdAbout>Телефоны:       <TD id=edit_telefon>"+res.telefon+"</TD>";
  HTML+="<TR"+(res.adres?'':' style=display:none')+">      <TD class=tdAbout>Адрес          <TD id=edit_adres>"+res.adres+"</TD>";
  HTML+="<TR><TD class=tdAbout>Баланс:            <TD><B style=color:#<?php echo ($client->balans<0?'A00':'090'); ?>><?php echo $client->balans; ?></B>";
  HTML+="</TABLE>";
  HTML+="<DIV class=info>"+res.info+"</DIV>";
  $("#tab").html(HTML);
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

function clientDel() {
  dialogShow({
    top:100,
    width:250,
    head:"Удаление",
    butSubmit:"Удалить",
    content:"<CENTER><B>Подтвердите удаление клиента</B></CENTER>",
    cancel:function () { $("#links").infoLinkSet(1); },
    submit:function(){
      $.getJSON("/gazeta/client/AjaxClientDel.php?" + G.values + "&id=<?php echo $client->id; ?>",function(){ location.href="<?php echo $URL; ?>&my_page=client"; },'json');
      }
    });
  }

</SCRIPT>

<?php $mLink1='Sel'; include 'gazeta/mainLinks.php'; ?>

<INPUT type=hidden id=msg value="<?php echo $msg; ?>";>
<TABLE cellpadding=0 cellspacing=0 width=100%>
<TR>
  <TD id=clientInfo>
    <DIV id=tab><IMG src=/img/upload.gif></DIV>
    
    <DIV id=zHead><DIV id=zResult><IMG src=/img/upload.gif></DIV>Список заявок</DIV>
    
    <DIV id=zSpisok></DIV>

  <TD id=clientRight>
    <DIV id=links></DIV>
    <DIV id=spLinks></DIV>

    <DIV id=catDop>
      <DIV class=findName>Категория</DIV><INPUT TYPE=hidden id=category value=0>
    </DIV>

</TABLE>



<?php include('incFooter.php'); ?>

