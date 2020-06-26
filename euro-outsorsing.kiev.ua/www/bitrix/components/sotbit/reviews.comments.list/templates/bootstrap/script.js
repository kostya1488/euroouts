$(document).ready(function(){

	var FirstAjax=$('.tabs-caption #comments').data('ajax');
	
	if(FirstAjax!='Y')
	{
	var SiteDir = $('#idsComments').attr("data-site-dir");
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
	
	
	
	$(document).on('click','.answer',
		function(){
		$(this).closest('.item').find('.spoiler-comments-body').toggle('normal');
			return false;
	});
	
	//Actions
	$(document).on('click','#comments-body .actions',
			function(){
				$(this).closest('.menu').toggleClass('open');
		});
	$(document).on('click','#comments-body .menu ul li',
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
					url: SiteDir+'bitrix/components/sotbit/reviews.comments.list/ajax/'+Action+'.php',
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


	$(document).on('click','#filter-pagination-comments button:not(.current)',
		function(){
			MaxPage=parseInt($("#filter-pagination-comments .last").attr("data-number"));
			CurrentPage=parseInt($(this).attr("data-number"));
			ReloadComments(CurrentPage);
		});
	
});
function ReloadComments(FilterPage)
{
	Url = $("#filter-pagination-comments").data("url");
	IdElement = $("#filter-pagination-comments").attr("data-id-element");
	SiteDir = $("#filter-pagination-comments").attr("data-site-dir");
	TEMPLATE = $("#filter-pagination-comments").attr("data-template");
	PrimaryColor=$("#filter-pagination-comments").attr("data-primary-color");
	DateFormat=$("#filter-pagination-comments").attr("data-date-format");
	BX.showWait();
	$.ajax({
		type: 'POST',
		url: SiteDir+'bitrix/components/sotbit/reviews.comments.list/ajax/reload_comments.php',
		data: {IdElement:IdElement,TEMPLATE:TEMPLATE,FilterPage:FilterPage,PrimaryColor:PrimaryColor,DateFormat:DateFormat,Url:Url},
		success: function(data){
			$('#comments-list').html(data);
			BX.closeWait();
		},
		error:  function (jqXHR, exception) {
		}
	});
}