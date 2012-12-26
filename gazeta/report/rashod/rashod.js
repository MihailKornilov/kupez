report.go['rashod'] = function () {
  var HTML="<DIV id=rashod>";
  HTML+="<DIV class=headName>Список расходов</DIV>";
  HTML+="<DIV class=info>Расходы нужны для учёта платежей, которые производятся для оплаты налогов, зарплаты сотрудникам, разносчикам, за канцтовары, печать газеты и тп. В дальнейшем эти данные будут использоваться для составления общего баланса доходов газеты.</DIV>";
  HTML+="<DIV class=vkButton><BUTTON onclick=rashodAdd();>Внести новый расход</BUTTON></DIV>";
  HTML+="<DIV id=spisok></DIV>";
  HTML+="</DIV>";
  $("#cont").html(HTML);
  $.getJSON("/gazeta/report/rashod/AjaxRashodGet.php?"+G.values,function(res){
    if(res!=null) {
      var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok><TR>";
      HTML+="<TH>Наименование";
      HTML+="<TH>Сумма";
      HTML+="<TH>Дата";
      HTML+="<TH>Внёс";
      for(var n = 0; n < res.length; n++) {
        HTML+="<TR><TD>"+res[n].name;
        HTML+="<TD align=right>"+res[n].summa;
        HTML+="<TD align=right>"+res[n].dtime;
        HTML+="<TD align=right>"+res[n].viewer_id;
      }
      HTML+="</TABLE>";
      $("#spisok").html(HTML);
      }
    frameBodyHeightSet();
    });
};
function rashodAdd() {
  HTML="<TABLE cellspacing=8 cellpadding=0>";
  HTML+="<TR><TD class=tdAbout>Наименование:<TD><INPUT type=text id=name style=width:250px;>";
  HTML+="<TR><TD class=tdAbout id=pbut>Сумма:<TD><INPUT type=text id=summa style=width:60px; maxlength=10> руб.";
  HTML+="</TABLE>";
  dialogShow({
    width:400,
    top:80,
    head:'Внесение нового расхода газеты',
    content:HTML,
    submit:function(){
      var NAME=$("#name").val();
      if(!NAME) { $("#pbut").alertShow({txt:'<SPAN class=red>Не указано наименование.</SPAN>',top:7,left:110}); $("#name").focus(); }
      else
        {
        var SUM=$("#summa").val();
        var reg=/^[0-9.]+$/;
        if(!reg.exec(SUM)) { $("#pbut").alertShow({txt:"<SPAN class=red>Не корректно введено значение.<BR>Используйте цифры и точку.</SPAN>",top:-7,left:110}); $("#summa").focus(); }
        else
          {
          $("#butDialog").butProcess();
          $.post("/gazeta/report/rashod/AjaxRashodAdd.php?"+G.values,{name:NAME,summa:SUM},function(){
            dialogHide();
            rashod();
            vkMsgOk("Внесение произведено!");
            });
          }
        }
      },
    focus:'#name'
    });
  }
