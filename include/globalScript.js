var G = {};
G.months = {1:'Январь',2:'Февраль',3:'Март',4:'Апрель',5:'Май',6:'Июнь',7:'Июль',8:'Август',9:'Сентябрь',10:'Октябрь',11:'Ноябрь',12:'Декабрь'};


var vkScroll = 0;


// ------============== КАЛЕНДАРЬ =============-----------------///////////////////////////////////////////////////////////////////
var getMon = {'Jan':1,'Feb':2,'Mar':3,'Apr':4,'May':5,'Jun':6,'Jul':7,'Aug':8,'Sep':9,'Oct':10,'Nov':11,'Dec':12};
var getMonRus = {1:'Январь',2:'Февраль',3:'Март',4:'Апрель',5:'Май',6:'Июнь',7:'Июль',8:'Август',9:'Сентябрь',10:'Октябрь',11:'Ноябрь',12:'Декабрь'};
var getMonRus1 = {'Jan':'января','Feb':'февраля','Mar':'марта','Apr':'апреля','May':'мая','Jun':'июня','Jul':'июля','Aug':'августа','Sep':'сентября','Oct':'октября','Nov':'ноября','Dec':'декабря'};
var getMonRus2 = {1:'января',2:'февраля',3:'марта',4:'апреля',5:'мая',6:'июня',7:'июля',8:'августа',9:'сентября',10:'октября',11:'ноября',12:'декабря'}
var getMonRus3 = {1:'янв.',2:'фев.',3:'мар.',4:'апр.',5:'май',6:'июн.',7:'июл.',8:'авг.',9:'сен.',10:'окт.',11:'ноя.',12:'дек.'}
var getMonRus4 = {1:'янв',2:'фев',3:'мар',4:'апр',5:'май',6:'июн',7:'июл',8:'авг',9:'сен',10:'окт',11:'ноя',12:'дек'}
var getMyWeek = {'Mon':'пн','Tue':'вт','Wed':'ср','Thu':'чт','Fri':'пт','Sat':'сб','Sun':'вс'};
var getMyWeekFull = {'Mon':'понедельник','Tue':'вторник','Wed':'среда','Thu':'четверг','Fri':'пятница','Sat':'суббота','Sun':'воскресенье'};
var cINPUT;
var calFunc='CalendarClose';

function getFullDay(data)
  {
  // среда, 14 апреля 2010
  var arr=data.split(/-/);
  var now = new Date(arr[0],Math.abs(arr[1])-1,arr[2]).toString();
  var arrNow=now.split(/ /);
  return getMyWeekFull[arrNow[0]]+", "+arr[2]+" "+getMonRus2[arr[1]]+" "+arr[0];
  }

function getSokrDay(data)
  {
  // ср. 14 апр. 2010
  var arr=data.split(/-/);
  var now = new Date(arr[0],Math.abs(arr[1])-1,arr[2]).toString();
  var arrNow=now.split(/ /);
  return getMyWeek[arrNow[0]]+". "+arr[2]+" "+getMonRus3[arr[1]]+" "+arr[0];

  }
Date.prototype.getWeek = function()
  {
  var wFirst=0;
  var onejan = new Date(this.getFullYear(),0,1,0,0,0);
  var dayW=onejan.getDay();
  if(dayW==5 || dayW==6) wFirst=1;
  return Math.ceil((((this - onejan) / 86400000) + dayW)/7)-wFirst;
  }

function Calendar(myDay,e,func)
  {
  var now;
  var calYear;
  var calMon;
  var calDay;
  var arr;

  if(func) calFunc=func;

// ====== получаем текущий день ==========
  now = new Date().toUTCString();
  arr=now.split(/ /);
  var today=arr[3]+"-"+getMon[arr[2]]+"-"+arr[1];
  var todayLink=Math.abs(arr[1])+" "+getMonRus1[arr[2]]+" "+arr[3];


  if(myDay)
    {
    arr=myDay.split(/-/);
    calYear=Math.abs(arr[0]);
    calMon=Math.abs(arr[1]);
    calDay=Math.abs(arr[2]);
    }
  else
    {
    now = new Date().toUTCString();
    calYear=Math.abs(arr[3]);
    calMon=Math.abs(getMon[arr[2]]);
    calDay=Math.abs(arr[1]);
    }

  var Prev=(calMon-1==0?calYear-1:calYear)+"-"+(calMon-1==0?12:calMon-1)+"-01";
  var Next=(calMon+1==13?calYear+1:calYear)+"-"+(calMon+1==13?1:calMon+1)+"-01";

  cINPUT=calYear;

  CAL="<TABLE cellpadding=0 cellspacing=0>";

  CAL+="<TR><TD><H1><A HREF='javascript:' onclick=Calendar('"+Prev+"');>&laquo;</A></H1>";
  CAL+="<TD colspan=3 align=left><H3><DL><DT><A HREF='javascript:' onclick=CalendarMonView("+calMon+");>"+getMonRus[calMon]+"</A>";
  for(n=1;n<=12;n++) CAL+="<DD><A HREF='javascript:' onclick=Calendar('"+calYear+"-"+n+"-01');>"+getMonRus[n]+"</A>";
  CAL+="</DL></H3>";

  CAL+="<TD colspan=3 align=center><FORM METHOD=POST ACTION='' target='calFrame' onsubmit=CalendarFormSubmit("+calMon+");><TABLE cellpadding=0 cellspacing=0>";
    CAL+="<TR><TD><INPUT TYPE=text value='"+calYear+"' maxlength=4 onkeyup=cINPUT=this.value;>";
    CAL+="<TD><IMG SRC=/img/CalYearUp_0.gif onmouseover=this.src='/img/CalYearUp_1.gif'; onmouseout=this.src='/img/CalYearUp_0.gif'; onclick=Calendar('"+(calYear+1)+"-"+calMon+"-01');><BR>";
    CAL+="<IMG SRC=/img/CalYearDown_0.gif onmouseover=this.src='/img/CalYearDown_1.gif'; onmouseout=this.src='/img/CalYearDown_0.gif'; onclick=Calendar('"+(calYear-1)+"-"+calMon+"-01');>";
  CAL+="</TABLE></FORM>";

  CAL+="<TD><H1><A HREF='javascript:' onclick=Calendar('"+Next+"');>&raquo;</A></H1>";

  CAL+="<TR><TD>&nbsp;<TH>пн<TH>вт<TH>ср<TH>чт<TH>пт<TH>сб<TH>вс";

// ====== определяем номер недели ==========
  var Week=new Date(calYear,calMon-1,1).getWeek();

  CAL+="<TR><TD><H2>"+Week+"</H2>";

// ====== определяем позицию для первого дня ==========
  var dayWeak=new Date(calYear,calMon-1,1).getDay();
  if(dayWeak==0) dayWeak=7;
  if(dayWeak>1) for(n=1;n<dayWeak;n++) CAL+="<TD>&nbsp;";

  var dayCount=new Date(calYear,calMon,0).getDate(); // ====== определяем количество дней в месяце ==========
  for(day=1;day<=dayCount;day++)
    {
    if(today==calYear+"-"+calMon+"-"+day) CAL+="<TD><H6><A HREF='javascript:' onclick="+calFunc+"('"+calYear+"-"+calMon+"-"+day+"');><B>"+day+"</B></A></H6>";
    else CAL+="<TD><H6><A HREF='javascript:' onclick="+calFunc+"('"+calYear+"-"+calMon+"-"+day+"');>"+day+"</A></H6>";
    dayWeak++;
    if(dayWeak>7) dayWeak=1;
    if(dayWeak==1 && day!=dayCount)
      {
      Week++;
      CAL+="<TR align=center><TD><H2>"+Week+"</H2>";
      }
    }
  CAL+="<TR><TD colspan=8><H4><I onclick=CalendarClose();>x</I><A HREF='javascript:' onclick="+calFunc+"('"+today+"');>сегодня, "+todayLink+"</A></H4>";
  CAL+="</TABLE>";

  if(!$("#calendar").text())
    {
    $("BODY").append("<DIV></DIV>");
    $("DIV:last").attr("id","calendar");
    $("#calendar").append("<DIV></DIV><IFRAME src='' name='calFrame' scrolling='no' frameborder='0'></IFRAME>").show();
    }

  if(e)
    {
    e=e || window.event;
    $("#calendar").css("top",e.clientY+$(window).scrollTop()+3).css("left",e.clientX+document.body.scrollLeft+5)
    }

  $("#calendar DIV:eq(0)").html(CAL);
  return false;
  }

