// preload

(function() {
    var preload = [
        'i/load.png',
        'i/bg.overlay.png'
    ];

    $.each(preload, function() {
        $('<img />').attr('src', this);
    });
})();


// features

var isMobile = (/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase()));

$('html')
    .addClass('js')
    .toggleClass('mobile', isMobile);


// screen size

var screenSize = {
    style: null,
    width: null,
    height: null,
    ratio: 1,
    update: function() {
        var w = parseInt($(window).width()),
            h = parseInt(window.innerHeight ? window.innerHeight : $(window).height());

        if (w == screenSize.width && h == screenSize.height) {
            return false;
        }

        screenSize.width = w;
        screenSize.height = h;
        screenSize.ratio = w / h;

        if (screenSize.style) {
            screenSize.style.remove();
        }

        screenSize.style = $('<style>.screen-full{height: ' + screenSize.height + 'px}</style>').appendTo('head');
    }
};

$(window).on('orientationchange.screen load.screen resize.screen', screenSize.update).triggerHandler('resize.screen');


// scroll page

var scrollPageTo = function(toPosition, callback) {
    var fromPosition = $('html').scrollTop() ? $('html').scrollTop() : $('body').scrollTop();

    $('html, body').stop().animate({
        scrollTop: toPosition
    }, {
        duration: 500,
        always: function() {
            $('body').off('.scrollPage');
        },
        complete: function() {
            if (callback) {
                callback();
            }
            callback = null;
        }
    });
};

// ready

