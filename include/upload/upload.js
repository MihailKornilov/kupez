// загрузка изображения
$.fn.imgUpload = function (obj) {
  var obj = $.extend({
    max:1, // максимально возможное количество изображений, которое можно загрузить
    func:''  // функция, исполняемая при изменении количества и порядка изображений
  }, obj);

  var ID = $(this).attr('id');
  var TH = $(this);
  
  $(this).after("<DIV id=" + ID + "_upload class=imgUpload></DIV>");

  var value = TH.val();  // проверка списка уже загруженных изображений
  var imgArr = value ? value.split('_') : [];

  window.timer = 0;

  print();

  // выбор вывода либо формы, либо изображений с формой
  function print() {
    clearInterval(window.timer);
    delCookie('upload');
    if (imgArr.length > 0) {
      imgPrint();
    } else {
      formPrint($("#" + ID + "_upload"));
    }
  }

  // вывод формы выбора файла
  function formPrint(div) {
    var HTML = "<FORM method=post action='/include/upload/imgUploadPost.php?" + G.values + "' name=" + ID + "_form enctype=multipart/form-data target=" + ID + "_frame>";
    HTML += "<INPUT TYPE=file NAME=file_name><DIV class=button>Обзор...</DIV>";
    HTML += "<IFRAME name=" + ID + "_frame></IFRAME></FORM>";

    div.html(HTML);
  
    $("#" + ID + "_upload INPUT:first").bind({
      mouseenter:function () { $(this).next().css('background-color','#DDE'); },
      mouseleave:function () { $(this).next().css('background-color','transparent'); },
      change:function () {
        $(this).next().remove();
        setCookie('upload','process');
        window.timer = setInterval(uploadStart, 500);
        document[ID + '_form'].submit();
        $(this)
          .css('left','-200px')
          .after("<DIV class=uploading><IMG src=/img/upload.gif><A class=img_del></A></DIV>")
          .next().click(print);
      }
    });
  }

  // процесс загрузки файла
  function uploadStart() {
    var cookie = getCookie("upload");
    if (cookie != 'process') {
      if (cookie != 'error') {
        clearInterval(window.timer);
        imgArr.push(G.domen + "/files/images/" + cookie);
        print();
      }
    }
  }

  // вывод загруженных изображений
  function imgPrint() {
    var HTML = "<DL>";
    var len = imgArr.length;
    for (var n = 0; n < len; n++) {
      HTML += "<DD><IMG src='" + imgArr[n] + "s.jpg' class=uploaded val='" + imgArr[n] + "'><A class=del val=" + n + "></A>";
    }

    $("#" + ID + "_upload").html(HTML + (len < obj.max ? '<DD>' : '') + "</DL>");

    $("#" + ID + "_upload DL").click(function (e) {
      var n = $(e.target).attr('val');
      if (n) {
        var tag = $(e.target)[0].tagName;
        switch (tag) {
        case 'A':
          imgArr.splice(n,1);
          print();
          break;
    //    case 'IMG': fotoShow(n); break;
        }
      }
    });

    $("#" + ID + "_upload DD").bind({
      mouseenter:function () { $(this).find('.del').show(); },
      mouseleave:function () { $(this).find('.del').hide(); }
    });

    if (len == 1) { $("#" + ID + "_upload DL IMG:first")[0].onload = frameBodyHeightSet; }

    if (len > 1) {
      $("#" + ID + "_upload .uploaded").css('cursor','move');
      $("#" + ID + "_upload DL").sortable({axis:'x',update:function(){
        var img = $(this).find('IMG');
        imgArr = [];
        for (var n = 0; n < img.length; n++) { imgArr.push(img.eq(n).attr('val')); }
        print();
      }});
    }

    if (len < obj.max) { formPrint($("#" + ID + "_upload DD:last")); }

    TH.val(imgArr.join('_'));

    if (obj.func) { obj.func(); }
  }
};