function CalendarFormSubmit(mon)
  {
  var reg = /^[0-9]+$/;
  if(reg.exec(cINPUT)==null) alert('Некорректный ввод года.');
  else
    if(cINPUT<1900 || cINPUT>2099) alert('Допустимые значания: 1900 - 2099.');
    else Calendar(cINPUT+"-"+mon+"-01");
  }

function CalendarMonView(mon)
  {
  var DL=$("#calendar DL:eq(0)");
  if(DL.find("DD:eq(0)").is(":hidden"))
    {
    DL.css("background-color","#FFF").css("border","#AAC solid 1px");
    DL.find("DT:eq(0)").css("border-bottom","#AAC solid 1px")
      .find("A:eq(0)").css("border","#FFF solid 1px");
    DL.find("DD:eq("+(mon-1)+") A:eq(0)").css("font-weight","bold");
    DL.find("DD").css("display","block");
    }
  else CalendarMonHide();
  }

function CalendarMonHide()
  {
  var DL=$("#calendar DL:eq(0)");
  DL.css("background-color","#DDF")
    .css("border-top","#DDF solid 1px")
    .css("border-left","#DDF solid 1px")
    .css("border-right","#DDF solid 1px")
    .css("border-bottom","#CCC solid 1px")
    .find("DT:eq(0)").css("border-bottom","#DDF solid 1px");
  DL.find("A:eq(0)").css("border","#DDF solid 1px");
  DL.find("DD").css("display","none");
  }

function CalendarClose(){ $("#calendar").remove(); }
















