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
    47:'Удалил$sex1 платёж на сумму $value руб.',

    61:'Удалил$sex1 объявление №$value.',
    62:'Удалил$sex1 рекламу №$value.',
    63:'Удалил$sex1 поздравление №$value.',
    64:'Удалил$sex1 статью №$value.',

    71:'Установил$sex1 начальное значение в кассе: $value руб.',
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
        {uid:4,title:'Внесение платежа'},
        {uid:47,title:'Удаление платежа'},
        {uid:1000,title:'Изменение настроек'}
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

