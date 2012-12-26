report.go['zayav'] = function () {
  report.img();
  var URL = "&year=" + report.thisYear;
  URL += "&mon=" + report.mon;
  URL += "&allmon=" + report.allmon;
  $.ajax({
    url:"/gazeta/report/zayav/AjaxZayavGet.php?" + G.values + URL,
    dataType:'json',
    success:function (res) {
      if (report.allmon) {
        report.monthsSet(res.allmon);
        report.allmon = '';
      }

      var HTML = "<DIV class=headName>Общее за " + (report.mon > 0 ? G.months[report.mon].toLowerCase() + ' ' : '') + report.thisYear + ":<B class=img></B></DIV>";
      HTML += "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok id=main>";
      HTML += "<TR><TH><TH>Подано<BR>заявок";
      var all = 0;
      for (var n = 1; n <= 4; n++) {
        c = parseInt(res.main[n]);
        all += c;
        HTML += "<TR><TD>" + G.zayavMn[n-1] + "<TD align=right>" + (c >0 ? c : '');
      }
      HTML += "<TR align=right><TD>Всего:<TD>" + all;
      HTML += "</TABLE>";

      if (res.nomer.length > 0) {
        report.gazetaNomer = res.nomer;
        report.n = -1;
        HTML += "<DIV class=headName>Номера выпусков за " + (report.mon > 0 ? G.months[report.mon].toLowerCase() + ' ' : '') + report.thisYear + ":</DIV>";
        HTML += "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok id=public>";
        HTML += "<TR><TH>Номер газеты<TH>Выходы";
        for (var n = 0; n < res.nomer.length; n++) {
          num = res.nomer[n];
          HTML += "<TR><TD class=nomer><H1><B>" + num.week_nomer + "</B> (" + num.general_nomer + ")</H1><EM> выход " + num.public + "</EM><TD class=count><A val=" + n + ">" + num.count + "</A>";
        }
      }

      $("#cont").html(HTML);
      $("#public").click(function (e) {
        var nNew = $(e.target).attr('val');
        if(!isNaN(nNew)) {
          var target = $(e.target).parent();
          target.html("<IMG src=/img/upload.gif>");
          $.getJSON("/gazeta/report/zayav/AjaxGazetaNomer.php?" + G.values + "&nomer=" + report.gazetaNomer[nNew].general_nomer, function (res) {
            var HTML = '';
            var all = 0, countCategory = 0;
            for (var n = 1; n <= 4; n++) {
              c = parseInt(res.zayav[n]);
              all += c;
              if (c > 0) {
                HTML += "<H2><EM>" + G.zayavMn[n-1] + ":</EM>" + (c >0 ? c : '') + "</H2>";
                countCategory++;
                }
            }
            if (countCategory > 1) { HTML += "<H2><EM><B>Всего</B>:</EM>" + all + "</H2>"; }
            target.html(HTML);
            if (report.n >= 0) { $("#public .count").eq(report.n).html("<A val=" + report.n + ">" + report.gazetaNomer[report.n].count + "</A>"); }
            report.n = nNew;
            frameBodyHeightSet();
          });
        }
      });
      frameBodyHeightSet();
    }
  });
};

