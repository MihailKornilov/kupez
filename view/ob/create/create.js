$(".info A:first").click(function () {
/*  opFonSet();
  $("#rules").show()[0].onclick = rulesHide;
  $("#opFon").click(rulesHide);
  function rulesHide() {
    $("#opFon").remove();
    $("#rules").hide();
  }
*/
  var html = "<DIV id=vk-create-rules>";
  html += "<DIV class=headName>������������ ��� �������� ����������:</DIV>";
  html += "<UL><LI>����� �������� ���������� ���� �����;";
  html += "<LI>�� ����������� ���������� ����������, ����� ������� ������������� ����� ��������� ������ ���������� ��, ��� �� �����������. ���������� ��������� ��������� �� 4-� ����������� �� ���� ����������;";
  html += "<LI>����������� ���������� �������� ����;";
  html += "<LI>�� ��������� ���� � �� �� ���������� ��������, ��� ����� ���� ����������� ��������� ������� �������. ��������� ���������� ����� ���������;";
  html += "<LI>�� ������ ���������� � ������� ��������;";
  html += "<LI>���������� ����� ����������� �������� � ��������������� ����;";
  html += "<LI>���� ���� ��������� ��� �� ���������, ������� ��� ��� ���������� � ����� � ������� \"��� ����������\".</UL>";

  html += "<DIV class=headName>������, ������� ������� �� �����������:</DIV>";
  html += "<UL><LI>�������, ������������ � (���) ���������� ������� ��������� ����������������� ���������� ���������;";
  html += "<LI>������������� �������, ������������ ������� � �����������;";
  html += "<LI>���������� ������� � ����������, �� ����������� ��������������� �������;";
  html += "<LI>������� � (���) ������ �������� � �������� �������� �����-�������;";
  html += "<LI>�������, ���������� ��������������� �����������, � ������ ���������� ����� �����������;";
  html += "<LI>�������, ���������� ������������ ������������ ��� ����� ������������� ������������� ������������ ����������� ����������� �����������, � ������ ���������� ����� ������������ ��� ������������� ������ ������������;";
  html += "<LI>������, �� ������������ � (���) ���������� ������� ��������� ��������� �������� ��� ���� ����������� ����������, � ������ ���������� ����� ����������.</UL></DIV>";
  dialogShow({
    width:500,
    top:20,
    head:"������� ���������� ����������",
    content:html,
    butSubmit:'�������',
    butCancel:'',
    submit:dialogHide
  });
});

$("#rubrika").vkSel({
  width:120,
  title0:'�� �������',
  spisok:G.rubrika_spisok,
  func:function(uid){
    $("#podrubrika").val(0);
    $("#vkSel_podrubrika").remove();
    if(G.podrubrika_spisok[uid]) {
      $("#podrubrika").vkSel({
        width:199,
        title0:'���������� �� �������',
        spisok:G.podrubrika_spisok[uid],
        func:preview
      });
    }
  preview();
  }
});

$("#txt")
  .autosize({callback:frameBodyHeightSet})
  .focus()
  .keyup(preview);



// �������� �����������
$("#images").imgUpload({max:4, func:preview});
$("#images_upload").bind({
    mouseenter: function () {
      $("#ms_images").alertShow({
        txt:"�� ������ ��������� �� 4 �����������.",
        left:-3,
        top:-43,
        otstup:30,
        delayShow:500,
        delayHide:0
      });
    },
    mouseleave: function () { $("#alert").remove(); },
    click:function () { $("#alert").remove(); $(this).unbind(); }
  });




$("#telefon").keyup(preview);

$("#countries").vkSel({
  bottom:4,
  width:180,
  spisok:[{uid:1,title:'������'}],
  func:preview
});

// ������ �������
if (G.vk.city > 0) {
  VK.api('places.getCityById', {cids:G.vk.city}, function (res) {
    G.cities.unshift({
      uid:res.response[0].cid,
      title:res.response[0].name,
      content:"<B>" + res.response[0].name + "</B>"
    });
    cityPrint()
  });
} else { cityPrint(); }

