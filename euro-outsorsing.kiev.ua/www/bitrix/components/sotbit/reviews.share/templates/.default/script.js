$(document).ready(function(){
	

	$(document).on('click','#sotbit_reviews_share .share_link', function(){ 
		
		var title=$(this).parent().find('.share-link-title').html();
		var text=$(this).data('url');
		$('body').append('<div class="reviews-sharelink-popup" id="reviews-sharelink-popup"><p class="title">'+title+'</p><span id="modal_close"><i class="fa fa-times" aria-hidden="true"></i></span><p class="text">'+text+'</p></div><div  id="reviews-sharelink-overlay"></div>');
		
		$('#reviews-sharelink-overlay').fadeIn(400, 
			 	function(){
						var html = document.documentElement;
						var w=$('#reviews-sharelink-popup').outerWidth(true);
						var h=parseInt($('#reviews-sharelink-popup').outerHeight(true));
			  			var left = (html.clientWidth/2)-(w/2);
			  			var top = (html.clientHeight/2)-(h/2);
			  					
					$('#reviews-sharelink-popup') 
						.css('display', 'block') 
						.animate({opacity: 1, top: top, left:left}, 200); 
			});
	});
	$(document).on('click','#reviews-sharelink-popup #modal_close, #reviews-sharelink-overlay', function(){ 
		$('#reviews-sharelink-popup') 
			.animate({opacity: 0, top: '45%'}, 200,  
				function(){ 
					$(this).css('display', 'none'); 
					$('#reviews-sharelink-overlay').fadeOut(400,function(){
						$("#reviews-sharelink-overlay").remove();
						$("#reviews-sharelink-popup").remove();
					}); 
					

					
				});
	});
});