	$(document).ready(function(){
	$(document).on('click','#current-option-select-rating',
		function(){
			customOptionsBlock = $("#custom-options-select-rating");
			
			if (customOptionsBlock.is(":hidden"))
			{
				$("#select-rating").attr("class","select-rating-open");
				$("#current-option-select-rating").find('.fa-angle-down').removeClass("fa-angle-down").addClass("fa-angle-up");
			}
		
			
			
			$("#custom-options-select-rating").slideToggle('normal',function(){		
				
			if (customOptionsBlock.is(":hidden")) {
				//$("#custom-options-select-rating").show();
				$("#select-rating").attr("class","select-rating-close");
				$("#current-option-select-rating").find('.fa-angle-up').removeClass("fa-angle-up").addClass("fa-angle-down");
			}
			else {
				//$("#custom-options-select-rating").hide();
				//$("#select-rating").attr("class","select-rating-open");
				
			}});

			
	});

	$(document).on('click','#custom-options-select-rating li',
		function(){
			oldValue=$("#current-option-select-rating").attr("data-value");
			choosenValue = $(this).attr("data-value");
			$("#current-option-select-rating span").html($(this).html());
			$("#current-option-select-rating").attr("data-value", choosenValue);
			//$("#custom-options-select-rating").hide();
			
			
			
			$("#custom-options-select-rating").slideToggle('normal',function(){
				$("#select-rating").attr("class","select-rating-close");
				$("#current-option-select-rating").find('.fa-angle-up').removeClass("fa-angle-up").addClass("fa-angle-down");
			});
			
			if(oldValue!=choosenValue)
			{
				ReloadReviews(1);
			}
	});

	$(document).on('change','#filter-images',
		function(){
			if($(this).attr("data-value")=="Y")
				{
					$(this).attr("data-value","N");
					$(this).siblings('label').addClass("label-active-filter");
				}
			else
				{
					$(this).attr("data-value","Y");
					$(this).siblings('label').removeClass("label-active-filter");
				}
			
			
			
			
			ReloadReviews(1);
	});


	$(document).on('click','#current-option-select-sort',
		function(){
			customOptionsBlock = $("#custom-options-select-sort");
			
			if (customOptionsBlock.is(":hidden"))
			{
				$("#select-sort").attr("class","select-rating-open");
				$("#current-option-select-sort").find('.fa-angle-down').removeClass("fa-angle-down").addClass("fa-angle-up");
			}
			
			$("#custom-options-select-sort").slideToggle('normal',function(){			
			if (customOptionsBlock.is(":hidden")) {
				//$("#custom-options-select-sort").show();
				$("#select-sort").attr("class","select-rating-close");
				$("#current-option-select-sort").find('.fa-angle-up').removeClass("fa-angle-up").addClass("fa-angle-down");
			}
			else {
				//$("#custom-options-select-sort").hide();
				//$("#select-sort").attr("class","select-rating-open");
				
			}});
	});

	$(document).on('click','#custom-options-select-sort li',
		function(){
			oldOrder = $("#current-option-select-sort").attr("data-sort-order");
			oldBy = $("#current-option-select-sort").attr("data-sort-by");
			choosenOrder = $(this).attr("data-sort-order");
			choosenBy = $(this).attr("data-sort-by");
			$("#current-option-select-sort span").text($(this).text());
			$("#current-option-select-sort").attr("data-sort-order", choosenOrder);
			$("#current-option-select-sort").attr("data-sort-by", choosenBy);
			//$("#custom-options-select-sort").hide();
			$("#custom-options-select-sort").slideToggle('normal',function(){
				$("#select-sort").attr("class","select-rating-close");
				$("#current-option-select-sort").find('.fa-angle-up').removeClass("fa-angle-up").addClass("fa-angle-down");
			});
			
			if(oldOrder!=choosenOrder || oldBy!=choosenBy)
			{
				ReloadReviews(1);
			}
	});

	/*function BuildPagination(MaxPage,CurrentPage)
	{
		if(MaxPage<=1)
		{
			$("#filter-pagination").hide();
		}
		else
		{
			$("#filter-pagination").show();
		}
		DataCntLeftPgn=parseInt($("#filter-pagination").attr("data-cnt-left-pgn"));
		DataCntRightPgn=parseInt($("#filter-pagination").attr("data-cnt-right-pgn"));
		OldCurrentPage=parseInt($("#filter-pagination button.current").attr("data-number"));
		if(CurrentPage+(DataCntLeftPgn)>MaxPage-DataCntRightPgn)
			{
				$("#filter-pagination .dots").hide();
			}
			else
			{
				$("#filter-pagination .dots").show();
			}
			$("#filter-pagination button:not(.prev, .first, .next, .last, .dots)").each(function(index,value) {
					OldLinkValueDot=parseInt($(this).attr("data-number"));
					if(CurrentPage+DataCntLeftPgn+DataCntRightPgn-1>=MaxPage)//не нужны точки
					{
						Start=MaxPage-DataCntLeftPgn-DataCntRightPgn+1;
						if(Start<1)
							Start=1;
						$(this).attr("data-number",Start+index);
						$(this).text(Start+index);
					}
					else
					{
						if(index<DataCntLeftPgn)//до точек
						{
							$(this).attr("data-number",CurrentPage+index);
							$(this).text(CurrentPage+index);
						}
						if(index>=DataCntLeftPgn)//после точек
						{
							$(this).attr("data-number",MaxPage-(DataCntRightPgn+1)+(index-DataCntLeftPgn));
							$(this).text(MaxPage-(DataCntRightPgn-1)+(index-DataCntLeftPgn));
						}
					}
					if($(this).attr("data-number")>MaxPage)
					{
						$(this).hide();
					}
					else
					{
						$(this).show();
					}						
				});
			$("#filter-pagination .dots").attr("data-number",CurrentPage+DataCntLeftPgn);
				
			if(CurrentPage==1)
			{
				$("#filter-pagination .left-arrows").hide();
			}
			else
			{
				$("#filter-pagination .left-arrows").show();
			}
			if(CurrentPage>=MaxPage)
			{
				$("#filter-pagination .right-arrows").hide();
			}
			else
			{
				$("#filter-pagination .right-arrows").show();
			}
			$("#filter-pagination .current").removeClass('current');
			$("#filter-pagination").find("[data-number='"+CurrentPage+"']").not('.prev, .next, .first, .last, .dots').addClass('current');
			$("#filter-pagination .prev").attr("data-number",CurrentPage-1);
			$("#filter-pagination .next").attr("data-number",CurrentPage+1);
	}*/
});