/* КОНТАКТОВСКИЙ СЕЛЕКТ */
$.fn.vkSel = function(OBJ){
  var ID=this.attr('id');      /* получение id=#имени. */

  var OBJ = $.extend({
    width:150,        /* установка ширины по умолчанию */
    spisok:[{uid:0,title:'Пустой список',content:'<DIV class=nosel>Пустой список</DIV>'}],    /* результаты в формате json */
    bottom:0,          /* отступ снизу от select */
    func:'',            /* функция, выполняемая при выборе элемента */
    funcAdd:'',        /* функция добавления нового значения. Если не пустая, то выводится плюсик. */
    create:1,          /* создание нового селекта, если 1, при этом удаляется старый с тем же именем */
    title0:'',            /* указываем поле с нулевым значением, если надо */
    ro:1,              /* запрет ввода в поле INPUT */
    msg:'Начните ввод здесь...',  /* если ro=0, то указываем msg в поле INPUT */
    url:'',
    vkfunc:'',          /* функция VK.api, выполняемая при вводе в INPUT */
    nofind:'Не найдено'  /* сообщение, выводимое при пустом поиске */
  },OBJ);

  /* создаём новый select */
  if(OBJ.create==1 || $("#vkSel_"+ID).length==0)
    {
    vkSelCreate();
    if(OBJ.url)
      {
      goURL('',1);
      if($("#"+ID).val()==0)
        $("#vkSel_"+ID+" INPUT:first").val(OBJ.msg).addClass('pusto');
      }
    if(OBJ.vkfunc)
      {
      OBJ.vkfunc('');
      if($("#"+ID).val()==0)
        $("#vkSel_"+ID+" INPUT:first").val(OBJ.msg).addClass('pusto');
      }
    }

  if(!OBJ.url) getSpisok(OBJ.spisok);    /* наполняем выпадающий список, если он не берётся из url */

  function vkSelCreate()
    {
    $("#vkSel_"+ID).remove();    /* удаление select если существует */

    var TDADD='', MI=29;
    if(OBJ.funcAdd) { TDADD="<TD class=tdAdd>&nbsp;"; MI=47; }

    var RO=''; if(OBJ.ro==1) RO='cursor:pointer readonly';    /* если ro=1, значит INPUT только для чтения */

    var HTML="<DIV class=vkSel style=width:"+OBJ.width+"px; id=vkSel_"+ID+">";
    HTML+="<TABLE cellpadding=0 cellspacing=0 class=selTab>";
    HTML+="<TR><TD class=tdLeft><INPUT TYPE=text style=width:"+(OBJ.width-MI)+"px;"+RO+">"+TDADD+"<TD class=tdRight>&nbsp;</TABLE>";
    HTML+="<DIV class=vkSelRes style=width:"+OBJ.width+"px;></DIV>";
    HTML+="</DIV>";
    $("#"+ID).after(HTML);
    if(OBJ.bottom>0) $("#vkSel_"+ID).css('margin-bottom',OBJ.bottom+'px');

    var RSLT=$("#vkSel_"+ID+" .vkSelRes");

    /* функция добавления нового значения */
    if(OBJ.funcAdd) $("#vkSel_"+ID+" .tdAdd").click(function(){ OBJ.funcAdd(); });

    /* подсветка треугольничка при наведении */
    $("#vkSel_"+ID).hover(function(){ $(this).find('.tdRight').css('background-color','#E1E8ED'); },function(){ if(RSLT.is(":hidden")) $(this).find('.tdRight').css('background-color','#FFF'); });

    /* раскрытие списка при нажатии на треугольник */
    $("#vkSel_"+ID+" .tdRight").click(function(event){
      if(RSLT.is(":hidden"))
        {
        vkSelHide(ID);
        event.stopPropagation();
        RSLT.show();
        if(OBJ.vkfunc) OBJ.vkfunc('');
        }
      });

    /* манипуляции с INPUT - click, focus, blur, keyup */
    if(!OBJ.ro)
      $("#vkSel_"+ID+" INPUT").bind({
        'click focus':function(event){
          vkSelHide(ID);
          event.stopPropagation();
          if($("#"+ID).val()==0)
            {
            $(this).val('').removeClass('pusto'); /* если значение HIDDEN INPUT нулевое, делаем ввод черными символами */
            if(OBJ.vkfunc) OBJ.vkfunc('');
            }
          RSLT.show();
          },
        blur:function(){ if(!$(this).val() || $("#"+ID).val()==0) $(this).val(OBJ.msg).addClass('pusto'); },
        keyup:function(){
          $("#"+ID).val(0); /* если что-то начинаем вводить - ставим HIDDEN INPUT в ноль */
          if(OBJ.vkfunc) OBJ.vkfunc($(this).val());
          if(OBJ.url) goURL($(this).val(),0);
          RSLT.show();
          }
        });
    else
      $("#vkSel_"+ID+" .tdLeft").click(function(event){ /* раскрытие списка при нажатии на INPUT */
        if(RSLT.is(":hidden"))
          {
          vkSelHide(ID);
          event.stopPropagation();
          RSLT.show();
          }
        });

    }

  /* загрузка списка при вводе данных в поле INPUT */
  function goURL(VAL,UP){
    var INP=$("#vkSel_"+ID+" INPUT:first");
    if(UP==1) INP.hide().after("<IMG src=/img/upload.gif>");
    $.getJSON(OBJ.url+"&value="+encodeURIComponent(VAL)+"&set="+$("#"+ID).val(),function(res){
      if(UP==1)
        {
        INP.show().next().remove();
        if(res!=null) if(res[0].set) $("#vkSel_"+ID+" INPUT:first").val(res[0].set);
        }
      if(res==null) res=[];
      getSpisok(res);
      });
    }




  /* составление выпадающего списка */
  function getSpisok(res)
    {
    var INP=$("#vkSel_"+ID+" INPUT:first");
    var VAL=$("#"+ID).val();
    var RSLT=$("#vkSel_"+ID+" .vkSelRes");
    var DD='';

    if(OBJ.title0)
      {
      if(OBJ.ro==1)
        {
        if(VAL==0) INP.val(OBJ.title0).addClass('pusto');
        OBJ.msg=OBJ.title0;
        }
      DD="<DD class=ddOut val=0><DIV class=nosel>"+OBJ.title0+"</DIV><INPUT type=hidden value='"+OBJ.msg+"'>";
      }

    if(res.length==0) RSLT.html("<DIV class=nofind>"+OBJ.nofind+"</DIV>");
    else
      {
      for(var n=0;n<res.length;n++)
        {
        if(VAL==res[n].uid) INP.val(res[n].title);
        DD+="<DD class=ddOut val="+res[n].uid+">"+(typeof(res[n].content)!='undefined'?res[n].content:res[n].title)+"<INPUT type=hidden value='"+res[n].title+"'>";
        }
      RSLT.html("<DL>"+DD+"</DL>");
      }

    var H=RSLT.css('height');
    var ARR=H.split(/px/);
    if(ARR[0]>250) RSLT.find("DL").css('height',250);
    RSLT.find("DD").bind({
      mouseover: function(){ $(this).parent().find("DD").attr('class','ddOut'); $(this).attr('class','ddOver'); },  /* изменение фона элемента списка при наведении мышью */
      click: function(){
        var uid=$(this).attr('val');
        $("#"+ID).val(uid);
        var INP=$("#vkSel_"+ID+" INPUT:first");
        INP.val($(this).find('INPUT:first').val());
        if(uid>0) INP.removeClass('pusto');
        else INP.addClass('pusto');
        if(OBJ.func) OBJ.func(uid);
        }
      });
    }

}

// установка значения в селекте автоматически
$.fn.vkSelSet = function(OBJ){
  var ID=this.attr('id');      /* получение id=#имени. */
  var OBJ = $.extend({
    value:0
    },OBJ);
  var DD=$("#vkSel_"+ID+" DD");
  var LEN=DD.length;
  if(LEN>0)
    {
    this.val(OBJ.value);
    var INP=$("#vkSel_"+ID+" INPUT:first");
    for(var n=0;n<LEN;n++)
      if(OBJ.value==DD.eq(n).attr('val'))
        {
        INP.val(DD.eq(n).find("INPUT:first").val());
        if(OBJ.value>0) INP.removeClass('pusto');
        else INP.addClass('pusto');
        }
    }
  }



/* скрытие всех результатов списка. Если ID указан, то этот список игнорируется */
function vkSelHide(ID)
  {
  if(typeof(ID)=='object') ID=0;
  var SEL=$('.vkSelRes');
  var KOL=SEL.length;
  for(var n=0;n<KOL;n++)
    if(!SEL.eq(n).parent("#vkSel_"+ID).length)
      {
      $(".tdRight").eq(n).css('background-color','#FFF');
      $(".vkSelRes").eq(n).hide();
      }
//  frameBodyHeightSet();
  }











// ПОКАЗЫВАЕТ ОЖИДАНИЕ ПРОГРЕССА ДЛЯ СИНЕЙ КНОПКИ
$.fn.butProcess = function(){
  var W=$(this).parent().css('width');
  $(this)
    .css('padding-top','7px')
    .css('padding-bottom','6px')
    .css('width',W)
    .attr('onclick','')
    .html("<IMG src=/img/upload.gif>");
  }












