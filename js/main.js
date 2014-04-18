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
	obFilter = function() {
		return {
			op:'ob_spisok',
			find:$('#find')._search('val'),
			country_id:$('#countries').val(),
			city_id:$('#cities').val(),
			rubric_id:$('#rub').val(),
			rubric_sub_id:$('#rubsub').val(),
			withfoto:$('#withfoto').val()
		};
	},
	obSpisok = function() {
		if($('.region').hasClass('_busy'))
			return;
		var send = obFilter();
		$('.region').addClass('_busy');
		$.post(AJAX_MAIN, send, function (res) {
			$('.region').removeClass('_busy');
			if(res.success) {
				$('.result').html(res.result);
				$('.left').html(res.spisok);
			}
		}, 'json');
	},
	obPreview = function() {
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
			//($foto ? '<td class="foto"><img src="'.$foto.'" />' : '').
					'<tr><td class="adres" colspan="2">' +
						($('#country_id').val() > 0 ? $('#country_id')._select('title') : '') +
						($('#city_id').val() > 0 ? ', ' + $('#city_id')._select('title') : '') +
						($('#viewer_id_show').val() == 1 ? VIEWER_LINK  : '') +
					'</table>' +
				'</div>';
		$('#preview').html(html);
	},
	cityGet = function(val) {
		if($('#country_id').val() == 0)
			return;
		if(!val)
			val = '';
		$('#city_id')._select('process');
		VK.api('places.getCities',{country:$('#country_id').val(), q:val}, function(data) {
			for(var n = 0; n < data.response.length; n++) {
				var sp = data.response[n];
				sp.uid = sp.cid;
				sp.content = sp.title + (sp.area ? '<span>' + sp.area + '</span>' : '');
			}
			if(val.length == 0)
				data.response[0].content = '<B>' + data.response[0].title + '</B>';
			$('#city_id')._select(data.response);
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
	};

$(document)
	.on('click', 'a.rub', function() {
		$('#rub').rightLink($(this).attr('val'));
		$('#rubsub').val(0);
		obSpisok();
	})
	.on('click', 'a.rubsub', function() {
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
				title0:'Страна не выбрана',
				spisok:COUNTRIES,
				func:function(id) {
					$('#cities')._select(0);
					$('#cities')._select(CITIES[id]);
					$('.city-sel')[(id ? 'remove' : 'add') + 'Class']('dn');
					obSpisok();
				}
			});
			$('#cities')._select({
				title0:'Город не выбран',
				spisok:[],
				func:obSpisok
			});
			$('#cities_select').vkHint({
				msg:'Показываются города,<br />для которых есть<br />активные объявления.',
				ugol:'right',
				top:-16,
				left:-175
			});
			$('#rub').rightLink(function() {
				$('#rubsub').val(0);
				obSpisok();
			});
			$('#withfoto')._check(obSpisok);
		}
		if($('#ob-create').length) {
			$('._info a').click(function () {
				var html =
					'<div id="ob-create-rules">' +
						'<div class="headName">Рекомендации при создании объявления:</div>' +
						'<ul><li>более подробно описывайте свой товар;' +
							'<li>по возможности прилагайте фотографию, таким образом пользователям будет визуально удобней определять то, что Вы предлагаете;' +
							//' Приложение позволяет загрузить до 4-х изображений на одно объявление;' +
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
				location.href = URL + '&p=ob';
			});
			$('.vkButton').click(function() {
				var t = $(this),
					send = {
						op:'ob_create',
						rubric_id:$('#rubric_id').val(),
						rubric_sub_id:$('#rubric_sub_id').val(),
						txt:$.trim($('#txt').val()),
						telefon:$('#telefon').val(),
						//file:$("#images").val(),
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
							location.href = URL + '&p=ob';
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
	});
