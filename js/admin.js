var AJAX_ADMIN = SITE + '/ajax/admin.php?' + VALUES,
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
	.on('click', '#rules-get', function() {
		VK.callMethod('showSettingsBox', 4);
	})
	.on('click', '#rules-test', function() {
		VK.api('account.getAppPermissions', {user_id:VIEWER_ID}, function(data) {
			if(data.response) {
				var r = data.response;
				alert('Уведомления: ' + (r&1) + '\n' +
					  'Фотографии: ' + (r&4) + '\n');
			} else
				alert('error');
		});
	})
	.on('click', '#server-get', function() {
		VK.api('photos.getUploadServer', {album_id:130124967}, function(data) {
			if(data.response) {
			/*  var send = {
					photo:'http://kupez.nyandoma.ru/files/images/ob14987-bpgqp612kx-b.jpg'
				};
				$.ajax({
					type:'POST',
					url:data.response.upload_url,
					crossDomain:true,
					data:send,
					success: function(res) {
						console.log('good');
						console.log(res);
					},
					failure: function(res) {
						console.log('bad');
						console.log(res);
					}

				});
//				$.post(data.response.upload_url, send, function(res) {
//					console.log(res);
//				});
				*/
				$('#vkform').attr('action', data.response.upload_url);
				alert($('#vkform').attr('action'))
			} else
				alert('error');
		});
	})
	.on('click', '#photo-save', function() {
		var send = {
			album_id:130124967,
			server:616221,
			photos_list:'[{"photo":"8628800c41:w","sizes":[["s","616221006","e8bb","rXuaF0y8GOk",75,56],["m","616221006","e8bc","RdifBrviJhE",130,97],["x","616221006","e8bd","cQjoJD4b4gQ",604,453],["y","616221006","e8be","zSeV3gU8Sus",807,605],["z","616221006","e8bf","JRlTRn_5Oo0",1280,960],["w","616221006","e8c0","pxp071QxKI8",2048,1536],["o","616221006","e8c1","54C99HYPNVw",130,98],["p","616221006","e8c2","SahMM5YTnaw",200,150],["q","616221006","e8c3","2GhZ5pjzo90",320,240],["r","616221006","e8c4","dDKvgsTDbWk",510,383]],"kid":"77831afc82434e135d74923fc52d81f6"}]',
			hash:'8376b2d5ddb5c3c3f83b9d51e525b839'
		};
		VK.api('photos.save', send, function(data) {
			if(data.response) {
				alert('saved')
			} else {
				alert('error');
				console.log(data);
			}
		});
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