/* меню - список  */
$.fn.infoLink = function(obj) {
  var obj = $.extend({
    spisok:[],
    func:''
    },obj);
  var dl = '';
  for (var n = 0; n < obj.spisok.length; n++) { dl += "<DD val=" + obj.spisok[n].uid + ">" + obj.spisok[n].title; }
  var TS = $(this);
  TS
    .addClass('infoLink')
    .html("<INPUT type=hidden value='" + obj.spisok[0].uid + "'><DL>" + dl + "</DL>")
    .find('DD:first').addClass('sel');
  TS.find('DD').mousedown(function () {
    TS.find('.sel').removeClass('sel');
    $(this).addClass('sel');
    if(obj.func) { obj.func($(this).attr('val')); }
  });
  return TS;
};
$.fn.infoLinkSet = function(id) {
  $(this).find('.sel').removeClass('sel');
  var dd = $(this).find('DD');
  for (var n = 0; n < dd.length; n++) {
    if(dd.eq(n).attr('val') == id) {
      dd.eq(n).addClass('sel');
      $(this).find("INPUT:first").val(id);
      break;
    }
  }
};










/* ПОИСКОВАЯ СТРОКА */
$.fn.topSearch = function(OBJ){
  var OBJ = $.extend({
    width:126,
    focus:0,
    txt:'Начните ввод...',
    func:'',
    enter:0    // если 1 - функция выполняется после нажатия Enter
    },OBJ);

  var TS=$(this);
  TS.addClass('topSearch').html("<H5><DIV>"+OBJ.txt+"</DIV></H5><H6 "+(OBJ.width>0?"style=width:"+OBJ.width+"px;":'')+"><DIV class=img_del></DIV><INPUT TYPE=text id="+TS.attr('id')+"_input style=width:"+(OBJ.width-20)+"px;></H6>");
  var DIV=TS.find("H5 DIV:first");

  DIV.click(function(){ TS.find("INPUT:first").focus(); });

  TS.find("INPUT:first").bind({
    focus:function(){ DIV.css('color','#CCC'); },
    blur:function(){ DIV.css('color','#777'); },
    keyup:function(){
      if(!$(this).val())
        {
        DIV.show();
        $(this).prev().hide();
        }
      else
        {
        DIV.hide();
        $(this).prev().show();
        }
      if(OBJ.func && OBJ.enter==0) OBJ.func($(this).val());
      }
    });

  TS.find(".img_del").click(function(){
    $(this).hide().next().val('');
    DIV.show();
    if(OBJ.func) OBJ.func($(this).next().val());
    });

  if(OBJ.enter==1)
    TS.find("INPUT:first").keydown(function(e){
      if(e.which==13 && OBJ.func)
        OBJ.func($(this).val());
      });

  if(OBJ.focus==1) TS.find("INPUT:first").focus();
  }

$.fn.topSearchClear = function(){
  this.find(".img_del").hide().next().val('');
  this.find("H5 DIV:first").show();
  }

$.fn.topSearchSet = function(VAL){
  if(!VAL)
    {
    this.find(".img_del").hide().next().val('');
    this.find("H5 DIV:first").show();
    }
  else
    {
    this.find(".img_del").show().next().val(VAL);
    this.find("H5 DIV:first").hide();
    }
  }














/* ВЫПАДАЮЩЕЕ МЕНЮ ПО ССЫЛКЕ */
$.fn.linkMenu = function(OBJ){
  var OBJ = $.extend({
    name:'Меню',
    grey0:0,
    right:0,
    spisok:[{uid:0,title:'Пусто'}],
    selected:-1,
    func:''
    },OBJ);

  var TS=$(this);
  var ID=TS.attr('id');

  var LEN=OBJ.spisok.length;
  var DD='';
  var GREY='';
  for(var n=0;n<LEN;n++)
    {
    if(OBJ.grey0==1)
      if(OBJ.spisok[n].uid==0)
        GREY=" grey";
      else GREY='';
    if(OBJ.selected==OBJ.spisok[n].uid) OBJ.name=OBJ.spisok[n].title;
    DD+="<DD class='over"+(n==LEN-1?' last':'')+GREY+"' val="+OBJ.spisok[n].uid+">"+OBJ.spisok[n].title;
    }
  var HTML="<DIV class=linkMenu><DL>";
  HTML+="<DT>"+OBJ.name+DD+"</DL></DIV>";
  HTML+="<A href='javascript:'>"+OBJ.name+"</A>";
  HTML+="<INPUT type=hidden name=linkMenu_"+ID+" id=linkMenu_"+ID+" value="+OBJ.selected+">";

  TS.html(HTML);

  if(OBJ.right)
    {
    var W=TS.find('.linkMenu').css('width');
    var arr=W.split(/px/);
    $("#findResult1").html(W);
    TS.find('DL').css('left',(145-arr[0])+'px').find('DT').css('text-align','right');
    }

  if(OBJ.grey0==1 && OBJ.selected==0)
    {
    TS.find('DT:first').css('color','#999');
    TS.find('A:last').css('color','#999');
    }

  TS.find('A:last').click(function(){ TS.find('.linkMenu').show(); });
  TS.find(".over").mouseover(function(){ TS.find(".hover").removeClass('hover').addClass('over'); $(this).removeClass('over').addClass('hover'); });
  TS.find("DT").click(function(){ TS.find('.linkMenu').hide(); });
  var stHide=0;
  TS.find("DL").bind({
    mouseout:function(){ stHide=setTimeout("$('#"+ID+"').find('.linkMenu').fadeOut(150);",500); },
    mouseover:function(){ clearTimeout(stHide); }
    });
  TS.find("DD").click(function(){
    var VAL=$(this).attr('val');
    $("#linkMenu_"+ID).val(VAL);
    var nam=$(this).html();
    TS.find('DT:first').html(nam);
    TS.find('A:last').html(nam)
    if(OBJ.grey0==1)
      {
      TS.find('DT:first').css('color',VAL>0?'#2C587D':'#999');
      TS.find('A:last').css('color',VAL>0?'#2C587D':'#999');
      }
    TS.find('.linkMenu').hide();
    clearTimeout(stHide);
    if(OBJ.func) OBJ.func(VAL);
    });
  }

/* ВЫПАДАЮЩЕЕ МЕНЮ ПО ССЫЛКЕ - УСТАНОВКА ЗНАЧЕНИЯ*/
$.fn.linkMenuSet = function(VAL){
  var DD=this.find('DD');
  var HTML;
  var COLOR;
  var N=DD.length;
  while(N>0)
    {
    N--;
    var EQ=DD.eq(N);
    var ATTR=EQ.attr('val');
    if(ATTR==VAL)
      {
      HTML=EQ.html();
      COLOR=EQ.css('color');
      break;
      }
    }
  this.find('A:last').html(HTML).css('color',COLOR);
  this.find('DT:first').html(HTML).css('color',COLOR);
  this.find('INPUT:first').val(VAL);
  }










