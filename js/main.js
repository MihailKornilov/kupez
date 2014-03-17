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
	};

$(document)
	.on('click', '.rub', function() {
		$('#rub').rightLink($(this).attr('val'));
		$('#rubsub').val(0);
		obSpisok();
	})
	.on('click', '.rubsub', function() {
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
			$('#rubric_id')._select({
				width:120,
				title0:'Не указана',
				spisok:RUBRIC_SPISOK,
				func:function(id) {
					$('#rubric_sub_id').val(0);
					if(RUBRIC_SUB_SPISOK[id])
						$('#rubric_sub_id')._select({
							width:200,
							title0:'Подрубрика не указана',
							spisok:RUBRIC_SUB_SPISOK[id],
						});
					else
						$('#rubric_sub_id')._select('remove');
				}
			});
			$('#txt').autosize().focus();
			$('#country_id')._select({
				width:180,
				title0:'Страна не указана',
				spisok:COUNTRY_SPISOK
			});
			//$('#viewer_id_show')._check();
			//$('#pay_service')._check();
			$('.vkCancel').click(function() {
				location.href = URL + '&p=ob';
			});
		}
	});