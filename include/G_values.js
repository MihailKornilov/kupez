// массив рубрик для выпадающего списка
G.rubrika_spisok = [{"uid":"1","title":"\u041f\u0440\u043e\u0434\u0430\u043c"},{"uid":"11","title":"\u041a\u0443\u043f\u043b\u044e"},{"uid":"3","title":"\u041c\u0435\u043d\u044f\u044e"},{"uid":"7","title":"\u0421\u043d\u0438\u043c\u0443"},{"uid":"6","title":"\u0421\u0434\u0430\u043c"},{"uid":"8","title":"\u0423\u0441\u043b\u0443\u0433\u0438"},{"uid":"9","title":"\u0420\u0430\u0437\u043d\u043e\u0435"},{"uid":"10","title":"\u0422\u0440\u0435\u0431\u0443\u0435\u0442\u0441\u044f"}];

// ассоциативный массив рубрик {"1":"Продам"}
G.rubrika_ass = {};
for (var n = 0; n < G.rubrika_spisok.length; n++) {
  var sp = G.rubrika_spisok[n];
  G.rubrika_ass[sp.uid] = sp.title;
}

// массив подрубрик для выпадающего списка
G.podrubrika_spisok = {"1":[{"uid":"4","title":"\u0422\u0435\u0445\u043d\u0438\u043a\u0430"},{"uid":"1","title":"\u041d\u0435\u0434\u0432\u0438\u0436\u0438\u043c\u043e\u0441\u0442\u044c"},{"uid":"9","title":"\u0417\u0435\u043c\u043b\u044f"},{"uid":"3","title":"\u0411\u044b\u0442\u043e\u0432\u0430\u044f \u0438 \u043e\u0440\u0433\u0442\u0435\u0445\u043d\u0438\u043a\u0430"},{"uid":"6","title":"\u041c\u0435\u0431\u0435\u043b\u044c"},{"uid":"7","title":"\u041e\u0434\u0435\u0436\u0434\u0430"},{"uid":"12","title":"\u0421\u043f\u043e\u0440\u0442-\u041e\u0442\u0434\u044b\u0445"},{"uid":"10","title":"\u0414\u0435\u0442\u044f\u043c"},{"uid":"11","title":"\u0420\u0430\u0437\u043d\u043e\u0435"},{"uid":"2","title":"\u0421\u0442\u0440\u043e\u0439\u043c\u0430\u0442\u0435\u0440\u0438\u0430\u043b\u044b"},{"uid":"8","title":"\u041f\u0438\u043b\u043e\u043c\u0430\u0442\u0435\u0440\u0438\u0430\u043b\u044b"},{"uid":"5","title":"\u0416\u0438\u0432\u043e\u0442\u043d\u044b\u0435"}]};

// ассоциативный массив подрубрик {"1":"Техника"}
G.podrubrika_ass = {};
for (var k in G.podrubrika_spisok) {
  var arr = G.podrubrika_spisok[k];
  for (var n = 0; n < arr.length; n++) {
    G.podrubrika_ass[arr[n].uid] = arr[n].title;
  }
}


G.cities = [{"uid":1,"title":"\u041c\u043e\u0441\u043a\u0432\u0430"},{"uid":2,"title":"\u0421\u0430\u043d\u043a\u0442-\u041f\u0435\u0442\u0435\u0440\u0431\u0443\u0440\u0433"},{"uid":10,"title":"\u0412\u043e\u043b\u0433\u043e\u0433\u0440\u0430\u0434"},{"uid":37,"title":"\u0412\u043b\u0430\u0434\u0438\u0432\u043e\u0441\u0442\u043e\u043a"},{"uid":153,"title":"\u0425\u0430\u0431\u0430\u0440\u043e\u0432\u0441\u043a"},{"uid":49,"title":"\u0415\u043a\u0430\u0442\u0435\u0440\u0438\u043d\u0431\u0443\u0440\u0433"},{"uid":60,"title":"\u041a\u0430\u0437\u0430\u043d\u044c"},{"uid":61,"title":"\u041a\u0430\u043b\u0438\u043d\u0438\u043d\u0433\u0440\u0430\u0434"},{"uid":72,"title":"\u041a\u0440\u0430\u0441\u043d\u043e\u0434\u0430\u0440"},{"uid":73,"title":"\u041a\u0440\u0430\u0441\u043d\u043e\u044f\u0440\u0441\u043a"},{"uid":95,"title":"\u041d\u0438\u0436\u043d\u0438\u0439 \u041d\u043e\u0432\u0433\u043e\u0440\u043e\u0434"},{"uid":99,"title":"\u041d\u043e\u0432\u043e\u0441\u0438\u0431\u0438\u0440\u0441\u043a"},{"uid":104,"title":"\u041e\u043c\u0441\u043a"},{"uid":110,"title":"\u041f\u0435\u0440\u043c\u044c"},{"uid":119,"title":"\u0420\u043e\u0441\u0442\u043e\u0432-\u043d\u0430-\u0414\u043e\u043d\u0443"},{"uid":123,"title":"\u0421\u0430\u043c\u0430\u0440\u0430"},{"uid":151,"title":"\u0423\u0444\u0430"},{"uid":158,"title":"\u0427\u0435\u043b\u044f\u0431\u0438\u043d\u0441\u043a"}];
for (var n = 0; n <= 1; n++) { G.cities[n].content = "<B>" + G.cities[n].title + "</B>"; } // выделение Москвы и Питера
