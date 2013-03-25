$("#clientFind").topSearch({
    width:585,
    focus:1,
    txt:'Начните вводить данные клиента',
    func:function (inp) {
        G.spisok.print({input:encodeURIComponent(inp)});
    }
});


// Внесение нового клиента
$("#buttonCreate").click(function () {
    clientAdd(function(res) {
        location.href = G.url + "&p=gazeta&d=client&d1=info&id=" + res.uid;
    });
});


$("#order").vkRadio({
    top:5,
    light:1,
    spisok:[
        {uid:1, title:'По дате добавления'},
        {uid:2, title:'По активности'}
    ],
    func:function (val) { G.spisok.print({order:val}); }

});
$("#order_radio").vkHint({
    width:210,
    msg:'<div style=text-align:justify;>' +
        '<b>По дате добавления:</b><br> клиенты, добавленные последними, стоят в списке первыми.<br><br>' +
        '<b>По активности:</b><br> сортировка по дате выхода последней заявки клиента. ' +
        'Также показывается дополнительное поле "Активность", в котором содержится дата последнего выхода заявки.' +
        '</div>',
    ugol:'right',
    indent:15,
    top:-39,
    left:-245,
    delayShow:1000
});


$("#dolg").myCheck({
    title:"Должники",
    func:function (id) {
        $("#clientFind").topSearchClear();
        person.val(0);
        G.spisok.print({dolg:id, input:'', person:0});
    }
});
$("#check_dolg").vkHint({
    msg:'Вывод списка клиентов,<br>' +
        'у которых баланс меньше 0.<br>' +
        'Другие параметры поиска<br>' +
        'сбрасываются.',
    ugol:'right',
    indent:15,
    top:-21,
    left:-202,
    delayShow:1000
});

// Категория клиентов
var person = $("#person").vkSel({
	width:150,
	spisok: G.person_spisok,
	title0:'Категория не выбрана',
	func:function (id) { G.spisok.print({person:id}); }
}).o;

// Скидка
$("#skidka").vkSel({
    width:150,
    spisok: G.skidka_spisok,
    title0:'Скидка не выбрана',
    func:function (id) { G.spisok.print({skidka:id}); }
}).o;

G.spisok.unit = function (sp) {
    var name_about = sp.org_name ? "Организация" : "Фио";
    var name = sp.org_name ? sp.org_name + (sp.fio ? "<span class=dop_fio>" + sp.fio + "</span>" : '') : sp.fio;
    return '' +
        (sp.balans ? "<DIV class=balans>Баланс: <B style=color:#" + (sp.balans < 0? 'A00' : '090') + ">" + sp.balans + "</B></DIV>" : '') +
        "<TABLE cellpadding=0 cellspacing=2>" +
        "<TR><TD class=tdAbout>" + name_about + ":<TD><A HREF='" + G.url + "&p=gazeta&d=client&d1=info&id=" + sp.id + "'>" + name + "</A>" +
        (sp.telefon ? "<TR><TD class=tdAbout>Телефон:<TD>" + sp.telefon : '') +
        (sp.adres ? "<TR><TD class=tdAbout>Адрес:<TD>" + sp.adres : '') +
        (sp.inn ? "<TR><TD class=tdAbout>ИНН:<TD>" + sp.inn : '') +
        (sp.kpp ? "<TR><TD class=tdAbout>КПП:<TD>" + sp.kpp : '') +
        (sp.email ? "<TR><TD class=tdAbout>E-mail:<TD>" + sp.email : '') +
        (sp.zayav_count ? "<TR><TD class=tdAbout>Заявки:<TD>" + sp.zayav_count : '') +
        (sp.activity ? "<TR><TD class=tdAbout>Активность:<TD>" + sp.activity : '') +
        "</TABLE>";
};

G.spisok.create({
    url:"/view/gazeta/client/spisok/AjaxClientSpisok.php",
    view:$("#spisok"),
    limit:20,
    cache_spisok:spisok.ob,
    result_view:$("#findResult"),
    result:"Показано $count клиент$cl",
    ends:{'$cl':['', 'а', 'ов']},
    next_txt:"Далее...",
    nofind:"Клиентов не найдено.",
    imgup:$("#findResult"),
    //a:1,
    values:{
        input:'',
        dolg:0,
        person:0,
        order:1,
        skidka:0
    },
    callback:function () {
        $("#findResult .dolg").remove();
        if (G.spisok.data.dolg && G.spisok.values.dolg == 1) {
            $("#findResult").append("<span class=dolg>(Сумма долга = " + G.spisok.data.dolg + " руб.)</span>")
        }
    }
});


