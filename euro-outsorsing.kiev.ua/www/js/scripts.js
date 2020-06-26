svg4everybody(); // support inline svg
// owl carousel

if ($('.partners__slider').length) {
    $('.partners__slider').owlCarousel({
        items: 1,
        nav: true,
        navText: ['<svg class="icon icon-arrow-bold"><use xlink:href="/img/icons.svg#icon-arrow-bold"/></svg>',
            '<svg class="icon icon-arrow-bold"><use xlink:href="/img/icons.svg#icon-arrow-bold"/></svg>'
        ],
        loop: true,
        dots: false,
        margin: 0,
        responsive: {
            0: {
                items: 1
            },
            450: {
                items: 2
            },
            600: {
                items: 3
            },
            992: {
                items: 4
            },
            1200: {
                items: 5
            }
        }
    });
}

if ($('.reviews__slider').length) {
    $('.reviews__slider').owlCarousel({
        items: 1,
        nav: true,
        navText: ['<svg class="icon icon-arrow"><use xlink:href="img/icons.svg#icon-arrow"/></svg>',
            '<svg class="icon icon-arrow"><use xlink:href="img/icons.svg#icon-arrow"/></svg>'
        ],
        loop: true,
        dots: false,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 3,
                margin: 35
            }
        }
    });
}

if ($('.specialists__slider').length) {
    $('.specialists__slider').owlCarousel({
        items: 1,
        nav: true,
        loop: false,
        dots: false,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 3,
                margin: 20
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });
}

// dublicate contact
if ($(".top-banner--contacts").length) {
    $(".branches").after($(' .block-form--call-back').clone());
    $(".branches").next().addClass("block-form--mobile");
}


// select
$('.select').styler();

// magnificPopup
$('.open-popup-link').magnificPopup({
    type: 'inline',
    midClick: true,
    removalDelay: 300,
    callbacks: {
        beforeOpen: function() {
            this.st.mainClass = this.st.el.attr('data-effect');
        }
    }
});
$('.open-popup-image').magnificPopup({
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    gallery: {
        enabled: true,
        navigateByImgClick: true,
        preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
    },
    removalDelay: 300,
    callbacks: {
        beforeOpen: function() {
            this.st.mainClass = this.st.el.attr('data-effect');
        }
    }
});
$('.open-popup-img').magnificPopup({
    type: 'image',
    tLoading: 'Loading image #%curr%...',
    removalDelay: 300,
    callbacks: {
        beforeOpen: function() {
            this.st.mainClass = this.st.el.attr('data-effect');
        }
    }
});

// toggle menu
function changeBottomMenu() {
    var menu = $('.menu');
    menu.slideToggle(300, function() {
        if (menu.css('display') === 'none') menu.removeAttr("style");
    });
}

function closeBottomMenu() {
    var menu = $('.menu');
    menu.removeAttr("style");
    $('.btn-toggle-menu').removeClass('active');
}
$('.btn-toggle-menu').on('click', function(e) {
    e.preventDefault();
    $(this).toggleClass("active");

    changeBottomMenu();

});

// toggle menu mobile
$('.btn-toggle').on('click', function(e) {
    e.preventDefault();
    $("body").append('<div class="bg-overlay"></div>');
    $("body").addClass('open-mobile-menu');
});

$("body").on('click', ".bg-overlay, .btn-close", function(e) {

    $('.bg-overlay').remove();
    $("body").removeClass('open-mobile-menu');
    $("body").removeClass('open-search');

});

// toggle search
$('.btn-toggle-search').on('click', function(e) {
    e.preventDefault();
    $("body").append('<div class="bg-overlay"></div>');
    $("body").addClass('open-search');
});


// click document

$(document).on('click', function(e) {

    var menu = '.menu',
        menuBtnToggle = '.btn-toggle-menu';
    if (!$(menu).is(e.target) &&
        $(e.target).closest(menu).length === 0 &&
        !$(menuBtnToggle).is(e.target) &&
        $(e.target).closest(menuBtnToggle).length === 0) {

        closeBottomMenu();
    }

});

// click addition form

$(".request-form__btn-more").on('click', function(e) {

    var block = $('.request-form__addition');

    block.show();
    $(this).remove();

});



// window resize
function windowSize() {
    var w = $(window).width();
    if (w <= 767) {

    } else {
        closeBottomMenu();
    }
}
windowSize();
$(window).on('resize', windowSize);

// window scroll
/*
$(window).on("load scroll", function(){
	var windowScroll = $(window).scrollTop(),
		blockOffset = $(".header__block").offset().top;
		
	if (windowScroll >= blockOffset){
		$(".nav").addClass("fixed");
	}
	else{
		$(".nav").removeClass("fixed");
	}
});
*/


// accordion

$('.accordion__header').click(function() {
    $('.accordion__content').slideUp();
    $('.accordion__header').removeClass('active');
    if ($(this).next().is(":visible")) {
        $(this).next().slideUp();
        $(this).removeClass('active');
    } else {
        $(this).next().slideToggle();
        $(this).toggleClass('active');
    }
    return false;
});


$('.nav-link').bind('click.smoothscroll', function(e) {
    e.preventDefault();

    var target = this.hash,
        $target = $(target);

    $('html, body').stop().animate({
        'scrollTop': $target.offset().top
    }, 600, 'swing');
});

// phone mask
$('.phone-mask').mask('+ 7 (999) 999-99-99', { showMaskOnFocus: false, showMaskOnHover: false });

// validate
/*
$(".form-validate").each(function(){
	$(this).validate();
});
*/

// select file
$('input[type="file"]').change(function(e) {
    $(this).closest('.file-upload').find('.file-upload__selected').text(e.target.files[0].name);
});

$('#form-raschet-industry').change(function(e) {
    $("#popup-raschet-industry").val($(this).find(":selected").text());
});

$('#form-raschet-vakansiya').bind('keyup', function() {
    $("#popup-raschet-vakansiya").val($(this).val());
});

$('#form-raschet-employees').bind('keyup', function() {
    $("#popup-raschet-employees").val($(this).val());
});

$('#form-raschet-city').bind('keyup', function() {
    $("#popup-raschet-city").val($(this).val());
});

$('#year_div').slider({
min: 0,
max: 100,
values: [18, 80],
slide: function(event, ui) {
    $("#year_span").text(ui.values[0] + " - " + ui.values[1]);
    $("#year_input").val(ui.values);
}
});

});