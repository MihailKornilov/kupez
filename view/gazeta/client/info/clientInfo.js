var linkSpisok = [
    {uid:1,title:'����������'},
    {uid:2,title:'�������������'},
    {uid:3,title:'<b>����� ������</b>'},
    {uid:4,title:'������� �����'}
];
if (G.client.del == 0) linkSpisok.push({uid:5, title:'<span class=red>������� �������</span>'});
$("#links").infoLink({
    spisok:linkSpisok,
    func:function (uid) {
        switch (uid) {
            case '2': clientAdd(function () { vkMsgOk("������ ������� ��������."); location.reload(); }, G.client); break;
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
        return "<H1><EM>" + sp.dtime + "</EM><A href='" + G.url + "&p=gazeta&d=zayav&d1=view&id="+sp.id+"'>" + G.category_ass[sp.category] + " �" + sp.id + "</A></H1>" +
            "<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>" +
            "<TABLE cellpadding=0 cellspacing=4>" +
            (sp.client_id > 0 ? "<TR><TD class=tdAbout>������:<TD><A HREF='" + G.url + "&p=gazeta&d=client&id=" + sp.client_id + "'>" + sp.client_fio + "</A>" : '') +
            (sp.category == 1 ?
                "<TR><TD class=tdAbout>�������:<TD>" + G.rubrika_ass[sp.rubrika] + (sp.podrubrika > 0 ? "<SPAN class=ug>�</SPAN>" + G.podrubrika_ass[sp.podrubrika] : '') +
                    "<TR><TD class=tdAbout valign=top>�����:<TD><DIV class=txt>" + sp.txt + "</DIV>" : '') +

            (sp.ob_dop ? "<TR><TD class=tdAbout>���. ��������:<TD>" + sp.ob_dop : '') +
            (sp.category == 2 ? "<TR><TD class=tdAbout>������:<TD>" + sp.size_x + " x " + sp.size_y + " = <b>" + sp.kv_sm + '</b> ��&sup2;' : '') +
//    if(sp.telefon) HTML+="<TR><TD class=tdAbout>�������:<TD>"+sp.telefon;
//    if(sp.adres) HTML+="<TR><TD class=tdAbout>�����:<TD>"+sp.adres;

            "<TR><TD class=tdAbout>���������:<TD><B>" + sp.summa + "</B> ���." + (sp.summa_manual == 1 ? '<SPAN class=manual>(������� �������)</SPAN>' : '') +
            "</TABLE>" +

//    if(sp.file) HTML+="<TD class=image><IMG src=/files/images/"+sp.file+"s.jpg onclick=fotoShow('"+sp.file+"');>";

            "</TABLE>";
    };

    G.spisok.create({
        view:$("#zayav"),
        limit:15,
        json: G.client.zayav_spisok,
        result_view:$("#result"),
        result:"�������$show $count ����$zayav",
        ends:{'$show':['�', '�'],'$zayav':['��', '��', '��']},
        next:"�������� ��� ������",
        nofind:"������ ���"
    });
}






function moneyShow(link) {
    $("#dopMenu A").attr('class', 'link')
    $(link).attr('class', 'linkSel')

    $("#zayav").html('');

    G.spisok.unit = function (sp) {
        var txt = sp.txt;
        if (sp.zayav_id > 0) { txt = "������ �� ������ <A href='/index.php?" + G.values + "&p=gazeta&d=zayav&d1=view&id=" + sp.zayav_id + "'><EM>�</EM>" + sp.zayav_id + "</A>"; }
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
        result:"�������$show $count ������$pay",
        ends:{'$show':['�', '�'],'$pay':['', '�', '��']},
        next:"�������� ���...",
        nofind:"�������� ���",
        callback:function (res) {
            if(res.length > 0) {
                var html = "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok width=100%>" +
                    "<TR><TH class=sum>�����" +
                    "<TH class=about>��������" +
                    "<TH class=data>����" +
                    "</TABLE>";
                $("#money").prepend(html);
            }
        }
    });

}








function clientDel() {
    var dialog = $("#dialog_client").vkDialog({
        width:250,
        head:"�������� �������",
        butSubmit:"�������",
        content:"<CENTER><B>����������� �������� �������</B></CENTER>",
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
    title0:'��������� �� �������',
    spisok:zayavCategoryVk,
    func:zayavSpisokGet
    });

  $.getJSON("/gazeta/client/AjaxClientEdit.php?"+G.values+"&id=<?php echo $client->id; ?>",getClient);

  zayavSpisokGet();
});


