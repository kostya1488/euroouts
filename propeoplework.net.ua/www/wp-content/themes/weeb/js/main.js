jQuery(function () {
    setTimeout(function () {
        jQuery('#map').html('<script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3Abb03fa042b0ff775d6215c15b6dfd70b69f54c9ed37a93dcd0d7717c0c930727&amp;width=100%25&amp;height=500&amp;lang=ru_RU&amp;scroll=true"></script>');
    },100);

    var win_height =  jQuery(window).height();

    //SCrollToTop
    jQuery(window).scroll(function () {
        if (jQuery(this).scrollTop() > win_height) {
            jQuery('#site-to-top').fadeIn();
        } else {
            jQuery('#site-to-top').fadeOut();
        }
    });
    jQuery('#site-to-top').click(function () {
        jQuery('body,html').animate({
            scrollTop: 0
        }, 600);
        return false;
    }); 



});

jQuery(function ($) {
    if( $('#seo_bottom_from').length > 0 && $('#seo_bottom_to').length > 0  ){
        console.log('magic start');
        var seo_from_html = $('#seo_bottom_from').html();
        $('#seo_bottom_to').html(seo_from_html);
        $('#seo_bottom_to').removeClass('seotext--hidden');
        $('#seo_bottom_from').remove();
    }
    
});