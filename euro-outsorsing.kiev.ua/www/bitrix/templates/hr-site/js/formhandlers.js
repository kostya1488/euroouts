$(document).ready( function(){


function afterSendMessageShow(){
	showOverlay();
	showAcenteredBlock2($('.js-after-send-message'));
	$('.js-after-send-message').show();
}

$('.js-call-back').on('click',function(event){
	event.preventDefault();
  showOverlay();
	showAcenteredBlock2($('.js-order_call-popup'));
	$('.js-order_call-popup').show();
});

    /* -------------------- Befin Open modal for select type of user ------------------- */
	
	type_of_user = (typeof($.cookie('type_of_user')) != 'undefined') ? parseInt($.cookie('type_of_user')) : '';
	
	if(type_of_user == '')
	{
		/*
        showOverlay();
        showAcenteredBlock2($('.js-select-type-user'));
        $('.js-select-type-user').show();
		*/
	}
	
	$('.type-user__btn').on('click', function()
	{
		user_type = $(this).attr('data-user-type');

		$.ajax({
			url: '/ajax.php' ,
			type: "POST",
			data: 'form_type=guest_type_select&guest_type=' + user_type,
			success: function (response)
			{
				$.cookie('type_of_user', user_type, { expires: 365, path: '/' });
				
				hideOverlay();
				
				$('.js-popup-form_container').hide();
				
				if(user_type == 'applicant') window.location.href = '/poisk-raboty/';
				
				console.log(response);
			}
		});
		
	});

    /* -------------------- End Open modal for select type of user --------------------- */
		
		
		
	function showAcenteredBlock2($block) {
		var $loading = $block;
		$loading.css("top", Math.max(0, ((window.innerHeight - $loading.outerHeight()) / 2)) + "px");
		$loading.css("left", Math.max(0, (($(window).width() - $loading.outerWidth()) / 2) + $(window).scrollLeft()) + "px");
	}

	function showAcenteredBlock($block) {
		var $loading = $block;
		$loading.css("top", Math.max(0, ((window.innerHeight - $loading.outerHeight()) / 2)) + "px");
		$loading.css("left", Math.max(0, (($(window).width() - $loading.outerWidth()) / 2) +
			$(window).scrollLeft()) + "px");
	}

	function showOverlay(){
			$('.overlay').show();
	}
		
	function hideOverlay(){
			$('.overlay').hide();
			$('.mfp-wrap').remove();
			$('.mfp-bg').remove();
			$('html').css("overflow", "auto");
	}

//****validatejs***/
//validationArray  fields {"form":'','toValidate':[{'field':'','message':''},]}
var validationArray = [
{"form":'#b-podbor-personala__form',
	'toValidate': [
	{'field':'name','message':'Заполните поле.'} ,
	{'field':'phone','message':'Заполните поле. '} 
	]
},
{"form":'#b-monitoring__form',
	'toValidate': [
	{'field':'name','message':'Заполните поле.'} ,
	{'field':'phone','message':'Заполните поле. '}
	]
},
{"form":'#b-get-info-form',
	'toValidate': [
	{'field':'name','message':'Заполните поле.'} ,
	{'field':'phone','message':'Заполните поле. '}
	]
},
{"form":'#b-hr-service-banner__left__form',
	'toValidate': [
	{'field':'name','message':'Заполните поле.'} ,
	{'field':'phone','message':'Заполните поле. '}
	]
},
{"form":'#order_call',
	'toValidate': [
	{'field':'name','message':'Заполните поле.'} ,
	{'field':'phone','message':'Заполните поле. '}
	]
},
{"form":'#b-personal-hr-department__form',
	'toValidate': [
	{'field':'name','message':'Заполните поле.'} ,
	{'field':'phone','message':'Заполните поле. '}
	]
},
{"form":'#b-call-back__form',
	'toValidate': [
	{'field':'name','message':'Заполните поле.'} ,
	{'field':'phone','message':'Заполните поле. '}
	]
},
{"form":'#order_raschet',
	'toValidate': [
	{'field':'email','message':'Заполните поле.'} ,
	{'field':'phone','message':'Заполните поле. '}
	]
}
];

(function labelHandler(blocks) {
if( Object.prototype.toString.call( blocks ) === '[object Array]' ) {
	$.each(blocks, function(index,value) {

	            var rulesObj    = {};
							var messagesObj = {};
	            for (var i = 0, l = value.toValidate.length; i < l; ++i) {
      
						  rulesObj[value.toValidate[i].field] = {
                        required: true,
                    };
										
					  	messagesObj[value.toValidate[i].field] = {
                        required: value.toValidate[i].message,
                    };		
							
              }
							
							$(value.form).validate({
                ignore: "",
                focusInvalid: true,
                rules: rulesObj,
                messages: messagesObj
            });	
		
  });
 
} else {
	return 'Wrong data type';
};

})(validationArray);	
	

//****validatejs***/

$(".js-form_submit").click(function (event) {

	event.preventDefault();

	var form    = $(this).closest('form');

	
	if($(this).hasClass('js-popup_form_submit')) {
		var container = $(this).closest('.js-popup-form_container');
	}

	//label actions
//	$(form).find('label').hide();

	//validation

	var valid = $(form).valid();

	if (valid) {
					
				 $.ajax({

						 url: '/ajax.php' ,
						 type: "POST",
						 data: $(form).serialize() + '&session=' + $('#sess-id-post').val(),
						 success: function (response) {
								console.log(response);
						 }
				 });	


				 
							$(form).find('input[type=text] ,textarea').each(function () {
									$(this).val('');
							});	


							if($(this).hasClass('js-popup_form_submit')) {
								$(container).hide();
								hideOverlay();
							};
							afterSendCheck({'id':$(form).attr('id')});

	
	}
});

function afterSendCheck(options) {

		var id = options.id;
		var aftersendArray = ['order_call', 'order_raschet', 'b-podbor-personala__form','b-monitoring__form','b-get-info-form','b-hr-service-banner__left__form','b-personal-hr-department__form','b-call-back__form'];

		for(var i = 0;i<aftersendArray.length;i++){
			if(aftersendArray[i] == id){afterSendMessageShow();}
		}

}


//*****closehandler*****//
	 $(".js-popup_form_close").on('click', function (event) {
       // $('.overlay').hide();
				hideOverlay();
				var container = $(this).closest('.js-popup-form_container');
				
				$(container).hide();
							
    });/**/
		
	
});