function cityPrint() {
  $("#cities").vkSel({
    ro:0,
    msg:'����� �� ������',
    title0:'����� �� ������',
    width:180,
    spisok:G.cities,
    func:preview,
    vkfunc:keyupCity
  });

  frameBodyHeightSet();

  $("#vkSel_cities").bind({
    mouseenter:function () {
      $("#ms_adres").alertShow({
        width:205,
        txt:"����������� ���������� �����, ���� ���� ���������� ������������� ������ �� ����, ����� ���������� ����� ������������ ������ � ����� ������.",
        ugol:'left',
        left:180,
        top:-15,
        otstup:0,
        delayHide:0
      });
    },
    mouseleave:function () { $("#alert").remove(); }
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
      $("#cities").val(0).vkSel({create:0, spisok:create.cities});
    }
  }
} // end cityPrint()




$("#viewer_id_show").myCheck({func:preview});
$("#pay_service").myCheck({func:function (id) {
  var val = $("#" + id).val();
  $("#payContent").css('display', val == 1 ? 'block' : 'none');
  if (val == 0) {
    $("#dop").myRadioSet(0);
    $("#top").myCheckVal(0);
    $("#top_week").css('visibility', 'hidden');
  }
  preview();
  printButton();
  frameBodyHeightSet();
}});


// �������������� �������
$("#dop").myRadio({
  width:200,
  bottom:6,
  spisok:[
    {uid:0,title:'<SPAN style=color:#888;>�� ��������</SPAN>'},
    {uid:'ramka',title:'������� � �����'},
    {uid:'bold',title:'�������� ������ �������'},
    {uid:'black',title:'�� ������ ����'}
  ],
  func:function () { preview(); printButton(); }
});


// ������� ����������
$("#top").myCheck({func:function (id) {
  var val = parseInt($("#" + id).val());
  $("#top_week").css('visibility', val == '1' ? 'visible' : 'hidden');
  if (val == 1) {
    create.top_week = 1;
    $("#top_week .inp").html(1);
    $("#top_week .end").html('�');
  }
  printButton();
}});
$("#top_week .a").mousedown(function (e) {
  switch ($(e.target).html()) {
  case '-': if (create.top_week > 1) { create.top_week--; } break;
  case '+': if (create.top_week < 9) { create.top_week++; } break;
  }
  $("#top_week .inp").html(create.top_week);
  $("#top_week .end").html(end(create.top_week));
  printButton();
  function end(count) {
    if(count / 10 % 10 == 1) {
      return '�';
    } else {
      switch(count % 10) {
      case 1: return '�';
      case 2: return '�';
      case 3: return '�';
      case 4: return '�';
      default: return '�';
      }
    }
  } // end

});

$("#butts .vkButton BUTTON")[0].onclick = vkCreateGo;
$("#butts .vkCancel BUTTON").click(function () { location.href = "/index.php?" + G.values + "&p=ob&d=" + create.back; });





function printButton() {
  create.order.votes = 0;
  if ($("#dop").val() != '0') { create.order.votes++; }
  if ($("#top").val() > 0) { create.order.votes += create.top_week; }
  var v = create.order.votes;
  $(".vkButton SPAN").html(v > 0 ? " �� " + v + " �����" + end(v) : '');

  function end(count) {
    if(count / 10 % 10 == 1) {
      return '��';
    } else {
      switch(count % 10) {
      case 1: return '';
      case 2: return '�';
      case 3: return '�';
      case 4: return '�';
      default: return '��';
      }
    }
  } // end
}







