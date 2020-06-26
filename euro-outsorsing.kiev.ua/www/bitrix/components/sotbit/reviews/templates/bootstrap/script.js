$(document).ready(function(){
 
	//Load after click
	(function($) {
		$(function() {
			$(document).on('click', 'ul.tabs-caption > li:not(.active)', function() {
				$(this)
				.addClass('active').siblings().removeClass('active');
				$('div.tabs-content').removeClass('active').eq($(this).index()).addClass('active');
				var ActiveTab = $('ul.tabs-caption').find('li').eq($(this).index());
				if($(this).attr('data-ajax')=='Y'){
					$(this).attr('data-ajax','N');
					Active=$(this).attr('id');
					IdElement=$("ul.tabs-caption").data('id-element');
					SiteDir=$("ul.tabs-caption").data('site-dir');
					LoadItems(IdElement,Active,SiteDir);
				}
			});

		});
	})(jQuery);

	//Load content
	function LoadItems(IdElement,Active,SiteDir)
	{
		var data=$('#ReviewsParams').html(); //Params of component
		//BX.showWait();
		$.ajax({
			url: SiteDir+'bitrix/components/sotbit/reviews/ajax/content-loader.php',
			data: {data:data,active:Active},
			type : "POST",
			success: function(html) { 

				$('#'+Active+'-body').html(html);
				
				//shows
				if(Active=='reviews')
				{
					var SiteDir = $('#idsReviews').data('site-dir');
					var Ids=$('#idsReviews').html();
					$.ajax({
						type: 'POST',
						url: SiteDir+'bitrix/components/sotbit/reviews.reviews.list/ajax/shows.php',
						data: {Ids:Ids},
						success: function(data){
						},
						error:  function (jqXHR, exception) {
						}
					});
				}
				if(Active=='comments')
				{
					var SiteDir = $('#idsComments').data('site-dir');
					var Ids=$('#idsComments').html();
					$.ajax({
						type: 'POST',
						url: SiteDir+'bitrix/components/sotbit/reviews.comments.list/ajax/shows.php',
						data: {Ids:Ids},
						success: function(data){
						},
						error:  function (jqXHR, exception) {
						}
					});
				}
				
				
				if(Active=='questions')
				{
					var SiteDir = $('#idsQuestions').data('site-dir');
					var Ids=$('#idsQuestions').html();
					$.ajax({
						type: 'POST',
						url: SiteDir+'bitrix/components/sotbit/reviews.questions.list/ajax/shows.php',
						data: {Ids:Ids},
						success: function(data){
						},
						error:  function (jqXHR, exception) {
						}
					});
				}
				
				
				//Recaptcha
				if($('#captcha-'+Active).html()!="")
				{
		
					if(Active=='comments')
					{
						if($('#recaptcha-comment-0').length>0){
				    	  var CapId = grecaptcha.render('recaptcha-comment-0', {
				    	   				'sitekey' : $('#captcha-'+Active).html()
				    	   			});
				    	  $('#captcha-ids-comments').html(CapId);
						}
				    	   			$.each($('#comments-body [data-captcha-comment="Y"]'), function (index, value) {
				    	   				var CapId = grecaptcha.render($(this).attr('id'), {
					    	   				'sitekey' : $('#captcha-'+Active).html()
					    	   			});
				    	   				$('#captcha-ids-comments').append('|'+CapId);
				    	   			});
				    	   			
							}
							if(Active=='reviews')
							{
								if($('#add_review [data-captcha-review=\"Y\"]').attr('id')!==undefined){
								var CapId = grecaptcha.render($('[data-captcha-reviews="Y"]').attr('id'), {
			    	   				'sitekey' : $('#captcha-reviews').html()
			    	   			});
								$('#captcha-ids-reviews').html(CapId);
								}
							}
							if(Active=='questions')
							{
								
								if($('#captcha-question-0').length>0){
										var CapId = grecaptcha.render($('[data-captcha-question="Y"]').attr('id'), {
											'sitekey' : $('#captcha-questions').html()
										});
										$('#captcha-ids-questions').html(CapId);
								}

							}

				}

				
				if(Active=='reviews'){
					$(".image-review").colorbox();
				}
				//BX.closeWait();
			}
		});
	}
});
