// ����� �����������
function imageView() {
    G.fotoView({spisok:[{link:G.zayav.image}]});
};

// ����� ��������� �������� ������
function lostView() {
    $("#zayavView .lost").show();
    $("#zayavView .lost_spisok").hide();
    frameBodyHeightSet();
}




if ($("#del").length > 0) {
    $("#del").vkHint({
        msg:'����� ������� ��� ������<br>' +
            '��������� � � �������.<br>' +
            '����� ������� ��� �������<br>' +
            '���� ������ �������� �������<br>' +
            '�� ������ ����� ��������.',
        ugol:'top',
        indent:120,
        top:17,
        left:426
    });
}



if ($("#delete").length > 0) {
    $("#delete").click(function () {
        var dialog = $("#dialog_zayav").vkDialog({
            width:250,
            head:"�������� ������",
            butSubmit:"�������",
            content:"<CENTER><B>����������� �������� ������</B></CENTER>",
            submit:function () {
                dialog.process();
                $.getJSON("/view/gazeta/zayav/view/AjaxZayavDel.php?" + G.values + "&id=" + G.zayav.id + "&category=" + G.zayav.category, function () {
                    location.href = G.url + "&p=gazeta&d=zayav";
                }, 'json');
            }
        }).o;
    });
}