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

    61:'������$sex1 ���������� �$value.',
    62:'������$sex1 ������� �$value.',
    63:'������$sex1 ������������ �$value.',
    64:'������$sex1 ������ �$value.',

    71:'���������$sex1 ��������� �������� � �����: $value ���.',
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

