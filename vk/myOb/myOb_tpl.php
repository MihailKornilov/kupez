<?php
include('incHeader.php');
?>

<SCRIPT LANGUAGE="JavaScript">
$(document).ready(function(){
  $("#links").infoLink({
    spisok:[
      {uid:0,title:'Все объявления'},
      {uid:1,title:'Активные'},
      {uid:2,title:'Архив'}],
     func:function (uid) { obSpisok({menu:uid}); }
  });

  obSpisok();
});

function obSpisok(OBJ)
  {
	var OBJ = $.extend({
  	page:1,
  	view:$("#spisok"),
  	menu:0
    },OBJ);

  $("#findResult").find('IMG').remove().end().append("<IMG src=/img/upload.gif>");

  $("#links .infoLinkSel").attr('class','infoLink');
  $("#links DIV:eq("+OBJ.menu+")").attr('class','infoLinkSel');

	var myOb="<A href='<?php echo $URL; ?>&my_page=vk-create&back=vk-myOb' class=vk-ob-a>Разместить объявление</A>";

	var URL="&page="+OBJ.page;
	URL+="&menu="+OBJ.menu;

  $.getJSON("/vk/myOb/AjaxObSpisok.php?" + G.values + URL,function(data){
  	if(data[0].count>0)
      {
    	var HTML='';
    	for(var n=0;n<data.length;n++)
        {
      	HTML+="<DIV id=unit"+data[n].id+" class=unit style=background-color:#"+(data[n].active==1?'DFD':'EEE')+";>";
      	HTML+="<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>";
    
        	HTML+="<TABLE cellpadding=0 cellspacing=5>";
        	HTML+="<TR><TD class=tdAbout>Рубрика:<TD class=rub>"+data[n].rub+(data[n].podrub?"<SPAN class=ug>»</SPAN>"+data[n].podrub:'');
        	HTML+="<TR><TD class=tdAbout valign=top>Текст:<TD class=txt>"+data[n].txt;
        	if(data[n].telefon) HTML+="<TR><TD class=tdAbout>Телефон:<TD>"+data[n].telefon;
        	if(data[n].adres) HTML+="<TR><TD class=tdAbout>Адрес:<TD>"+data[n].adres;
        	if(data[n].vk_name) HTML+="<TR><TD class=tdAbout>Имя из VK:<TD><A href='http://vk.com/id"+data[0].viewer_id+"' target=_vk class=vk_name>"+data[n].vk_name+"</A>";
        	HTML+="<TR><TD class=tdAbout>Размещено:<TD>"+data[n].dtime;
        	HTML+="</TABLE>";

        	if(data[n].file) HTML+="<TD class=image><IMG src=/files/images/"+data[n].file+"s.jpg onclick=fotoShow('"+data[n].file+"');>";
        	HTML+="<TR><TD colspan=2>";
          	HTML+="<TABLE cellpadding=0 cellspacing=5>";
          	HTML+="<TD class=tdAbout>Актуальность:<TD><DIV class=edit>";
          	if(data[n].active==1) HTML+="<SPAN><A href='javascript:' onclick=goArchiv("+data[n].id+",this);>В архив</A> | </SPAN>";
          	HTML+="<A href='<?php echo $URL; ?>&my_page=vk-myObEdit&id="+data[n].id+"'>Изменить</A> | ";
          	HTML+="<A href='javascript:' onclick=obDel("+data[n].id+",this);>Удалить</A>";
          	HTML+="</DIV><EM>"+(data[n].active==1?data[n].day_last:'В архиве')+"</EM>";
          	HTML+="</TABLE>";

      	HTML+="</TABLE>";
      	HTML+="</DIV>";
        }
    	if(data[0].page>0) HTML+="<DIV><DIV id=ajaxNext onclick=zayavNext("+data[0].page+");>Показать ещё объявления</DIV></DIV>";
      $("#findResult").html(myOb+data[0].result);
    	OBJ.view.html(HTML);
      $(".unit").hover(function(){ $(this).find(".edit").show(); },function(){ $(this).find(".edit").hide(); });
      }
  	else
      {
      $("#findResult").html(myOb+data[0].result);
    	OBJ.view.html("<DIV class=findEmpty>Объявлений не найдено.</DIV>");
      }
  	frameBodyHeightSet();
    });
  }

function obDel(ID,OBJ)
  {
  $(OBJ).attr('onclick','');
  $.getJSON("/vk/myOb/AjaxObDel.php?" + G.values + "&id="+ID,function(){
    $("#unit"+ID).hide().after("<DIV id=del"+ID+" class=deleted>Объявление удалено. <A href='javascript:' onclick=obRec("+ID+",this);>Восстановить</A></DIV>");
    });
  }
function obRec(ID,OBJ)
  {
  $(OBJ).attr('onclick','');
  $.getJSON("/vk/myOb/AjaxObRec.php?" + G.values + "&id="+ID,function(){
    $("#del"+ID).remove();
    $("#unit"+ID).show();
    });
  }

function goArchiv(ID,OBJ)
  {
  $(OBJ).parent().html("<IMG src=/img/upload.gif>");
  $.getJSON("/vk/myOb/AjaxObGoArchiv.php?" + G.values + "&id="+ID,function(){
    $("#unit"+ID)
      .css('background-color','#EEE')
      .find(".edit").find("IMG").remove().end()
      .next().html("В архиве");

    });
  }
</SCRIPT>

<DIV class=path><A href="<?php echo $URL; ?>">Газета Купецъ</A> » Мои объявления</DIV>

<DIV id=findResult>&nbsp;</DIV>

<TABLE cellpadding=0 cellspacing=0 id=vk-myOb>
<TR>
  <TD id=spisok>&nbsp;
  <TD id=cond><DIV id=links></DIV>

</TABLE>
<?php include('incFooter.php'); ?>



