var hashLoc,
	hashSet = function(hash) {
		if(!hash && !hash.p)
			return;
		hashLoc = hash.p;
		var s = true;
		switch(hash.p) {
			case 'client':
				if(hash.d == 'info')
					hashLoc += '_' + hash.id;
				break;
			case 'zayav':
				if(hash.d == 'info')
					hashLoc += '_' + hash.id;
				else if(hash.d == 'add')
					hashLoc += '_add' + (REGEXP_NUMERIC.test(hash.id) ? '_' + hash.id : '');
				else if(!hash.d)
					s = false;
				break;
			default:
				if(hash.d) {
					hashLoc += '_' + hash.d;
					if(hash.d1)
						hashLoc += '_' + hash.d1;
				}
		}
		if(s)
			VK.callMethod('setLocation', hashLoc);
	},

	obFilter = function(find) {
		return {
			op:'ob_spisok',
			find:$('#find')._search('val'),
			find_query:find ? 1 : 0,
			country_id:$('#countries').val(),
			city_id:$('#cities').val(),
			rubric_id:$('#rub').val(),
			rubric_sub_id:$('#rubsub').val(),
			withfoto:$('#withfoto').val(),
			nokupez:$('#nokupez').length ? $('#nokupez').val() : 0
		};
	},
	obSpisok = function(v, attr_id) {
		if($('.region').hasClass('_busy'))
			return;
		$('.region').addClass('_busy');
		$.post(AJAX_MAIN, obFilter(attr_id == 'find'), function (res) {
			$('.region').removeClass('_busy');
			if(res.success) {
				$('.result').html(res.result);
				$('.left').html(res.spisok);
				if(VK_SCROLL > 145)
					VK.callMethod('scrollWindow', 42);
			}
		}, 'json');
	},
	obPreview = function() {
		if(!$('#ob-create').length)
			return;
		var img_id = 0,
			img_url = '',
			iview = $('._image-spisok ._iview');
		if(iview.length) {
			img_id = iview.eq(0).attr('val');
			img_url = iview.eq(0).find('img').attr('src');
		}
		var txt = $('#txt').val().replace(/\n/g, '<br />'),
			rubric_id = $('#rubric_id').val() * 1,
			rubric_sub_id = $('#rubric_sub_id').val() * 1,
			html =
				'<div class="ob-unit">' +
					'<table class="utab">' +
						'<tr><td class="txt">' +
			   (rubric_id ? '<span class="rub">' + RUBRIC_ASS[rubric_id] + '</span><u>»</u>' : '') +
		   (rubric_sub_id ? '<span class="rubsub">' + RUBRIC_SUB_ASS[rubric_sub_id] + '</span><u>»</u>' : '') +
							txt +
	 ($('#telefon').val() ? '<div class="tel">' + $('#telefon').val() + '</div>' : '') +
			  (img_id ? '<td class="foto"><img src="' + img_url + '" class="_iview" val="' + img_id + '" />' : '') +
					'<tr><td class="adres" colspan="2">' +
						($('#country_id').val() > 0 ? $('#country_id')._select('title') : '') +
						($('#city_id').val() > 0 ? ', ' + $('#city_id')._select('title') : '') +
						($('#viewer_id_show').val() == 1 ? VIEWER_LINK  : '') +
					'</table>' +
				'</div>';
		$('#preview').html(html);
	},

	obMySpisok = function(v, attr_id) {
		if($('.result').hasClass('_busy'))
			return;
		OBMY[attr_id] = v;
		$('.result').addClass('_busy');
		$.post(AJAX_MAIN, OBMY, function (res) {
			$('.result').removeClass('_busy');
			if(res.success) {
				$('.result').html(res.result);
				$('.left').html(res.spisok);
			}
		}, 'json');
	},

	cityGet = function(val, city_id, city_name) {
		if($('#country_id').val() == 0)
			return;
		if(!val)
			val = '';
		if(city_id == undefined || city_id == '0')
			city_id = 0;
		$('#city_id')._select('process');
		VK.api('places.getCities',{country:$('#country_id').val(), q:val}, function(data) {
			var insert = 1; // Вставка города при редактировании, если отсутствует в списке
			for(var n = 0; n < data.response.length; n++) {
				var sp = data.response[n];
				sp.uid = sp.cid;
				sp.content = sp.title + (sp.area ? '<span>' + sp.area + '</span>' : '');
				if(city_id == sp.uid)
					insert = 0;
			}
			if(city_id && insert)
				data.response.unshift({uid:city_id,title:city_name});
			if(val.length == 0)
				data.response[0].content = '<B>' + data.response[0].title + '</B>';
			$('#city_id')._select(data.response);
			if(city_id)
				$('#city_id')._select(city_id);
		});
	},
	cityShow = function() {
		if($('#country_id').val() == 0) {
			$('#city_id')._select('remove');
			return;
		}
		if($('#city_id_select').length)
			return;
		$('#city_id')._select({
			width:180,
			block:1,
			title0:'Город не указан',
			spisok:[],
			write:1,
			func:obPreview,
			funcKeyup:cityGet
		});
		$('#city_id_select').vkHint({
			width:180,
			msg:'<div style="text-align:justify">' +
					'Обязательно указывайте город, ' +
					'если Ваше объявление ориентировано только на него, ' +
					'иначе объявление будет отображаться только в общем списке.' +
				'</div>',
			ugol:'left',
			top:-12,
			left:211,
			indent:15
		});
	},
	wallTest = function(o) {
		VK.api('account.getAppPermissions', {}, function(data) {
			if(typeof data.response == 'number') {
				if(data.response & 8192)
					wallPost(o);
				else {
					VK.addCallback('onSettingsChanged', function(data) {
						if(data & 8192) {
							VK.removeCallback('onWindowFocus');
							wallPost(o);
						}
					});
					VK.addCallback('onWindowFocus', function() {
						location.href = URL + '&p=ob';
					});
					VK.callMethod('showSettingsBox', 8192);
				}
			} else
				location.href = URL + '&p=ob';
		});
	},
	wallPost = function(o) {
		var send = {
			message:o.msg,
			attachments:(o.photo ? o.photo + ',' : '') + 'http://vk.com/kupezz'
		};
		VK.api('wall.post', send, function(data) {
			if(data.response)
				_msg('Объявление размещено на стене Вашей страницы.', function() {
					location.href = URL + '&p=ob&wallpost=1&insert_id=' + o.insert_id;
				});
			else
				location.href = URL + '&p=ob&wallpost=0&insert_id=' + o.insert_id;
		});
	},
	_post = function(o) {
		o = $.extend({
			viewer_id:0,
			photo:'',
			name:'',
			dtime:'Дата и время',
			cont:'Содержание',
			sa_gazeta_id:0,
			sa_viewer_id:0,
			sa_name:''
		}, o);

		var html =
			'<div id="_post">' +
				'<div class="head">' +
					'<table>' +
						'<tr>' +
			 (o.viewer_id ? '<td class="im"><a href="//vk.com/id' + o.viewer_id + '" target="_blank">' + o.photo + '</a>' : '') +
							'<td>' +
								'<a class="close">Закрыть</a>' +
				 (o.viewer_id ? '<a class="uname" href="//vk.com/id' + o.viewer_id + '" target="_blank">' + o.name + '</a>' : '') +
								'<div class="dtime">' + o.dtime + '</div>' +
					'</table>' +
				'</div>' +
			(o.sa ?
				'<div class="psa">' +
					(o.sa_gazeta_id ? 'КупецЪ' : '') +
					(o.sa_viewer_id ? '<a href="' + URL + '&p=admin&id=' + o.sa_viewer_id + '">' + o.sa_name + '</a>' : '') +
					'<div class="ed">' +
						'<a class="to-arch" val="' + o.id + '">в архив</a>' +
						'<div class="img_edit ob-edit" val="' + o.id + '"></div>' +
					'</div>' +
				'</div>'
			: '') +
				'<div class="cont">' +
					'<div class="rub">' + o.rub + '</div>' +
					'<div class="txt">' + o.txt + '</div>' +
		(o.images ? '<div class="images">' + o.images + '</div>' : '') +
		   (o.tel ? '<div class="tel">' + o.tel + '</div>' : '') +
		  (o.city ? '<div class="city">' + o.city + '</div>' : '') +
					'<div class="meter">Просмотры: ' + o.view + '</div>' +
				'</div>' +
				'<div class="foot">Отправка сообщения автору в разработке.</div>' +
			'</div>';

		if($('#_post').length)
			close();

		var post = $('body').append(html).find('#_post'),
			h;
		_backfon(post);
		if(o.images)
			h = 10;
		else {
			h = 540 - post.height();
			h = Math.round(h < 0 ? 10 : h / 2);
		}
		post.css('top', $(this).scrollTop() + (VK_SCROLL > 60 ? VK_SCROLL - 60 : 0) + h);
		post.find('.close,.ob-edit').click(close);
		post.find('.to-arch').click(function() {
			var t = $(this),
				id = t.attr('val');
			var send = {
				op:'ob_archive',
				id:id
			};
			t.hide();
			$.post(AJAX_MAIN, send, function(res) {
				if(res.success) {
					close();
					$('#ob' + id).remove();
				} else
					t.fadeIn(700);
			}, 'json');
		});
		function close() {
			$('#_post').remove();
			_backfon(false);
		}
	};