$(function() {
    // lang

    var lang = {
        ajaxError: 'Ошибка загрузки данных. Проверьте подключение к&#160;интернету и&#160;повторите попытку&#160;снова.',
        post: 'Ошибка отправки формы. Проверьте подключение к&#160;интернету и&#160;повторите попытку&#160;снова.',
        sendEmptyForm: 'Укажите телефон или&#160;электронную почту для&#160;связи.'
    }


    // error

    var error = function(message) {
        var container = $('<div />', { html: '<p>' + message + '</p>' });
        wScreen(container, 'error-message');
    }

    //custom inputs
    window.customInputs = function() {
        $('form').each(function() {
            var holder = $(this);
            var allLabel = $('label', holder);
            var allInput = $("input", allLabel);

            $(".custom-input, .custom-normal-input", holder).each(function() {
                var label = $(this);
                var input = $("input", label);
                if (input.data('customized') == 'Y') {
                    return;
                }
                input.data('customized', 'Y');

                label.toggleClass('active', input.is(':checked'))

                switch (input.attr("type")) {
                    case "radio":
                        input.click(function(event) {
                            if (label.hasClass('active') && !label.hasClass('oneAlways')) {
                                label.removeClass('active');
                                input.attr('checked', false);
                            } else {
                                if (!label.hasClass('active')) {
                                    label.siblings().removeClass('active');
                                    label.addClass('active');
                                }
                            }
                        });
                        break;
                    case "checkbox":
                        input.click(function(event) {
                            if (label.hasClass('disabled')) {
                                input.removeAttr('checked');
                            } else {
                                label.toggleClass('active');
                            }
                        });
                        break;
                }
            });
        });
    }
    window.customInputs();

    // bg video

    /*var bgVideos = $('.bg-video');

    bgVideos.each(function(){
    	var video = $('video', this),
    		preview = $('img', this),
    		element = video;

    	if (!element.length){
    		element = preview;
    	}

    	var size = {
    		width: element[0].width,
    		height: element[0].height
    	};

    	size.ratio = size.width / size.height;
    	$(this).data('size', size);

    	if (isMobile){
    		preview.appendTo(this);
    		video.remove();
    	}
    });

    bgVideos.update = function(){
    	bgVideos.each(function(){
    		var container = $(this),
    			bg = $('video', this),
    			videoWidth,
    			videoHeight,
    			ratio = $(this).data('size').ratio;

    		if (!bg.length){
    			bg = $('img', this);
    		}

    		if (screenSize.ratio > ratio){
    			videoWidth = screenSize.width;
    			videoHeight = screenSize.width / ratio;
    		}else{
    			videoWidth = screenSize.height * ratio;
    			videoHeight = screenSize.height;
    		}

    		bg.attr({
    			width: videoWidth,
    			height: videoHeight
    		});

    		container.css({
    			top: '50%',
    			left: '50%',
    			width: videoWidth,
    			height: videoHeight,
    			marginTop: -videoHeight / 2,
    			marginLeft: -videoWidth / 2
    		});
    	});
    }

    $(window).on('resize.bgVideos', bgVideos.update).trigger('resize.bgVideos'); */

    $('.bg-video').each(function() {
        var videoCnt = $('.video', this);
        if (!videoCnt.length) {
            return false;
        }
        var videoHTML = $('.video', this)[0].childNodes[0].data;

        if (!isMobile) {
            videoCnt.empty().html(videoHTML);

            var intro = $(this),
                video = $('video', videoCnt),
                ratio = 1280 / 726,
                w,
                h;

            $(window).on('resize.intro', function() {
                var introWidth = intro.width(),
                    introHeight = intro.height();

                if (introWidth / introHeight > ratio) {
                    w = Math.round(intro.width());
                    h = Math.round(w / ratio);
                } else {
                    h = Math.round(intro.height());
                    w = Math.round(h * ratio);
                }

                video[0].width = w;
                video[0].height = h;

                videoCnt.css({
                    top: (introHeight - h) / 2,
                    left: (introWidth - w) / 2
                }).show();

                video.css({
                    width: w,
                    height: h
                });
            }).trigger('resize.intro');
        }
    });


    // main nav

    var mainNav = $('.main-nav');

    if (mainNav.length) {
        (function() {
            var nav = $('nav ul', mainNav),
                linkToMain = $('.title a', mainNav),
                linksMenu = $('a', nav),
                navHeight = mainNav.height(),
                screens = $('section.screen'),
                screenContacts = $('.screen-contacts'),
                screenOrder = $('.screen-order'),
                screenActive = 0,
                lastScreenActive = 0,
                screenOffset = 100,
                activeBorder = $('<li />', { 'class': 'border' }).hide().appendTo(nav),
                social = $('.social .wr', mainNav);

            var updateScrollPosition = function() {
                if (screenActive == 0) {
                    linkToMain.trigger('click');
                } else {
                    linksMenu.eq(screenActive - 1).trigger('click');
                }
            }

            screens.update = function() {
                screens.each(function() {
                    $(this).data('top', $(this).offset().top);
                });
            };

            screens.update();

            activeBorder.update = function() {
                if (!screenActive) {
                    activeBorder.hide();
                    saveState('', null);
                    return false;
                }

                var activeElement = linksMenu.eq(screenActive - 1),
                    li = activeElement.closest('li');

                activeBorder.css({
                    width: li.width(),
                    left: li.position().left
                }).show();

                saveState(activeElement.attr('href'), activeElement.text());
            };

            $(window).on('load.menu resize.menu', function() {
                screens.update();
                activeBorder.update();
            }).trigger('resize.menu');

            $(window).on('scroll.menu', function() {
                var topPosition = $('html').scrollTop() ? $('html').scrollTop() : $('body').scrollTop();

                mainNav.toggleClass('main-nav-fixed', screenSize.height - navHeight < topPosition);

                screens.each(function(i) {
                    if ($(this).data('top') - screenOffset < topPosition) {
                        screenActive = i;
                    }
                });

                if (lastScreenActive != screenActive) {
                    lastScreenActive = screenActive;
                    activeBorder.update();
                }
            }).trigger('scroll.menu');


            // menu links

            linksMenu.on('click', function(e) {
                e.preventDefault();

                var index = linksMenu.index(this),
                    hash = $(this).attr('href'),
                    title = $(this).text(),
                    screenPosition = screens.eq(index + 1).data('top'),
                    callback = function() {};

                if (hash == '#order') {
                    var screenOrderInputs = screenOrder.find('input[type=text], textarea');

                    callback = function() {
                        screenOrderInputs.each(function() {
                            if ($(this).val() == '') {
                                $(this).focus();
                                return false;
                            }
                        });
                    }
                }

                scrollPageTo(screenPosition, callback);
            });


            // to main page

            linkToMain.on('click', function(e) {
                e.preventDefault();
                scrollPageTo(0);
            });


            // to order page

            $('.to-order').on('click', function(e) {
                e.preventDefault();
                linksMenu.filter('[href=#order]').trigger('click');
            });


            // keyboard nav

            if (!isMobile) {
                $(window).on({
                    'keydown.menu': function(e) {
                        switch (e.keyCode) {
                            case 33:
                            case 34:
                            case 38:
                            case 40:
                                return false;
                                break;
                        }
                    },
                    'keyup.menu': function(e) {
                        var oldScreenActive = screenActive;

                        switch (e.keyCode) {
                            case 33:
                            case 38:
                                screenActive -= 1;
                                break;
                        }

                        switch (e.keyCode) {
                            case 34:
                            case 40:
                                screenActive += 1;
                                break;
                        }

                        if (oldScreenActive != screenActive) {
                            screenActive = screenActive < 0 ? 0 : screenActive;
                            screenActive = screenActive >= screens.length ? screens.length - 1 : screenActive;

                            updateScrollPosition();

                            return false;
                        }
                    }
                });
            }


            // main nav toggler

            $('nav .toggler', mainNav).on('click', function() {
                if (!nav.is(':visible')) {
                    var navClone = wScreen(nav.clone(), 'nav-page');

                    $('a', navClone).each(function(i) {
                        $(this).on('click', function(e) {
                            e.preventDefault();
                            $('a', nav).eq(i).trigger('click');
                        });
                    });
                }
            });


            // social-toggler

            $('.social-toggler', mainNav).on('click', function() {
                if (!nav.is(':visible')) {
                    var socialClone = wScreen(social.clone(), 'social-page');
                }
            });
        })();
    }

    $(".price-block .button").each(function() {
        $(this).click(function(event) {
            event.preventDefault();
            var screenPosition = $('section.screen').eq(3).data('top')
            scrollPageTo(screenPosition);
        });
    });


    // w screen

    var wScreen = function(content, className) {
        $('html').addClass('w-screen-open');

        var screen = $('<div />', { 'class': 'w-screen' }).append(content.addClass(className)).hide().appendTo('body'),
            close = $('<span />', {
                'class': 'close',
                'html': '<em />'
            }).appendTo(screen);

        $('.w-screen').stop(true, true);

        screen.fadeIn(isMobile ? 0 : 600, function() {
            if ($('.w-screen').length > 1) {
                $.fx.off = true;
                $('.w-screen').not(screen[0]).find('.close').trigger('click');
                $.fx.off = false;
                $('html').addClass('w-screen-open');
            }

            $(window).off('.wScreen').on({
                'keydown.wScreen': function(e) {
                    if (e.keyCode == 27) {
                        screen.find('.close').trigger('click');
                    }
                }
            });
        });

        screen.remove = function() {
            $('html').removeClass('w-screen-open');
            content.remove();
            $(this).remove();
            $(window).off('.wScreen');
        }

        screen.add(close).on('click', function(e) {
            screen.stop().fadeOut(isMobile ? 0 : 600, function() {
                screen.remove();
            });
        });

        return screen;
    };

    var loader = function() {
        var container = $('<div />', {
            'class': 'loader',
            'html': '<em />'
        });

        container.position = 0;
        container.step = 30;
        container.maxPosition = 690;
        container.timer;

        container.recAnimate = function() {
            container.position -= container.step;

            if (-container.position > container.maxPosition) {
                container.position = 0;
            }

            container.css('background-position', this.position + 'px 0px');

            container.timer = setTimeout(function() {
                if (container) {
                    container.recAnimate();
                }
            }, 30);
        }

        container.remove = function() {
            $(this).remove();
            container = null;
        }

        container.recAnimate();

        return container;
    }


    // get ajax content

    var ajaxContentBase = [];

    var ajaxContent = function(url, callback) {
        if (ajaxContentBase[url]) {
            callback(data);
            return true;
        }

        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function(data) {
            ajaxContent[url] = data;
            callback(data);
        }).fail(function() {
            callback(null);
            error(lang.ajaxError);
        });
    }


    // details

    $('.details a').on('click', function(e) {
        e.preventDefault();

        var detailsLoader = loader();
        var detailsClone = wScreen(detailsLoader, 'details-page');

        ajaxContent($(this).attr('href'), function(data) {
            detailsLoader.remove();
            if (data) {
                var details = $(data).on('click', false);
                detailsClone.append(details.addClass('tx-page'));
            } else {
                detailsClone.remove();
            }
        });
    });


    // map

    var mapContainer = $('.map');

    if (mapContainer.length) {
        var mapZoom = 15,
            markerRostov = {
                position: [50.4521505, 30.4819563],
                mapCenters: {
                    0: [50.4521505, 30.4819563],
                    650: [50.4521505, 30.4819563],
                    1000: [50.4521505, 30.4819563]
                }
            },
            markerMoscow = {
                position: [55.718186, 37.410105],
                mapCenters: {
                    0: [55.719856, 37.4156450],
                    650: [55.720356, 37.413345],
                    1000: [55.720356, 37.409345]
                }
            };

        var activeTown = 'rostov',
            mapCenter = markerRostov.mapCenters[1000];

        $('.map-nav a').each(function() {
            $(this).on('click', function(e) {
                $(this).addClass('active').siblings('a').removeClass('active');
                e.preventDefault();
                activeTown = $(this).data('town').toLowerCase();
                $(window).triggerHandler('resize.map');
            });
        })

        window.mapInit = function() {
            var mapStyles = [{
                    stylers: [
                        { "saturation": -100 }
                    ]
                }],
                map = new google.maps.Map(mapContainer[0], {
                    zoom: mapZoom,
                    maxZoom: 19,
                    minZoom: 3,
                    center: new google.maps.LatLng(mapCenter[0], mapCenter[1]),
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    disableDefaultUI: true,
                    scrollwheel: false,
                    draggable: !isMobile,
                    styles: mapStyles
                });

            var gooddeOverlay = function(position, content, map) {
                this.center = position;
                this.map = map;
                this.content = content;

                this.setMap(map);
            }

            gooddeOverlay.prototype = new google.maps.OverlayView();

            gooddeOverlay.prototype.onAdd = function() {
                var panes = this.getPanes();
                panes.overlayMouseTarget.appendChild(this.content);
            }

            gooddeOverlay.prototype.draw = function() {
                var overlayProjection = this.getProjection();
                var position = overlayProjection.fromLatLngToDivPixel(this.center);

                $(this.content).css({
                    left: position.x,
                    top: position.y
                }).show();
            }

            gooddeOverlay.prototype.onRemove = function() {
                $(this.content).remove();
            }

            var overlayRostov = new gooddeOverlay(new google.maps.LatLng(markerRostov.position[0], markerRostov.position[1]), mapContainer.nextAll('.overlay')[0], map),
                overlayMoscow = new gooddeOverlay(new google.maps.LatLng(markerMoscow.position[0], markerMoscow.position[1]), mapContainer.nextAll('.overlay')[1], map);

            $(window).on('resize.map', function() {
                var mapCenters = activeTown == 'rostov' ? markerRostov.mapCenters : markerMoscow.mapCenters;

                $.each(mapCenters, function(index) {
                    if (screenSize.width > parseInt(index)) {
                        mapCenter = this;
                    }
                });


                setTimeout(function() {
                    map.setCenter(new google.maps.LatLng(mapCenter[0], mapCenter[1]));
                    map.setZoom(mapZoom);
                }, 100);
            }).triggerHandler('resize.map');
        };

        $.ajax({
            'dataType': 'script',
            'url': 'https://maps.googleapis.com/maps/api/js?sensor=false&callback=mapInit&key=AIzaSyD08J-sHHyIm4hcM3Rk7F3oe1hd7gdlo6c'
        });
    }


    // peoples

    var peoples = $('.peoples .item').not('.jobs');

    if (peoples.length) {
        (function() {
            var toLeft = $('<span />', {
                    'class': 'nav-left',
                    'html': '<em />',
                    on: {
                        click: function(e) {
                            activeIndex--;
                            changePeople();
                            return false;
                        }
                    }
                }),
                toRight = $('<span />', {
                    'class': 'nav-right',
                    'html': '<em />',
                    on: {
                        click: function(e) {
                            activeIndex++;
                            changePeople();
                            return false;
                        }
                    }
                }),
                changePeople = function() {
                    activeIndex = activeIndex < 0 ? maxActiveIndex : activeIndex;
                    activeIndex = activeIndex > maxActiveIndex ? 0 : activeIndex;

                    peoples.eq(activeIndex).find('a').trigger('click');
                },
                activeIndex = 0,
                maxActiveIndex = peoples.length - 1,
                allLoad = false,
                loadPeoples = function() {
                    $('a', peoples).each(function() {
                        ajaxContent($(this).data('href'), function() {});
                        $('<img />').attr('src', $(this).data('img'));
                    });

                    allLoad = true;
                };

            peoples.each(function(i) {
                $('a', this).on('click', function(e) {
                    e.preventDefault();

                    var peopleLink = $(this),
                        peopleLoader = loader(),
                        peoplePage = wScreen(peopleLoader, '').addClass('people-page');

                    ajaxContent($(this).data('href'), function(data) {
                        peopleLoader.remove();

                        if (data) {
                            var content = $(data).on('click', function(e) {
                                e.stopPropagation();
                            });

                            peoplePage.append(content);

                            // nav

                            activeIndex = i;
                            toLeft.add(toRight).clone(true, true).show().prependTo(peoplePage);

                            //saveState(peopleLink.attr('href'), $('h1', content).text());

                            if (!allLoad) {
                                loadPeoples();
                            }
                        } else {
                            peoplePage.remove();
                        }
                    });
                });
            });
        })();
    }

    // works

    (function() {
        var works = $('.screen-works'),
            startPosition = [0, 0],
            tolerance = 20;

        var worksList = $('.works-list'),
            worksListWr = $('.wr', worksList),
            worksItems = $('.item', worksList),
            images = [],
            scroller = $('.scroller', worksList),
            maxScroll = 0,
            activeWorkIndex = 0;

        worksItems.each(function(index) {
            images[index] = {
                bg: $('.bg', this).data('bg'),
                element: $('.bg', this),
                load: false
            };
        });

        scroller.animate = function(to) {
            to = to > 0 ? 0 : to;
            to = to < maxScroll ? maxScroll : to;

            changeNavState(to);

            $(this).animate({
                left: to
            }, 700, 'easeOutCubic');

            scroller.loadImage();
        };

        scroller.loadImage = function() {
            $.each([activeWorkIndex - 1, activeWorkIndex, activeWorkIndex + 1, activeWorkIndex + 2], function() {
                var index = this;

                if (images[index] && !images[index].load && images[index].bg) {
                    $('<img />').load(function() {
                        images[index].element.css('background-image', 'url(' + images[index].bg + ')');
                        images[index].load = true;
                    }).attr('src', images[index].bg);
                }
            });
        };

        scroller.getPosition = function() {
            return $(this).position().left;
        };

        var worksNav = $('.works-nav', works),
            navLeft = $('.nav-left', works),
            navRight = $('.nav-right', works),
            inRange = false,
            scrollCorrectTimer;

        $(window).on('scroll.worksNav', function() {
            var topPosition = $('html').scrollTop() ? $('html').scrollTop() : $('body').scrollTop();

            inRange = Math.abs(topPosition - screenSize.height) < screenSize.height / 4;

            clearTimeout(scrollCorrectTimer);
            scrollCorrectTimer = setTimeout(function() {
                if (inRange) {
                    scrollPageTo(screenSize.height);
                }
            }, 300);
        });

        $(window).on('keydown.worksNav', function(e) {
            if (inRange) {
                if (e.keyCode == 37) {
                    navLeft.trigger('click');
                }

                if (e.keyCode == 39) {
                    navRight.trigger('click');
                }
            }
        });

        var changeNavState = function(left) {
            if (left === undefined) {
                var left = scroller.getPosition();
            }

            navLeft.toggleClass('nav-hidden', left >= 0);
            navRight.toggleClass('nav-hidden', left <= maxScroll);

            activeWorkIndex = Math.round(Math.abs(left) / worksList.width());
        }

        navLeft.add(navRight).on('click', function() {
            scroller.stop();

            var to = -worksList.width() * Math.round(Math.abs(scroller.getPosition()) / worksList.width());
            to += (this == navRight[0]) ? -worksList.width() : worksList.width();

            scroller.animate(to);
            return false;
        });

        $(window).on(isMobile ? 'orientationchange.scroller resize.scroller' : 'resize.scroller', function() {
            var offsetWidth = worksListWr[0].offsetWidth,
                scrollWidth = scroller[0].scrollWidth,
                isDrag = false,
                isAnimate = false;

            maxScroll = offsetWidth - scrollWidth;

            $.fx.off = true;
            scroller.stop().animate(-activeWorkIndex * worksList.width());
            $.fx.off = false;

            if (maxScroll < -10 && !isMobile) {
                var lastPosition = 0,
                    dragSpeed = 0;

                var isWheel = false,
                    wheelTimer;

                worksList.off('mousewheel').on('mousewheel', function(e, delta, deltaX, deltaY) {
                    if (Math.abs(deltaX) > Math.abs(deltaY)) {
                        if (!isWheel) {
                            isWheel = true;

                            wheelTimer = setTimeout(function() {
                                isWheel = false;
                            }, 1200);

                            if (deltaX < 0) {
                                navLeft.trigger('click');
                            } else {
                                navRight.trigger('click');
                            }
                        }
                        return false;
                    }
                });

                scroller.draggable({
                    axis: 'x',
                    start: function(e, ui) {
                        scroller.stop(false, false);
                    },
                    drag: function(e, ui) {
                        scroller.stop(false, false);

                        var l = ui.position.left;

                        dragSpeed = lastPosition - l;
                        lastPosition = l;

                        if (l < maxScroll) {
                            ui.position.left = maxScroll - (maxScroll - l) / 20;
                        }

                        if (l > 0) {
                            ui.position.left = l / 20;
                        }
                    },
                    stop: function(e, ui) {
                        scroller.stop(false, false);

                        if (!dragSpeed) {
                            dragSpeed = 0;
                        }

                        var revers = dragSpeed < 0 ? 1 : -1;

                        dragSpeed = Math.abs(dragSpeed);
                        dragSpeed = dragSpeed > 40 ? 40 : dragSpeed;

                        var to = ui.position.left + dragSpeed * revers * 20;

                        if (to > 0) {
                            to = 0;
                        }

                        if (to < maxScroll) {
                            to = maxScroll;
                        }

                        to = -worksList.width() * Math.round(Math.abs(to) / worksList.width());

                        scroller.animate(to);
                    }
                });
            }
        }).trigger(isMobile ? 'orientationchange.scroller' : 'resize.scroller');
    })();


    // form elements

    (function() {
        $('.services input[type=checkbox]').each(function() {
            $(this).on({
                click: function() {
                    $(this).closest('figure').toggleClass('active', $(this).is(':checked'));
                },
                focus: function() {
                    $(this).closest('figure').addClass('focus');
                },
                blur: function() {
                    $(this).closest('figure').removeClass('focus');
                }
            });
        });

        $('input[type=text], textarea').on('keyup', function(e) {
            switch (e.keyCode) {
                case 38:
                case 40:
                    e.stopPropagation();
                    break;
            }
        });

        var orderForm = $('.order-form form'),
            orderFormSubmitButton = $('input[type=submit]', orderForm),
            orderFormReqInputs = $('#order-email, #order-phone', orderForm),
            orderFormReqChkbox = $('#order-politika', orderForm),
            orderFormState = false,
            orderFormValidate = function() {
                orderFormState = false;

                orderFormReqInputs.each(function() {
                    if ($(this).val() != '' && $(this).val().length >= 5) {
                        orderFormState = true;
                    }
                });
                // console.log(orderFormState);
                if (orderFormReqChkbox.is(':checked') && (orderFormState == true)) {
                    orderFormState = true;
                } else {
                    orderFormState = false;
                }

                orderFormSubmitButton.attr('disabled', !orderFormState);
            },
            blockFormElements = function() {
                $('input, textarea', orderForm).attr('disabled', true);
            },
            unblockFormElements = function() {
                $('input, textarea', orderForm).attr('disabled', false);
            },
            sendSuccess = function(text) {
                var form = $('.wrapper', orderForm);
                formHeight = form.innerHeight(),
                    formWidth = form.width(),
                    formTop = form.position().top,
                    formLeft = form.offset().left - orderForm.offset().left;

                orderForm.css({
                    height: formHeight,
                    overflow: 'hidden'
                });

                form.css({
                    position: 'absolute',
                    width: formWidth,
                    top: formTop,
                    left: formLeft,
                    margin: 0
                }).addClass('form-shadow').animate({
                    fakeProperty: 1
                }, {
                    duration: 300,
                    easing: 'easeInCubic',
                    step: function(now, fx) {
                        form.css('transform', 'scale(' + (1 - .15 * fx.pos) + ')');
                    },
                    complete: function() {
                        form.animate({
                            marginLeft: '100%'
                        }, {
                            duration: 400,
                            easing: 'easeInBack',
                            complete: function() {
                                form.remove();
                                $(text).hide().appendTo(orderForm).fadeIn(1200);
                            }
                        });
                    }
                });
            };

        orderFormReqInputs.on('keyup blur change', orderFormValidate);
        orderFormReqChkbox.on('keyup blur change', orderFormValidate);
        orderFormValidate();

        orderForm.append('<input type="hidden" name="js" value="' + parseInt((Math.random() * 1000)) + '" />');

        orderForm.on('submit', function(e) {
            e.preventDefault();

            if (!orderFormState) {
                return false;
            }

            $.ajax({
                type: "POST",
                url: orderForm.attr('action'),
                data: orderForm.serialize(),
                dataType: 'JSON'
            }).done(function(data) {
                if (data && data.result) {
                    if (data.result == 'error') {
                        unblockFormElements();
                        error(data.message);
                    }

                    if (data.result == 'success') {
                        blockFormElements();
                        sendSuccess(data.message);
                    }
                }
            }).fail(function() {
                error(lang.post);
                unblockFormElements();
            }).always(function() {
                orderForm.removeClass('sendingForm');
            });

            orderForm.addClass('sendingForm');
            blockFormElements();
        });

        orderFormSubmitButton.on('click', function() {
            if (!orderFormState) {
                error(lang.sendEmptyForm);
                return false;
            }
        });
    })();


    // states

    function openState(state) {
        if (state == '') {
            return false;
        }

        $.fx.off = true;
        $('a[href="#' + state.replace(/(^\/)|(\/$)|[^a-z0-9\/]/gi, '') + '"]').eq(0).trigger('click');

        $.fx.off = false;
    }

    function saveState(state, title) {
        if (window.history && window.history.replaceState) {
            //window.history.replaceState(null, title, '/' + state.replace(/[^a-z0-9\/]/gi, ''));
        }
    }

    $(window).on('popstate.state', function(e) {
        var link = history.location || document.location;
        openState(link.pathname);
    }).trigger('popstate.state');



    // placeholders

    if (!('placeholder' in document.createElement('input'))) {

        $('input[placeholder], textarea[placeholder]').each(function() {
            $(this)
                .off('.placeholder')
                .on({
                    'blur.placeholder': function() {
                        $(this).val($(this).val() == '' ? $(this).attr('placeholder') : $(this).val());
                    },
                    'focus.placeholder': function() {
                        $(this).val($(this).val() == $(this).attr('placeholder') ? '' : $(this).val());
                    }
                })
                .trigger('blur.placeholder');
        });

        $('form')
            .off('submit.placeholder')
            .on('submit.placeholder', function() {
                $('input[placeholder], textarea[placeholder]', this).each(function() {
                    $(this).val($(this).val() == $(this).attr('placeholder') ? '' : $(this).val());
                });
            });
    }
});