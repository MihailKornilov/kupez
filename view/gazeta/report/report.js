$("#links").infoLink({
  spisok:[
    {uid:'zayav',title:'Заявки'},
    {uid:'oplata',title:'Платежи'},
    {uid:'rashod',title:'Расходы'}],
  func:function (uid) { report.page = uid; report.go[uid](); }
});



// год
report.year = {
  left:0,
  speed:2,
  span:$("#ycenter SPAN"),
  width:Math.round($("#ycenter").css('width').split(/px/)[0] / 2),  // ширина центральной части, где год
  ismove:0,
};
report.year.next = function (side) {
  var y = report.year;
  if (y.ismove == 0) {
    y.ismove = 1;
    var changed = 0;
    var timer = setInterval(function () {
      var span = y.span;
      y.left -= y.speed * side; 

      if (y.left > 0 && changed == 1 && side == -1 ||
          y.left < 0 && changed == 1 && side == 1) {
        y.left = 0;
        y.ismove = 0;
        y.speed = 0;
        clearInterval(timer);
      }

      span[0].style.left = y.left + 'px';
      y.speed += 2;

      if (y.left > y.width && changed == 0 && side == -1 ||
          y.left < -y.width && changed == 0 && side == 1) {
        changed = 1;
        report.thisYear += side;
        report.go[report.page]();
        span.html(report.thisYear);
        y.left = y.width * side;
      }
    }, 25);
  }
};

$("#years .but:first").mousedown(function () { report.allmon = 1; report.year.next(-1); });
$("#ycenter").mousedown(function () {
  report.mon = 0;
  $("#months").myRadioSet(0);
  report.go[report.page]();
});
$("#years .but:eq(1)").mousedown(function () { report.allmon = 1; report.year.next(1); });


report.monthsSet = function (count) {
  var spisok = [];
  for (var n in G.months) {
    var title =  G.months[n];
    if (count) { title += "<EM>" + count[n] + "</EM>"; }
    spisok.push({uid:n,title:title});
  }
  $("#months").myRadio({
    spisok:spisok,
    bottom:5,
    func:function (uid) {
      report.mon = uid;
      report.go[report.page]();
    }
  });
};


report.go[report.page]();