$(document)
	.on('click', 'a.rub', function(e) {
		e.stopPropagation();
		$('#rub').rightLink($(this).attr('val'));
		$('#rubsub').val(0);
		obSpisok();
	})
	.on('click', 'a.rubsub', function(e) {
		e.stopPropagation();
		var v = $(this).attr('val').split('_');
		$('#rub').rightLink(v[0]);
		$('#rubsub').val(v[1]);
		obSpisok();
	})
	.on('click', '.ob_next', function() {
		var next = $(this),
			send = obFilter();
		send.page = next.attr('val');
		if(next.hasClass('busy'))
			return;
		next.addClass('busy');
		$.post(AJAX_MAIN, send, function(res) {
			if(res.success)
				next.after(res.spisok).remove();
			else
				next.removeClass('busy');
		}, 'json');
	})
	.on('click', '#ob_my_next', function() {
		var t = $(this);
		if(t.hasClass('busy'))
			return;
		OBMY.page = t.attr('val');
		t.addClass('busy');
		$.post(AJAX_MAIN, OBMY, function(res) {
			if(res.success)
				t.after(res.spisok).remove();
			else
				t.removeClass('busy');
		}, 'json');
	})
	.on('click', '.ob-unit.show', function() {
		var t = $(this),
			full = t.find('.full');
		if(full.length) {
			full.next().removeClass('dn');
			full.remove();
			return;
		}
		var send = {
			op:'ob_post',
			id:t.attr('id').split('ob')[1]
		};
		_wait();
		$.post(AJAX_MAIN, send, function(res) {
			_wait(false);
			if(res.success)
				_post(res.o);
		}, 'json');
	})
	.on('click', '.ob-edit', function(e) {
		e.stopPropagation();
		var t = $(this),
			id = t.attr('val'),
			dialog = _dialog({
				top:20,
				width:550,
				head:'Редактирование объявления',
				load:1,
				butSubmit:'Сохранить',
				submit:submit
			}),
			send = {
				op:'ob_load',
				id:id
			};
		$.post(AJAX_MAIN, send, function(res) {
			if(res.success) {
				var html =
					'<table class="ob-edit-tab">' +
						'<tr><td class="label">Рубрика:' +
							'<td><input type="hidden" id="rubric_id" value="' + res.rubric_id + '" />' +
								'<input type="hidden" id="rubric_sub_id" value="' + res.rubric_sub_id + '" />' +
						'<tr><td class="label topi">Текст:<td><textarea id="txt">' + res.txt + '</textarea>' +
						'<tr><td class="label">Контактные телефоны:' +
							'<td><input type="text" id="telefon" maxlength="200" value="' + res.telefon + '" />' +
						'<tr><td><td>' + res.images +
						'<tr><td class="label topi">Регион:' +
							'<td><input type="hidden" id="country_id" value="' + res.country_id + '" />' +
								'<input type="hidden" id="city_id" value="' + res.city_id + '" />' +
					(res.viewer_id_add ?
						'<tr><td class="label">Показывать имя из VK:<td>'
					: '') +
							'<input type="hidden" id="viewer_id_show" value="' + res.viewer_id_show + '" />' +
						'<tr><td class="label topi">Активность:' +
							'<td><input type="hidden" id="active" value="' + res.active + '" />' +
				'</table>';
				dialog.content.html(html);
				$('#rubric_id')._select({
					width:130,
					spisok:RUBRIC_SPISOK,
					func:rubricSub
				});
				rubricSub(res.rubric_id, res.rubric_sub_id);
				$('#txt').autosize();
				imageSortable();
				cityShow();
				cityGet('', res.city_id, res.city_name);
				$('#country_id')._select({
					width:180,
					title0:'Страна не указана',
					spisok:COUNTRY_SPISOK,
					func:function(id) {
						cityShow();
						if(id) {
							$('#city_id')._select(0)._select('process');
							VK.api('places.getCities',{country:id}, function (data) {
								var d = data.response;
								for(n = 0; n < d.length; n++)
									d[n].uid = d[n].cid;
								d[0].content = '<b>' + d[0].title + '</b>';
								$('#city_id')._select(d);
							});
						}
					}
				});
				if(res.viewer_id_add)
					$('#viewer_id_show')._check();
				$('#active')._radio({
					spisok:[
						{uid:1,title:'Объявление видно всем'},
						{uid:0,title:'В архиве'}
					],
					light:1
				});
			} else
				dialog.loadError();
		}, 'json');
		function rubricSub(id, sub_id) {
			$('#rubric_sub_id').val(typeof sub_id == 'number' ? sub_id : 0);
			if(RUBRIC_SUB_SPISOK[id])
				$('#rubric_sub_id')._select({
					width:200,
					title0:'Подрубрика не указана',
					spisok:RUBRIC_SUB_SPISOK[id]
				});
			else
				$('#rubric_sub_id')._select('remove');
		}
		function submit() {
			var send = {
					op:'ob_edit',
					my:$('.ob-spisok').length ? 0 : 1,
					id:id,
					rubric_id:$('#rubric_id').val(),
					rubric_sub_id:$('#rubric_sub_id').val(),
					txt:$.trim($('#txt').val()),
					telefon:$('#telefon').val(),
					country_id:$('#country_id').val(),
					country_name:$('#country_id')._select('title'),
					city_id:$('#city_id').val(),
					city_name:$('#city_id')._select('title') || '',
					viewer_id_show:$('#viewer_id_show').val(),
					active:$('#active').val()
				};
			if(!send.txt) { err('Введите текст объявления'); $('#txt').focus(); }
			else {
				dialog.process();
				$.post(AJAX_MAIN, send, function(res) {
					if(res.success) {
						$('#ob' + id).after(res.html).remove();
						dialog.close();
						_msg('Объявление изменено');
					} else
						dialog.abort();
				}, 'json');
			}
		}
		function err(msg) {
			dialog.bottom.vkHint({
				msg:'<span class="red">' + msg + '</span>',
				top:-48,
				left:177,
				indent:50,
				show:1,
				remove:1
			});
		}
	})
	.on('mouseenter', '.ob-unit.edited', function() {
		$(this).removeClass('edited');
	})
	.on('click', '.ob-unit .img_del', function(e) {
		e.stopPropagation();
		var id = $(this).attr('val'),
			dialog = _dialog({
				top:90,
				width:260,
				head:'Удаление объявления',
				content:'<center>' +
							'После удаления<br />' +
							'объявление восстановить<br />' +
							'будет невозможно.<br /><br />' +
							'<b>Подтвердите удаление.</b>' +
						'</center>',
				butSubmit:'Удалить',
				submit:submit
			});
		function submit() {
			var send = {
				op:'ob_del',
				id:id
			};
			dialog.process();
			$.post(AJAX_MAIN, send, function(res) {
				if(res.success) {
					dialog.close();
					_msg('Объявление удалено');
					$('#ob' + id).remove();
				} else
					dialog.abort();
			}, 'json');
		}
	});

