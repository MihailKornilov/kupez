var AJAX_GAZ = 'http://' + DOMAIN + '/ajax/gazeta.php?' + VALUES,
	clientFilter = function() {
		var v = {
			fast:cFind.inp(),
			person:$('#person').val(),
			order:$('#order').val(),
			skidka:$('#skidka').val(),
			dolg:$('#dolg').val()
		};
		$('.filter')[v.fast ? 'hide' : 'show']();
		return v;
	},
	clientSpisokLoad = function() {
		var send = clientFilter(),
			result = $('.result');
		send.op = 'client_spisok';
		if(result.hasClass('busy'))
			return;
		result.addClass('busy');
		$.post(AJAX_GAZ, send, function (res) {
			result.removeClass('busy');
			if(res.success) {
				result.html(res.result);
				$('.left').html(res.spisok);
			}
		}, 'json');
	};

$(document)
	.on('click', '#client .ajaxNext', function() {
		if($(this).hasClass('busy'))
			return;
		var next = $(this),
			send = clientFilter();
		send.op = 'client_next';
		send.page = next.attr('val');
		next.addClass('busy');
		$.post(AJAX_GAZ, send, function (res) {
			if(res.success) {
				next.remove();
				$('#client .left').append(res.spisok);
			} else
				next.removeClass('busy');
		}, 'json');
	})

	.ready(function() {
		if($('#client').length > 0) {
			window.cFind = $('#find')._search({
				width:602,
				focus:1,
				enter:1,
				txt:'������� ������ ������� � ������� Enter',
				func:clientSpisokLoad
			});
			//$('#buttonCreate').click(clientAdd);
			$('#order')._radio({
				light:1,
				spisok:[
					{uid:1,title:'�� ���� ����������'},
					{uid:2,title:'�� ����������'}
				],
				func:clientSpisokLoad

			});
			$('#order_radio').vkHint({
				width:210,
				msg:'<div style="text-align:justify">' +
						'<b>�� ���� ����������:</b><br> �������, ����������� ����������, ����� � ������ �������.<br><br>' +
						'<b>�� ����������:</b><br> ���������� �� ���� ������ ��������� ������ �������. ' +
						'����� ������������ �������������� ���� "����������", � ������� ���������� ���� ���������� ������ ������.' +
					'</div>',
				ugol:'right',
				indent:15,
				top:-34,
				left:-246,
				delayShow:1000
			});
			$('#person').vkSel({
				width:150,
				spisok:PERSON_SPISOK,
				title0:'��������� �� �������',
				func:clientSpisokLoad
			});
			$('#skidka').vkSel({
				width:150,
				spisok:SKIDKA_SPISOK,
				title0:'������ �� �������',
				func:clientSpisokLoad
			});
			$('#dolg')._check(clientSpisokLoad);
			$('#dolg_check').vkHint({
				msg:'<b>������ ���������.</b><br /><br />' +
					'��������� �������, � ������� ������ ����� 0. ����� � ���������� ������������ ����� ����� �����.',
				ugol:'right',
				width:150,
				top:-6,
				left:-185,
				indent:20,
				delayShow:1000,
				correct:0
			});
		}
	});

/*
// �������� �������
function moneyAdd(obj) {
    $("#dialog_prihod").remove();
    $("BODY").append("<div id=dialog_prihod></div>");

    var obj = $.extend({
        zayav_id:0,
        client_id:0,
        func:function () { location.reload(); }
    }, obj);

    var html = "<TABLE cellpadding=0 cellspacing=10 id=prihod_add_tab>" +
        "<TR><TD class=tdAbout>���:<TD><INPUT type=hidden id=prihod_type><a class=img_edit href='" + G.url + "&p=gazeta&d=setup&id=11'></a>" +
        "<TR><TD class=tdAbout>�����:<TD><INPUT type=text id=prihod_sum maxlength=8> ���." +
        "<TR><TD class=tdAbout>�����������:<TD><INPUT type=text id=prihod_txt maxlength=250>" +
        "<TR id=tr_kassa><TD class=tdAbout>������ ���������<br>� �����?:<TD><INPUT type=hidden id=prihod_kassa value='-1'>" +
        "</TABLE>";
    var dialog = $("#dialog_prihod").vkDialog({
        top:50,
        width:420,
        head:"�������� �������",
        content:html,
        submit:submit,
        focus:"#prihod_sum"
    }).o;

    $("#prihod_type").vkSel({
        width:190,
        display:'inline-block',
        title0:'�� ������',
        spisok:G.money_type_spisok,
        func:function (id) {
            $("#tr_kassa")[id == 1 ? 'show' : 'hide']();
            $("#prihod_kassa").val(-1);
            $("#prihod_kassa").vkRadio({
                display:'inline-block',
                right:15,
                spisok:[{uid:1, title:'��'},{uid:0, title:'���'}],
            });
        }
    });

    function submit() {
        var send = {
            zayav_id:obj.zayav_id,
            client_id:obj.client_id,
            type:$("#prihod_type").val(),
            txt:$("#prihod_txt").val(),
            sum:$("#prihod_sum").val(),
            kassa:$("#prihod_kassa").val()
        };

        var msg;
        if (send.type == 0) { msg = "�� ������ ��� �������."; }
        else if (!G.reg_sum.test(send.sum)) { msg = "����������� ������� �����."; $("#prihod_sum").focus(); }
        else if (!send.txt && send.zayav_id == 0 && send.client_id == 0) { msg = "������� �����������."; $("#prihod_txt").focus(); }
        else if (send.kassa == -1 && send.type == 1) { msg = "�������, ������ ��������� � ����� ��� ���."; }
        else {
            if (send.kassa == -1) send.kassa = 0;
            dialog.process();
            $.post("/view/gazeta/report/money/AjaxPrihodRashodAdd.php?" + G.values, send, function (res) {
                dialog.close();
                vkMsgOk("����� ������� �����.");
                obj.func();
            }, 'html');
        }
        if (msg) {
            $("#dialog_prihod .bottom:first").vkHint({
                msg:"<SPAN class=red>" + msg + "</SPAN>",
                remove:1,
                indent:40,
                show:1,
                top:-48,
                left:125
            });
        }
    }
} // end moneyAdd()

// �������� �������
function moneyDel(id) {
    $("#dialog_money").remove();
    $("BODY").append("<div id=dialog_money></div>");

    var dialog = $("#dialog_money").vkDialog({
        width:250,
        head:"�������� �������",
        butSubmit:"�������",
        content:"<CENTER><B>����������� �������� �������</B></CENTER>",
        submit:function () {
            dialog.process();
            $.getJSON("/view/gazeta/report/money/AjaxMoneyDel.php?" + G.values + "&id=" + id, function () {
                location.reload();
            }, 'json');
        }
    }).o;
} // end moneyDel()
*/