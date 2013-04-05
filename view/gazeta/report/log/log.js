G.log = {};
G.log.type = {
    11:'������$sex1 ����� ���������� $zayav.',
    12:'������$sex1 ����� ������� $zayav.',
    13:'������$sex1 ����� ������������ $zayav.',
    14:'������$sex1 ����� ������ $zayav.',

    21:'������$sex1 ����� ���������� $zayav ��� ������� $client.',
    22:'������$sex1 ����� ������� $zayav ��� ������� $client.',
    23:'������$sex1 ����� ������������ $zayav ��� ������� $client.',
    24:'������$sex1 ����� ������ $zayav ��� ������� $client.',

    31:'�������$sex1 ������ ���������� $zayav.',
    32:'�������$sex1 ������ ������� $zayav.',
    33:'�������$sex1 ������ ������������ $zayav.',
    34:'�������$sex1 ������ ������ $zayav.',

    41:'��$sex2 ����� �� ����� $value ���. �� ���������� $zayav.',
    42:'��$sex2 ����� �� ����� $value ���. �� ������� $zayav.',
    43:'��$sex2 ����� �� ����� $value ���. �� ������������ $zayav.',
    44:'��$sex2 ����� �� ����� $value ���. �� ������ $zayav.',
    45:'��$sex2 ����� �� ����� $value ���. ($dop).',
    46:'��$sex2 ����� �� ����� $value ���. ($dop). ������ $client.',
    47:'������$sex1 ����� �� ����� $value ���.',

    51:'��$sex2 ������ ������� $client.',
    52:'�������$sex1 ������ ������� $client.',
    53:'������$sex1 ������� $value.',

    61:'������$sex1 ���������� �$value.',
    62:'������$sex1 ������� �$value.',
    63:'������$sex1 ������������ �$value.',
    64:'������$sex1 ������ �$value.',

    71:'���������$sex1 ��������� �������� � �����: $value ���.',

    // ���������
    1011:'� ���������� �������$sex1 ����� ��������� ������� "$value".',
    1012:'� ���������� �������$sex1 ������ ��������� ������� "$value".',
    1013:'� ���������� ������$sex1 ������ ��������� ������� "$value".',

    1021:'� ���������� �������$sex1 ����� ������� "$value".',
    1022:'� ���������� �������$sex1 ������� "$value".',
    1023:'� ���������� ������$sex1 ������� "$value".',

    1031:'� ���������� �������$sex1 ����� $value-� ����� ������.',
    1032:'� ���������� ��������������$sex1 ������ $value-�� ������ ������.',
    1033:'� ���������� ������$sex1 ������ $value-�� ������ ������.',
    1034:'� ���������� ������$sex1 ������ ������� ����� �� $value ���.',

    1041:'� ���������� �������$sex1 ����� �������� ������ "$value".',
    1042:'� ���������� �������$sex1 ������ ������ "$value".',
    1043:'� ���������� ������$sex1 ������ ������ "$value".',

    1051:'� ���������� �������$sex1 ����� ������ $value%.',
    1052:'� ���������� �������$sex1 ������ ������ $value%.',
    1053:'� ���������� ������$sex1 ������ $value%.',

    1062:'� ���������� �������$sex1 ��������� ��������������� ��������� ���������� "$value".',

    1071:'� ���������� �������$sex1 ����� ���������� $value.',
    1072:'� ���������� �������$sex1 ���������� $value.',
    1073:'� ���������� ������$sex1 ���������� $value.',


    1081:'� ���������� �������$sex1 ������ ���������� $value.',
    1082:'� ���������� ������$sex1 ���������� $value.',

    1101:'� ���������� �������$sex1 ����� ��������� ������� "$value".',
    1102:'� ���������� �������$sex1 ��������� ������� "$value".',
    1103:'� ���������� ������$sex1 ��������� ������� "$value".',

    1111:'� ���������� �������$sex1 ����� ��� ������� "$value".',
    1112:'� ���������� �������$sex1 ��� ������� "$value".',
    1113:'� ���������� ������$sex1 ��� ������� "$value".',


    1091:'� ���������� �������$sex1 ��������� ����� ����������:<br />$value'
};

// ������������ ��������� �� ����
function endSex(us, txt) {
    txt = txt.replace('$sex1', us.sex == 1 ? '�' : '');
    txt = txt.replace('$sex2', us.sex == 1 ? '����' : '��');
    return txt;
}



$("#log_type").vkSel({
    width:140,
    title0:'����� ���������',
    spisok:[
        {uid:1,title:'�������� ������'},
        {uid:3,title:'��������� ������'},
        {uid:6,title:'�������� ������'},
        {uid:51,title:'�������� �������'},
        {uid:52,title:'��������� �������'},
        {uid:53,title:'�������� �������'},
        {uid:4,title:'�������� �������'},
        {uid:47,title:'�������� �������'},
        {uid:1000,title:'��������� ��������'}
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
    title0:'��� ����������',
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
            '(�����)');
    }

    if (sp.zayav_id) { txt = txt.replace('$zayav', "<A href='/index.php?" + G.values + "&p=gazeta&d=zayav&d1=view&id=" + sp.zayav_id + "'>�" + sp.zayav_id + "</A>"); }
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
    nofind:"������� ���.",
    //   a:1,
    values:{
        type:0,
        worker:0,
        day_begin:$("#day_begin").val(),
        day_end:$("#day_end").val()
    },
    callback:function (data) {}
});
