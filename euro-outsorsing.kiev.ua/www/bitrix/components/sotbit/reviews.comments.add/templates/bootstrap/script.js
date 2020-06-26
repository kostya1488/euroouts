$(document).ready(function(){
	//Подсчет количества символов в тексте
	$(document).on('keyup','#add_comment  #contentbox',function()
		{ 
			var MaxInput=$(this).attr('maxlength');
			var box=$(this).val();
			if(box.length <= MaxInput)
			{
				$(this).siblings('.count').children('.count-now').html(box.length);
			}
			else{}
			return false;
	});
	 
	 
	//Quote
	$(document).on('click','#comments-list .quote',
			function(){
				$(this).closest('.tabs-content').parent().find('.spoiler-comments-body').show('normal');
				var text=$(this).closest('.text').find('.text').html();
				$(this).closest('.tabs-content').parent().find('#comments-editor').find('iframe').contents().find('body').append('<blockquote class="bxhtmled-quote">'+text+'</blockquote>');
			    $('html, body').animate({
			        scrollTop: $(".spoiler-comments-body").offset().top
			    }, 2000);
				return false;
		});
	
	
	
	//Спойлер
	$(document).on('click','.spoiler',
		function(){
			$(this).siblings('.spoiler-comments-body').toggle('normal');
			return false;
	});


	$(document).on('click',"#add_comment > #reset-form",function() {
		$(this).closest('#add_comment').find('iframe').contents().find('body').empty();
		$(this).siblings('.count').children('.count-now').html(0);
		$(this).closest('#add_comment')[0].reset();
	});

	//обработчик форм
	$(function(){
		$(document).on('submit',"#add_comment, #auth_comment, #registration_comment",function() {
			var _this=$(this);
			var input = _this.serialize();
			var IdElement=_this.find("input[name='ID_ELEMENT']").attr('value');
			var Moderation=_this.find("input[name='MODERATION']").attr('value');
			var TEMPLATE=_this.find("input[name='TEMPLATE']").attr('value');
			var PRIMARY_COLOR=_this.find("input[name='PRIMARY_COLOR']").attr('value');
			var BUTTON_BACKGROUND=_this.find("input[name='BUTTON_BACKGROUND']").attr('value');
			var TEXTBOX_MAXLENGTH=_this.find("input[name='TEXTBOX_MAXLENGTH']").attr('value');
			var Action=_this.attr("id");//Определяет в какой ajax отправить данные
			var SiteDir=_this.find("input[name='SITE_DIR']").attr('value');
			if(typeof Action === "undefined")//завершаем если нет Action
				return;
			if(Action=='registration_comment')
			{
				var register=$("input[name='register_submit_button']").attr('value');//добавление значения кнопки для модуля
				var arparams=$("#registration_comment").attr("data-params");
				input=input+'&register_submit_button='+register;
				input=input+'&arparams='+arparams;
			}
			$.ajax({
				type: 'POST',
				url: SiteDir+'bitrix/components/sotbit/reviews.comments.add/ajax/'+Action+'.php',
				data: input,
				success: function(data){
					if(data.trim()=='SUCCESS')
					{
						if(Action=='auth_comment' || Action=='registration_comment')
						{
							location.reload();
						}
						else
						{
							$(_this)[0].reset();
							var MaxInput=$(_this).children('#contentbox').attr('maxlength');
							$(_this).find('.count-now').html(0);
							$(_this).closest('.item').find(".success").show();
							$(_this).closest('.spoiler-comments-body').toggle('normal');
							if(Moderation != 'Y')
							{
								var count=Number($('#comments').html().replace(/[^0-9]/gim,''));
								var newCount=count+1;
								var Html=$('#comments').html();
								$('#comments').html(Html.replace(count,newCount));
								BX.showWait();
								$.ajax({
									type: 'POST',
									url: '/bitrix/components/sotbit/reviews.comments.add/ajax/reload_comments.php',
									data: {IdElement:IdElement,TEMPLATE:TEMPLATE,PRIMARY_COLOR:PRIMARY_COLOR,BUTTON_BACKGROUND:BUTTON_BACKGROUND,TEXTBOX_MAXLENGTH:TEXTBOX_MAXLENGTH},
									success: function(data){

										$('#comments-body').find('.list').html(data);
										if($('#captcha-comments').html()!="")
										{
											var str = $('#captcha-ids-comments').html();
											var Ids = str.split("|");
											$('#captcha-ids-comments').html('');
											$.each(Ids, function( index, value ) {
												if(index==0)
													{
														grecaptcha.reset(
																value
														);
														$('#captcha-ids-comments').html(value);
													}
												else
													{

													}
												});
											
											$.each($('#comments-body [data-captcha-comment=\"Y\"]'), function (index, value) {
												var CapId = grecaptcha.render($(this).attr('id'), {
													 'sitekey' : $('#captcha-comments').html()
												});
												$('#captcha-ids-comments').append('|'+CapId);
											});
											
											
											
										}
										
										BX.closeWait();
									},
									error:  function (jqXHR, exception) {
									}
								});
							}
							else
							{
								if($('#captcha-comments').html()!="")
								{
									var str = $('#captcha-ids-comments').html();
									var Ids = str.split("|");
									$.each(Ids, function( index, value ) {
										grecaptcha.reset(
												value
										);
										});
								}
							}
							return false;
						}
					}
					else
					{
						
						if($('#captcha-comments').html()!="")
						{
							var str = $('#captcha-ids-comments').html();
							var Ids = str.split("|");
							$.each(Ids, function( index, value ) {
								grecaptcha.reset(
										value
								);
								});
						}
						
						
						if(Action=='auth_comment' || Action=='registration_comment')
						{
							$(_this).closest('.forms').find("#"+Action+"-check-error").html(data);
							$(_this).closest('.forms').find("#"+Action+"-check-error").show();
							if(Action=='registration_comment')
								change_captcha(_this,SiteDir);
						}
						else
						{
							$(_this).closest('.forms').find(".add-check-error").html(data);
							$(_this).closest('.forms').find(".add-check-error").show();
						}
					}
				},
				error:  function (jqXHR, exception) {
				}
			});
		});
	});
	function change_captcha(e,SiteDir)
	{
		$.ajax({
			type: 'POST',
			url: SiteDir+'bitrix/components/sotbit/reviews.comments.add/ajax/change_captcha.php',
			success: function(data){
				e.find("input[name='captcha_sid']").val(data);
				e.find("img").attr({"src":"/bitrix/tools/captcha.php?captcha_sid="+data});
			},
			error:  function(xhr, str){
				alert(xhr.responseCode);
			}
		});
	}
});
$(document).on('click', '#registration_comment .checkbox label', function() {
	$(this).addClass('checked');
	$(this).closest('.checkbox').find('input[type="checkbox"]').attr('checked', '');
	//return false;
});
$(document).on('click', '#registration_comment .checkbox label.checked', function() {
	$(this).removeClass('checked');
	$(this).closest('.checkbox').find('input[type="checkbox"]').removeAttr('checked', '');
	//return false;
});