var AJAX_GAZ = 'http://' + DOMAIN + '/ajax/gazeta.php?' + VALUES,
	clientAdd = function(callback) {
		var html = '<table class="client-add">' +
			'<tr><td class="label">Категория:<td><input type="hidden" id="cperson">' +
				'<a href="' + URL + '&p=gazeta&d=setup&d1=person" class="img_edit"></a>' +
			'<tr><td class="label">Контактное лицо (фио):<td><input type="text" id="fio" maxlength="200">' +
			'<tr><td class="label">Название организации:<td><input type="text" id="org_name" maxlength="200">' +
			'<tr><td class="label">Телефоны:<td><input type="text" id="telefon" maxlength="300">' +
			'<tr><td class="label">Адрес:<td><input type="text" id="adres" maxlength="200">' +
			'<tr><td class="label">ИНН:<td><input type="text" id="inn" maxlength="100">' +
			'<tr><td class="label">КПП:<td><input type="text" id="kpp" maxlength="100">' +
			'<tr><td class="label">E-mail:<td><input type="text" id="email" maxlength="100">' +
			'<tr><td class="label">Скидка:<td><input type="hidden" id="cskidka">' +
			'</table>',
			dialog = _dialog({
				top:40,
				width:440,
				head:'Добавление нoвого клиента',
				content:html,
				submit:submit
			});
		$('#cperson')._select({
			width:180,
			title0:'Не выбрана',
			spisok:PERSON_SPISOK
		});
		$('.client-add .img_edit:first').vkHint({
			msg:'Перейти к настройкам категорий клиентов',
			indent:110,
			top:-76,
			left:75
		});
		$('#cskidka')._select({
			width:60,
			title0:'Нет',
			spisok:SKIDKA_SPISOK
		});
		$('#fio').focus();
		$('#fio,#org_name,#telefon,#adres,#inn,#kpp,#email').keyEnter(submit);
		function submit() {
			var send = {
				op:'client_add',
				person:$('#cperson').val(),
				fio:$('#fio').val(),
				telefon:$('#telefon').val(),
				org_name:$('#org_name').val(),
				adres:$('#adres').val(),
				inn:$('#inn').val(),
				kpp:$('#kpp').val(),
				email:$('#email').val(),
				skidka:$('#cskidka').val()
			};
			if(send.person == 0) err('Не выбран заявитель', -48);
			else if(!send.fio && !send.org_name) err('Необходимо указать контактное лицо<br />либо название организации', -61);
			else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						dialog.close();
						_msg('Новый клиент внесён.');
						if(typeof callback == 'function')
							callback(res);
						else
							document.location.href = URL + '&p=gazeta&d=client&d1=info&id=' + res.uid;
					} else
						dialog.abort();
				}, 'json');
			}
		}
		function err(msg, top) {
			dialog.bottom.vkHint({
				msg:'<SPAN class="red">' + msg + '</SPAN>',
				top:top,
				left:131,
				indent:40,
				show:1,
				remove:1
			});
		}
	},
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
	},

	zayavFilter = function() {
		var v = {
			find:zFind.inp(),
			cat:$('#cat').val(),
			gnyear:$('#gnyear').val(),
			nomer:$('#nomer').val(),
			nopublic:$('#nopublic').val()
		};
		$('.filter')[v.find ? 'hide' : 'show']();
		return v;
	},
	zayavSpisokLoad = function(year) {
		var send = zayavFilter();
		send.op = 'zayav_spisok';
		if($('#mainLinks').hasClass('busy'))
			return;
		$('#mainLinks').addClass('busy');
		$.post(AJAX_GAZ, send, function (res) {
			$('#mainLinks').removeClass('busy');
			if(res.success) {
				$('.result').html(res.result);
				$('.left').html(res.spisok);
				if(year == 'change')
					$('#nomer')._select({
						width:147,
						title0:'Номер не указан',
						spisok:res.gn_sel,
						func:zayavSpisokLoad
					});
			}
		}, 'json');
	},
	zayavRubric = function() {
		$('#rubric')._select({
			width:120,
			title0:'Не указана',
			spisok:RUBRIC_SPISOK,
			func:function(id) {
				$('#rubric_sub').val(0)._select('remove');
				zayavRubricSub(id);
			}
		});
	},
	zayavRubricSub = function(id) {
		if(RUBRIC_SUB_SPISOK[id]) {
			$('#rubric_sub')._select({
				width:180,
				title0:'Подрубрика не указана',
				spisok:RUBRIC_SUB_SPISOK[id]
			});
		}
	},
	zayavObSumCalc = function() {// Вычисление стоимости объявления
		var txt_sum = 0, // сумма только за текст
			podr_about = '', // подробное расписывание длины объявления
			txt = $('#zatxt').val()
					.replace(/\./g, '')    // точки
					.replace(/,/g, '')     // запятые
					.replace(/\//g, '')    // слеш /
					.replace(/\"/g, '')    // двойные кавычки
					.replace(/( +)/g, ' ') // вторые пробелы
					.replace( /^\s+/g, '') // пробелы в начале
					.replace( /\s+$/g, '');// пробелы в конце
		if(!txt.length)
			$('#txt-count').html('');
		else {
			txt_sum += TXT_CENA_FIRST * 1;
			if(txt.length > TXT_LEN_FIRST) {
				podr_about = ' = ';
				var CEIL = Math.ceil((txt.length - TXT_LEN_FIRST) / TXT_LEN_NEXT);
				podr_about += TXT_LEN_FIRST;
				var LAST = txt.length - TXT_LEN_FIRST - (CEIL - 1) * TXT_LEN_NEXT;
				txt_sum += CEIL * TXT_CENA_NEXT;
				if(TXT_LEN_NEXT == LAST) CEIL++;
				if(CEIL > 1) podr_about += ' + ' + TXT_LEN_NEXT;
				if(CEIL > 2) podr_about += 'x' + (CEIL - 1);
				if(TXT_LEN_NEXT > LAST) podr_about += ' + ' + LAST;
			}
			var html = 'Длина: <b>' + txt.length + '</b>' + podr_about + '<br />' +
					   'Цена: <b>' + txt_sum + '</b> руб.<span>(без учёта доп. параметров)</span>';
			$('#txt-count').html(html);
		}
		//zayav.gn.cenaSet(txt_sum);
	};

$.fn.gnGet = function(o) {
	o = $.extend({
		show:4,  // количество номеров, которые показываются изначально, а также отступ от уже выбранных
		add:8,   // количество номеров, добавляющихся к показу
		category:1,
		gns:null,
		manual:null,
		summa:null,
		skidka:null,
		skidka_sum:0
	}, o);
	var t = $(this),
		pix = 21, // высота номера выпуска в пикселях
		gn_show_current = o.show, // Количество отображаемых номеров
		gns_begin = GN_FIRST_ACTIVE,
		gns_end = gns_begin + o.show,
		gnSel = [],
		html =
			'<div id="gnGet">' +
				'<table>' +
					'<tr><td><div id="dopLinks">' +
								'<a class="link" val="4">Месяц</a>' +
								'<a class="link" val="13">3 месяца</a>' +
								'<a class="link" val="26">Полгода</a>' +
								'<a class="link" val="52">Год</a>' +
							'</div>' +
						'<TD><input type="hidden" id="dopDef">' +
				'</table>' +
				'<table>' +
					'<tr><td id="selCount">' +
						'<td><div id="gns"></div>' +
				'</table>' +
			'</div>';
	t.html(html);

	$(document)
		.on('click', '#darr', function () {// Разворачивание списка
			gns_begin = gns_end;
			gns_end += o.add;
			gnsPrint();
		})
		.on('click', '.gns-week', function () {// Действие по нажатию на номер газеты
			dopMenuA.removeClass('sel');
			var sel = $(this).hasClass('gnsel');
			$(this)[(sel ? 'remove': 'add') + 'Class']('gnsel');
//		sel_calc();
//		cenaSet();
		});

	var gnGet = $('#gnGet'),                 // Основная форма
		gns = gnGet.find('#gns'),            // Список номеров
		dopMenuA = gnGet.find('#dopLinks A'), // Список меню с периодами
		selCount = gnGet.find('#selCount'),  // Количество выбранных номеров
		globalDop = 0,
		cena = 0;                            //

	gnsPrint();

	function gnsPrint() {// Вывод списка номеров
		gnGet.find('#darr').remove();
		var html = '',
			spisok_start = gns_begin == GN_FIRST_ACTIVE,//Начинать или дополнять список
			h_end = ((spisok_start ? 0 : gns.find('.gns-week').length * 1) + gns_end - gns_begin) * pix;
		for(var n = gns_begin; n < gns_end; n++) {
			if(n > GN_LAST_ACTIVE)
				break;
			var sp = GN[n];
			if(!sp) { // если номер пропущен, тогда не выводится
				end++;
				continue;
			}
			html +=
				'<table><tr>' +
					'<td><table class="gns-week' + (sp.sel == 1 ? ' gnsel' : '') + (sp.prev == 1 ? ' prev' : '') + '" val="' + n + '">' +
							'<tr><td class="td"><b>' + sp.week + '</b><span class="g">(' + n + ')</span>' +
								'<td class="td"><span class="g">выход</span> ' + sp.txt +
								'<td class="cena" id="cena' + n + '">' +
						'</table>' +
					'<td class="vdop"><input type="hidden" id="vdop' + n + '" value="' + sp.dop + '" />' +
				'</table>';
		}
		html += gns_end < GN_LAST_ACTIVE ? '<div id="darr">&darr; &darr; &darr;</div>' : '';
		gns[spisok_start ? 'html' : 'append'](html);
		gns.animate({height:h_end + 'px'}, 300);
		//gns.find('.tab').click(gn_set);
		//gn_action_active(linkMenu_create);
		//cenaSet();
	}
	dopMenuA.click(function () {// выбор номеров на месяц, 3 месяца, полгода и год начиная сначала
		var t = $(this),
			v = t.attr('val') * 1;
		if(t.hasClass('sel')) {
			v = 0;
			t.removeClass('sel');
		} else {
			dopMenuA.removeClass('sel');
			t.addClass('sel');
		}
		gns_begin = GN_FIRST_ACTIVE;
		gns_end = gns_begin + v + o.show;
		//gns_clear();
		gnsPrint();
		//sel_calc();
	});
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

	.on('click', '.zayav_next', function() {
		if($(this).hasClass('busy'))
			return;
		var next = $(this),
			send = zayavFilter();
		send.op = 'zayav_next',
		send.page = next.attr('val');
		next.addClass('busy');
		$.post(AJAX_GAZ, send, function(res) {
			if(res.success)
				next.after(res.html).remove();
			else
				next.removeClass('busy');
		}, 'json');
	})

	.on('click', '#history_next', function() {
		if($(this).hasClass('busy'))
			return;
		var next = $(this),
			send = {
				op:'history_next',
				page:next.attr('val')
			};
		next.addClass('busy');
		$.post(AJAX_GAZ, send, function(res) {
			if(res.success)
				next.after(res.html).remove();
			else
				next.removeClass('busy');
		}, 'json');
	})

	.on('click', '#setup_worker .add', function() {
		var html = '<div id="setup_worker_add">' +
				'<h1>Ссылка на страницу или ID пользователя ВКонтакте:</h1>' +
				'<input type="text" />' +
				'<DIV class="vkButton"><BUTTON>Найти</BUTTON></DIV>' +
				'</div>',
			dialog = _dialog({
				top:50,
				width:360,
				head:'Добавление нового сотрудника',
				content:html,
				butSubmit:'Добавить',
				submit:submit
			}),
			user_id,
			input = dialog.content.find('input'),
			but = input.next();
		input.focus().keyEnter(user_find);
		but.click(user_find);

		function user_find() {
			if(but.hasClass('busy'))
				return;
			user_id = false;
			var send = {
				user_ids:$.trim(input.val()),
				fields:'photo_50',
				v:5.2
			};
			if(!send.user_ids)
				return;
			but.addClass('busy').next('.res').remove();
			VK.api('users.get', send, function(data) {
				but.removeClass('busy');
				if(data.response) {
					var u = data.response[0],
						html = '<table class="res">' +
								'<TR><TD class="photo"><IMG src=' + u.photo_50 + '>' +
									'<TD class="name">' + u.first_name + ' ' + u.last_name +
							'</table>';
					but.after(html);
					user_id = u.id;
				}
			});
		}
		function submit() {
			if(!user_id) {
				err('Не выбран пользователь', -47);
				return;
			}
			var send = {
				op:'setup_worker_add',
				id:user_id
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				dialog.abort();
				if(res.success) {
					dialog.close();
					_msg('Новый сотрудник успешно добавлен.');
					$('#spisok').html(res.html);
				} else
					err(res.text, -60);
			}, 'json');
		}
		function err(msg, top) {
			dialog.bottom.vkHint({
				msg:'<SPAN class="red">' + msg + '</SPAN>',
				remove:1,
				indent:40,
				show:1,
				top:top,
				left:92
			});
		}
	})
	.on('click', '#setup_worker .img_del', function() {
		var u = $(this);
		while(!u.hasClass('unit'))
			u = u.parent();
		var dialog = _dialog({
			top:110,
			width:250,
			head:'Удаление сотрудника',
			content:'<center>Подтвердите удаление сотрудника.</center>',
			butSubmit:'Удалить',
			submit:submit
		});
		function submit() {
			var send = {
				op:'setup_worker_del',
				viewer_id:u.attr('val')
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					dialog.close();
					_msg('Сотрудник удален.');
					$('#spisok').html(res.html);
				} else
					dialog.abort();
			}, 'json');
		}
	})

	.on('click', '#setup_gn .link', function() {
		var t = $(this),
			send = {
				op:'setup_gn_spisok_get',
				year:t.html()
			};
		t.parent().find('.sel').removeClass('sel');
		t.addClass('sel');
		$.post(AJAX_GAZ, send, function(res) {
			if(res.success)
				$('#spisok').html(res.html);
		}, 'json');
	})
	.on('click', '#setup_gn .vkButton', function() {
		var t = $(this),
			year = $('#dopLinks .sel').html(),
			html = '<table class="setup-gn-tab">' +
				'<tr><td colspan="2">' +
					'<div class="gn-info">' +
						'Для создания списка номеров газет <b>' + year + '</b> года ' +
						'укажите данные <b>первого номера</b>, который будет выходить в этом году.<br />' +
						'Все поля обязательны для заполнения.' +
					'</div>' +
				'<tr><td class="label">Первый номер выпуска:' +
					'<td><input type="text" id="week_nomer" maxlength="2" value="1" />' +
						'<input type="text" id="general_nomer" maxlength="4" value="' + GN_MAX + '" />' +
				'<tr><td class="label">Дни отправки в печать:<td><input type="hidden" id="day_print" value="1" />' +
				'<tr><td class="label">Дни выхода:<td><input type="hidden" id="day_public" value="4" />' +
				'<tr><td class="label">Первый день выхода:<td><input type="hidden" id="day_first" value="' + year + '-01-01" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:310,
				head:'Создание списка номеров газеты',
				content:html,
				butSubmit:'Создать',
				submit:submit
			}),
			weeks = [
				{uid:0,title:'Понедельник'},
				{uid:1,title:'Вторник'},
				{uid:2,title:'Среда'},
				{uid:3,title:'Четверг'},
				{uid:4,title:'Пятница'},
				{uid:5,title:'Суббота'},
				{uid:6,title:'Воскресенье'}
			];
		$('#week_nomer').focus();
		$('#week_nomer,#general_nomer').keyEnter(submit);
		$('#day_print')._select({width:100, spisok:weeks});
		$('#day_public')._select({width:100, spisok:weeks});
		$('#day_first')._calendar({lost:1});
		function submit() {
			var send = {
				op:'setup_gn_spisok_create',
				year:year,
				week_nomer:$('#week_nomer').val(),
				general_nomer:$('#general_nomer').val(),
				day_print:$('#day_print').val(),
				day_public:$('#day_public').val(),
				day_first:$('#day_first').val()
			};
			if(!REGEXP_NUMERIC.test(send.week_nomer)) {
				err('Некорректно указан номер недели выпуска');
				$('#week_nomer').focus();
			} else if(!REGEXP_NUMERIC.test(send.general_nomer)) {
				err('Некорректно указан общий номер выпуска');
				$('#general_nomer').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#dopLinks').html(res.year);
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
					} else {
						dialog.abort();
						err(res.text);
					}
				}, 'json');
			}
		}
		function err(msg) {
			dialog.bottom.vkHint({
				msg:'<SPAN class=red>' + msg + '</SPAN>',
				top:-47,
				left:58,
				indent:50,
				show:1,
				remove:1
			});
		}
	})
	.on('click', '#setup_gn .add', function() {
		var t = $(this),
			html = '<table class="setup-gn-tab">' +
				'<tr><td class="label r">Номер выпуска:' +
					'<td><input type="text" id="week_nomer" maxlength="2" />' +
						'<input type="text" id="general_nomer" maxlength="4" />' +
				'<tr><td class="label r">День отправки в печать:<td><input type="hidden" id="day_print" />' +
				'<tr><td class="label r">День выхода:<td><input type="hidden" id="day_public" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:310,
				head:'Добавление номера газеты',
				content:html,
				submit:submit
			});
		$('#week_nomer').focus();
		$('#week_nomer,#general_nomer').keyEnter(submit);
		$('#day_print')._calendar({lost:1});
		$('#day_public')._calendar({lost:1});
		function submit() {
			var send = {
				op:'setup_gn_add',
				week_nomer:$('#week_nomer').val(),
				general_nomer:$('#general_nomer').val(),
				day_print:$('#day_print').val(),
				day_public:$('#day_public').val(),
				year:$('#dopLinks .sel').html()
			};
			if(!REGEXP_NUMERIC.test(send.week_nomer)) {
				err('Некорректно указан номер недели выпуска');
				$('#week_nomer').focus();
			} else if(!REGEXP_NUMERIC.test(send.general_nomer)) {
				err('Некорректно указан общий номер выпуска');
				$('#general_nomer').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#dopLinks').html(res.year);
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
					} else {
						dialog.abort();
						err(res.text);
					}
				}, 'json');
			}
		}
		function err(msg) {
			dialog.bottom.vkHint({
				msg:'<SPAN class=red>' + msg + '</SPAN>',
				top:-47,
				left:58,
				indent:50,
				show:1,
				remove:1
			});
		}
	})
	.on('click', '#setup_gn .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'TR')
			t = t.parent();
		var week = t.find('.nomer b').html(),
			general = t.find('.nomer span').html(),
			print = t.find('.print s').html(),
			pub = t.find('.pub s').html(),
			html = '<table class="setup-gn-tab">' +
				'<tr><td class="label r">Номер выпуска:' +
				'<td><input type="text" id="week_nomer" maxlength="2" value="' + week + '" />' +
				'<input type="text" id="general_nomer" maxlength="4"  value="' + general + '" />' +
				'<tr><td class="label r">День отправки в печать:<td><input type="hidden" id="day_print" value="' + print + '" />' +
				'<tr><td class="label r">День выхода:<td><input type="hidden" id="day_public" value="' + pub + '" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:310,
				head:'Редактирование номера газеты',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#week_nomer').focus();
		$('#week_nomer,#general_nomer').keyEnter(submit);
		$('#day_print')._calendar({lost:1});
		$('#day_public')._calendar({lost:1});
		function submit() {
			var send = {
				op:'setup_gn_edit',
				gn:general,
				week_nomer:$('#week_nomer').val(),
				general_nomer:$('#general_nomer').val(),
				day_print:$('#day_print').val(),
				day_public:$('#day_public').val(),
				year:$('#dopLinks .sel').html()
			};
			if(!REGEXP_NUMERIC.test(send.week_nomer)) {
				err('Некорректно указан номер недели выпуска');
				$('#week_nomer').focus();
			} else if(!REGEXP_NUMERIC.test(send.general_nomer)) {
				err('Некорректно указан общий номер выпуска');
				$('#general_nomer').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#dopLinks').html(res.year);
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Изменено!');
					} else {
						dialog.abort();
						err(res.text);
					}
				}, 'json');
			}
		}
		function err(msg) {
			dialog.bottom.vkHint({
				msg:'<SPAN class=red>' + msg + '</SPAN>',
				top:-47,
				left:58,
				indent:50,
				show:1,
				remove:1
			});
		}
	})
	.on('click', '#setup_gn .img_del', function() {
		var t = $(this);
		while(t[0].tagName != 'TR')
			t = t.parent();
		var dialog = _dialog({
			top:90,
			width:300,
			head:'Удаление номера газеты',
			content:'<center>Подтвердите удаление номера газеты ' + t.find('.nomer').html() + '.</center>',
			butSubmit:'Удалить',
			submit:submit
		});
		function submit() {
			var send = {
				op:'setup_gn_del',
				general:t.find('.nomer span').html(),
				year:$('#dopLinks .sel').html()
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					$('#dopLinks').html(res.year);
					$('#spisok').html(res.html);
					dialog.close();
					_msg('Удалено!');
				} else
					dialog.abort();
			}, 'json');
		}
	})

	.on('click', '#setup_person .add', function() {
		var t = $(this),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Внесение новой категории',
				content:html,
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_person_add',
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_person .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'DD')
			t = t.parent();
		var id = t.attr('val'),
			name = t.find('.name').html(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" value="' + name + '" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Редактирование категории клиентов',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_person_edit',
				id:id,
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_person .img_del', function() {
		var t = $(this),
			dialog = _dialog({
				top:90,
				width:300,
				head:'Удаление категории клиента',
				content:'<center><b>Подтвердите удаление категории клиента.</b></center>',
				butSubmit:'Удалить',
				submit:submit
			});
		function submit() {
			while(t[0].tagName != 'DD')
				t = t.parent();
			var send = {
				op:'setup_person_del',
				id:t.attr('val')
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					$('#spisok').html(res.html);
					dialog.close();
					_msg('Удалено!');
					sortable();
				} else
					dialog.abort();
			}, 'json');
		}
	})

	.on('click', '#setup_rubric .add', function() {
		var t = $(this),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Внесение новой рубрики',
				content:html,
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_rubric_add',
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_rubric .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'DD')
			t = t.parent();
		var id = t.attr('val'),
			name = t.find('.name a').html(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" value="' + name + '" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Редактирование рубрики',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_rubric_edit',
				id:id,
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_rubric .img_del', function() {
		var t = $(this),
			dialog = _dialog({
				top:90,
				width:300,
				head:'Удаление рубрики',
				content:'<center><b>Подтвердите удаление рубрики.</b></center>',
				butSubmit:'Удалить',
				submit:submit
			});
		function submit() {
			while(t[0].tagName != 'DD')
				t = t.parent();
			var send = {
				op:'setup_rubric_del',
				id:t.attr('val')
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					$('#spisok').html(res.html);
					dialog.close();
					_msg('Удалено!');
					sortable();
				} else
					dialog.abort();
			}, 'json');
		}
	})

	.on('click', '#setup_rubric_sub .add', function() {
		var t = $(this),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Внесение новой подрубрики',
				content:html,
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_rubric_sub_add',
				rubric_id:RUBRIC_ID,
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_rubric_sub .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'DD')
			t = t.parent();
		var id = t.attr('val'),
			name = t.find('.name').html(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" value="' + name + '" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Редактирование подрубрики',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_rubric_sub_edit',
				id:id,
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_rubric_sub .img_del', function() {
		var t = $(this),
			dialog = _dialog({
				top:90,
				width:300,
				head:'Удаление подрубрики',
				content:'<center><b>Подтвердите удаление подрубрики.</b></center>',
				butSubmit:'Удалить',
				submit:submit
			});
		function submit() {
			while(t[0].tagName != 'DD')
				t = t.parent();
			var send = {
				op:'setup_rubric_sub_del',
				id:t.attr('val')
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					$('#spisok').html(res.html);
					dialog.close();
					_msg('Удалено!');
					sortable();
				} else
					dialog.abort();
			}, 'json');
		}
	})

	.on('click', '#setup_oblen .vkButton', function() {
		var t = $(this),
			send = {
				op:'setup_oblen',
				txt_len_first:$('#txt_len_first').val(),
				txt_cena_first:$('#txt_cena_first').val(),
				txt_len_next:$('#txt_len_next').val(),
				txt_cena_next:$('#txt_cena_next').val()
			};

		if(!REGEXP_NUMERIC.test(send.txt_len_first)) {
			err(-2, 98);
			$('#txt_len_first').focus();
		} else if(!REGEXP_NUMERIC.test(send.txt_cena_first)) {
			err(-2, 191);
			$('#txt_cena_first').focus();
		} else if(!REGEXP_NUMERIC.test(send.txt_len_next)) {
			err(25, 98);
			$('#txt_len_next').focus();
		} else if(!REGEXP_NUMERIC.test(send.txt_cena_next)) {
			err(25, 191);
			$('#txt_cena_next').focus();
		} else {
			t.addClass('busy');
			$.post(AJAX_GAZ, send, function(res) {
				t.removeClass('busy');
				if(res.success)
					_msg('Сохранено!');
			}, 'json');
		}
		function err(top, left) {
			$('#setup_oblen').vkHint({
				msg:'<SPAN class=red>Некорректный ввод</SPAN>',
				top:top,
				left:left,
				indent:50,
				show:1,
				remove:1
			});
		}
	})

	.on('click', '#setup_obdop .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'TR')
			t = t.parent();
		var id = t.attr('val'),
			name = t.find('.name').html(),
			cena = t.find('.cena').html(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><b>' + name + '</b>' +
				'<tr><td class="label">Стоимость:<td><input id="cena" type="text" maxlength="6" value="' + cena + '" /> руб.' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Редактирование параметра',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#cena').keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_obdop_edit',
				id:id,
				cena:$('#cena').val()
			};
			if(!REGEXP_NUMERIC.test(send.cena) || send.cena == 0) {
				err('Некорректно указана цена');
				$('#cena').focus();
			} else{
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
					} else
						dialog.abort();
				}, 'json');
			}
		}
		function err(msg) {
			dialog.bottom.vkHint({
				msg:'<SPAN class=red>' + msg + '</SPAN>',
				top:-47,
				left:99,
				indent:50,
				show:1,
				remove:1
			});
		}
	})

	.on('click', '#setup_polosa .add', function() {
		var t = $(this),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" />' +
				'<tr><td class="label">Цена за см&sup2;:<td><input id="cena" type="text" maxlength="6" /> руб.' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Внесение новой полосы',
				content:html,
				submit:submit
			});
		$('#name').focus();
		$('#name,#cena').keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_polosa_add',
				name:$('#name').val(),
				cena:$('#cena').val()
			};
			if(!send.name) {
				err('Не указано наименование');
				$('#name').focus();
			} else if(!REGEXP_CENA.test(send.cena)) {
				err('Некорректно указана цена');
				$('#cena').focus();
			} else{
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
		function err(msg) {
			dialog.bottom.vkHint({
				msg:'<SPAN class=red>' + msg + '</SPAN>',
				top:-47,
				left:99,
				indent:50,
				show:1,
				remove:1
			});
		}
	})
	.on('click', '#setup_polosa .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'DD')
			t = t.parent();
		var id = t.attr('val'),
			name = t.find('.name').html(),
			cena = t.find('.cena').html(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" value="' + name + '" />' +
				'<tr><td class="label">Цена за см&sup2;:<td><input id="cena" type="text" maxlength="6" value="' + cena + '" /> руб.' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Редактирование данных полосы',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#name').focus();
		$('#name,#cena').keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_polosa_edit',
				id:id,
				name:$('#name').val(),
				cena:$('#cena').val()
			};
			if(!send.name) {
				err('Не указано наименование');
				$('#name').focus();
			} else if(!REGEXP_CENA.test(send.cena)) {
				err('Некорректно указана цена');
				$('#cena').focus();
			} else{
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
		function err(msg) {
			dialog.bottom.vkHint({
				msg:'<SPAN class=red>' + msg + '</SPAN>',
				top:-47,
				left:99,
				indent:50,
				show:1,
				remove:1
			});
		}
	})

	.on('click', '#setup_money .add', function() {
		var t = $(this),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="100" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Внесение новой вида платежа',
				content:html,
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_money_add',
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_money .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'DD')
			t = t.parent();
		var id = t.attr('val'),
			name = t.find('.name').html(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="100" value="' + name + '" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Редактирование вида платежа',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_money_edit',
				id:id,
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_money .img_del', function() {
		var t = $(this),
			dialog = _dialog({
				top:90,
				width:300,
				head:'Удаление вида платежа',
				content:'<center><b>Подтвердите удаление вида платежа.</b></center>',
				butSubmit:'Удалить',
				submit:submit
			});
		function submit() {
			while(t[0].tagName != 'DD')
				t = t.parent();
			var send = {
				op:'setup_money_del',
				id:t.attr('val')
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					$('#spisok').html(res.html);
					dialog.close();
					_msg('Удалено!');
					sortable();
				} else
					dialog.abort();
			}, 'json');
		}
	})

	.on('click', '#setup_skidka .add', function() {
		var t = $(this),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Размер:<td><input id="razmer" type="text" maxlength="3" /> %' +
				'<tr><td class="label">Описание:<td><input id="about" type="text" maxlength="200" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Внесение новой скидки',
				content:html,
				submit:submit
			});
		$('#razmer').focus();
		$('#razmer,#about').keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_skidka_add',
				razmer:$('#razmer').val(),
				about:$('#about').val()
			};
			if(!REGEXP_NUMERIC.test(send.razmer) || send.razmer == 0 || send.razmer > 100) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Некорректно указан размер скидки</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#razmer').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_skidka .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'TR')
			t = t.parent();
		var razmer = t.find('.razmer b').html(),
			about = t.find('.about').html(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Размер:<td><b>' + razmer + '</b>%' +
				'<tr><td class="label">Описание:<td><input id="about" type="text" maxlength="200" value="' + about + '" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Редактирование скидки',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#razmer').focus();
		$('#razmer,#about').keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_skidka_edit',
				razmer:razmer,
				about:$('#about').val()
			};
			if(!REGEXP_NUMERIC.test(send.razmer) || send.razmer == 0 || send.razmer > 100) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Некорректно указан размер скидки</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#razmer').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_skidka .img_del', function() {
		var t = $(this),
			dialog = _dialog({
				top:90,
				width:300,
				head:'Удаление скидки',
				content:'<center><b>Подтвердите удаление скидки.</b></center>',
				butSubmit:'Удалить',
				submit:submit
			});
		function submit() {
			while(t[0].tagName != 'TR')
				t = t.parent();
			var send = {
				op:'setup_skidka_del',
				razmer:t.find('.razmer b').html()
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					$('#spisok').html(res.html);
					dialog.close();
					_msg('Удалено!');
				} else
					dialog.abort();
			}, 'json');
		}
	})

	.on('click', '#setup_rashod .add', function() {
		var t = $(this),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="200" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Внесение новой категории расхода',
				content:html,
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_rashod_add',
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Внесено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_rashod .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'DD')
			t = t.parent();
		var id = t.attr('val'),
			name = t.find('.name').html(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="200" value="' + name + '" />' +
				'</table>',
			dialog = _dialog({
				top:60,
				width:390,
				head:'Редактирование категории расхода',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		function submit() {
			var send = {
				op:'setup_rashod_edit',
				id:id,
				name:$('#name').val()
			};
			if(!send.name) {
				dialog.bottom.vkHint({
					msg:'<SPAN class=red>Не указано наименование</SPAN>',
					top:-47,
					left:99,
					indent:50,
					show:1,
					remove:1
				});
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('#spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
						sortable();
					} else
						dialog.abort();
				}, 'json');
			}
		}
	})
	.on('click', '#setup_rashod .img_del', function() {
		var t = $(this),
			dialog = _dialog({
				top:90,
				width:300,
				head:'Удаление категории расхода',
				content:'<center><b>Подтвердите удаление категории расхода.</b></center>',
				butSubmit:'Удалить',
				submit:submit
			});
		function submit() {
			while(t[0].tagName != 'DD')
				t = t.parent();
			var send = {
				op:'setup_rashod_del',
				id:t.attr('val')
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					$('#spisok').html(res.html);
					dialog.close();
					_msg('Удалено!');
					sortable();
				} else
					dialog.abort();
			}, 'json');
		}
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
			$('#buttonCreate').click(clientAdd);
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
			$('#person')._select({
				spisok:PERSON_SPISOK,
				title0:'Категория не выбрана',
				func:clientSpisokLoad
			});
			$('#skidka')._select({
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
		if($('#clientInfo').length > 0) {
			$('.cedit').click(function() {
				var html = '<table class="client-add">' +
						'<tr><td class="label">Категория:<td><input type="hidden" id="cperson" value="' + CLIENT.person + '" />' +
							'<a href="' + URL + '&p=gazeta&d=setup&d1=person" class="img_edit"></a>' +
						'<tr><td class="label">Контактное лицо (фио):<td><input type="text" id="fio" maxlength="200" value="' + CLIENT.fio + '" />' +
						'<tr><td class="label">Название организации:<td><input type="text" id="org_name" maxlength="200" value="' + CLIENT.org_name + '" />' +
						'<tr><td class="label">Телефоны:<td><input type="text" id="telefon" maxlength="300" value="' + CLIENT.telefon + '" />' +
						'<tr><td class="label">Адрес:<td><input type="text" id="adres" maxlength="200" value="' + CLIENT.adres + '" />' +
						'<tr><td class="label">ИНН:<td><input type="text" id="inn" maxlength="100" value="' + CLIENT.inn + '" />' +
						'<tr><td class="label">КПП:<td><input type="text" id="kpp" maxlength="100" value="' + CLIENT.kpp + '" />' +
						'<tr><td class="label">E-mail:<td><input type="text" id="email" maxlength="100" value="' + CLIENT.email + '" />' +
						'<tr><td class="label">Скидка:<td><input type="hidden" id="cskidka" value="' + CLIENT.skidka + '" />' +
						'</table>',
					dialog = _dialog({
						top:40,
						width:440,
						head:'Редактирование данных клиента',
						content:html,
						submit:submit
					});
				$('#cperson')._select({
					width:180,
					spisok:PERSON_SPISOK
				});
				$('.client-add .img_edit:first').vkHint({
					msg:"Перейти к настройкам заявителей",
					indent:110,
					top:-76,
					left:75
				});
				$('#cskidka')._select({
					width:60,
					title0:'Нет',
					spisok:SKIDKA_SPISOK
				});
				$('#fio').focus();
				$('#fio,#org_name,#telefon,#adres,#inn,#kpp,#email').keyEnter(submit);
				function submit() {
					var send = {
						op:'client_edit',
						id:CLIENT.id,
						person:$('#cperson').val(),
						fio:$('#fio').val(),
						telefon:$('#telefon').val(),
						org_name:$('#org_name').val(),
						adres:$('#adres').val(),
						inn:$('#inn').val(),
						kpp:$('#kpp').val(),
						email:$('#email').val(),
						skidka:$('#cskidka').val()
					};
					if(!send.fio && !send.org_name) err('Необходимо указать контактное лицо<br />либо название организации');
					else {
						dialog.process();
						$.post(AJAX_GAZ, send, function(res) {
							if(res.success) {
								CLIENT = res;
								$('#clientInfo .left:first').html(res.html);
								dialog.close();
								_msg('Данные изменены.');

							} else
								dialog.abort();
						}, 'json');
					}
				}
				function err(msg) {
					dialog.bottom.vkHint({
						msg:'<SPAN class="red">' + msg + '</SPAN>',
						top:-61,
						left:131,
						indent:40,
						show:1,
						remove:1
					});
				}
			});
			$('.cdel').click(function() {
				var dialog = _dialog({
					top:90,
					width:300,
					head:'Удаление клиента',
					content:'<center>Внимание!<br />Будут удалены все данные о клиенте,<br />его заявки, и платежи.<br /><b>Подтвердите удаление.</b></center>',
					butSubmit:'Удалить',
					submit:submit
				});
				function submit() {
					var send = {
						op:'client_del',
						id:CLIENT.id
					};
					dialog.process();
					$.post(AJAX_GAZ, send, function(res) {
						if(res.success) {
							dialog.close();
							_msg('Клиент удален.');
							location.href = URL + '&p=gazeta&d=client';
						} else
							dialog.abort();
					}, 'json');
				}
			});

		}

		if($('#zayav').length > 0) {
			window.zFind = $('#find')
				.vkHint({
					width:145,
					msg:'<div style="text-align:justify">' +
						'Введите значение и нажмите <b>Enter</b>. ' +
						'Поиск производится по всем объявлениям, ' +
						'то есть другие параметры не учитываются. ' +
						'Если указано число и оно совпадает с номером ' +
						'заявки, то эта заявка выводится первая в списке.<div>',
					ugol:'right',
					indent:10,
					top:-1,
					left:-182,
					delayShow:1500
				})
				._search({
					width:148,
					focus:1,
					enter:1,
					txt:'Быстрый поиск..',
					func:zayavSpisokLoad
				});
			$('#cat').rightLink(zayavSpisokLoad);
			$('.img_word')
				.click(function () {
					var gn = $('#nomer').val();
					if(gn == 0)
						$(this).vkHint({
							width:150,
							ugol:'right',
							msg:'<span class=red>Не выбран номер газеты.</span>',
							indent:5,
							top:-26,
							left:-198,
							show:1,
							remove:1
						});
					else
						location.href = 'http://' + DOMAIN + '/view/ob-word.php?' + VALUES + "&gn=" + gn;
					return false;
				})
				.vkHint({
					ugol:'right',
					width:145,
					msg:'<span style="color:#444">' +
							'Открыть список объявлений ' +
							'в газетном варианте в формате ' +
							'Microsoft Word.' +
						'</span>',
					indent:10,
					top:-30,
					left:-193
				});
			$('#nopublic')._check(function() {
				$('.filter_nomer').toggle();
				zayavSpisokLoad();
			});
			$('#nopublic_check').vkHint({
				width:145,
				msg:'Заявки, которые не публиковались ни в одном номере газеты.',
				indent:60,
				top:-64,
				left:-61
			});
			$('#gnyear').years({
				func:function() {
					$('#nomer').val(0);
					zayavSpisokLoad('change');
				}
			});
			$('#nomer')._select({
				title0:'Номер не указан',
				spisok:GN_SEL,
				func:zayavSpisokLoad
			});
		}
		if($('#zayav-add').length > 0) {
			$('#category')._select({
				width:120,
				spisok:CATEGORY_SPISOK,
				func:function(category_id) {
					$('.ob').addClass('dn');
					$('#rubric').val(0)._select('remove');
					$('#rubric_sub').val(0)._select('remove');
					$('#zatxt').val('');
					zayavObSumCalc();
					$('#telefon').val('');
					$('#adres').val('');

					$('.rek').addClass('dn');
					$('#size_x').val('');
					$('#size_y').val('');
					$('#kv_sm').val('');

					switch(category_id) {
						case '1':
							$('.ob').removeClass('dn');
							zayavRubric();
							break;
						case '2':
							$('.rek').removeClass('dn');
							break;
						default:;
					}
				}
			});
			zayavRubric();
			$('#zatxt')
				.autosize()
				.focus()
				.keyup(zayavObSumCalc);
			$('#gn_spisok').gnGet();
		}
		if($('#zayav-info').length > 0) {
			$('#lost-count').click(function() {
				$(this).parent().find('.lost').show()
				$(this).remove();
			});
			$('.zinfo').click(function() {
				$(this).parent().find('.sel').removeClass('sel');
				$(this).addClass('sel');
				$('#zayav-info').removeClass('h');
			});
			$('.hist').click(function() {
				$(this).parent().find('.sel').removeClass('sel');
				$(this).addClass('sel');
				$('#zayav-info').addClass('h');
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

    var html = "<table cellpadding=0 cellspacing=10 id=prihod_add_tab>" +
        "<TR><TD class=tdAbout>Вид:<TD><INPUT type=hidden id=prihod_type><a class=img_edit href='" + G.url + "&p=gazeta&d=setup&id=11'></a>" +
        "<TR><TD class=tdAbout>Сумма:<TD><INPUT type=text id=prihod_sum maxlength=8> руб." +
        "<TR><TD class=tdAbout>Комментарий:<TD><INPUT type=text id=prihod_txt maxlength=250>" +
        "<TR id=tr_kassa><TD class=tdAbout>Деньги поступили<br>в кассу?:<TD><INPUT type=hidden id=prihod_kassa value='-1'>" +
        "</table>";
    var dialog = $("#dialog_prihod").vkDialog({
        top:50,
        width:420,
        head:"Внесение платежа",
        content:html,
        submit:submit,
        focus:"#prihod_sum"
    }).o;

    $("#prihod_type")._select({
        width:190,
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