$(document)
	.ready(function() {
		if($('.ob-spisok').length) {
			$('#find')._search({
				width:300,
				focus:1,
				enter:1,
				txt:'Поиск объявлений: кто ищет, тот найдёт!',
				func:obSpisok
			});
			$('.vkButton').click(function() {
				document.location.href = URL + '&p=ob&d=create';
			});
			$('#countries')._select({
				width:140,
				title0:'Страна не выбрана',
				spisok:COUNTRIES,
				func:function(id) {
					$('#cities')._select(0);
					$('#cities')._select(CITIES[id]);
					$('.city-sel')[(id ? 'remove' : 'add') + 'Class']('dn');
					obSpisok();
				}
			});
			var v = $('#countries').val();
			$('#cities')._select({
				width:140,
				title0:'Город не выбран',
				spisok:v ? CITIES[v] : [],
				func:obSpisok
			});
			$('#cities_select').vkHint({
				width:145,
				msg:'Показываются города,<br />для которых есть<br />активные объявления.',
				ugol:'right',
				top:-16,
				left:-178
			});
			$('#rub').rightLink(function() {
				$('#rubsub').val(0);
				obSpisok();
			});
			$('#withfoto')._check(obSpisok);
			$('#nokupez')._check(obSpisok);
			var timer = 0;
			window._onScroll.push(function(top) {
				clearTimeout(timer);
				timer = setTimeout(function() {
					$('#filter').animate({top:(top <= 148 ? 0 : top - 148) + 'px'}, 500);
				}, 2000);
			});
		}
		if($('#ob-create').length) {
			VK.api('account.getAppPermissions', {}, function(data) {
				if(typeof data.response == 'number') {
					if(!(data.response & 8192) || !(data.response & 4)) {
						VK.addCallback('onSettingsChanged', function(data) {
							if((data & 8192) && (data & 4))
								VK.removeCallback('onWindowFocus');
						});
						VK.addCallback('onWindowFocus', function() {
							location.href = URL + '&p=ob';
						});
						VK.callMethod('showSettingsBox', 8192 + 4);
					}
				} else
					location.href = URL + '&p=ob';
			});
			$('._info a').click(function () {
				var html =
					'<div id="ob-create-rules">' +
						'<div class="headName">Рекомендации при создании объявления:</div>' +
						'<ul><li>более подробно описывайте свой товар;' +
							'<li>по возможности прилагайте фотографии, таким образом пользователям будет визуально удобней определять то, что Вы предлагаете; ' +
								'Приложение позволяет загрузить до <u>8-и изображений</u> на одно объявление;' +
							'<li>обязательно указывайте реальную цену;' +
							'<li>не подавайте одно и то же объявление повторно, для этого есть специальные недорогие платные сервисы. ' +
								'Повторные объявления будут удаляться;' +
							'<li>не пишите объявление в ВЕРХНЕМ РЕГИСТРЕ;' +
							'<li>указывайте номер контактного телефона в соответствующем поле;' +
							'<li>если Ваше оъявление уже не актуально, удалите его или перенесите в архив в разделе "Мои объявления".' +
						'</ul>' +

						'<div class=headName>Товары, реклама которых не допускается:</div>' +
						'<ul><li>товаров, производство и (или) реализация которых запрещены законодательством Российской Федерации;' +
							'<li>наркотических средств, прихотропных веществ и прекурсоров;' +
							'<li>взрывчатых веществ и материалов, за исключением пиротехнических изделий;' +
							'<li>органов и (или) тканей человека в качестве объектов купли-продажи;' +
							'<li>товаров, подлежащих государственной регистрации, в случае отсутствия такой регистрации;' +
							'<li>товаров, подлежащих обязательной сертификации или иному обязательному подтверждению ' +
								'соответствия требованиям технических регламентов, в случае отсутствия такой сертификации ' +
								'или подтверждения такого соответствия;' +
							'<li>товары, на производство и (или) реализацию которых требуется получение лицензий ' +
								'или иных специальных разрешений, в случае отсутствия таких разрешений.' +
						'</ul>' +
					'</div>';
				_dialog({
					top:20,
					width:500,
					head:'Правила размещения объявлений',
					content:html,
					butSubmit:'',
					butCancel:'Закрыть'
				});
			});
			$('#rubric_id')._select({
				width:130,
				title0:'Не указана',
				spisok:RUBRIC_SPISOK,
				func:function(id) {
					$('#rubric_sub_id').val(0);
					if(RUBRIC_SUB_SPISOK[id])
						$('#rubric_sub_id')._select({
							width:200,
							title0:'Подрубрика не указана',
							spisok:RUBRIC_SUB_SPISOK[id],
							func:obPreview
						});
					else
						$('#rubric_sub_id')._select('remove');
					obPreview();
				}
			});
			$('#txt').autosize().focus().keyup(obPreview);
			$("#telefon").keyup(obPreview);
			if(!COUNTRY_ASS[$('#country_id').val()]) // проверка наличия страны в списке
				$('#country_id').val(0); //если нет, страна сбрасывается
			cityShow();
			cityGet();
			$('#country_id')._select({
				width:180,
				title0:'Страна не указана',
				spisok:COUNTRY_SPISOK,
				func:function(id) {
					cityShow();
					if(id) {
						$('#city_id')._select(0)._select('process');
						VK.api('places.getCities',{country:id}, function (data) {
							var d = data.response;
							for(n = 0; n < d.length; n++)
								d[n].uid = d[n].cid;
							d[0].content = '<b>' + d[0].title + '</b>';
							$('#city_id')._select(d);
						});
					}
					obPreview();
				}
			});
			$('#viewer_id_show')._check(obPreview);
			$('#pay_service')._check(function(v) {
				$('.pay')[(v ? 'remove' : 'add') + 'Class']('dn');
				$('#dop')._radio(0);
			});
			$('#dop').html(obPreview);
			$('.vkCancel').click(function() {
				location.href = URL + '&p=ob' + $(this).attr('val');
			});
			$('.vkButton').click(function() {
				var t = $(this),
					send = {
						op:'ob_create',
						rubric_id:$('#rubric_id').val(),
						rubric_sub_id:$('#rubric_sub_id').val(),
						txt:$.trim($('#txt').val()),
						telefon:$('#telefon').val(),
						country_id:$('#country_id').val(),
						country_name:$('#country_id')._select('title'),
						city_id:$('#city_id').val(),
						city_name:$('#city_id')._select('title'),
						viewer_id_show:$('#viewer_id_show').val(),
						dop:$('#dop').val()
//					order_id:create.order.id,
//					order_votes:create.order.votes
					};
				if(send.rubric_id == 0) err('Не выбрана рубрика');
				else if(!send.txt) { err('Введите текст объявления'); $('#txt').focus(); }
				else {
					if(t.hasClass('busy'))
						return;
					t.addClass('busy');
					$.post(AJAX_MAIN, send, function(res) {
						if(res.success)
							wallTest(res);
						else
							t.removeClass('busy');
					}, 'json');
				}
				function err(msg) {
					t.vkHint({
						msg:'<span class="red">' + msg + '</span>',
						top:-57,
						left:17,
						indent:50,
						show:1,
						remove:1
					});
				}
			});
		}
		if($('#ob-my').length) {
			$('#status').rightLink(obMySpisok);
		}
	});
