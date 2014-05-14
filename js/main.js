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
			   (rubric_id ? '<span class="rub">' + RUBRIC_ASS[rubric_id] + '</span><u>�</u>' : '') +
		   (rubric_sub_id ? '<span class="rubsub">' + RUBRIC_SUB_ASS[rubric_sub_id] + '</span><u>�</u>' : '') +
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

	obMyFilter = function() {
		return {
			op:'ob_my_spisok',
			menu:$('#menu').val()
		};
	},
	obMySpisok = function() {
		if($('.result').hasClass('_busy'))
			return;
		var send = obMyFilter();
		$('.result').addClass('_busy');
		$.post(AJAX_MAIN, send, function (res) {
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
			var insert = 1; // ������� ������ ��� ��������������, ���� ����������� � ������
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
			title0:'����� �� ������',
			spisok:[],
			write:1,
			func:obPreview,
			funcKeyup:cityGet
		});
		$('#city_id_select').vkHint({
			width:180,
			msg:'<div style="text-align:justify">' +
					'����������� ���������� �����, ' +
					'���� ���� ���������� ������������� ������ �� ����, ' +
					'����� ���������� ����� ������������ ������ � ����� ������.' +
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
	})
	.on('click', '#ob-my ._next', function() {
		var next = $(this),
			send = obMyFilter();
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
	.on('click', '#ob-my .img_edit', function() {
		var t = $(this);
		while(!t.hasClass('ob-unit'))
			t = t.parent();
		var dialog = _dialog({
			top:20,
			width:550,
			head:'�������������� ����������',
			load:1,
			butSubmit:'���������',
			submit:submit
		});
		var send = {
			op:'ob_load',
			id:t.attr('val')
		};
		$.post(AJAX_MAIN, send, function(res) {
			if(res.success) {
				var html =
					'<table class="ob-edit-tab">' +
						'<tr><td class="label">�������:' +
							'<td><input type="hidden" id="rubric_id" value="' + res.rubric_id + '" />' +
								'<input type="hidden" id="rubric_sub_id" value="' + res.rubric_sub_id + '" />' +
						'<tr><td class="label topi">�����:<td><textarea id="txt">' + res.txt + '</textarea>' +
						'<tr><td class="label">���������� ��������:' +
							'<td><input type="text" id="telefon" maxlength="200" value="' + res.telefon + '" />' +
						'<tr><td><td>' + res.images +
						'<tr><td class="label topi">������:' +
							'<td><input type="hidden" id="country_id" value="' + res.country_id + '" />' +
								'<input type="hidden" id="city_id" value="' + res.city_id + '" />' +
						'<tr><td class="label">���������� ��� �� VK:' +
							'<td><input type="hidden" id="viewer_id_show" value="' + res.viewer_id_show + '" />' +
						'<tr><td class="label topi">����������:' +
							'<td><input type="hidden" id="active" value="' + res.active + '" />' +
				'</table>';
				dialog.content.html(html);
				$('#rubric_id')._select({
					width:130,
					spisok:RUBRIC_SPISOK,
					func:rubricSub
				});
				rubricSub(res.rubric_id, res.rubric_sub_id * 1);
				$('#txt').autosize();
				imageSortable();
				cityShow();
				cityGet('', res.city_id, res.city_name);
				$('#country_id')._select({
					width:180,
					title0:'������ �� �������',
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
				$('#viewer_id_show')._check();
				$('#active')._radio({
					spisok:[
						{uid:1,title:'���������� ����� ����'},
						{uid:0,title:'� ������'}
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
					title0:'���������� �� �������',
					spisok:RUBRIC_SUB_SPISOK[id]
				});
			else
				$('#rubric_sub_id')._select('remove');
		}
		function submit() {
			var send = {
					op:'ob_edit',
					id:t.attr('val'),
					rubric_id:$('#rubric_id').val(),
					rubric_sub_id:$('#rubric_sub_id').val(),
					txt:$.trim($('#txt').val()),
					telefon:$('#telefon').val(),
					country_id:$('#country_id').val(),
					country_name:$('#country_id')._select('title'),
					city_id:$('#city_id').val(),
					city_name:$('#city_id')._select('title'),
					viewer_id_show:$('#viewer_id_show').val(),
					active:$('#active').val()
				};
			if(!send.txt) { err('������� ����� ����������'); $('#txt').focus(); }
			else {
				dialog.process();
				$.post(AJAX_MAIN, send, function(res) {
					if(res.success) {
						t.after(res.html).remove();
						dialog.close();
						_msg('���������� ��������');
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
	.on('click', '#ob-my .img_del', function() {
		var t = $(this);
		while(!t.hasClass('ob-unit'))
			t = t.parent();
		var dialog = _dialog({
			top:90,
			width:260,
			head:'�������� ����������',
			content:'<center>' +
						'����� ��������<br />' +
						'���������� ������������<br />' +
						'����� ����������.<br /><br />' +
						'<b>����������� ��������.</b>' +
					'</center>',
			butSubmit:'�������',
			submit:submit
		});
		function submit() {
			var send = {
				op:'ob_del',
				id:t.attr('val')
			};
			dialog.process();
			$.post(AJAX_MAIN, send, function(res) {
				if(res.success) {
					dialog.close();
					_msg('���������� �������');
					t.remove();
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
				txt:'����� ����������: ��� ����, ��� �����!',
				func:obSpisok
			});
			$('.vkButton').click(function() {
				document.location.href = URL + '&p=ob&d=create';
			});
			$('#countries')._select({
				title0:'������ �� �������',
				spisok:COUNTRIES,
				func:function(id) {
					$('#cities')._select(0);
					$('#cities')._select(CITIES[id]);
					$('.city-sel')[(id ? 'remove' : 'add') + 'Class']('dn');
					obSpisok();
				}
			});
			$('#cities')._select({
				title0:'����� �� ������',
				spisok:[],
				func:obSpisok
			});
			$('#cities_select').vkHint({
				msg:'������������ ������,<br />��� ������� ����<br />�������� ����������.',
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
						'<div class="headName">������������ ��� �������� ����������:</div>' +
						'<ul><li>����� �������� ���������� ���� �����;' +
							'<li>�� ����������� ���������� ����������, ����� ������� ������������� ����� ��������� ������� ���������� ��, ��� �� �����������; ' +
								'���������� ��������� ��������� �� <u>8-� �����������</u> �� ���� ����������;' +
							'<li>����������� ���������� �������� ����;' +
							'<li>�� ��������� ���� � �� �� ���������� ��������, ��� ����� ���� ����������� ��������� ������� �������. ' +
								'��������� ���������� ����� ���������;' +
							'<li>�� ������ ���������� � ������� ��������;' +
							'<li>���������� ����� ����������� �������� � ��������������� ����;' +
							'<li>���� ���� ��������� ��� �� ���������, ������� ��� ��� ���������� � ����� � ������� "��� ����������".' +
						'</ul>' +

						'<div class=headName>������, ������� ������� �� �����������:</div>' +
						'<ul><li>�������, ������������ � (���) ���������� ������� ��������� ����������������� ���������� ���������;' +
							'<li>������������� �������, ������������ ������� � �����������;' +
							'<li>���������� ������� � ����������, �� ����������� ��������������� �������;' +
							'<li>������� � (���) ������ �������� � �������� �������� �����-�������;' +
							'<li>�������, ���������� ��������������� �����������, � ������ ���������� ����� �����������;' +
							'<li>�������, ���������� ������������ ������������ ��� ����� ������������� ������������� ' +
								'������������ ����������� ����������� �����������, � ������ ���������� ����� ������������ ' +
								'��� ������������� ������ ������������;' +
							'<li>������, �� ������������ � (���) ���������� ������� ��������� ��������� �������� ' +
								'��� ���� ����������� ����������, � ������ ���������� ����� ����������.' +
						'</ul>' +
					'</div>';
				_dialog({
					top:20,
					width:500,
					head:'������� ���������� ����������',
					content:html,
					butSubmit:'',
					butCancel:'�������'
				});
			});
			$('#rubric_id')._select({
				width:130,
				title0:'�� �������',
				spisok:RUBRIC_SPISOK,
				func:function(id) {
					$('#rubric_sub_id').val(0);
					if(RUBRIC_SUB_SPISOK[id])
						$('#rubric_sub_id')._select({
							width:200,
							title0:'���������� �� �������',
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
			if(!COUNTRY_ASS[$('#country_id').val()]) // �������� ������� ������ � ������
				$('#country_id').val(0); //���� ���, ������ ������������
			cityShow();
			cityGet();
			$('#country_id')._select({
				width:180,
				title0:'������ �� �������',
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
				if(send.rubric_id == 0) err('�� ������� �������');
				else if(!send.txt) { err('������� ����� ����������'); $('#txt').focus(); }
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
		if($('#ob-my').length) {
			$('#menu').rightLink(obMySpisok);
		}
	});

