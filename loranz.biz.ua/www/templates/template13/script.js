$(function() {
	var zoomImage = $('img#zoom');
	var image = $('#additional-images a');
	var zoomConfig = {
		gallery:'additional-images',
		cursor: 'zoom-in',
		zoomType: "inner",
		galleryActiveClass: 'active',
		responsive: true
	};

	$("#zoom").elevateZoom(zoomConfig);

	image.on('click', function(){
		var largeImage = $(this).attr('data-zoom-image');
		$('.thumbnail').attr('href', largeImage);
		// Remove old instance od EZ
		$('.zoomContainer').remove();
		zoomImage.removeData('elevateZoom');
		// Update source for images
		zoomImage.attr('src', $(this).data('image'));
		zoomImage.data('zoom-image', $(this).data('zoom-image'));
		// Reinitialize EZ
		zoomImage.elevateZoom(zoomConfig);
	});

	$(document).on('click', '.thumbnail', function () {
		$('.thumbnails').magnificPopup('open', 0);
		return false;
	});

	$('.thumbnails').magnificPopup({
		delegate: 'a.elevatezoom-gallery:has(img)',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-with-zoom',
		gallery: {
			enabled: true,
			//navigateByImgClick: true,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
		}
	});

	// ----------- match-height -----------
	$('.grid_wrap.match-height').matchHeight();

	$('.additional-images-slider').owlCarousel({
		loop: false,
		margin: 10,
		nav: true,
		dots: false,
		items:3,
    	navText: [
     	 	"<i class='fa fa-chevron-left'></i>",
			"<i class='fa fa-chevron-right'></i>"
		],
	});

	$('.stars').stars({
		inputType: "select", disableValue: false
	});

	$(".slider").slider({
		range: true,
		//step: 1000,
		slide: function( event, ui ) {
			$(this).prev().find("input[name$='_from']").val(ui.values[0]);
			$(this).prev().find("input[name$='_to']").val(ui.values[1]);
		},
		create: function(event, ui) {
			var min_value_original = parseInt($(this).prev().find("input[name$='_from_original']").val()),
			max_value_original = parseInt($(this).prev().find("input[name$='_to_original']").val()),
			min_value = parseInt($(this).prev().find("input[name$='_from']").val()),
			max_value = parseInt($(this).prev().find("input[name$='_to']").val());

			$(this).slider({
				min: min_value_original,
				max: max_value_original,
				values: [min_value, max_value]
			});
		}
	});

	//jQuery.Autocomplete selectors
	$('#search').autocomplete({
		serviceUrl: '/search/?autocomplete=1',
		delimiter: ',',
		maxHeight: 200,
		width: 300,
		deferRequestBy: 300,
		appendTo: '.top-search-form',
		onSelect: function (suggestion) {
			$(this).closest("form").submit();
		}
	});

	// Little cart
	var delay=500, setTimeoutConst;
	$('.little-cart').hover(function() {
		clearTimeout(setTimeoutConst );
		$(this).addClass('cart-active').find('.more-cart-info').stop().slideDown();
	}, function(){
		var littleCart = $(this);
		setTimeoutConst = setTimeout(function(){
			littleCart.removeClass('cart-active').find('.more-cart-info').stop().slideUp();
		}, delay);
		});

	$('#gallery').photobox('a',{ time:0 });

	$(window).scroll(function () {
		if ($(this).scrollTop() > 70) {
			$('.top_button').fadeIn();
		} else {
			$('.top_button').fadeOut();
		}
	});
	// scroll body to 0px on click
	$('.top_button').click(function () {
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		return false;
	});

	$(".modification-prices").select2();

	// Cart quantity
	$(".qty-inner .qty-up").on("click", function() {
		var jInput = $($(this).data('src')),
			oldValue = jInput.val();
		jInput.val(parseInt(oldValue) + 1);
	});
	$(".qty-inner .qty-down").on("click", function() {
		var jInput = $($(this).data('src')),
			oldValue = jInput.val();
		if (oldValue > 1) {
			jInput.val(parseInt(oldValue) - 1);
		}
	});

	// Функции без создания коллекции
	$.extend({
		addIntoCart: function(path, shop_item_id, count){
			$.clientRequest({
				path: path,
				data: {add: shop_item_id, count: count},
				callBack: $.addIntoCartCallback,
				context: $('#little-cart')
			});
			return false;
		},
		addIntoCartCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			$(this).replaceWith(data);
		},
		addCompare: function(path, shop_item_id, object){
			$(object).toggleClass('current');
			$.clientRequest({path: path + '?compare=' + shop_item_id, 'callBack': function(){
					$.loadingScreen('hide');
				}, context: $(object)});
			$('#compareButton').show();
			return false;
		},
		addFavorite: function(path, shop_item_id, object){
			$(object).toggleClass('favorite_current');
			$.clientRequest({path: path + '?favorite=' + shop_item_id, 'callBack': function(){
					$.loadingScreen('hide');
				}, context: $(object)});
			return false;
		},
		sendVote: function(id, vote, entity_type){
			$.clientRequest({path: '?id=' + id + '&vote=' + vote + '&entity_type=' + entity_type , 'callBack': $.sendVoteCallback});
			return false;
		},
		sendVoteCallback: function(data, status)
		{
			$.loadingScreen('hide');
			$('#' + data.entity_type + '_id_' + data.item).removeClass("up down");
			if (!data.delete_vote)
			{
				data.value == 1
				? $('#' + data.entity_type + '_id_' + data.item).addClass("up")
				: $('#' + data.entity_type + '_id_' + data.item).addClass("down");
			}

			$('#' + data.entity_type + '_rate_' + data.item).text(data.rate);
			$('#' + data.entity_type + '_likes_' + data.item).text(data.likes);
			$('#' + data.entity_type + '_dislikes_' + data.item).text(data.dislikes);
		},
		bootstrapAddIntoCart: function(path, shop_item_id, count){
			$.clientRequest({
				path: path + '?add=' + shop_item_id + '&count=' + count,
				'callBack': $.bootstrapAddIntoCartCallback,
				context: $('.little-cart')
			});
			return false;
		},
		bootstrapAddIntoCartCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			$(this).html(data);
		},
		subscribeMaillist: function(path, maillist_id, type){
			$.clientRequest({
				path: path + '?maillist_id=' + maillist_id + '&type=' + type,
				'callBack': $.subscribeMaillistCallback,
				context: $('#subscribed_' + maillist_id)
			});
			return false;
		},
		subscribeMaillistCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			$(this).removeClass('hidden').next().hide();
		},
		oneStepCheckout: function(path, shop_item_id, count)
		{
			$("div#oneStepCheckout").remove();

			$.clientRequest({
				path: path + '?oneStepCheckout&showDialog&shop_item_id=' + shop_item_id + '&count=' + count,
				'callBack': $.oneStepCheckoutCallback,
				context: ''
			});
			return false;
		},
		oneStepCheckoutCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			$("body").append(data.html);
			$("#oneStepCheckout").modal("show");
		},
		getOnestepDeliveryList: function(path, shop_item_id, jForm)
		{
			var shop_country_id = jForm.find("#shop_country_id").val(),
				shop_country_location_id = jForm.find("#shop_country_location_id").val(),
				shop_country_location_city_id = jForm.find("#shop_country_location_city_id").val(),
				shop_country_location_city_area_id = jForm.find("#shop_country_location_city_area_id").val();

			$.clientRequest({
				path: path + '?oneStepCheckout&showDelivery&shop_country_id=' + shop_country_id + '&shop_country_location_id=' + shop_country_location_id + '&shop_country_location_city_id=' + shop_country_location_city_id + '&shop_country_location_city_area_id=' + shop_country_location_city_area_id + '&shop_item_id=' + shop_item_id,
				'callBack': $.getOnestepDeliveryListCallback,
				context: jForm.find("#shop_delivery_condition_id")
			});
		},
		getOnestepDeliveryListCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			$("#shop_delivery_condition_id").empty();

			$.each(data.delivery, function(key, object) {
				$('#shop_delivery_condition_id').append('<option value=' + object.shop_delivery_condition_id + '>' + object.name + '</option>');
			});
		},
		getOnestepPaymentSystemList: function(path, jForm)
		{
			var shop_delivery_condition_id = jForm.find("#shop_delivery_condition_id").val();

			$.clientRequest({
				path: path + '?oneStepCheckout&showPaymentSystem&shop_delivery_condition_id=' + shop_delivery_condition_id,
				'callBack': $.getOnestepPaymentSystemListCallback,
				context: jForm.find("#shop_payment_system_id")
			});
		},
		getOnestepPaymentSystemListCallback: function(data, status, jqXHR)
		{
			$.loadingScreen('hide');
			$("#shop_payment_system_id").empty();
			$.each(data.payment_systems, function(key, object) {
				$('#shop_payment_system_id').append('<option value=' + object.id + '>' + object.name + '</option>');
			});
		},
		changePrice: function(object, item_id)
		{
			var jOption = $(object).find('option:selected'),
				id = jOption.val(),
				price = jOption.data('price');

			// Подмена для корзины
			$('button#cart').data('item-id', id);

			// Подмена для быстрого заказа
			$('button#fast_order').data('item-id', id).data('target', '#oneStepCheckout' + id);

			// Подмена для цены
			$('div#item-' + item_id + ' .item-price').text(price);
		}
	});

	// Личные сообщения
	$.fn.messageTopicsHostCMS = function(settings) {
		// Настройки
		settings = $.extend({
			timeout:					10000, // Таймаут обновлений
			data:						'.messages-data', // блок с данными переписки для обновления
			url:						'#url', // значение URL
			page:						'#page', // значение total
			message_field:				'textarea', // поле ввода сообщения
			page_link:					'.page_link', // ссылки на страницы
			keyToSend:					13 // Отправка сообщения
		}, settings);

		var Obj = $.extend({
				_url:			this.find(settings.url).text(),
				_page:			parseInt(this.find(settings.page).text()) + 1,
				oData:			this.find(settings.data),
				oForm:			this.find('form'),
				oField:		this.find(settings.message_field),	// поле ввода сообщения
				oPage:			this.find(settings.page_link),	// ссылки на страницы
				oTemp:			{} // блок временных данных
			}, this);

		function _start() {
			if (Obj.length == 1) {
				// обновление данных по таймауту
				setInterval(_ajaxLoad, settings.timeout);

				Obj.oField.keydown(function(e) {
					if (e.ctrlKey && e.keyCode == settings.keyToSend) Obj.oForm.submit();
				});

				// отправка формы по Ctrl+Enter
				Obj.oField.keydown(function(e) {
					if (e.ctrlKey && e.keyCode == settings.keyToSend) Obj.oForm.submit();
				});

				// отправка сообщения из формы
				Obj.oForm.submit(function() {
					if (Obj.oField.val().trim().length) {
						_ajaxLoad({form: Obj.oForm.serialize()});
						Obj.oForm.find(':input:not([type=submit],[type=button])').each(function(){$(this).val('')});
					}
					return false;
				});
			}
			return false;
		}

		// Ajax запрос
		function _ajaxLoad(data) {
			if (!data) data = {};
			form = data.form ? '&' + data.form: '';

			return $.ajax({
				url: Obj._url + 'page-' + Obj._page + '/',
				type: 'POST',
				data: 'ajaxLoad=1' + form,
				dataType: 'json',
				success: function (ajaxData) {
					//Obj.oData.html($(ajaxData.content).find(settings.data).html());
					Obj.oData.html(ajaxData.content);
				},
				error: function (){return false}
			});
		}
		return this.ready(_start);
	};

	$.fn.messagesHostCMS = function(settings) {
	//jQuery.extend({
		//messagesHostCMS: function(settings){
			// Настройки
			settings = $.extend({
				// chat_height :					465, // Высота чата переписки
				timeout :						10000, // Таймаут обновлений
				load_messages :					'#load_messages', // кнопка подгрузки старых сообщений
				chat_window :					'#chat_window', // окно чата переписки
				messages :						'#messages', // список сообщений чата
				prefix_message_id :				'msg_', // префикс идентификатора сообщения в DOM
				message_field :					'textarea', // поле ввода сообщения
				url :							'#url', // значение URL
				limit :							'#limit', // значение limit
				total :							'#total', // значение total
				topic_id :						'#topic_id', // значение message_topic id
				keyToSend :						13 // Отправка сообщения
			}, settings);

		var Obj = $.extend({
				_activity :		1,
				_autoscroll :	1,
				_url :				this.find(settings.url).text(),
				_limit :			this.find(settings.limit).text(),
				_total :			this.find(settings.total).text(),
				_topic_id :		this.find(settings.topic_id).text(),
				_count_msg :	0, // количество сообщений в чате
				oLoad :				this.find(settings.load_messages), // кнопка подгрузки старых сообщений
				oWindow :			this.find(settings.chat_window), // окно чата переписки
				oMessages :		this.find(settings.messages), // список сообщений чата
				oField :			this.find(settings.message_field),	// поле ввода сообщения
				oForm :				this.find('form'),
				oTemp :				{} // блок временных данных
			}, this);

		function _start() {
			if (Obj.length == 1) {
				_recountChat();

				// обновление данных по таймауту
				setInterval(_ajaxLoad, settings.timeout);

				// проверка активности пользователя
				$("body").mousemove(function(){
					Obj._activity = Obj._autoscroll == 1 ? 1 : 0;
				});

				// отправка формы по Ctrl+Enter
				Obj.oField.keydown(function(e) {
					if (e.ctrlKey && e.keyCode == settings.keyToSend) Obj.oForm.submit();
				});

				/*Obj.oWindow.scroll(function(){
					Obj._autoscroll = Obj.oMessages.height() == Obj.oWindow.scrollTop() + settings.chat_height ? 1 : 0;
				});*/

				// отправка сообщения из формы
				Obj.oForm.submit(function() {
					if (Obj.oField.val().trim().length) {
						Obj._autoscroll = 1;
						Obj._activity = 1;
						_ajaxLoad({form : Obj.oForm.serialize()});
						Obj.oField.val('');
					}
					return false;
				});

				Obj.oLoad.click(function(){
					_ajaxLoad({
						preload : true,
						page : 'page-' + parseInt(Obj._count_msg / Obj._limit + 1)
					})
				});
			}
			return false;
		}

		// Ajax запрос
		function _ajaxLoad(data) {
			if (!data) data = {};
			page = data.page ? data.page + '/' : '';
			form = data.form ? '&' + data.form : '';
			return $.ajax({
				url : Obj._url + Obj._topic_id + '/' + page,
				type : 'POST',
				data : 'ajaxLoad=1&activity=' + Obj._activity + form,
				dataType : 'json',
				success :	function (ajaxData) {
					Obj.oTemp = $(ajaxData.content);

					if (!data.preload && Obj._count_msg > Obj._limit)
					{
						Obj.oTemp.find(':first[id^='+settings.prefix_message_id+']', settings.messages).remove();
					}

					// замена сообщений чата
					Obj.oTemp.find('li[id^='+settings.prefix_message_id+']', settings.messages).each(function(){
						oMsg = Obj.oMessages.find('[id="' + $(this).attr('id') +'"]');
						if (oMsg.length == 1) oMsg.replaceWith($(this));
					});

					newMessages = Obj.oTemp.find('li[id ^= ' + settings.prefix_message_id + ']', settings.messages);

					if (newMessages.length) {
						if (data.preload){
							Obj.oMessages.prepend(newMessages);
							Obj._autoscroll = 0;
							Obj.oWindow.scrollTop(0);
						}
						else {
							Obj.oMessages.append(newMessages);
						}
						_recountChat();
					}
				},
				error: function (){return false}
			});
		}

		function _recountChat() {
			if (Obj.oWindow.height() > settings.chat_height) Obj.oWindow.height(settings.chat_height + 'px');
			if (Obj._autoscroll == 1) Obj.oWindow.scrollTop(Obj.oMessages.height() - settings.chat_height);
			if (Obj.oTemp.length) Obj._total = Obj.oTemp.find(settings.total).text();
			Obj._count_msg = Obj.oMessages.find('> *[id^='+settings.prefix_message_id+']').length;
			if (Obj._count_msg >= Obj._total && Obj.oLoad.is(':visible')) Obj.oLoad.toggle();
			Obj._activity = 0;
		}

		return this.ready(_start);
	};
});