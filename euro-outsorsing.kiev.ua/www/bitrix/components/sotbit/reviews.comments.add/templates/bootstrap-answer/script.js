$(document).ready(function(){
	//Quote 
	$(document).on('click','.add-comments .quote',
			function(){
				$(this).closest('.add-comments').find('.spoiler-comments-body').show('normal');
				var text=$(this).closest('.item').find('.text').find('.text').html();
				var Scroll = $(this).closest('.add-comments').find('.spoiler-comments-body');
				$(this).closest('.add-comments').find('.spoiler-comments-body').find('#comments-editor').find('iframe').contents().find('body').append('<blockquote class="bxhtmled-quote">'+text+'</blockquote>');
			    $('html, body').animate({
			        scrollTop: Scroll.offset().top
			    }, 2000);
				return false;
		});
});