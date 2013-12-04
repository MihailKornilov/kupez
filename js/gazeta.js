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
				txt:'Введите данные клиента и нажмите Enter',
				func:clientSpisokLoad
			});
			//$('#buttonCreate').click(clientAdd);
			$('#order')._radio({
				light:1,
				spisok:[
					{uid:1,title:'По дате добавления'},
					{uid:2,title:'По активности'}
				],
				func:clientSpisokLoad

			});
			$('#order_radio').vkHint({
				width:210,
				msg:'<div style="text-align:justify">' +
						'<b>По дате добавления:</b><br> клиенты, добавленные последними, стоят в списке первыми.<br><br>' +
						'<b>По активности:</b><br> сортировка по дате выхода последней заявки клиента. ' +
						'Также показывается дополнительное поле "Активность", в котором содержится дата последнего выхода заявки.' +
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
				title0:'Категория не выбрана',
				func:clientSpisokLoad
			});
			$('#skidka').vkSel({
				width:150,
				spisok:SKIDKA_SPISOK,
				title0:'Скидка не выбрана',
				func:clientSpisokLoad
			});
			$('#dolg')._check(clientSpisokLoad);
			$('#dolg_check').vkHint({
				msg:'<b>Список должников.</b><br /><br />' +
					'Выводятся клиенты, у которых баланс менее 0. Также в результате отображается общая сумма долга.',
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
// Внесение платежа
function moneyAdd(obj) {
    $("#dialog_prihod").remove();
    $("BODY").append("<div id=dialog_prihod></div>");

    var obj = $.extend({
        zayav_id:0,
        client_id:0,
        func:function () { location.reload(); }
    }, obj);

    var html = "<TABLE cellpadding=0 cellspacing=10 id=prihod_add_tab>" +
        "<TR><TD class=tdAbout>Вид:<TD><INPUT type=hidden id=prihod_type><a class=img_edit href='" + G.url + "&p=gazeta&d=setup&id=11'></a>" +
        "<TR><TD class=tdAbout>Сумма:<TD><INPUT type=text id=prihod_sum maxlength=8> руб." +
        "<TR><TD class=tdAbout>Комментарий:<TD><INPUT type=text id=prihod_txt maxlength=250>" +
        "<TR id=tr_kassa><TD class=tdAbout>Деньги поступили<br>в кассу?:<TD><INPUT type=hidden id=prihod_kassa value='-1'>" +
        "</TABLE>";
    var dialog = $("#dialog_prihod").vkDialog({
        top:50,
        width:420,
        head:"Внесение платежа",
        content:html,
        submit:submit,
        focus:"#prihod_sum"
    }).o;

    $("#prihod_type").vkSel({
        width:190,
        display:'inline-block',
        title0:'Не указан',
        spisok:G.money_type_spisok,
        func:function (id) {
            $("#tr_kassa")[id == 1 ? 'show' : 'hide']();
            $("#prihod_kassa").val(-1);
            $("#prihod_kassa").vkRadio({
                display:'inline-block',
                right:15,
                spisok:[{uid:1, title:'да'},{uid:0, title:'нет'}],
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
        if (send.type == 0) { msg = "Не указан вид платежа."; }
        else if (!G.reg_sum.test(send.sum)) { msg = "Некорректно указана сумма."; $("#prihod_sum").focus(); }
        else if (!send.txt && send.zayav_id == 0 && send.client_id == 0) { msg = "Укажите комментарий."; $("#prihod_txt").focus(); }
        else if (send.kassa == -1 && send.type == 1) { msg = "Укажите, деньги поступили в кассу или нет."; }
        else {
            if (send.kassa == -1) send.kassa = 0;
            dialog.process();
            $.post("/view/gazeta/report/money/AjaxPrihodRashodAdd.php?" + G.values, send, function (res) {
                dialog.close();
                vkMsgOk("Платёж успешно внесён.");
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

// Удаление платежа
function moneyDel(id) {
    $("#dialog_money").remove();
    $("BODY").append("<div id=dialog_money></div>");

    var dialog = $("#dialog_money").vkDialog({
        width:250,
        head:"Удаление платежа",
        butSubmit:"Удалить",
        content:"<CENTER><B>Подтвердите удаление платежа</B></CENTER>",
        submit:function () {
            dialog.process();
            $.getJSON("/view/gazeta/report/money/AjaxMoneyDel.php?" + G.values + "&id=" + id, function () {
                location.reload();
            }, 'json');
        }
    }).o;
} // end moneyDel()
*/