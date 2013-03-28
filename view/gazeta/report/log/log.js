G.log = {};
G.log.type = {
    11:'Создал$sex1 новое объявление $zayav.',
    12:'Создал$sex1 новую рекламу $zayav.',
    13:'Создал$sex1 новое поздравление $zayav.',
    14:'Создал$sex1 новую статью $zayav.',

    21:'Создал$sex1 новое объявление $zayav для клиента $client.',
    22:'Создал$sex1 новую рекламу $zayav для клиента $client.',
    23:'Создал$sex1 новое поздравление $zayav для клиента $client.',
    24:'Создал$sex1 новую статью $zayav для клиента $client.',

    31:'Изменил$sex1 данные объявления $zayav.',
    32:'Изменил$sex1 данные рекламы $zayav.',
    33:'Изменил$sex1 данные поздравления $zayav.',
    34:'Изменил$sex1 данные статьи $zayav.',

    41:'Вн$sex2 платёж на сумму $value руб. за объявление $zayav.',
    42:'Вн$sex2 платёж на сумму $value руб. за рекламу $zayav.',
    43:'Вн$sex2 платёж на сумму $value руб. за поздравление $zayav.',
    44:'Вн$sex2 платёж на сумму $value руб. за статью $zayav.',
    45:'Вн$sex2 платёж на сумму $value руб. ($dop).',
    46:'Вн$sex2 платёж на сумму $value руб. ($dop). Клиент $client.',

    51:'Вн$sex2 нового клиента $client.',
    52:'Изменил$sex1 данные клиента $client.',
    53:'Удалил$sex1 клиента $value.',

    61:'Удалил$sex1 объявление №$value.',
    62:'Удалил$sex1 рекламу №$value.',
    63:'Удалил$sex1 поздравление №$value.',
    64:'Удалил$sex1 статью №$value.',

    71:'Установил$sex1 начальное значение в кассе: $value руб.',

    // Настройки
    1081:'Добавил$sex1 нового сотрудника $value',
    1082:'Удалил$sex1 сотрудника $value'
};

// Формирование окончаний по полу
function endSex(us, txt) {
    txt = txt.replace('$sex1', us.sex == 1 ? 'а' : '');
    txt = txt.replace('$sex2', us.sex == 1 ? 'есла' : 'ёс');
    return txt;
}



$("#log_type").vkSel({
    width:140,
    title0:'Любая категория',
    spisok:[
        {uid:1,title:'Создание заявки'},
        {uid:3,title:'Изменение заявки'},
        {uid:6,title:'Удаление заявки'},
        {uid:51,title:'Внесение клиента'},
        {uid:52,title:'Изменение клиента'},
        {uid:53,title:'Удаление клиента'},
        {uid:4,title:'Внесение платежа'}
    ],
    func:function (id) {
        G.spisok.print({type:id});
    }
});


var users = [];
for (var k in G.vkusers) {
    users.push({uid:k,title:G.vkusers[k].name});
}

$("#log_worker").vkSel({
    width:140,
    title0:'Все сотрудники',
    spisok:users,
    func:function (id) {
        G.spisok.print({worker:id});
    }
});


$("#day_begin").vkCalendar({lost:1, place:'left', func:function (data) { G.spisok.print({day_begin:data}); }});
$("#day_end").vkCalendar({lost:1, place:'left', func:function (data) { G.spisok.print({day_end:data}); }});


G.spisok.unit = function (sp) {
    var us = G.vkusers[sp.viewer_id];
    var txt = G.log.type[sp.type];
    if (!txt) return '';
    txt = endSex(us, txt);

    if (sp.client_id) {
        txt = txt.replace('$client',
            sp.client_fio ?
            "<A href='/index.php?" + G.values + "&p=gazeta&d=client&d1=info&id=" + sp.client_id + "'>" + sp.client_fio + "</A>" :
            '(удалён)');
    }

    if (sp.zayav_id) { txt = txt.replace('$zayav', "<A href='/index.php?" + G.values + "&p=gazeta&d=zayav&d1=view&id=" + sp.zayav_id + "'>№" + sp.zayav_id + "</A>"); }
    if (sp.value) { txt = txt.replace('$value', sp.value); }
    if (sp.dop) { txt = txt.replace('$dop', sp.dop); }

    return "<div class=head>" +
                sp.dtime +
                "<A href='http://vk.com/id" + sp.viewer_id + "' target='_blank'>" + us.name + "</A>" +
           "</div>" +
           "<div class=txt>" + txt + "</div>";
};

G.spisok.create({
    url:"/view/gazeta/report/log/AjaxLogGet.php",
    limit:20,
    view:$("#spisok"),
    nofind:"Истории нет.",
    //   a:1,
    values:{
        type:0,
        worker:0,
        day_begin:$("#day_begin").val(),
        day_end:$("#day_end").val()
    },
    callback:function (data) {}
});
