$(document).ready(function(){
	var FirstAjax=$('.tabs-caption #reviews').data('ajax');
	if(FirstAjax!='Y')
	{
	var SiteDir = $('#idsReviews').attr("data-site-dir");
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
	
	
	
	
	//Quote
	$(document).on('click','#reviews-list .quote',
			function(){
				$(this).closest('.tabs-content').parent().find('.spoiler-reviews-body').show('normal');
				var text=$(this).closest('.text').find('.text').html();
				$(this).closest('.tabs-content').parent().find('#review-editor').find('iframe').contents().find('body').append('<blockquote class="bxhtmled-quote">'+text+'</blockquote>');
			    $('html, body').animate({
			        scrollTop: $(".review-add-rating-title1").offset().top
			    }, 2000);
				return false;
		});
	//Actions
	$(document).on('click','#reviews-list .actions',
			function(){
				$(this).closest('.menu').toggleClass('open');
		});
	$(document).on('click','#reviews-list .menu ul li',
			function(){
				
				var _this= $(this);
				var Action=$(this).data('action');
				if(Action=='ban')
					var r = confirm($(_this).closest('.menu').find('#ban-confirm-text').html());
				
				if (r != true)
					return;
				var ID=$(this).closest('.item').data('id');
				var SiteDir=$(this).closest('.item').data('site-dir');
				$.ajax({
					type: 'POST',
					url: SiteDir+'bitrix/components/sotbit/reviews.reviews.list/ajax/'+Action+'.php',
					data: {ID:ID},
					success: function(data) {
						var Top=_this.position().top;
						Top-=9;
						var Left=_this.position().left;
						Left-=26;
						if(data.trim()=='SUCCESS')
						{

							if(Action=='ban')
							{
								$(_this).closest('.menu').find('.ban-message-success').css({'top':Top,'left':Left,'z-index':2});
								$(_this).closest('.menu').find('.ban-message-success').animate({
									    opacity: 1,
									  }, 500, function() {
										  
										  setTimeout(function() {
											  $(_this).closest('.menu').find('.ban-message-success').animate({
												    opacity: 0,
												  }, 500, function(){
													  $(_this).closest('.menu').find('.ban-message-success').css({'top':'25px','left':'0','z-index':0});
												  });
											  }, 2000);
									  });
							}
						}
						else
						{
							if(Action=='ban')
							{
								$(_this).closest('.menu').find('.ban-message-error').css({'top':Top,'left':Left,'z-index':2});
								$(_this).closest('.menu').find('.ban-message-error').animate({
									    opacity: 1,
									  }, 500, function() {
										  
										  setTimeout(function() {
											  $(_this).closest('.menu').find('.ban-message-error').animate({
												    opacity: 0,
												  }, 500, function(){
													  $(_this).closest('.menu').find('.ban-message-error').css({'top':'25px','left':'0','z-index':0});
												  });
											  }, 1000);
									  });
							}
						}
					},
					error:  function(xhr, str){
						alert(xhr.responseCode);
					}
				});
	});
	
	
	
	
	//Нажат Like
	$(document).on('click', '.yes',
		function(){
			$(this).removeClass("yes");
			$(this).addClass("voted-yes");
			var Dislike= $(this).siblings(".no");
			Dislike.removeClass("no");
			Dislike.addClass("voted-no");
			var Parent = $(this).closest('.item').data('id');
			var SiteDir=$(this).closest('.item').data('site-dir');
			var Like = $(this).siblings(".yescnt");
			Likes=parseInt(Like.html());
			$.ajax({
				type: 'POST',
				url: SiteDir+'bitrix/components/sotbit/reviews.reviews.list/ajax/likes.php',
				data: {action:'LIKES',id:Parent,Likes:Likes},
				success: function(data) {
					Likes=Likes+1;
					Like.html(Likes);

				},
				error:  function(xhr, str){
					alert(xhr.responseCode);
				}
			});
	});
	//Нажат Dislike
	$(document).on('click', '.no',
		function(){
			$(this).removeClass("no");
			$(this).addClass("voted-no");
			var Dislike= $(this).siblings(".yes");
			Dislike.removeClass("yes");
			Dislike.addClass("voted-yes");
			var Parent = $(this).closest('.item').data('id');
			var SiteDir=$(this).closest('.item').data('site-dir');
			var Like = $(this).siblings(".nocnt");
			Likes=parseInt(Like.html());
			$.ajax({
				type: 'POST',
				url: SiteDir+'bitrix/components/sotbit/reviews.reviews.list/ajax/likes.php',
				data: {action:'DISLIKES',id:Parent,Likes:Likes},
				success: function(data) {
					Likes=Likes+1;
					Like.html(Likes);
				},
				error:  function(xhr, str){
					alert(xhr.responseCode);
				}
			});
	}); 
	$(document).on('click','#filter-pagination button:not(.current)',
		function(){
			MaxPage=parseInt($("#filter-pagination .last").attr("data-number"));
			CurrentPage=parseInt($(this).attr("data-number"));
			ReloadReviews(CurrentPage);
	});
});
	function ReloadReviews(FilterPage)
	{
		Url = $("#filter").data("url");
		IdElement = $("#filter").attr("data-id-element");
		MAX_RATING = $("#filter").attr("data-max-rating");
		SiteDir = $("#filter").attr("data-site-dir");
		TEMPLATE = $("#filter").attr("data-template");
		FilterRating=$("#current-option-select-rating").attr("data-value");
		FilterImages=$("#filter-images").attr("data-value");
		FilterSortBy=$("#current-option-select-sort").attr("data-sort-by");
		FilterSortOrder=$("#current-option-select-sort").attr("data-sort-order");
		PrimaryColor=$("#reviews-list").attr("data-primary-color");
		DateFormat=$("#reviews-list").attr("data-date-format");
		BX.showWait();
		$.ajax({
			type: 'POST',
			url: SiteDir+'bitrix/components/sotbit/reviews.reviews.list/ajax/reload_reviews.php',
			data: {IdElement:IdElement,MAX_RATING:MAX_RATING,TEMPLATE:TEMPLATE,FilterRating:FilterRating,FilterImages:FilterImages,FilterSortOrder:FilterSortOrder,FilterSortBy:FilterSortBy,FilterPage:FilterPage,PrimaryColor:PrimaryColor,DateFormat:DateFormat,Url:Url},
			success: function(data){
				$('#reviews-list').html(data);
				var SiteDir = $("#filter").attr("data-site-dir");
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
				BX.closeWait();
			},
			error:  function (jqXHR, exception) {
			}
		});
	}