function clientEdit()
  {
  var HTML="<TABLE cellpadding=0 cellspacing=6 class=clientAdd>";
  HTML+="<TR><TD class=tdAbout>���������:<TD><INPUT TYPE=hidden id=person value="+$("#edit_person_id").val()+">";
  HTML+="<TR><TD class=tdAbout>�������� �����������:<TD><INPUT TYPE=text id=org_name class=input value='"+$("#edit_org_name").html()+"'>";
  HTML+="<TR><TD class=tdAbout>���:<TD><INPUT TYPE=text id=fio class=input value='"+$("#edit_fio").html()+"'>";
  HTML+="<TR><TD class=tdAbout>��������:<TD><INPUT TYPE=text id=telefon class=input value='"+$("#edit_telefon").html()+"'>";
  HTML+="<TR><TD class=tdAbout>�����:<TD id=ms><INPUT TYPE=text id=adres class=input value='"+$("#edit_adres").html()+"'>";
  HTML+="</TABLE>";
  dialogShow({
    width:440,
    top:60,
    butSubmit:'���������',
    head:"�������������� ������ �������",
    content:HTML,
    cancel:function () { $("#links").infoLinkSet(1); },
    submit:function () {
      if(!$("#fio").val() && !$("#org_name").val()) {
        $("#ms").alertShow({txt:"<DIV class=red>���������� ������� ��� �������<BR>���� �������� �����������.</DIV>",top:-3,left:-5});
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
            vkMsgOk("������ ������� ��������!");
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








// �������� ������
function zayavShow(OBJ)
  {
  $("#spLinks .infoLinkSel").attr('class','infoLink');
  $(OBJ).attr('class','infoLinkSel');
  $("#zHead").html("<DIV id=zResult><IMG src=/img/upload.gif></DIV>������ ������").show();
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
          HTML+="<H1><EM>"+data[n].dtime+"</EM><A href='<?php echo $URL; ?>&my_page=zayavView&id="+data[n].id+"'>"+data[n].cat_name+" �"+data[n].id+"</A></H1>";
  
          HTML+="<TABLE cellpadding=0 cellspacing=0><TR><TD valign=top>";
  
            HTML+="<TABLE cellpadding=0 cellspacing=2>";
            if(data[n].cat_id==1) HTML+="<TR><TD class=tdAbout>�����:<TD><DIV class=txt>"+data[n].txt+"</DIV>";
            if(data[n].cat_id==2) HTML+="<TR><TD class=tdAbout>������:<TD>"+data[n].size_x+" x "+data[n].size_y+" = "+data[n].kv_sm;
            HTML+="<TR><TD class=tdAbout>���������:<TD><B>"+data[n].summa+"</B> ���.";
            HTML+="</TABLE>";

          if(data[n].file) HTML+="<TD class=image><IMG src=/files/images/"+data[n].file+"s.jpg>";

          HTML+="</TABLE>";
        HTML+="</DIV>";
        }
      if(data[0].page>0) HTML+="<DIV><DIV id=ajaxNext onclick=zayavNext("+data[0].page+");>��������� 20 ����������</DIV></DIV>";
      $("#zResult").html(data[0].result);
      OBJ.view.html(HTML);
      }
    else
      {
      $("#zResult").html("������ �� �������.");
      OBJ.view.html('');
      }

    frameBodyHeightSet();
    });
  }





// �������� �������
function oplataInsert() {
  var HTML="<TABLE cellpadding=0 cellspacing=6 id=oplataInsert>";
  HTML+="<TR><TD class=tdAbout>��� �������:<TD><INPUT TYPE=hidden id=oplata_tip value=1>";
  HTML+="<TR><TD class=tdAbout>�����:<TD id=pn><INPUT TYPE=text id=summa maxlength=6>";
  HTML+="<TR><TD class=tdAbout>����������:<TD><INPUT TYPE=text id=prim maxlength=250>";
  HTML+="</TABLE>";
  dialogShow({
    top:60,
    head:"�������� �������",
    content:HTML,
    cancel:function () { $("#links").infoLinkSet(1); },
    submit:function () {
      var SUMMA=$("#summa").val();
      if(!SUMMA) $("#pn").alertShow({txt:"<SPAN class=red>���������� ������ �����</SPAN>",top:-42,left:-3});
      else
        {
        var reg = /^[0-9]*$/i;
        if(reg.exec(SUMMA)==null) $("#pn").alertShow({txt:"<SPAN class=red>����������� ������� �����</SPAN>",top:-42,left:-3});
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
              vkMsgOk("����� ����� �����!");
              $("#links").infoLinkSet(1);
              },'json');
          }
        }
      },
    focus:'#summa'
    });
  $("#oplata_tip").vkSel({
    width:120,
    spisok:[{uid:1,title:'��������'},{uid:2,title:'�����������'},{uid:3,title:'�����������'}]
    });
  }



// �������� ��������
function oplataShow(OBJ) {
  $("#spLinks .infoLinkSel").attr('class','infoLink');
  $(OBJ).attr('class','infoLinkSel');
  $("#zHead").html("<DIV id=zResult><IMG src=/img/upload.gif></DIV>������ ��������").show();
  $("#catDop").hide();
  $("#zSpisok").html('');
  var URL="&page=1";
  $.getJSON("/gazeta/client/AjaxOplataSpisok.php?<?php echo $VALUES; ?>"+URL+"&client=<?php echo $client->id; ?>",function(data){
    if(data[0].count>0)
      {
      var HTML="<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>";
      HTML+="<TR><TH>�����<TH>����������<TH>����<TH>������";
      for(var n=0;n<data[0].count;n++)
        HTML+="<TR><TD align=center width=40><B>"+data[n].summa+"</B><TD>"+data[n].prim+"<TD class=dtime>"+data[n].dtime+"<TD width=90>"+data[n].viewer_id;
      HTML+="</TABLE>";
      $("#zResult").html(data[0].result);
      $("#zSpisok").html(HTML);
      }
    else $("#zResult").html("�������� ���");
    });
  }

// �������� �������
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