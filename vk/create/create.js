$("#rubrika").vkSel({
  width:120,
  title0:'Не указана',
  spisok:rubrika,
  func:function(uid){
    $("#podrubrika").val(0);
    $("#vkSel_podrubrika").remove();
    if(podrubrika[uid]) {
      $("#podrubrika").vkSel({
        width:200,
        title0:'Подрубрика не указана',
        spisok:podrubrika[uid]
      });
    }
  }
});

$("#txt").textareaResize({minH:50,first:0});

tdUploadSet();

$("#vk_viewer_id_show").myCheck();

$("#vk_srok").vkSel({
  width:90,
  spisok:[{uid:1,title:'1 неделя'},{uid:2,title:'2 недели'},{uid:3,title:'3 недели'}]
});



// ЗАГРУЗКА ФАЙЛА
var timer = 0;
function tdUploadSet() {
  clearInterval(timer);
  delCookie('upload');
  var HTML="<INPUT TYPE=file NAME=file_name id=file_name onchange=fileSelected();>";
  HTML+="<IFRAME src='' name=uploadFrame scrolling=yes frameborder=1 style=display:none;></IFRAME>";
  $("#tdUpload").html(HTML);
  frameBodyHeightSet();
  }

function fileSelected()
  {
  $("#file_name").after("<IMG src=/img/upload.gif class=upload><A href='javascript:' onclick=tdUploadSet();>отменить</A>");
  setCookie('upload','process');
  timer=setInterval("fileUploadStart();",500);
  document.FormCreate.submit();
  $("#file_name").attr('disabled','on');
  }

function fileUploadStart()
  {
  var COOKIE=getCookie("upload");
  if(COOKIE!='process')
    if(COOKIE!='error')
      {
      clearInterval(timer);
      var HTML="<TABLE cellpadding=0 cellspacing=0 id=fileTab>";
      HTML+="<TR><TD><IMG src=/files/images/"+COOKIE+"s.jpg onclick=fotoShow('"+COOKIE+"'); onload=frameBodyHeightSet();><TD valign=top><A href='javascript:' class=img_del onclick=tdUploadSet();></A>";
      HTML+="</TABLE><INPUT TYPE=hidden NAME=file id=file value='"+COOKIE+"'>";
      $("#tdUpload").html(HTML);
      delCookie("upload");
      }
  }






function vkCreateGo() {
  var MSG = '', GO = 1;

  if($("#rubrika").val()==0) { MSG="Не указана рубрика"; GO=0; }
    else
      if(!$("#txt").val()) { MSG="Введите текст объявления"; GO=0; }
  
  
  if(GO==0) {
    $("#zMsg").alertShow({txt:"<DIV class=red>"+MSG+"</DIV>",top:-43,left:220});
  } else {
    document.FormCreate.action="/index.php?" + G.values + "&my_page=vk-create";
    document.FormCreate.enctype='';
    document.FormCreate.target='';
    document.FormCreate.submit();
  }  
}

