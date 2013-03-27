G.report = {
    year:(new Date).getFullYear(),
    format:'Month'
};
G.report.curYear = G.report.year;
G.report.curMonth = (new Date).getMonth();

$("#zayav_year").years({
    start:function () { $(".headName").append("<img src=/img/upload.gif>"); },
    func:function (year) {
        G.report.year = year;
        window['report' + G.report.format]();
    }
});

$("#format").vkSel({
    width:140,
    spisok:[{uid:'Month', title:'По месяцам'}, {uid:'Nomer', title:'По номерам газеты'}],
    func:function (f) {
        $(".headName").append("<img src=/img/upload.gif>");
        G.report.format = f;
        window['report' + f]();
    }
});

reportMonth();


function reportMonth() {
    var year = G.report.year;
    //location.href = "/view/gazeta/report/zayav/AjaxZayavMonth.php?" + G.values + "&year=" + year
    $.getJSON("/view/gazeta/report/zayav/AjaxZayavMonth.php?" + G.values + "&year=" + year, function (res) {
        var html = "<div class=headName>Отчёт по количеству публикаций по месяцам за " + year + " год: </div>" +
                   "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>" +
                   "<tr><td>" +
                       "<td class=h>Объявления" +
                       "<td class=h>Реклама" +
                       "<td class=h>Поздравл." +
                       "<td class=h>Статьи" +
                       "<th>Всего";
        var y_count = [0,0,0,0,0];
        var y_summa = [0,0,0,0,0];
        for (var k = 1; k <= 12; k++) {
            var sp = res[k] || [];
            var all_count = 0;
            var all_summa = 0;
            var cur = (G.report.curYear == year && G.report.curMonth == k - 1 ? ' class=cur' : '');
            html += "<tr" + cur + "><td class=mon>" + G.months_ass[k];
            for (var n = 1; n <= 4; n++) {
                sp[n] = sp[n] ? sp[n] : '';
                if (sp[n]) {
                    all_count += sp[n].count * 1;
                    y_count[n - 1] += sp[n].count * 1;
                    all_summa += sp[n].summa * 1;
                    y_summa[n - 1] += sp[n].summa * 1;
                    sp[n].summa = "<div class=sum>" + sp[n].summa + " руб.</div>";
                } else sp[n] = {count:'',summa:''}
                html += "<td class=c>" + sp[n].count + sp[n].summa;
            }
            html += "<td class=c>" + (all_count ? all_count + "<div class=sum>" + (Math.round(all_summa * 100) / 100) + " руб.</div>" : '');
            y_count[4] += all_count;
            y_summa[4] += all_summa;
        }
        html += "<tr><td class=year>Год:";
        for (var n = 0; n <= 4; n++)
            html += "<td class=c>" + (y_count[n] ? y_count[n] + "<div class=sum>" + (Math.round(y_summa[n] * 100) / 100) + " руб.</div>" : '');
        html += "</table>";
        $("#spisok").html(html);
        frameBodyHeightSet();
    });
}

function reportNomer() {
    var year = G.report.year;
    var url = G.url + "&p=gazeta&d=zayav&year=" + year;
   // location.href = "/view/gazeta/report/zayav/AjaxZayavNomer.php?" + G.values + "&year=" + year
    $.getJSON("/view/gazeta/report/zayav/AjaxZayavNomer.php?" + G.values + "&year=" + year, function (res) {
        var html = "<div class=headName>Отчёт по количеству публикаций по номерам за " + year + " год: </div>" +
            "<TABLE cellpadding=0 cellspacing=0 class=tabSpisok>" +
            "<tr><td class=gn_about>Номер,<br>день выхода" +
                "<td class=h>Объявления" +
                "<td class=h>Реклама" +
                "<td class=h>Поздравл." +
                "<td class=h>Статьи" +
                "<th>Всего";
        var y_count = [0,0,0,0,0];
        var y_summa = [0,0,0,0,0];
        for (var k in res) {
            var sp = res[k];
            var cur = (G.gn.first_active == k ? ' class=cur' : '');
            html += "<tr" + cur + "><td class=gn><b>" + sp.gn.week + "<b><em>(" + k + ")</em><div class=pub>" + sp.gn.public + "</div>";
            var all_count = 0;
            var all_summa = 0;
            for (var n = 1; n <= 4; n++) {
                if (sp[n]) {
                    all_count += sp[n].count * 1;
                    y_count[n - 1] += sp[n].count * 1;
                    sp[n].count = "<a href='" + url + "&gn=" + k + "&cat=" + n + "'>" + sp[n].count + "</a>";
                    all_summa += sp[n].summa * 1;
                    y_summa[n - 1] += sp[n].summa * 1;
                    sp[n].summa = "<div class=sum>" + sp[n].summa + " руб.</div>";
                } else sp[n] = {count:'',summa:''}
                html += "<td class=c>" + sp[n].count + sp[n].summa;
            }
            html += "<td class=c>" +
                (all_count ?
                    "<a href='" + url + "&gn=" + k + "'>" + all_count + "</a>" +
                        "<div class=sum>" + (Math.round(all_summa * 100) / 100) + " руб.</div>" : '');
            y_count[4] += all_count;
            y_summa[4] += all_summa;
        }
        html += "<tr><td class=year>Год:";
        for (var n = 0; n <= 4; n++)
            html += "<td class=c>" + (y_count[n] ? y_count[n] + "<div class=sum>" + (Math.round(y_summa[n] * 100) / 100) + " руб.</div>" : '');
        html += "</table>";
        $("#spisok").html(html);
        frameBodyHeightSet();
    });
}