// ЧЕКБОКС
$.fn.myCheck = function(OBJ){
  var ID=$(this).attr('id');
  if(!$("#check_"+ID).length)
    {
    var OBJ = $.extend({
      name:'',
      value:0,
      func:''
      },OBJ);

    var HID=this;

    // устанавливаем в INPUT значение, если оно не равно 0 или 1
    var V=HID.val();
    if(V!='0' && V!='1')
      HID.val(OBJ.value);
    else OBJ.value=V;

    var HTML="<SPAN id=check_"+ID+" class=check"+OBJ.value+"><DIV>"+OBJ.name+"</DIV></SPAN>";
    HID.after(HTML);


    HID.next().click(function(){
      var VAL=HID.val();
      HID.val(VAL==0?1:0)
      $(this).attr('class','check'+(VAL==0?'1':'0'))
      if(OBJ.func) OBJ.func(HID.attr('id'));
      });
    }
  }
$.fn.myCheckVal = function(VAL){
  if(!VAL) VAL=0;
  this.val(VAL).next().attr('class','check'+VAL);
  }






// КНОПКА РАДИО
$.fn.myRadio = function(OBJ){
  var OBJ = $.extend({
    width:0,
    spisok:[{uid:0,title:'radio'}],
    bottom:0,
    func:''
    },OBJ);

  var INP = $(this);
  var ID=INP.attr('id');
  if ($("#" + ID+"_radio").length > 0) { INP.next().remove(); }
  var VAL=INP.val();
  if(VAL.length==0) VAL=-1;
  var HTML="<DIV class=radio id="+ID+"_radio>";
  for(var n=0;n<OBJ.spisok.length;n++)
    HTML+="<DIV class="+(OBJ.spisok[n].uid==VAL?'on':'off')+" val="+OBJ.spisok[n].uid+">"+OBJ.spisok[n].title+"</DIV>";
  HTML+="</DIV>";
  INP.after(HTML);

  if (OBJ.width > 0) { INP.next().width(OBJ.width); }

  if(OBJ.bottom>0) $("#"+ID+"_radio DIV").css('margin-bottom',OBJ.bottom+'px');

  $("#"+ID+"_radio DIV").click(function(){
    $("#"+ID+"_radio .on").removeClass('on').addClass('off');
    $(this).removeClass('off').addClass('on');
    var V=$(this).attr('val');
    INP.val(V);
    if(OBJ.func) OBJ.func(V);
    });
  }
$.fn.myRadioSet = function(VAL){
  this.val(VAL);
  var ID=this.attr('id');
  var DIVS=$("#"+ID+"_radio DIV");
  DIVS.attr('class','off');
  var LEN=DIVS.length;
  for(var n=0;n<LEN;n++)
    if(VAL==DIVS.eq(n).attr('val'))
      DIVS.eq(n).attr('class','on');
  }








/* АЛЕРТЫ */
$.fn.alertShow = function(OBJ) {
  var OBJ = $.extend({
    width:0,
    txt:'txt: текст сообщения.',
    top:0,
    left:0,
    delayShow:0,      // задержка перед появлением сообщения
    delayHide:3000,  // длительность отображения сообщения, 0 - бесконечно
    ugol:'bottom',      // с какой стороны вырисовывать треугольник. В эту же сторону будет происходить движение
    otstup:20           // отступ треугольничка
    },OBJ);
  if ($("#alert").length > 0) { $("#alert").remove(); }
  var HTML="<DIV id=alert>";

    HTML+="<TABLE cellpadding=0 cellspacing=0 id=table>";
    if(OBJ.ugol=='top') HTML+="<TR><TD class=UGT><DIV>&nbsp;</DIV>";
    HTML+="<TR>";
    if(OBJ.ugol=='left') HTML+="<TD class=UGL><DIV>&nbsp;</DIV>";

    HTML+="<TD>";
      HTML+="<TABLE cellpadding=0 cellspacing=0>";
      HTML+="<TR><TD class=LR1><TD class=LR2><TD class=RAM>";
      HTML+="<DIV class=txt"+(OBJ.width>0?' style=max-width:'+OBJ.width+'px':'')+">"+OBJ.txt+"</DIV>";
      HTML+="<TD class=LR2><TD class=LR1>";
      HTML+="<TR><TD colspan=5 class=BOT1>";
      HTML+="<TR><TD colspan=5 class=BOT2>";
      HTML+="</TABLE>";

    if(OBJ.ugol=='right') HTML+="<TD class=UGR><DIV>&nbsp;</DIV>";
    if(OBJ.ugol=='bottom') HTML+="<TR><TD class=UGB><DIV>&nbsp;</DIV>";
    HTML+="</TABLE>";

  HTML+="</DIV>";
  this.prepend(HTML);

  var NTOP=OBJ.top, NLEFT=OBJ.left;
  switch (OBJ.ugol) {
  case 'top':    OBJ.top+=15; this.find('.UGT DIV').css('margin-left',OBJ.otstup+'px'); break;
  case 'bottom':  OBJ.top-=15; this.find('.UGB DIV').css('margin-left',OBJ.otstup+'px'); break;
  case 'left':      OBJ.left+=25; break;
  case 'right':    OBJ.left-=25; break;
  }

  var TAB = $("#alert #table");

  setTimeout(function () {
    TAB
      .css({top:OBJ.top,left:OBJ.left})
      .animate({top:NTOP,left:NLEFT,opacity:'show'},250);
    aHide(OBJ.delayHide);
  },OBJ.delayShow);

  $("#alert").mouseenter(function (){
    clearTimeout(window.delay)
    $(this).stop().animate({opacity:1},200);
  });

  $("#alert").mouseleave(function () { aHide(2000); });

  function aHide(dh) {
    window.delay = setTimeout(function () {
      $("#alert").animate({opacity:0},2000,function(){ $(this).remove(); });
    },dh);
  }
}






/* АВТОМАТИЧЕСКОЕ ИЗМЕНЕНИЕ ВЫСОТЫ TEXTAREA */
$.fn.textareaResize = function(OBJ){
  var OBJ = $.extend({
    func:'',
    minH:55,
    first:1    // делать ли первый запуск функции resize()
    },OBJ);

  var TH=this;
  TH.css('resize','none').css('overflow','hidden');


  if(OBJ.first==1) resize();

  TH.keyup(function(){ resize(); if(OBJ.func) OBJ.func(); });

  function resize()
    {
    if(TH[0].scrollHeight>OBJ.minH)
      {
      if($.browser.opera) TH.css('height',OBJ.minH+'px');
      var H=TH[0].scrollHeight;
      if($.browser.opera) H-=6;
      TH.css('height',H+'px');
      }
    else TH.css('height',OBJ.minH+'px');
    frameBodyHeightSet();
    }
  }

















