$("#cDolg").myCheck({name:"��������",func:function(){
		$("#clientFind INPUT:first").val('');
		$("#clientFind H5 DIV:first").show();
        G.spisok.print();
		}
	});

$("#clientFind").topSearch({
	width:585,
	focus:1,
	txt:'������� ������� ������ �������',
	func:function (INP) {
		$("#cDolg").myCheckVal();
        G.spisok.print({input:encodeURIComponent(INP)});
		}
	});

$("#personFind").vkSel({
	width:177,
	spisok: G.person_spisok,
	title0:'��������� �� ������',
	func:function (id) { G.spisok.print({person:id}); }
});


G.spisok.unit = function (sp) {
    HTML = "";
    if(sp.balans != 0) HTML += "<DIV class=balans>������: <B style=color:#"+(sp.balans<0?'A00':'090')+">"+sp.balans+"</B></DIV>";
    HTML += "<TABLE cellspacing=3 cellpadding=0>";
    if(sp.org_name)
        HTML += "<TR><TD class=tdAbout>�����������:<TD><A HREF='" + G.url + "&p=client&d=info&id=" + sp.id + "'>" + sp.org_name + "</A>";
    else HTML += "<TR><TD class=tdAbout>���:<TD><A HREF='" + G.url + "&p=client&d=info&id=" + sp.id + "'>" + sp.fio + "</A>";
    if(sp.telefon) HTML += "<TR><TD class=tdAbout>�������:<TD>" + sp.telefon;
    if(sp.adres) HTML += "<TR><TD class=tdAbout>�����:<TD>" + sp.adres;
    if(sp.zayav_count > 0) HTML += "<TR><TD class=tdAbout>������:<TD>" + sp.zayav_count;
    HTML += "</TABLE>";
    return HTML;
};

G.spisok.create({
    url:"/view/gazeta/client/spisok/AjaxClientSpisok.php",
    view:$("#spisok"),
    limit:20,
    cache_spisok:spisok.ob,
    result_view:$("#findResult"),
    result:"�������� $count ������$cl",
    ends:{'$cl':['', '�', '��']},
    next:"�����...",
    nofind:"�������� �� �������.",
    imgup:$("#findResult"),
    //a:1,
    values:{
        input:'',
        dolg:0,
        person:0
    }
});

function ca() {
    clientAdd(function (id) { location.href = G.url + "&p=gazeta&g=client&id=" + id; });
}