function preview() {
  var txt = $("#txt").val();
  txt = txt.replace(/\n/g,"<BR>");

  files = $("#images").val();

  var sp = {
    rubrika:parseInt($("#rubrika").val()) ? "<EM class=aRub>" + $("#vkSel_rubrika INPUT:first").val() + "</EM><U>�</U>" : '',
    podrubrika:parseInt($("#podrubrika").val()) ? "<EM class=aRub>" + $("#vkSel_podrubrika INPUT:first").val() + "</EM><U>�</U>" : '',
    txt:txt,
    telefon:$("#telefon").val(),
    file:files ? files.split('_')[0] : '',
    dop:$("#dop").val()
  };
  var HTML = "<DIV class=unit>";
  HTML += "<DIV class='" + sp.dop + "'>";
  HTML += "<TABLE cellpadding=0 cellspacing=0 width=100%>";
  HTML += "<TR><TD class=txt>" + sp.rubrika + sp.podrubrika + sp.txt;
  if (sp.telefon) HTML+=" <DIV class=tel>"+sp.telefon+"</DIV>";
  var name = '';
  if (parseInt($("#viewer_id_show").val())) name = "<A href='http://vk.com/id" + G.vk.viewer_id+"' target=_vk>" + G.vk.first_name + " " + G.vk.last_name + "</A>";
  var city = '';
  if ($("#cities").val() > 0) { city = $("#vkSel_countries INPUT:first").val() + ", " + $("#vkSel_cities INPUT:first").val(); }
  HTML += " <DIV class=adres>" + city + name + "</DIV>";
  if (sp.file) HTML+="<TD class=foto><IMG src=" + sp.file + "s.jpg onclick=fotoShow('" + files + "');>";
  HTML+="</TABLE></DIV></DIV>";
  $("#obSpisok").html(HTML);
  frameBodyHeightSet();
}




function vkCreateGo() {
  var MSG = '';

  var dop = $("#dop").val();
  var country_id = $("#countries").val();
  var country_name = $("#vkSel_countries INPUT:first").val();
  var city_id = $("#cities").val();
  var city_name = $("#vkSel_cities INPUT:first").val();
  var top_week = 0;
  if ($("#top_week").css('visibility') == 'visible') { top_week = create.top_week; }


  var obj = {
    rubrika:$("#rubrika").val(),
    podrubrika:$("#podrubrika").val(),
    txt:$("#txt").val(),
    telefon:$("#telefon").val(),
    adres:$("#adres").val(),
    file:$("#images").val(),
    dop:dop != '0' ? dop : '',
    country_id:country_id,
    country_name:country_id != '0' ? country_name : '',
    city_id:city_id,
    city_name:city_id != '0' ? city_name : '',
    viewer_id_show:$("#viewer_id_show").val(),
    top_day:top_week * 7,
    order_id:create.order.id,
    order_votes:create.order.votes
  };

  if (obj.rubrika == 0) {
    MSG = "�� ������� �������";
  } else if (!obj.txt) {
    MSG = "������� ����� ����������";
  }

  if (MSG) {
    $("#zMsg").alertShow({txt:"<DIV class=red>" + MSG + "</DIV>", top:-23, left:190});
  } else {
    if (create.order.votes > 0 && create.order.id == 0) {
      VK.callMethod('showOrderBox', {type:'item', item:'votes_' + create.order.votes});
    } else {
      $(".vkButton BUTTON").butProcess();
      $.post("/view/ob/create/AjaxObCreate.php?" + G.values, obj, function (res) {
        alert(res)
        location.href = "/index.php?" + G.values + "&p=ob&o=spisok";
      },'html');
    }
  }
}




//var callbacksResults = document.getElementById('callbacks');

VK.addCallback('onOrderSuccess', function(order_id) {
  create.order.id = order_id;
  vkCreateGo();
//  callbacksResults.innerHTML += '<br />onOrderSuccess '+order_id;
//  frameBodyHeightSet();
});
/*
VK.addCallback('onOrderFail', function(id) {
  callbacksResults.innerHTML += '<br />onOrderFail = ' + id;
  frameBodyHeightSet();
});
*/

/*
VK.addCallback('onOrderCancel', function() {
  callbacksResults.innerHTML += '<br />onOrderCancel';
  frameBodyHeightSet();
});
*/