// КОММЕНТАРИИ ВК
$.fn.vkComment = function(OBJ){
  var OBJ = $.extend({
    width:400,
    title:'Добавить заметку...',
    viewer_id:0,
    first_name:'',
    last_name:'',
    photo:''
    },OBJ);

  var THIS=this;

  var HTML="<DIV class=vkComment style=width:"+OBJ.width+"px;><DIV class=headBlue><DIV id=count><IMG src=/img/upload.gif></DIV>Заметки</DIV></DIV>";
  THIS.html(HTML);

  $.getJSON("/include/comment/AjaxCommentGet.php?"+$("#VALUES").val()+"&table_name="+OBJ.table_name+"&table_id="+OBJ.table_id,function(res){
    OBJ.viewer_id=res[0].autor_viewer_id;
    OBJ.first_name=res[0].autor_first_name;
    OBJ.last_name=res[0].autor_last_name;
    OBJ.photo=res[0].autor_photo;

    var TX="<DIV id=add><TEXTAREA style=width:"+(OBJ.width-28)+"px;>"+OBJ.title+"</TEXTAREA>";
    TX+="<DIV class=vkButton><BUTTON>Добавить</BUTTON></DIV></DIV>";
    THIS.find(".headBlue").after(TX);

    if(res[0].count>0)
      {
      var HTML='';
      for(n=0;n<res.length;n++)
        HTML+=createUnit({
                      id:res[n].id,
                      viewer_id:res[n].viewer_id,
                      first_name:res[n].first_name,
                      last_name:res[n].last_name,
                      photo:res[n].photo,
                      txt:res[n].txt,
                      child:res[n].child,
                      dtime_add:res[n].dtime_add
                      });
      THIS.find("#add").after(HTML); // выводим список комментариев
      THIS.find(".unit").hover(function(){ $(this).find(".img_del:first").show(); },function(){ $(this).find(".img_del:first").hide(); }); // показываем и убираем картинку удаления при наведении
      THIS.find(".img_del").click(function(){ commDel($(this).attr('val')); });
      THIS.find(".cdat A").click(function(){ commDopShow($(this)); });  // показ дополнительных комментариев
      }

    THIS.find("#add TEXTAREA").bind({
      click:function(){
        var BUT=$(this).next();
        if(BUT.is(':hidden'))
          {
          $(this).val('').css('color','#000').height(26);
          BUT.show().css('display','inline-block');
          frameBodyHeightSet();
          }
        },
      blur:function(){
        if(!$(this).val())
          {
          $(this).val(OBJ.title).css('color','#777').height(13).next().hide();
          frameBodyHeightSet();
          }
        }
      }).textareaResize({minH:26,first:0});

    THIS.find("#add BUTTON").click(commAdd);
    commCount(res[0].count);
    });

  /* новый комментарий */
  function commAdd()
    {
    THIS.find("#add BUTTON").butProcess();
    $.post("/include/comment/AjaxCommentAdd.php?"+$("#VALUES").val(),{table_name:OBJ.table_name,table_id:OBJ.table_id,parent_id:0,viewer_id:OBJ.viewer_id,txt:THIS.find("#add TEXTAREA").val()},function(res){
      THIS.find(".deleted").remove();
      THIS.find("#add").after(createUnit({
                      id:res.id,
                      viewer_id:OBJ.viewer_id,
                      first_name:OBJ.first_name,
                      last_name:OBJ.last_name,
                      photo:OBJ.photo,
                      txt:res.txt,
                      child:0,
                      dtime_add:res.dtime_add
                      }));
      THIS.find(".cdat A:first").click(function(){ commDopShow($(this)); });
      THIS.find("#add TEXTAREA")
        .val(OBJ.title)
        .css('color','#777')
        .height(13)
        .next()
        .remove()
        .end()
        .after("<DIV class=vkButton><BUTTON>Добавить</BUTTON></DIV>");
      THIS.find("#add BUTTON").click(commAdd);
      THIS.find(".unit:first").hover(
        function(){ $(this).find(".img_del:first").show(); },
        function(){ $(this).find(".img_del:first").hide(); }
        );
      THIS.find(".img_del:first").click(function(){ commDel($(this).attr('val')); });
      commCount(res.count);
      },'json');
    }

  /* создание комментария */
  function createUnit(RES)
    {
    var UNIT="<DIV class=unit id=unit"+RES.id+"><TABLE cellspacing=0 cellpadding=0>";
    UNIT+="<TR><TD width=50><IMG src="+RES.photo+">";
    UNIT+="<TD width="+(OBJ.width-50)+">";
    if(RES.viewer_id==OBJ.viewer_id) UNIT+="<DIV class=img_del val="+RES.id+"></DIV>";
    UNIT+="<A href='http://vk.com/id"+RES.viewer_id+"' target='_blank' class=name>"+RES.first_name+" "+RES.last_name+"</A>";
    UNIT+="<DIV class=ctxt>"+RES.txt+"</DIV>";
    UNIT+="<DIV class=cdat>"+RES.dtime_add+"<SPAN> | <A href='javascript:' val="+RES.id+">Коммент"+(RES.child>0?'арии ('+RES.child+')':'ировать')+"</A></SPAN></DIV>";
    UNIT+="<DIV class=cdop></DIV>";
    UNIT+="<INPUT type=hidden value="+RES.child+">";
    UNIT+="</TABLE></DIV>";
    return UNIT;
    }

  /* создание дополнительного комментария */
  function createUnitDop(RES)
    {
    var DOP="<DIV class=dunit id=dunit"+RES.id+"><TABLE cellspacing=0 cellpadding=0>";
    DOP+="<TR><TD width=30><IMG src="+RES.photo+" width=30>";
    DOP+="<TD width="+(OBJ.width-85)+">";
    if(RES.viewer_id==OBJ.viewer_id) DOP+="<DIV class=img_minidel val="+RES.id+"></DIV>";
    DOP+="<A href='http://vk.com/id"+RES.viewer_id+"' target='_blank' class=dname>"+RES.first_name+" "+RES.last_name+"</A>";
    DOP+="<DIV class=dtxt>"+RES.txt+"</DIV>";
    DOP+="<DIV class=ddat>"+RES.dtime_add+"</DIV>";
    DOP+="</TABLE></DIV>";
    return DOP;
    }

  /* внесение дополнительного комментария */
  function commDopAdd(OB)
    {
    OB.butProcess();
    var ID=OB.attr('val');
    $.post("/include/comment/AjaxCommentAdd.php?"+$("#VALUES").val(),{table_name:OBJ.table_name,table_id:OBJ.table_id,parent_id:ID,viewer_id:OBJ.viewer_id,txt:$("#unit"+ID+" TEXTAREA").val()},function(res){
      $("#unit"+ID+" .dadd").remove();
      $("#unit"+ID+" .cadd").remove();
      $("#unit"+ID+" .deleted").remove();
      $("#unit"+ID+" .cdop").append(createUnitDop({
                      id:res.id,
                      viewer_id:OBJ.viewer_id,
                      first_name:OBJ.first_name,
                      last_name:OBJ.last_name,
                      photo:OBJ.photo,
                      txt:res.txt,
                      dtime_add:res.dtime_add
                      }));
      $("#dunit"+res.id).hover(
        function(){ $(this).find(".img_minidel").show(); },
        function(){ $(this).find(".img_minidel").hide(); }
        );
      $("#dunit"+res.id+" .img_minidel").click(function(){ commDopDel($(this).attr('val')); });
      setArea(ID);
      },'json');
    }

  /* показ дополнительных комментариев и комментирование */
  function commDopShow(OB)
    {
    THIS.find(".cdat SPAN").show(); // показываем все ссылки 'комментарии'
    THIS.find(".cadd").remove();    // удаление всех TEXTAREA для добавления дополнительных комментариев
    var ID=OB.attr('val');
    CHILD=$("#unit"+ID+" INPUT").val();
    if(CHILD>0)
      {
      OB.parent().html(" <IMG src=/img/upload.gif>");
      commDopLoad(ID);
      }
    else
      {
      OB.parent().hide();
      var HTML="<DIV class=cadd><TEXTAREA style=width:"+(OBJ.width-77)+"px;></TEXTAREA><DIV class=vkButton><BUTTON val="+ID+">Добавить</BUTTON></DIV></DIV>";
      $("#unit"+ID+" .cdop").after(HTML);
      $("#unit"+ID+" TEXTAREA")
        .focus()
        .blur(function(){
          if(!$(this).val())
            {
            $("#unit"+ID+" .cdat SPAN").show();
            $(this).parent().remove();
            frameBodyHeightSet();
            }
          })
        .textareaResize({minH:26});
      $("#unit"+ID+" BUTTON").click(function(){ commDopAdd($(this)); });
      }
    }

  /* загрузка списка дополнительных комментариев */
  function commDopLoad(ID)
    {
    $.getJSON("/include/comment/AjaxCommentDopGet.php?"+$("#VALUES").val()+"&table_name="+OBJ.table_name+"&table_id="+OBJ.table_id+"&viewer_id="+OBJ.viewer_id+"&parent_id="+ID,function(res){
      var HTML='';
      for(n=0;n<res.length;n++)
        HTML+=createUnitDop({
                  id:res[n].id,
                  viewer_id:res[n].viewer_id,
                  first_name:res[n].first_name,
                  last_name:res[n].last_name,
                  photo:res[n].photo,
                  txt:res[n].txt,
                  dtime_add:res[n].dtime_add
                  });
      $("#unit"+ID+" .cdop").html(HTML);
      $("#unit"+ID+" .dunit").hover(
        function(){ $(this).find(".img_minidel").show(); },
        function(){ $(this).find(".img_minidel").hide(); }
        );
      $("#unit"+ID+" .img_minidel").click(function(){ commDopDel($(this).attr('val')); });
      setArea(ID);
      });
    }

  /* вставление TEXTAREA к дополнительным комментариям */
  function setArea(ID)
    {
    var HTML="<DIV class=dadd><TEXTAREA style=width:"+(OBJ.width-77)+"px;>Комментировать...</TEXTAREA><DIV class=vkButton><BUTTON val="+ID+">Добавить</BUTTON></DIV></DIV>";
    $("#unit"+ID+" .cdop").append(HTML);
    $("#unit"+ID+" .cdat SPAN").remove();
    $("#unit"+ID+" TEXTAREA").bind({
      click:function(){
        var BUT=$(this).next();
        if(BUT.is(":hidden"))
          {
          $(this).css('color','#000').val('').height(26);
          BUT.css('display','inline-block');
          frameBodyHeightSet();
          }
        },
      blur:function(){
        if(!$(this).val())
          {
          $(this).val('Комментировать...').css('color','#777').height(13).next().hide();
          frameBodyHeightSet();
          }
        }
      }).textareaResize({minH:26,first:0});
    $("#unit"+ID+" BUTTON").click(function(){ commDopAdd($(this)); });
    frameBodyHeightSet();
    }

  /* удаление комментария */
  function commDel(ID)
    {
    $.post("/include/comment/AjaxCommentDel.php?"+$("#VALUES").val(),{del:ID},function(res){
      $("#unit"+ID)
        .append("<CENTER>Заметка удалена. <A href='javascript:' val="+ID+">Восстановить</A></CENTER>")
        .addClass('deleted')
        .find("TABLE").hide();
      $("#unit"+ID+" A").click(function(){ commRec($(this).attr('val')); });
      commCount(res.count);
      },'json');
    }

  /* восстановление комментария */
  function commRec(ID)
    {
    $.post("/include/comment/AjaxCommentRec.php?"+$("#VALUES").val(),{rec:ID},function(res){
      $("#unit"+ID).removeClass('deleted');
      $("#unit"+ID+" CENTER").remove();
      $("#unit"+ID+" TABLE").show();
      commCount(res.count);
      },'json');
    }

  /* удаление дополнительного комментария */
  function commDopDel(ID)
    {
    $.post("/include/comment/AjaxCommentDopDel.php?"+$("#VALUES").val(),{del:ID},function(res){
      $("#dunit"+ID)
        .append("<CENTER>Комментарий удалён. <A href='javascript:' val="+ID+">Восстановить</A></CENTER>")
        .addClass('deleted')
        .find("TABLE").hide();
      $("#dunit"+ID+" A").click(function(){ commDopRec($(this).attr('val')); });
      frameBodyHeightSet();
      });
    }

  /* восстановление дополнительного комментария */
  function commDopRec(ID)
    {
    $.post("/include/comment/AjaxCommentDopRec.php?"+$("#VALUES").val(),{rec:ID},function(res){
      $("#dunit"+ID).removeClass('deleted');
      $("#dunit"+ID+" CENTER").remove();
      $("#dunit"+ID+" TABLE").show();
      frameBodyHeightSet();
      });
    }
  }

