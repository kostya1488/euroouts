$(document).ready(function() {

  if ($('.b-trust-slider__wrapper').length) {
    $(".b-trust-slider__wrapper").jCarouselLite({
      btnNext: ".b-trust-slider__arrow-right",
      btnPrev: ".b-trust-slider__arrow-left",
      circular: true,
      visible: 5
    });
  }
  $(".dropdown").dblclick(function(e) {
    e.preventDefault();
  });

  $(".dropdown img.flag").addClass("flagvisibility");

  $(".dropdown dt a").click(function(event) {
    event.preventDefault();
  });

  $(".dropdown dt").click(function() {
    $(".dropdown dd ul").toggle();
  });

  $(document).bind('click', function(e) {
    var $clicked = $(e.target);
    if (!$clicked.parents().hasClass("dropdown"))
      $(".dropdown dd ul").hide();
  });

  $("#flagSwitcher").click(function() {
    $(".dropdown img.flag").toggleClass("flagvisibility");
  });



  // $(".b-design-projects-types").tabs();
  $('.b-top5-problems-list').on('click', '.title', function(e) {
    e.preventDefault();
    if (
      $(this).parent().hasClass('active')) {
      $(this).parent().removeClass('active');
      $(this).parent().find('.content').slideUp();


    } else {
      $(this).parent().addClass('active');
      $(this).parent().find('.content').slideDown();
    }

  });


  if ($('.b-specialist-slider__wrapper').length) {
    var l = $('.b-specialist-slider__wrapper ul li').length;
    if (l<=4) {
      $('.b-specialist-slider .b-specialist-slider__arrow-left, .b-specialist-slider .b-specialist-slider__arrow-right').hide();
      return;
    }
    $(".b-specialist-slider__wrapper").jCarouselLite({
      btnNext: ".b-specialist-slider__arrow-right",
      btnPrev: ".b-specialist-slider__arrow-left",
      circular: true,
      visible: 4
    });
  }


  if ($('.b-our-clients-feedback-slider__wrapper').length) {
    $(".b-our-clients-feedback-slider__wrapper").jCarouselLite({
      btnNext: ".b-our-clients-feedback-slider__arrow-right",
      btnPrev: ".b-our-clients-feedback-slider__arrow-left",
      circular: true,
      visible: 3
    });
  }

});

$(window).load(function()
{
/*
	VK.init({apiId: 5113464, onlyWidgets: true});

	VK.Widgets.Like("vk_like", {type: "button", height: 18});

	VK.Widgets.Comments("vk_comments", {limit: 10, width: "1092", attach: "*"});
*/
});

$("#b-podbor-personala__form").click(function(){
  /*switch ((this).attr("id"))
  {
    case "order_call":
          console.log("123");
          break;
    case  "b-podbor-personala__form":
          console.log("321");
          break;
  }*/
  console.log("---");
});