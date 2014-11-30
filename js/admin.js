var AJAX_ADMIN = APP_HTML + '/ajax/admin.php?' + VALUES,
	userFilter = function() {
		return {
			op:'user_spisok',
			find:$('#find')._search('val'),
			ob_write:$('#ob_write').val(),
			rules:$('#rules').val()
		};
	},
	userSpisok = function() {
		if($('.result').hasClass('_busy'))
			return;
		$('.result').addClass('_busy');
		$.post(AJAX_ADMIN, userFilter(), function(res) {
			$('.result').removeClass('_busy');
			if(res.success) {
				$('.result').html(res.result);
				$('.left').html(res.spisok);
			}
		}, 'json');
	},
	adminObSpisok = function(v, attr_id) {
		if($('.fstat').hasClass('_busy'))
			return;
		OBMY[attr_id] = v;
		$('.fstat').addClass('_busy');
		$.post(AJAX_MAIN, OBMY, function (res) {
			$('.fstat').removeClass('_busy');
			if(res.success) {
				$('.res').html(res.result);
				$('#spisok').html(res.spisok);
			}
		}, 'json');
	};

$(document)
	.on('click', '.admin-user ._next', function() {
		var t = $(this),
			send = userFilter();
		send.page = t.attr('val');
		if(t.hasClass('busy'))
			return;
		t.addClass('busy');
		$.post(AJAX_ADMIN, send, function(res) {
			if(res.success)
				t.after(res.spisok).remove();
			else
				t.removeClass('busy');
		}, 'json');
	})
	.on('click', '#find-query ._next', function() {
		var t = $(this);
		var send = FQ;
		send.page = t.attr('val');
		if(t.hasClass('busy'))
			return;
		t.addClass('busy');
		$.post(AJAX_ADMIN, send, function(res) {
			if(res.success) {
				t.remove();
				$('#find-query ._spisok').append(res.spisok);
			} else
				t.removeClass('busy');
		}, 'json');
	})

	.ready(function() {
		if($('.admin-user').length) {
			$('#find')._search({
				width:188,
				focus:1,
				enter:1,
				txt:'Быстрый поиск',
				func:userSpisok
			});
			$('#ob_write')._radio(userSpisok);
			$('#rules')._radio(userSpisok);
		}
		if($('#user-info').length) {
			$('#menu').rightLink(function(v) {
				location.href = URL + '&p=admin&d=user&d1=' + v + '&id=' + VID;
			});
			$('#status')._radio(adminObSpisok);
			$('.update').click(function() {
				var t = $(this),
					send = {
						op:'user_update',
						viewer_id: t.attr('val')
					};
				if(t.hasClass('busy'))
					return;
				t.addClass('busy');
				$.post(AJAX_ADMIN, send, function(res) {
					t.removeClass('busy');
					if(res.success) {

					}
				}, 'json');
			});
		}
	});