/* вывод количества комментариев */
function commCount(C) {
  var TX;
  if(C>0)
    {
    var END='ок';
    if(Math.floor(C/10%10)!=1)
      switch(C%10)
        {
        case 1: END='ка'; break;
        case 2: END='ки'; break;
        case 3: END='ки'; break;
        case 4: END='ки'; break;
        }
    TX="Всего "+C+" замет"+END;
    }
  else TX="Заметок нет";
  $(".vkComment #count").html(TX);
  frameBodyHeightSet();
}













//УСТАНОВКА ВЫСОТЫ ФРЕЙМА КОНТАКТА ПОД РАЗМЕР ОКНА
function frameBodyHeightSet(y) {
  var FB=document.getElementById('frameBody');
  if (!y) { FB.style.height='auto'; }
  var H=FB.offsetHeight-1;
  if (y && y > H) {
    H=y;
    FB.style.height=(H+1)+'px';
  }
    
  var dialog = $("#dialog");
  if (dialog.length > 0) {
    var DH = dialog.height() + parseInt(dialog.css('top'));
    if (H < (DH + 30)) {
      H = DH + 30;
      FB.style.height = H + 'px';
    }
  }

  VK.callMethod('resizeWindow',627,H);
}





//УПРАВЛЕНИЕ КУКАМИ
function setCookie(name,value)
  {
  var exdate=new Date();
  exdate.setDate(exdate.getDate()+1);
  document.cookie=name+"="+value+"; path=/; expires="+exdate.toGMTString();
  }
