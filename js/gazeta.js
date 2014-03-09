var AJAX_GAZ = 'http://' + DOMAIN + '/ajax/gazeta.php?' + VALUES,
	clientAdd = function(callback) {
		var html = '<table class="client-add">' +
			'<tr><td class="label">���������:<td><input type="hidden" id="cperson">' +
				'<a href="' + URL + '&p=gazeta&d=setup&d1=person" class="img_edit' + _tooltip('��������� ��������� ��������', -95) + '</a>' +
			'<tr><td class="label">���������� ���� (���):<td><input type="text" id="fio" maxlength="200">' +
			'<tr><td class="label">�������� �����������:<td><input type="text" id="org_name" maxlength="200">' +
			'<tr><td class="label">��������:<td><input type="text" id="ctelefon" maxlength="300">' +
			'<tr><td class="label">�����:<td><input type="text" id="cadres" maxlength="200">' +
			'<tr><td class="label">���:<td><input type="text" id="inn" maxlength="100">' +
			'<tr><td class="label">���:<td><input type="text" id="kpp" maxlength="100">' +
			'<tr><td class="label">E-mail:<td><input type="text" id="email" maxlength="100">' +
			'<tr><td class="label">������:<td><input type="hidden" id="cskidka">' +
			'</table>',
			dialog = _dialog({
				top:40,
				width:440,
				head:'���������� �o���� �������',
				content:html,
				submit:submit
			});
		$('#cperson')._select({
			width:180,
			title0:'�� �������',
			spisok:PERSON_SPISOK
		});
		$('#cskidka')._select({
			width:60,
			title0:'���',
			spisok:SKIDKA_SPISOK
		});
		$('#fio').focus();
		$('#fio,#org_name,#ctelefon,#cadres,#inn,#kpp,#email').keyEnter(submit);
		function submit() {
			var send = {
				op:'client_add',
				person:$('#cperson').val(),
				fio:$('#fio').val(),
				telefon:$('#ctelefon').val(),
				org_name:$('#org_name').val(),
				adres:$('#cadres').val(),
				inn:$('#inn').val(),
				kpp:$('#kpp').val(),
				email:$('#email').val(),
				skidka:$('#cskidka').val()
			};
			if(send.person == 0) err('�� ������� ���������', -48);
			else if(!send.fio && !send.org_name) err('���������� ������� ���������� ����<br />���� �������� �����������', -61);
			else {
				dialog.process();
				$.post(AJAX_GAZ, send, function(res) {
					if(res.success) {
						dialog.close();
						_msg('����� ������ �����.');
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
			op:'zayav_spisok',
			find:$('#fz-find').val(),
			client_id:$('#fz-client_id').val(),
			cat:$('#fz-cat').val(),
			gnyear:$('#fz-gnyear').val(),
			nomer:$('#fz-nomer').val(),
			nopublic:$('#fz-nopublic').val()
		};
		return v;
	},
	zayavSpisok = function(v, id) {
		var send = zayavFilter();
		send[id] = v;
		if(id == 'gnyear') {
			$('#nomer')._select(0);
			send.nomer = 0;
		}
		$('.filter')[send.find ? 'hide' : 'show']();
		if($('#mainLinks').hasClass('busy'))
			return;
		$('#mainLinks').addClass('busy');
		$.post(AJAX_GAZ, send, function (res) {
			$('#mainLinks').removeClass('busy');
			if(res.success) {
				$('.result').html(res.result);
				$('.left').html(res.spisok);
				if(v == 'change')
					$('#nomer')._select({
						width:147,
						title0:'����� �� ������',
						spisok:res.gn_sel,
						func:zayavSpisok
					});
			}
		}, 'json');
	},
	zayavRubric = function() {
		$('#rubric_id')._select({
			width:120,
			title0:'�� �������',
			spisok:RUBRIC_SPISOK,
			func:function(id) {
				$('#rubric_sub_id').val(0)._select('remove');
				zayavRubricSub(id);
			}
		});
	},
	zayavRubricSub = function(id) {
		if(RUBRIC_SUB_SPISOK[id]) {
			$('#rubric_sub_id')._select({
				width:180,
				title0:'���������� �� �������',
				spisok:RUBRIC_SUB_SPISOK[id]
			});
		}
	},
	zayavObSumCalc = function() {// ���������� ��������� ����������
		var txt_sum = 0, // ����� ������ �� �����
			podr_about = '', // ��������� ������������ ����� ����������
			txt = $('#ztxt').val()
					.replace(/\./g, '')    // �����
					.replace(/,/g, '')     // �������
					.replace(/\//g, '')    // ���� /
					.replace(/\"/g, '')    // ������� �������
					.replace(/( +)/g, ' ') // ������ �������
					.replace( /^\s+/g, '') // ������� � ������
					.replace( /\s+$/g, '');// ������� � �����
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
			var html = '�����: <b>' + txt.length + '</b>' + podr_about + '<br />' +
					   '����: <b>' + txt_sum + '</b> ���.<span>(��� ����� ���. ����������)</span>';
			$('#txt-count').html(html);
		}
		window.gnGet.cena(txt_sum);
		if($('#summa_manual').val() == '0')
			$('#summa').val(window.gnGet.summa());
	},
	zayavRekSumCalc = function() {// ���������� ��������� �������
		var t = $(this),
			v = t.val(),
			id = t.attr('id'),
			val_x = $('#size_x').val(),
			val_y = $('#size_y').val(),
			x = REGEXP_CENA.test(val_x) ? val_x.replace(',', '.') : 0,
			y = REGEXP_CENA.test(val_y) ? val_y.replace(',', '.') : 0,
			kv_sm = 0;
		$('#kv_sm').val('');
		if(!REGEXP_CENA.test(v)) {
			t.vkHint({
				msg:'<span class="red">�� ��������� ������� ��������.</span>',
				remove:1,
				indent:40,
				show:1,
				top:id == 'size_x' ? -57 : -79,
				left:id == 'size_x' ? -33 : 20
			});
		} else {
			t.prev().remove('.hint');
			kv_sm = Math.round(x * y);
			if(kv_sm)
				$("#kv_sm").val(kv_sm);
		}
		window.gnGet.cena(kv_sm);
		if($('#summa_manual').val() == '0')
			$('#summa').val(window.gnGet.summa());
		$('#skidka-txt').html(window.gnGet.skidka());
	};

$.fn.clientSel = function(o) {
	var t = $(this);
	o = $.extend({
		width:270,
		add:null,
		client_id:t.val() || 0,
		func:function() {}
	}, o);

	if(o.add)
		o.add = function() {
			clientAdd(function(res) {
				var arr = [];
				arr.push(res);
				t._select(arr);
				t._select(res.uid);
			});
		};

	t._select({
		width:o.width,
		title0:'������� ������� ������ �������...',
		spisok:[],
		write:1,
		nofind:'�������� �� �������',
		func:o.func,
		funcAdd:o.add,
		funcKeyup:clientsGet
	});
	clientsGet();

	function clientsGet(val) {
		var send = {
			op:'client_sel',
			val:val || '',
			client_id:o.client_id
		};
		t._select('process');
		$.post(AJAX_GAZ, send, function(res) {
			t._select('cancel');
			if(res.success) {
				t._select(res.spisok);
				if(o.client_id) {
					t._select(o.client_id);
					o.client_id = 0;
				}
			}
		}, 'json');
	}
	return t;
};
$.fn.gnGet = function(o) {
	o = $.extend({
		show:4,     // ���������� �������, ������� ������������ ����������, � ����� ������ �� ��� ���������
		add:8,      // ���������� �������, ������������� � ������
		category:1,
		gns:null,   // ��������� ������ (��� ��������������)
		skidka:0,
		manual:0,   // ����������� �� ������� ��� ����� ����� ����� �������
		func:function() {}
	}, o);
	var t = $(this),
		n,
		pix = 21, // ������ ������ ������� � ��������
		gns_begin = GN_FIRST_ACTIVE,
		gns_end = gns_begin + o.show,
		html =
			'<div id="gnGet">' +
				'<table>' +
					'<tr><td><div id="dopLinks">' +
								'<a class="link" val="4">�����</a>' +
								'<a class="link" val="13">3 ������</a>' +
								'<a class="link" val="26">�������</a>' +
								'<a class="link" val="52">���</a>' +
							'</div>' +
						'<td><input type="hidden" id="dopDef">' +
				'</table>' +
				'<table class="gn-spisok">' +
					'<tr><td id="selCount">' +
						'<td><div id="gns"></div>' +
				'</table>' +
			'</div>';
	t.html(html);

	$(document)
		.on('click', '#darr', function () {// �������������� ������
			gns_begin = gns_end;
			gns_end += o.add;
			gnsPrint();
		})
		.on('click', '.gns-week', function () {// �������� �� ������� �� ����� ������
			dopMenuA.removeClass('sel');
			var th = $(this),
				sel = !th.hasClass('gnsel'),
				v = th.attr('val');
			th[(sel ? 'add': 'remove') + 'Class']('gnsel');
			th.removeClass('prev');
			GN[v].prev = 0;
			GN[v].sel = sel;
			GN[v].dop = 0;
			if(o.category < 3)
				$('#vdop' + v).val(0)._dropdown(!sel ? 'remove' : {
					title0:o.category == 1 ? '���. �������� �� ������' : '������ �� �������',
					spisok:o.category == 1 ? OBDOP_SPISOK : POLOSA_SPISOK,
					func:function(id) {
						GN[v].dop = id;
						cenaSet();
						o.func();
					}
				});
			gnsCount();
			cenaSet();
			o.func();
		});

	var gnGet = $('#gnGet'),                 // �������� �����
		gns = gnGet.find('#gns'),            // ������ �������
		dopMenuA = gnGet.find('#dopLinks a'),// ������ ���� � ���������
		dopDef = gnGet.find("#dopDef"),      // ����� �������������� ���������� �� ���������
		selCount = gnGet.find('#selCount'),  // ���������� ��������� �������
		cena = 0,   // ���� �� ���� �����
		summa_manual = 0,
		skidka_sum = 0;

	gnsClear();
	if(o.gns) {// ��������� ��������� ������� ��� ��������������
		var max = 0;
		for(n in o.gns) {
			if(n > GN_LAST_ACTIVE)
				break;
			if(!GN[n])
				continue;
			var sp = GN[n];
			sp.sel = 1;
			sp.prev = 1;
			sp.cena = o.gns[n][0];
			sp.dop = o.gns[n][1];
			max = n;
		}
		gnsPrint(1, max - GN_FIRST_ACTIVE + 1);
		gnsCount();
	} else
		gnsPrint();
	dopMenu();

	dopMenuA.click(function () {// ����� ������� �� �����, 3 ������, ������� � ��� ������� �������
		var t = $(this),
			v = t.attr('val') * 1;
		gnsClear();
		if(t.hasClass('sel')) {
			v = 0;
			t.removeClass('sel');
		} else {
			dopMenuA.removeClass('sel');
			t.addClass('sel');
			n = GN_FIRST_ACTIVE;
			var c = v;
			while(c) {
				if(n > GN_LAST_ACTIVE)
					break;
				if(!GN[n])
					continue;
				GN[n].sel = 1;
				c--;
				n++;
			}
		}
		gnsCount();
		gnsPrint(1, v);
		o.func();
	});
	function gnsPrint(first, count) {// ����� ������ �������
		if(first) {// ������ ������� ��������� � ������ ������, � �� �����������
			gns_begin = GN_FIRST_ACTIVE;
			gns_end = gns_begin + (count || 0) + o.show;
		}
		gnGet.find('#darr').remove();
		var html = '';
		for(n = gns_begin; n < gns_end; n++) {
			if(n > GN_LAST_ACTIVE)
				break;
			var sp = GN[n];
			if(!sp) { // ���� ����� ��������, ����� �� ���������
				end++;
				continue;
			}
			html +=
				'<table><tr>' +
					'<td><table class="gns-week' + (sp.sel ? ' gnsel' : '') + (sp.prev ? ' prev' : '') + '" val="' + n + '">' +
							'<tr><td class="td"><b>' + sp.week + '</b><span class="g">(' + n + ')</span>' +
								'<td class="td"><span class="g">�����</span> ' + sp.txt +
								'<td class="cena" id="cena' + n + '">' +
						'</table>' +
					'<td class="vdop"><input type="hidden" id="vdop' + n + '" value="' + sp.dop + '" />' +
				'</table>';
		}
		html += gns_end < GN_LAST_ACTIVE ? '<div id="darr">&darr; &darr; &darr;</div>' : '';
		gns[first ? 'html' : 'append'](html);
		gns.animate({height:(gns.find('.gns-week').length * pix) + 'px'}, 300);
		if(first && o.category < 3)
			gnsActionActive(function(sp) {
				$('#vdop' + sp.n)._dropdown({
					title0:o.category == 1 ? '���. �������� �� ������' : '������ �� �������',
					spisok:o.category == 1 ? OBDOP_SPISOK : POLOSA_SPISOK,
					func:function(v) {
						GN[sp.n].dop = v;
						cenaSet();
						o.func();
					}
				});
			});
		cenaSet();
	}
	function dopMenu() {
		dopDef._dropdown(o.category > 2 ? 'remove' : {
			head:'���������� ����...',
			headgrey:1,
			title0:o.category == 1 ? '���. �������� �� ������' : '������ �� �������',
			nosel:1,
			spisok:o.category == 1 ? OBDOP_SPISOK : POLOSA_SPISOK,
			func:function(id) {
				gnsActionActive(function(sp) {
					if(!sp.prev) {
						$('#vdop' + sp.n)._dropdown(id);
						sp.dop = id;
					}
				});
				cenaSet();
				o.func();
			}
		});
	}
	function gnsActionActive(func, all) {// ���������� �������� � ��������� �������
		for(n = GN_FIRST_ACTIVE; n <= GN_LAST_ACTIVE; n++) {
			var sp = GN[n];
			if(!sp)
				continue; // E��� ����� ��������, ����� ��� ��������
			if(all || sp.sel)
				func(sp, n);
		}
	}
	function gnsCount() {// ����� ���������� ��������� �������
		var count = 0;
		gnsActionActive(function() {
			count++;
		})
		if(count) {
			var html = '������' + _end(count, ['', '�']) + ' ' +
						count + ' �����' + _end(count, ['', 'a', '��']) +
						'<a>��������</a>';
			selCount
				.html(html)
				.find('a').click(function() {
					gnsClear();
					gnsPrint(1);
					selCount.html('');
					dopMenuA.removeClass('sel');
					o.func();
				});
		} else
			selCount.html('');
	}
	function gnsClear() {// ������� ��������� �������
		gnsActionActive(function(sp, n) {
			sp.n = n;
			sp.sel = 0;
			sp.prev = 0;
			sp.cena = 0;
			sp.dop = 0;
		}, 1);
	}
	function cenaSet() {// ��������� ���� � ��������� ������
		var sum = 0,
			count = 0;
		switch(o.category) {
			case 1:
				var four = 0;
				if(o.manual) {
					gnsActionActive(function(sp) {
						if(!sp.prev) {
							four++;
							if (four == 4)
								four = 0;
							else
								count++;
						}
					});
					four = 0;
					sum = Math.round((summa_manual / count) * 1000000) / 1000000;
				}
				gnsActionActive(function(sp) {
					if(!sp.prev) {
						four++;
						if(four == 4) {
							four = 0;
							sp.cena = 0;
						} else
						if(o.manual)
							sp.cena = sum;
						else
							sp.cena = cena ? cena + (sp.dop ? OBDOP_CENA_ASS[sp.dop] : 0) : 0;
					}
					gnGet.find('#cena' + sp.n).html(Math.round(sp.cena * 100) / 100);
				});
				break;
			case 2:
				if(o.manual) {
					gnsActionActive(function(sp) {
						if(sp.dop > 0 && !sp.prev)
							count++;
					});
					sum = Math.round((summa_manual / count) * 1000000) / 1000000;
				}
				skidka_sum = 0;
				gnsActionActive(function(sp) {
					if(!sp.prev) {
						sp.cena = 0;
						if(sp.dop) {
							if(o.manual)
								sp.cena = sum;
							else {
								sp.cena = cena * POLOSA_CENA_ASS[sp.dop];
								var sk = sp.cena * o.skidka / 100;
								sp.cena -= sk;
								skidka_sum += sk;
							}
						}
					}
					gnGet.find('#cena' + sp.n).html(Math.round(sp.cena * 100) / 100);
				});
				skidka_sum = Math.round(skidka_sum * 100) / 100;
				break;
			default:
				gnsActionActive(function(sp) {
					if(!sp.prev)
						count++;
				});
				sum = Math.round((summa_manual / count) * 1000000) / 1000000;
				gnsActionActive(function(sp) {
					if(!sp.prev)
						sp.cena = sum;
					gnGet.find('#cena' + sp.n).html(Math.round(sp.cena * 100) / 100);
				});
		}
	};
	function summaGet() {
		var sum = 0;
		gnsActionActive(function(sp) {
			if(!sp.prev)
				sum += sp.cena;
		});
		return Math.round(sum * 100) / 100;
	}
	t.cena = function(c) {
		cena = c || 0;
		cenaSet();
	};
	t.skidka = function(v) {
		if(v != undefined) {
			o.skidka = v;
			cenaSet();
			return '';
		}
		return o.category == 2 && skidka_sum ? '����� ������: <b>' + skidka_sum + '</b> ���.' : '';
	};
	t.summa = summaGet;
	t.clear = function(v) {
		o.category = v;
		o.manual = 0;
		o.skidka = 0;
		skidka_sum = 0;
		summa_manual = 0;
		dopMenuA.removeClass('sel');
		gnsClear();
		gnsPrint(1);
		dopMenu();
	};
	t.manual = function(v, sum) {
		o.manual = v;
		summa_manual = summaGet();
		cenaSet();
		o.func();
	};
	t.manualSumma = function(sum) {
		summa_manual = REGEXP_CENA.test(sum) ? sum.replace(',', '.') * 1 : 0;
		cenaSet();
	};
	t.result = function() {
		var spisok = [],
			no_polosa = 0; // ��������, ��� �� ������ �������
		gnsActionActive(function(sp) {
			if(o.category == 2 && !sp.dop)
				no_polosa = 1;
			spisok.push(sp.n + ":" + sp.cena + ":" + sp.dop);
		});
		return no_polosa ? 'no_polosa' : spisok.join();
	};
	return t;
};

$(document)
	.on('click', '#client ._next', function() {
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
		var next = $(this),
			send = zayavFilter();
		send.page = next.attr('val');
		if(next.hasClass('busy'))
			return;
		next.addClass('busy');
		$.post(AJAX_GAZ, send, function(res) {
			if(res.success)
				next.after(res.spisok).remove();
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

	.ready(function() {
		if($('#client').length) {
			window.cFind = $('#find')._search({
				width:602,
				focus:1,
				enter:1,
				txt:'������� ������ ������� � ������� Enter',
				func:clientSpisokLoad
			});
			$('#buttonCreate').click(clientAdd);
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
			$('#person')._select({
				spisok:PERSON_SPISOK,
				title0:'��������� �� �������',
				func:clientSpisokLoad
			});
			$('#skidka')._select({
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
		if($('#clientInfo').length) {
			$('.cedit').click(function() {
				var html = '<table class="client-add">' +
						'<tr><td class="label">���������:<td><input type="hidden" id="cperson" value="' + CLIENT.person + '" />' +
							'<a href="' + URL + '&p=gazeta&d=setup&d1=person" class="img_edit"></a>' +
						'<tr><td class="label">���������� ���� (���):<td><input type="text" id="fio" maxlength="200" value="' + CLIENT.fio + '" />' +
						'<tr><td class="label">�������� �����������:<td><input type="text" id="org_name" maxlength="200" value="' + CLIENT.org_name + '" />' +
						'<tr><td class="label">��������:<td><input type="text" id="telefon" maxlength="300" value="' + CLIENT.telefon + '" />' +
						'<tr><td class="label">�����:<td><input type="text" id="adres" maxlength="200" value="' + CLIENT.adres + '" />' +
						'<tr><td class="label">���:<td><input type="text" id="inn" maxlength="100" value="' + CLIENT.inn + '" />' +
						'<tr><td class="label">���:<td><input type="text" id="kpp" maxlength="100" value="' + CLIENT.kpp + '" />' +
						'<tr><td class="label">E-mail:<td><input type="text" id="email" maxlength="100" value="' + CLIENT.email + '" />' +
						'<tr><td class="label">������:<td><input type="hidden" id="cskidka" value="' + CLIENT.skidka + '" />' +
						'</table>',
					dialog = _dialog({
						top:40,
						width:440,
						head:'�������������� ������ �������',
						content:html,
						submit:submit
					});
				$('#cperson')._select({
					width:180,
					spisok:PERSON_SPISOK
				});
				$('.client-add .img_edit:first').vkHint({
					msg:"������� � ���������� ����������",
					indent:110,
					top:-76,
					left:75
				});
				$('#cskidka')._select({
					width:60,
					title0:'���',
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
					if(!send.fio && !send.org_name) err('���������� ������� ���������� ����<br />���� �������� �����������');
					else {
						dialog.process();
						$.post(AJAX_GAZ, send, function(res) {
							if(res.success) {
								CLIENT = res;
								$('#clientInfo .left:first').html(res.html);
								dialog.close();
								_msg('������ ��������.');

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
					head:'�������� �������',
					content:'<center>��������!<br />����� ������� ��� ������ � �������,<br />��� ������, � �������.<br /><b>����������� ��������.</b></center>',
					butSubmit:'�������',
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
							_msg('������ ������.');
							location.href = URL + '&p=gazeta&d=client';
						} else
							dialog.abort();
					}, 'json');
				}
			});
			$('#dopLinks .link').click(function() {
				$('#dopLinks .link').removeClass('sel');
				$(this).addClass('sel');
				var val = $(this).attr('val');
				$('#zayav_spisok').css('display', val == 'zayav' ? 'block' : 'none');
				$('#income_spisok').css('display', val == 'inc' ? 'block' : 'none');
				$('#notes').css('display', val == 'note' ? 'block' : 'none');
				$('#histories').css('display', val == 'hist' ? 'block' : 'none');
			});
		}

		if($('#zayav').length) {
			$('#find')
				.vkHint({
					width:145,
					msg:'<div style="text-align:justify">' +
						'������� �������� � ������� <b>Enter</b>. ' +
						'����� ������������ �� ���� �����������, ' +
						'�� ���� ������ ��������� �� �����������. ' +
						'���� ������� ����� � ��� ��������� � ������� ' +
						'������, �� ��� ������ ��������� ������ � ������.<div>',
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
					txt:'������� �����..',
					func:zayavSpisok
				});
			$('#cat').rightLink(zayavSpisok);
			$('.img_word')
				.click(function () {
					var gn = $('#nomer').val();
					if(gn == 0)
						$(this).vkHint({
							width:150,
							ugol:'right',
							msg:'<span class=red>�� ������ ����� ������.</span>',
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
							'������� ������ ���������� ' +
							'� �������� �������� � ������� ' +
							'Microsoft Word.' +
						'</span>',
					indent:10,
					top:-30,
					left:-193
				});
			$('#nopublic')._check(function(v, id) {
				$('.filter_nomer').toggle();
				zayavSpisok(v, id);
			});
			$('#nopublic_check').vkHint({
				width:145,
				msg:'������, ������� �� ������������� �� � ����� ������ ������.',
				indent:60,
				top:-64,
				left:-61
			});
			$('#gnyear').years({func:zayavSpisok});
			$('#nomer')._select({
				title0:'����� �� ������',
				spisok:GN_SEL,
				func:zayavSpisok
			});
		}
		if($('#zayav-add').length) {
			$('#client_id').clientSel({add:1});
			$('#category')._select({
				width:120,
				spisok:CATEGORY_SPISOK,
				func:function(category) {
					$('.ob').addClass('dn');
					$('#rubric_id').val(0)._select('remove');
					$('#rubric_sub_id').val(0)._select('remove');
					$('#ztxt').val('');
					zayavObSumCalc();
					$('#telefon').val('');
					$('#adres').val('');
					$('#summa_manual')._check(0);

					$('.rek').addClass('dn');
					$('#size_x').val('');
					$('#size_y').val('');
					$('#kv_sm').val('');
					$('.skd').addClass('dn');
					$('#skidka')._select(0);

					$('.manual').addClass('dn');
					$('#summa').attr('readonly', true).val(0);

					window.gnGet.clear(category);

					switch(category) {
						case 1:
							$('.ob').removeClass('dn');
							$('.manual').removeClass('dn');
							zayavRubric();
							break;
						case 2:
							$('.rek').removeClass('dn');
							$('.skd').removeClass('dn');
							$('.manual').removeClass('dn');
							break;
						default:
							$('#summa').attr('readonly', false);
					}
				}
			});
			zayavRubric();
			$('#ztxt')
				.autosize()
				.focus()
				.keyup(zayavObSumCalc);
			$('#size_x').keyup(zayavRekSumCalc);
			$('#size_y').keyup(zayavRekSumCalc);
			window.gnGet = $('#gn_spisok').gnGet({
				func:function() {
					if($('#summa').attr('readonly')) {
						$('#summa').val(window.gnGet.summa());
						$('#skidka-txt').html(window.gnGet.skidka());
					}
				}
			});
			$('#skidka')._select({
				width:60,
				title0:'���',
				spisok:SKIDKA_SPISOK,
				func:function(v) {
					window.gnGet.skidka(v);
					if($('#summa_manual').val() == '0')
						$('#summa').val(window.gnGet.summa());
					$('#skidka-txt').html(window.gnGet.skidka());
				}
			});
			$('#summa_manual')._check(function(id) {
				$('#summa').attr('readonly', !id).focus();
				window.gnGet.manual(id);
				$('#skidka-txt').html(window.gnGet.skidka());
			});
			$('#summa').keyup(function() {
				window.gnGet.manualSumma($(this).val());
			});
			$('#note').autosize();
			$('.vkButton').click(function() {
				var but = $(this),
					send = {
						op:'zayav_add',
						client_id:$('#client_id').val(),
						category:$('#category').val(),

						rubric_id:$('#rubric_id').val(),
						rubric_sub_id:$('#rubric_sub_id').val(),
						txt:$('#ztxt').val(),
						telefon:$('#telefon').val(),
						adres:$('#adres').val(),

						size_x:$('#size_x').val(),
						size_y:$('#size_y').val(),

						gns:window.gnGet.result(),

						skidka:$('#skidka').val(),
						summa_manual:$('#summa_manual').val(),
						note:$('#note').val()
					};

				if(send.category == 1 && send.rubric_id == 0) err('�� ������� �������');
				else if(send.category == 1 && !send.txt) { err('������� ����� ����������'); $('#ztxt').focus(); }
				else if(send.category == 1 && !send.telefon && !send.adres) { err('������� ���������� ������� ��� ����� �������'); $('#telefon').focus(); }
				else if(send.category == 2 && send.client_id == 0) err('�� ������ ������');
				else if(send.category == 2 && (!REGEXP_CENA.test(send.size_x) || !REGEXP_CENA.test(send.size_y))) err('����������� ������ ������ �����');
				else if(!send.gns) err('����� ������� ���� �� ���� ����� �������');
				else if(send.gns == 'no_polosa') err('���������� ������� ������ � ���� �������');
				else if(!REGEXP_CENA.test($('#summa').val())) { err('����������� ������� �������� ���������'); $('#summa').focus(); }
				else {
					but.addClass('busy');
					$.post(AJAX_GAZ, send, function(res) {
						if(res.success) {
							_msg('������ �������.');
							location.href = URL + '&p=gazeta&d=zayav&d1=info&id=' + res.id;
						} else
							but.removeClass('busy');
					}, 'json');
				}
				function err(msg) {
					but.vkHint({
						msg:'<SPAN class=red>' + msg + '</SPAN>',
						remove:1,
						indent:40,
						show:1,
						top:-58,
						left:-14
					});
				}
			});
			$('.vkCancel').click(function() {
				location.href = URL + '&p=gazeta&d=' + $(this).attr('val');
			});
		}
		if($('#zayav-info').length) {
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
		if($('#zayav-edit').length) {
			if($('#client_id').val() == 0)
				$('#client_id').clientSel({add:1});
			window.gnGet = $('#gn_spisok').gnGet({
				category:ZAYAV.category,
				gns:ZAYAV.gns,
				skidka:ZAYAV.skidka,
				manual:ZAYAV.manual,
				func:function() {
					if($('#summa').attr('readonly')) {
						$('#summa').val(window.gnGet.summa());
						$('#skidka-txt').html(window.gnGet.skidka());
					}
				}
			});
			switch(ZAYAV.category) {
				case 1:
					zayavRubric();
					zayavRubricSub($('#rubric_id').val());
					$('#ztxt')
						.autosize()
						.focus()
						.keyup(zayavObSumCalc);
					zayavObSumCalc();
					break;
				case 2:
					$('#size_x').keyup(zayavRekSumCalc);
					$('#size_y').keyup(zayavRekSumCalc);
					$('#skidka')._select({
						width:60,
						title0:'���',
						spisok:SKIDKA_SPISOK,
						func:function(v) {
							window.gnGet.skidka(v);
							if($('#summa_manual').val() == '0')
								$('#summa').val(window.gnGet.summa());
							$('#skidka-txt').html(window.gnGet.skidka());
						}
					});
					window.gnGet.cena(ZAYAV.kv_sm);
			}
			if(ZAYAV.category < 3)
				$('#summa_manual')._check(function(id) {
					$('#summa').attr('readonly', !id).focus();
					window.gnGet.manual(id);
					$('#skidka-txt').html(window.gnGet.skidka());
				});
			$('#summa').keyup(function() {
				window.gnGet.manualSumma($(this).val());
			});
			$('.vkButton').click(function() {
				var but = $(this),
					send = {
						op:'zayav_edit',
						zayav_id:ZAYAV.id,
						client_id:$('#client_id').val(),
						gns:window.gnGet.result()
					};
				if(ZAYAV.category == 1) {
					send.rubric_id = $('#rubric_id').val();
					send.rubric_sub_id = $('#rubric_sub_id').val();
					send.txt = $('#ztxt').val();
					send.telefon = $('#telefon').val();
					send.adres = $('#adres').val();
				}
				if(ZAYAV.category == 2) {
					send.size_x = $('#size_x').val();
					send.size_y = $('#size_y').val();
					send.skidka = $('#skidka').val();
				}
				if(ZAYAV.category < 3)
					send.summa_manual = $('#summa_manual').val();

				if(ZAYAV.category == 1 && send.rubric_id == 0) err('�� ������� �������');
				else if(ZAYAV.category == 1 && !send.txt) { err('������� ����� ����������'); $('#ztxt').focus(); }
				else if(ZAYAV.category == 1 && !send.telefon && !send.adres) { err('������� ���������� ������� ��� ����� �������'); $('#telefon').focus(); }
				else if(ZAYAV.category == 2 && send.client_id == 0) err('�� ������ ������');
				else if(ZAYAV.category == 2 && (!REGEXP_CENA.test(send.size_x) || !REGEXP_CENA.test(send.size_y))) err('����������� ������ ������ �����');
				else if(send.gns == 'no_polosa') err('���������� ������� ������ � ���� �������');
				else if(!REGEXP_CENA.test($('#summa').val())) { err('����������� ������� �������� ���������'); $('#summa').focus(); }
				else {
					but.addClass('busy');
					$.post(AJAX_GAZ, send, function(res) {
						if(res.success) {
							_msg('������ ��������.');
							location.href = URL + '&p=gazeta&d=zayav&d1=info&id=' + ZAYAV.id;
						} else
							but.removeClass('busy');
					}, 'json');
				}
				function err(msg) {
					but.vkHint({
						msg:'<SPAN class=red>' + msg + '</SPAN>',
						remove:1,
						indent:40,
						show:1,
						top:-58,
						left:-14
					});
				}
			});
			$('.vkCancel').click(function() {
				location.href = URL + '&p=gazeta&d=zayav&d1=info&id=' + ZAYAV.id;
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

    var html = "<table cellpadding=0 cellspacing=10 id=prihod_add_tab>" +
        "<TR><TD class=tdAbout>���:<TD><INPUT type=hidden id=prihod_type><a class=img_edit href='" + G.url + "&p=gazeta&d=setup&id=11'></a>" +
        "<TR><TD class=tdAbout>�����:<TD><INPUT type=text id=prihod_sum maxlength=8> ���." +
        "<TR><TD class=tdAbout>�����������:<TD><INPUT type=text id=prihod_txt maxlength=250>" +
        "<TR id=tr_kassa><TD class=tdAbout>������ ���������<br>� �����?:<TD><INPUT type=hidden id=prihod_kassa value='-1'>" +
        "</table>";
    var dialog = $("#dialog_prihod").vkDialog({
        top:50,
        width:420,
        head:"�������� �������",
        content:html,
        submit:submit,
        focus:"#prihod_sum"
    }).o;

    $("#prihod_type")._select({
        width:190,
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