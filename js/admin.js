var AJAX_ADMIN = SITE + '/ajax/admin.php?' + VALUES,
	userFilter = function() {
		return {
			op:'user_spisok',
			find:$('#find')._search('val'),
			ob_write:$('#ob_write').val(),
			is_app_user:$('#is_app_user').val(),
			left_menu:$('#left_menu').val()
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
	};

$(document)
	.on('click', '.admin-user ._next', function() {
		var next = $(this),
			send = userFilter();
		send.page = next.attr('val');
		if(next.hasClass('busy'))
			return;
		next.addClass('busy');
		$.post(AJAX_ADMIN, send, function(res) {
			if(res.success)
				next.after(res.spisok).remove();
			else
				next.removeClass('busy');
		}, 'json');
	});

$(function() {
		if($('.admin-user').length) {
			$('#find')._search({
				width:148,
				focus:1,
				enter:1,
				txt:'Быстрый поиск',
				func:userSpisok
			});
			$('#ob_write')._radio(userSpisok);
			$('#is_app_user')._check(userSpisok);
			$('#left_menu')._check(userSpisok);
		}
		if($('#user-info').length) {
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