function delCookie(name)
  {
  var exdate=new Date();
  exdate.setDate(exdate.getDate()-1);
  document.cookie=name+"=; path=/; expires="+exdate.toGMTString();
  }
function getCookie(name)
  {
  var arr1=document.cookie.split(name);
  if(arr1.length>1)
    {
    var arr2=arr1[1].split(/;/);
    var arr3=arr2[0].split(/=/);
    return arr3[0]?arr3[0]:arr3[1];
    }
  else return null;
  }




//МАТОВЫЙ ФОН
function opFonSet()
  {
  if($("#opFon").length==0)
    {
    $("#frameBody").after("<DIV id=opFon></DIV>");
    var H=document.getElementById('frameBody').offsetHeight
    $("#opFon").css('height',H);
    }
  }
//ВЫВОД НА ЭКРАН ДИАЛОГА
function dialogShow(obj) {
  var obj = $.extend({
    width:360,
    head:'head: Название заголовка',
    content:'content: содержимое центрального поля',
    submit:'',    /* функция, которая выполняется при нажатии синей кнопки */
    cancel:'',    /* функция, которая выполняется при нажатии кнопки отмена */
    top:100,    /* отступ сверху с учётом скрола */
    focus:'',    /* установка фокуса на указанный элемент в виде #focus */
    butSubmit:'Внести'
    },obj);
  opFonSet();
  var HTML="<DIV id=dialog style=width:"+obj.width+"px;><H1><DIV><A href='javascript:'>&nbsp;</A>"+obj.head+"</DIV></H1>";
  HTML+="<H2>"+obj.content+"</H2>";
  HTML+="<H3><CENTER><DIV class=vkButton><BUTTON id=butDialog onclick=null>"+obj.butSubmit+"</BUTTON></DIV><DIV class=vkCancel><BUTTON>Отмена</BUTTON></DIV></CENTER></H3></DIV>";
  $("#frameBody").after(HTML);
  var LEFT=313-(obj.width/2);
  $("#dialog")
    .css('top',$(window).scrollTop()+vkScroll+obj.top)
    .css('left',LEFT)
    .show()
    .find("H1:first A").click(dialogHide).end()
    .find(".vkCancel:first BUTTON").click(dialogHide).end()
    .find(".vkButton:first BUTTON").click(obj.submit);
  if(obj.cancel) {
    $("#dialog .vkCancel BUTTON").click(obj.cancel);
  }
  if(obj.focus) { $(obj.focus).focus().select(); }
}
function dialogHide() {
  if($("#dialog").length>0) $("#dialog").remove();
  if($("#opFon").length>0) $("#opFon").remove();
}






// СООБЩЕНИЕ О РЕЗУЛЬТАТЕ ВЫПОЛНЕННЫХ ДЕЙСТВИЙ
function vkMsgOk(MSG)
  {
  if($("#vkMsgOk").length>0) $("#vkMsgOk").remove();
  $("BODY").append("<DIV id=vkMsgOk>"+MSG+"</DIV>");
  $("#vkMsgOk").css('top',$(this).scrollTop()+200+vkScroll).delay(1200).fadeOut(400,function(){ $(this).remove(); });
  }






// ПРОСМОТР ФОТОГРАФИИ
function fotoShow(NAME) {
  var HTML="<DIV>";
  HTML+="<TABLE cellspacing=0 cellpadding=0><TR><TD class=fHead>Просмотр фотографии<TD class=close><A href='javascript:' onclick=fotoClose();>закрыть</A></TABLE>";
  HTML+="<CENTER onclick=fotoClose();><IMG SRC=/files/images/"+NAME+"b.jpg></CENTER>";
  HTML+"</DIV>";
  opFonSet();
  $("#opFon").after("<DIV id=fotoShow>"+HTML+"</DIV>");
  if(vkScroll<70) vkScroll=0; else vkScroll-=70;
  $("#fotoShow").css('top',$(window).scrollTop()+6+vkScroll);
  frameBodyHeightSet(650);
  }
function fotoClose() {
  $('#opFon').remove();
  $('#fotoShow').remove();
  frameBodyHeightSet();
  }






// ВЫВОД ПОДСКАЗОК
function hintTxt(txt,id) {
  var html = "<DIV id=hint>";
  html += "<H3><DIV class=img_del onclick=hintHide();></DIV>Подсказка:</H3>";
  html += txt;
  html += "<H4><A href='javascript:' onclick=hintNoShow(" + id + ");>Не показывать эту подсказку</A></H4>";
  html += "</DIV>";
  return html;
}

function hintNoShow(id) {
  $("#hint H4").html("<IMG src=/img/upload.gif>");
  $.post('/superadmin/hint/AjaxHintNoShow.php?' + $("#VALUES").val(),{hint_id:id},hintHide);
}

function hintHide() {
  clearInterval(window.delay);
  $("#alert").remove();
}














function op(OBJ) {
//  var div = document.createElement('div');
//  div.id = 'op';
//  document.getElementByTagName('BODY').appendChild(div);
  $("BODY").append("<DIV id=op style=margin:10px;></DIV>");
	var txt = '';
  for (var i in OBJ) {
		txt += "<A href='javascript:'>"+i+"</A> = "+OBJ[i]+"<BR>";
	}
  document.getElementById('op').innerHTML = txt;
}
