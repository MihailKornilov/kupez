$(document)
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

	.on('click', '#setup_invoice .add', function() {
		var t = $(this),
			html = '<table class="setup-tab">' +
				'<tr><td class="label">Наименование:<td><input id="name" type="text" maxlength="50" />' +
				'<tr><td class="label topi">Описание:<td><textarea id="about"></textarea>' +
				'<tr><td class="label topi">Виды платежей:<td><input type="hidden" id="types" />' +
				'</table>',
			dialog = _dialog({
				width:400,
				head:'Добавление нового счёта',
				content:html,
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		$('#types')._select({
			width:218,
			multiselect:1,
			spisok:INCOME_SPISOK
		});
		function submit() {
			var send = {
				op:'setup_invoice_add',
				name:$('#name').val(),
				about:$('#about').val(),
				types:$('#types').val()
			};
			if(!send.name) {
				err('Не указано наименование');
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('.spisok').html(res.html);
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
				left:100,
				indent:50,
				show:1,
				remove:1
			});
		}
	})
	.on('click', '#setup_invoice .img_edit', function() {
		var t = $(this);
		while(t[0].tagName != 'TR')
			t = t.parent();
		var id = t.attr('val'),
			name = t.find('.name div').html(),
			about = t.find('.name pre').html(),
			types = t.find('.type_id').val(),
			html = '<table class="setup-tab">' +
				'<tr><td class="label r">Наименование:<td><input id="name" type="text" maxlength="100" value="' + name + '" />' +
				'<tr><td class="label r top">Описание:<td><textarea id="about">' + about + '</textarea>' +
				'<tr><td class="label topi">Виды платежей:<td><input type="hidden" id="types" value="' + types + '" />' +
				'</table>',
			dialog = _dialog({
				width:400,
				head:'Редактирование данных счёта',
				content:html,
				butSubmit:'Сохранить',
				submit:submit
			});
		$('#name').focus().keyEnter(submit);
		$('#types')._select({
			width:218,
			multiselect:1,
			spisok:INCOME_SPISOK
		});
		function submit() {
			var send = {
				op:'setup_invoice_edit',
				id:id,
				name:$('#name').val(),
				about:$('#about').val(),
				types:$('#types').val()
			};
			if(!send.name) {
				err('Не указано наименование');
				$('#name').focus();
			} else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						$('.spisok').html(res.html);
						dialog.close();
						_msg('Сохранено!');
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
				left:100,
				indent:50,
				show:1,
				remove:1
			});
		}
	})
	.on('click', '#setup_invoice .img_del', function() {
		var t = $(this),
			dialog = _dialog({
				top:90,
				width:300,
				head:'Удаление счёта',
				content:'<center><b>Подтвердите удаление счёта.</b></center>',
				butSubmit:'Удалить',
				submit:submit
			});
		function submit() {
			while(t[0].tagName != 'TR')
				t = t.parent();
			var send = {
				op:'setup_invoice_del',
				id:t.attr('val')
			};
			dialog.process();
			$.post(AJAX_GAZ, send, function(res) {
				if(res.success) {
					$('.spisok').html(res.html);
					dialog.close();
					_msg('Удалено!');
				} else
					dialog.abort();
			}, 'json');
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
	});
