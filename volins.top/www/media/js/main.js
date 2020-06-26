/*
 * Created by Valentina Strativnova
 */

(function($) {
    /* Set variables */
    var History = window.History,
        State = History.getState(),
        rootUrl = History.getRootUrl(),
        document = window.document,

        _s = TweenMax.set,
        _t = TweenMax.to,
        _f = TweenMax.fromTo,
        _k = TweenMax.killTweensOf,
        _st = TweenMax.staggerTo,
        _sf = TweenMax.staggerFromTo,

        b = {
            contentSelector: '.scene',
            popupSelector: '.popup',

            isDragging: false,
            startDragX: 0,
            startDragY: 0,

            animated: false,

            isEng: (document.location.href.indexOf('/en') > -1),

            isHandheld: function() {
                var agent = navigator.userAgent;
                if (agent.match(/(iPhone|iPod|iPad|Blackberry|Android)/)) {
                    return true;
                }
            },
            documentHtml: function(html) {
                var result = String(html)
                    .replace(/<\!DOCTYPE[^>]*>/i, '')
                    .replace(/<(html|head|body|title|meta|script)([\s\>])/gi, '<div class="document-$1"$2')
                    .replace(/<\/(html|head|body|title|meta|script)\>/gi, '</div>');

                return result;
            }
        },

        preloader = {
            init: function() {
                $('.scene,.footer,.popup,.overlay,.nav-arrows').hide();
                _s($('.wrapper'), {
                    autoAlpha: 0
                });
                _s($('.preloader-circle1,.preloader-circle2,#preloader__fish'), {
                    scale: 0
                });
                b.loader(['/media/img/preloader.png', '/media/img/bg_grad.jpg'], function() {
                    _t($('.wrapper'), 0.3, {
                        autoAlpha: 1
                    });
                    preloader.start(0.3);
                    b.init();
                });
            },
            start: function(delay) {
                _k($('.preloader-circle1,.preloader-circle2,#preloader__fish'));
                _t($('#preloader'), 0.1, {
                    autoAlpha: 1,
                    delay: delay
                });
                _t($('.preloader-circle1'), 0.5, {
                    scale: 1,
                    delay: delay + 0.1
                });
                _t($('.preloader-circle2'), 0.5, {
                    scale: 1,
                    delay: delay + 0.2
                });
                _t($('#preloader__fish'), 0.5, {
                    scale: 1,
                    delay: delay + 0.3
                });
                _f($('#preloader__fish'), 2, {
                    backgroundPosition: '0 0'
                }, {
                    backgroundPosition: '-5900px 0',
                    ease: SteppedEase.config(59),
                    delay: delay + 0.1,
                    repeat: -1
                });
            },
            stop: function(callback) {
                $('.footer,.popup,.overlay').show();

                _k($('.preloader-circle1,.preloader-circle2,#preloader__fish'));
                _t($('#preloader__fish'), 0.4, {
                    scale: 0
                });
                _t($('.preloader-circle2'), 0.4, {
                    scale: 0,
                    delay: 0.1
                });
                _t($('.preloader-circle1'), 0.4, {
                    scale: 0,
                    delay: 0.2
                });

                _t($('#preloader'), 0.1, {
                    autoAlpha: 0,
                    delay: 0.6,
                    onComplete: callback
                });
                _s($('#preloader__fish'), {
                    backgroundPosition: '0 0',
                    delay: 0.7
                });
            }
        },
        SubControls = {
            _buttonContainerClass: 'subcontrols-button-container',
            _buttonBgClass: 'subcontrols-button-bg',
            _iconBaseClass: 'subcontrols-icon',
            _iconSize: 20,
            _iconSwapTime: 0.2
        };

    SubControls.init = function(buildTo, delay) {
        buildTo = $(buildTo)[0];
        if (!buildTo) {
            console.log("FooterControls.init require html element to work");
            return;
        }
        this._createdByButton = this._createButton(buildTo, 0, ((b.isEng) ? '&copy;&nbsp;&laquo;Barracuda&raquo;<br />Done by <a href="http://wow.wearewowagency.com/" class="dev-link no-ajaxy" target="_blank"><img src="/media/img/wow.png" alt="WOW" width="31" /></a>' : '&copy;&nbsp;&laquo;Barracuda&raquo;<br />Разработка сайта&nbsp;&mdash; <a href="http://wow.wearewowagency.com/" class="dev-link no-ajaxy" target="_blank"><img src="/media/img/wow.png" alt="WOW" width="31" /></a>'), true);
        this._languageButton = this._createButton(buildTo, 1, ((b.isEng) ? 'Может, по-русски?' : 'Еnglish, maybe?'), false);
        this._shareButton = this._createButton(buildTo, 2, ((b.isEng) ? 'Share it' : 'Расскажи друзьям'), true, $('.footer-share').html());

        var delay = delay | 0;
        var buttons = _s([this._createdByButton, this._languageButton, this._shareButton], {
            transformOrigin: "50% 50%"
        }).target;
        _sf(buttons, 0.35, {
            opacity: 0,
            scale: 0
        }, {
            opacity: 1,
            scale: 1,
            ease: Back.easeOut,
            delay: delay
        }, 0.1);

        SubControls.ToolTip.init(buildTo);

        $(this._createdByButton).on('click', function() {
            window.open($('.dev-link').attr('href'));
        });
        $(this._languageButton).on('click', function() {
            document.location.href = ((b.isEng) ? '/' : '/en');
        });
        $(this._shareButton).on('click', function() {
            SubControls.ToolTip.show(this, 16, 2, this._clickContent, true);
        });

    };
    SubControls._createDiv = function(createTo, className) {
        var div = document.createElement("div");
        if (className) {
            div.className = className;
        }
        if (createTo) {
            createTo.appendChild(div);
        }
        return div;
    };
    SubControls._createButton = function(addTo, iconId, content, useTooltipMouse, clickContent) {
        var el = this._createDiv(addTo, this._buttonContainerClass);

        el._content = content;
        el._clickContent = clickContent || '';
        el._useTooltipMouse = useTooltipMouse;

        var bg = this._createDiv(el, this._buttonBgClass);
        el._bg = bg;

        var icon = this._createDiv(el, this._iconBaseClass);
        icon._bgPosX = (-iconId * SubControls._iconSize);
        _s(icon, {
            backgroundPosition: icon._bgPosX + "px 0px",
            transformOrigin: "50% 50%",
            transformPerspective: 100
        });
        el._icon = icon;

        $(el).on('mouseenter', SubControls._buttonMouseHandler).on('mouseleave', SubControls._buttonMouseHandler);
        return el;
    };
    SubControls._buttonMouseHandler = function(e) {
        var over = e.type == 'mouseenter';

        if (over) {
            // yeap, some magic numbers
            SubControls.ToolTip.show(this, 16, 2, this._content, this._useTooltipMouse);
        } else {
            SubControls.ToolTip.hide();
        }
        _t(this._bg, 0.5, {
            scale: over ? 1.2 : 1.0,
            ease: Back.easeOut
        });
        _t(this._icon, SubControls._iconSwapTime, {
            rotationY: "-90deg",
            onComplete: SubControls._setIconImagePositionY,
            onCompleteParams: [this._icon, over ? -SubControls._iconSize : 0]
        });
        _t(this._icon, SubControls._iconSwapTime, {
            rotationY: "0deg",
            delay: SubControls._iconSwapTime,
            overwrite: false
        });
    };
    SubControls._setIconImagePositionY = function(icon, to) {
        _s(icon, {
            backgroundPosition: icon._bgPosX + "px " + to + "px"
        });
    };

    SubControls.ToolTip = {
        _containerClass: 'subcontrols-tooltip-container',
        _arrowClass: 'subcontrols-tooltip-arrow',
        _bgClass: 'subcontrols-tooltip-bg',
        _contentClass: 'subcontrols-tooltip-content',
        _hideDelay: 0.5
    };

    SubControls.ToolTip.init = function(buildTo) {
        buildTo = $(buildTo)[0];
        if (!buildTo) {
            console.log("ToolTip.init require html element to work")
            return;
        }

        this._container = SubControls._createDiv(buildTo, SubControls.ToolTip._containerClass);
        this._jContainer = $(this._container);
        this._arrow = SubControls._createDiv(this._container, SubControls.ToolTip._arrowClass);
        this._bg = SubControls._createDiv(this._container, SubControls.ToolTip._bgClass);
        this._content = SubControls._createDiv(this._bg, SubControls.ToolTip._contentClass);

        _s([this._container, this._bg], {
            transformOrigin: "50% 100%",
            perspective: 100
        });
        _s(this._container, {
            autoAlpha: 0,
            rotationX: "90deg"
        });

        this._hided = true;

        this._jContainer.mouseover(SubControls.ToolTip._mouseOverHandler).mouseout(SubControls.ToolTip._mouseOutHandler)
    };
    SubControls.ToolTip.show = function(owner, offsetX, offsetY, content, useMouse) {
        this._useMouse = useMouse | false;

        clearTimeout(SubControls.ToolTip._hideTimeout);

        if (owner != this._owner || (content != this._content.innerHTML && this._content.innerHTML != SubControls._shareButton._clickContent)) {
            this._swapContentTo(content);
            this._owner = owner;
            var jOwner = $(owner);
            var elPosition = jOwner.position();
            var x = (elPosition.left - this._width / 2) + offsetX;
            var y = (elPosition.top - this._height) - offsetY;
            if (this._hided) {
                _s(this._container, {
                    x: x + 'px',
                    y: y + 'px'
                });
            } else {
                _k([this._container, this._bg]);
                _t(this._container, 0.35, {
                    x: x + 'px'
                });
                _s(this._container, {
                    y: y + 'px'
                });
            }
        }
        this._needHide = false;
        this._hided = false;

        _t(this._container, 0.5, {
            autoAlpha: 1,
            rotationX: '0deg',
            ease: Back.easeOut
        });
        _t(this._bg, 0.5, {
            scale: 1,
            ease: Back.easeOut
        });
    };
    SubControls.ToolTip._swapContentTo = function(content) {
        SubControls.ToolTip._content.innerHTML = content;

        SubControls.ToolTip._height = this._jContainer.height();
        SubControls.ToolTip._width = this._jContainer.width();

        _f(SubControls.ToolTip._content, 0.35, {
            autoAlpha: 0
        }, {
            autoAlpha: 1
        });
    };
    SubControls.ToolTip._mouseOverHandler = function(e) {
        SubControls.ToolTip._mouseOverState = true;
        if (SubControls.ToolTip._useMouse) {
            clearTimeout(SubControls.ToolTip._hideTimeout);
        }
    };
    SubControls.ToolTip._mouseOutHandler = function(e) {
        SubControls.ToolTip._mouseOverState = false;
        if (SubControls.ToolTip._needHide) {
            SubControls.ToolTip.hide();
        }
    };
    SubControls.ToolTip.hide = function() {
        var allowHide = true;
        if (this._useMouse) {
            if (this._mouseOverState == true) {
                allowHide = false;
            }
        }
        this._needHide = true;
        if (allowHide) {
            clearTimeout(SubControls.ToolTip._hideTimeout);
            SubControls.ToolTip._hideTimeout = setTimeout(SubControls.ToolTip._hide, SubControls.ToolTip._hideDelay * 1000);
        }
    };
    SubControls.ToolTip._hide = function() {
        clearTimeout(SubControls.ToolTip._hideTimeout);
        _t(SubControls.ToolTip._container, 0.5, {
            autoAlpha: 0,
            rotationX: "90deg",
            onComplete: SubControls.ToolTip._switchToHidedState
        });
        _t(SubControls.ToolTip._bg, 0.5, {
            scale: 0.2,
            ease: Back.easeOut
        });
    };
    SubControls.ToolTip._switchToHidedState = function() {
        SubControls.ToolTip._owner = null;
        SubControls.ToolTip._hided = true;
    };

    /* Ajaxify content */
    $.fn.ajaxify = function() {

        var $this = $(this);

        $this.find('a:not(.no-ajaxy):not(.pseudo-link)').on('click', function(event) {
            var $this = $(this),
                url = $this.attr('href'),
                title = $this.attr('title') || null;

            clearTimeout(b.timer);
            b.timer = setTimeout(b.stages.showTooltip['simple'], 15000);

            if ($this.hasClass('popup-link')) b.popup.left = $this.hasClass('popup-link_left');

            History.pushState({
                popup: $this.hasClass('popup-link'),
                close: $this.hasClass('popup__close')
            }, title, url);
            event.preventDefault();
            return false;
        });

        $this.find('img').on('mousedown touchstart', function(event) {
            event.preventDefault();
        });

        if (b.currentScene && b.currentScene !== 'error') b.stages.ajaxFunc[b.currentScene]();
        if (b.currentScene && b.currentScene !== 'error' && !State.data.popup) {

            b.events.checkWidth();

            if (b.timer) clearTimeout(b.timer);
            b.timer = setTimeout(b.stages.showTooltip['simple'], 15000);

            $('body').on('click', '*', function() {
                clearTimeout(b.timer);
                b.timer = setTimeout(b.stages.showTooltip['simple'], 15000);
            });

            $('.tooltip').on('click', function() {
                b.animated = true;
                b.$menuChildren.eq(b.currentSceneIndex + 1).find('a').trigger('click');
            });
        } else b.popup.ajaxFunc();

        return $this;
    };

    /* Get random item */
    $.fn.getRandoms = function(array) {
        var $this = $(this),
            rnd = Math.round(Math.random() * ($this.length - 1));

        array.push($($this[rnd]).find('i'));
        $this = $this.not($this[rnd]);

        if (array.length < 3) {
            $this.getRandoms(array);
        }

        return array;
    };

    /* Push <img> images to array */
    $.fn.pushImages = function() {
        var $this = $(this);

        $this.find('img').each(function() {
            if (b.currentScene && !State.data.popup) b.stages.images[b.currentScene].push($(this).attr('src'));
            else b.popup.images.push($(this).attr('src'));
        });

        return $this;
    };

    /* Barracuda ajax page load */
    b.ajaxPageLoad = function(url) {
        $.ajax({
            url: url || State.url,
            success: function(data, textStatus, jqXHR) {
                var isPopup = State.data.popup && !url,
                    $data = $(b.documentHtml(data)),
                    $dataBody = $data.find('.document-body:first'),
                    $dataContent = (isPopup) ? $dataBody.find(b.popupSelector).filter(':first') :
                    $dataBody.find(b.contentSelector).filter(':first');

                if (!isPopup) {
                    $('body .footer-share').html($dataBody.find('.footer-share').html());
                    if (typeof SubControls._shareButton !== 'undefined') SubControls._shareButton._clickContent = $('.footer-share').html();
                }

                if (!url) {
                    b.updateDOM.menu();
                    b.updateDOM.title($data.find('.document-title:first').text());
                }

                if (isPopup) b.popup.open($dataContent);
                else b.updateDOM.content($dataContent);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                document.location.href = url;
                return false;
            }
        });
    };

    b.error = {
        init: function() {
            $('a').addClass('no-ajaxy').off('click');

            _f($('#error').show(), 1, {
                autoAlpha: 0
            }, {
                autoAlpha: 1
            });
            _t($('.scene__item-ghost'), 1, {
                top: '91px',
                yoyo: true,
                repeat: -1,
                ease: Circ.easeInOut
            });
            _t($('.scene__item-ghost-shadow'), 1, {
                scale: 0.95,
                yoyo: true,
                repeat: -1,
                ease: Circ.easeInOut
            });

            _t($('.scene__item-error-text1'), 0.5, {
                rotation: '-5deg',
                yoyo: true,
                repeat: -1,
                ease: Linear.easeNone
            });
            _f($('.scene__item-error-text1'), 3, {
                autoAlpha: 1,
                top: '416px'
            }, {
                autoAlpha: 0,
                top: '100px',
                repeat: -1,
                ease: Cubic.easeIn
            });

            _t($('.scene__item-error-text2'), 0.5, {
                rotation: '5deg',
                yoyo: true,
                repeat: -1,
                ease: Linear.easeNone
            });
            _f($('.scene__item-error-text2'), 5, {
                autoAlpha: 1,
                top: '370px'
            }, {
                autoAlpha: 0,
                top: '89px',
                repeat: -1,
                ease: Cubic.easeIn
            });

            _t($('.scene__item-error-text3'), 0.5, {
                rotation: '-8deg',
                yoyo: true,
                repeat: -1,
                ease: Linear.easeNone
            });
            _f($('.scene__item-error-text3'), 4, {
                autoAlpha: 1,
                top: '316px'
            }, {
                autoAlpha: 0,
                top: '105px',
                repeat: -1,
                ease: Cubic.easeIn
            });
        }
    };

    /* Barracuda update DOM */
    b.updateDOM = {
        menu: function() {
            var relativeUrl = State.url.replace(rootUrl, '');

            b.$menuChildren.filter('.menu__item_active').removeClass('menu__item_active');
            var $menuCur = b.$menuChildren.has('a[href="' + relativeUrl + '"],a[href="/' + relativeUrl + '"],a[href="/' + relativeUrl + '/"],a[href="' + State.url + '"]');
            if ($menuCur.length === 1) {
                $menuCur.addClass('menu__item_active');
                b.prevSceneIndex = b.currentSceneIndex;
                b.currentSceneIndex = b.$menuChildren.index($menuCur);
            }
        },
        title: function(title) {
            document.title = title;
            try {
                document.getElementsByTagName('title')[0].innerHTML = document.title.replace('<', '&lt;').replace('>', '&gt;').replace(' & ', ' &amp; ');
            } catch (Exception) {}
        },
        content: function($dataContent) {
            b.prevScene = b.currentScene;
            b.currentScene = $dataContent.attr('id');

            if (!State.data.close) {
                $dataContent = (($('#' + b.currentScene).length) ? $('#' + b.currentScene) : $dataContent.appendTo(b.$wrapper).pushImages()).hide();

                _s($dataContent.show(), {
                    autoAlpha: 0
                });

                b.events.checkHeight();

                if (!b.currentSceneIndex && !b.prevSceneIndex) {
                    _s($dataContent.ajaxify(), {
                        autoAlpha: 1
                    });
                    return false;
                }

                if (b.popup.opened) b.popup.close();

                var endId = (b.animated) ? b.prevScene : 'simple',
                    startId = (b.animated) ? b.currentScene : 'simple';

                if (b.clicked) {
                    TweenMax.killAll(true, true, true);
                    b.stages.resetAll[b.prevScene]();
                    b.stages.resetAll[b.currentScene]();
                    _s($(b.contentSelector), {
                        autoAlpha: 0
                    });
                    b.events.preloadScene($dataContent);
                } else {
                    b.clicked = true;

                    b.stages.endAnimation[endId](b.events.preloadScene, $dataContent);
                }
            }
        }
    };

    /* Barracuda events */
    b.events = {
        preloadScene: function($dataContent) {
            var endId = (b.animated) ? b.prevScene : 'simple',
                startId = (b.animated) ? b.currentScene : 'simple';

            preloader.start(0.5);
            b.loader(b.stages.images[b.currentScene], function() {
                b.stages.resetAll[b.prevScene]();

                preloader.stop(b.stages.startAnimation[startId]);
                b.clicked = false;
                b.animated = false;

                $dataContent.ajaxify();
                $('#' + b.currentScene).css('z-index', 5);
                $('#' + b.prevScene).css('z-index', -1);
            });
        },
        checkHeight: function() {
            var h = b.$wrapper.height(),
                _isSocial = $('.social-links').length;
            if (h < 985) {
                var o = (h - 985) / 2
                $(b.contentSelector).css({
                    top: o,
                    bottom: o
                });
            } else $(b.contentSelector).css({
                top: 0,
                bottom: 0
            });

            if (_isSocial) {
                if (h < 770) $('.social-links').addClass('social-links_small');
                else $('.social-links').removeClass('social-links_small');
            }

            if (b.popup.scrollApi) {
                b.popup.attachScroll(0.1);
            }

        },
        checkWidth: function() {
            if (b.$wrapper.width() < 1200) {
                if (b.arrows) b.hideArrows();
            } else {
                if (!b.arrows) b.initArrows();
            }
        },
        checkSize: function() {
            b.events.checkHeight();
            b.events.checkWidth();
        },
        stateChange: function() {
            b.hideArrows();
            State = History.getState();
            b.ajaxPageLoad();
        },
        bounceMouseDown: function(e) {
            if (e && e.touches) {
                e = e.touches[0];
            } else e.preventDefault();
            b.isDragging = true;
            b.startDragY = e.pageY;

            var $bounce = $(this);
            $bounce.css('z-index', 100);
        },
        bounceMouseUp: function(e) {
            b.isDragging = false;

            var $bounce = $(this);
            _t($bounce, 0.4, {
                height: $bounce.attr('data-height') + 'px',
                ease: Bounce.easeOut
            });
            $bounce.css('z-index', 'auto');
        },
        bounceLightMouseUp: function(e) {
            b.isDragging = false;

            var $bounce = $(this),
                $bounceN = $bounce.find('.scene__item-cloud_n');
            $bounceAct = $bounce.find('.scene__item-cloud_active');
            $bounceLight = $bounce.find('.scene__item-cloud__light');

            _t($bounce, 0.4, {
                height: $bounce.attr('data-height') + 'px',
                ease: Bounce.easeOut
            });

            if (!b.lightStart) {
                b.lightStart = true;
                _s($bounceLight, {
                    autoAlpha: 0
                });
                _f($bounceAct.show(), 0.3, {
                    autoAlpha: 0
                }, {
                    autoAlpha: 1,
                    yoyo: true,
                    repeat: 4,
                    delay: 0.4
                });
                _t($bounceN, 0.3, {
                    autoAlpha: 0,
                    delay: 1
                });
                _t([$bounceAct, $bounceN], 0.1, {
                    rotation: '5deg',
                    yoyo: true,
                    repeat: 7,
                    ease: Elastic.easeInOut,
                    delay: 1
                });
                _t($bounceLight, 0.1, {
                    autoAlpha: 1,
                    yoyo: true,
                    repeat: 4,
                    delay: 1.6
                });
                _t($bounceLight, 0.1, {
                    bottom: '-51px',
                    ease: Bounce.easeIn,
                    delay: 1.6
                });

                _t([$bounceLight, $bounceAct], 0.3, {
                    autoAlpha: 0,
                    delay: 4.5,
                    onComplete: function() {
                        b.lightStart = false;
                    }
                });
                _t($bounceN, 0.3, {
                    autoAlpha: 1,
                    delay: 4.3
                });
            }
        },
        bounceMouseMove: function(e) {
            var mouseY, $bounce = $(this);
            if (b.isDragging) {
                e.preventDefault();
                if (e && e.touches) {
                    e = e.touches[0];
                }
                mouseY = e.pageY;

                var dragDistance = mouseY - b.startDragY;
                b.startDragY = mouseY;
                _s($bounce, {
                    height: '+=' + dragDistance + 'px'
                });
                if (($bounce.height() - $bounce.attr('data-height')) >= 60) $bounce.trigger('mouseup');
            }
        },
        kittyMouseDown: function(e) {
            if (e && e.touches) {
                e = e.touches[0];
            } else e.preventDefault();
            b.isDragging = true;

            var $kitty = $(this);
            _s($kitty.css('z-index', 100).addClass('scene__item-door__kitty_drag').appendTo('.wrapper'), {
                left: (e.pageX - 75),
                top: (e.pageY - 20)
            });
        },
        kittyMouseUp: function(e) {
            if (b.isDragging) {
                b.isDragging = false;

                var $kitty = $(this);
                _t($kitty, 0.7, {
                    top: '100%',
                    ease: Sine.easeIn,
                    onComplete: function() {
                        $kitty.removeAttr('style').removeClass('scene__item-door__kitty_drag').appendTo('.scene__item-door');
                        _s($kitty, {
                            autoAlpha: 0
                        });
                        _t($kitty, 0.4, {
                            autoAlpha: 1,
                            delay: 0.5
                        });
                    }
                });
            }
        },
        kittyMouseMove: function(e) {
            var $kitty = $(this);
            if (b.isDragging) {
                e.preventDefault();
                if (e && e.touches) {
                    e = e.touches[0];
                }

                _s($kitty, {
                    left: (e.pageX - 75),
                    top: (e.pageY - 20)
                });
            }
        },
        switchMouseDown: function(e) {
            if (e && e.touches) {
                e = e.touches[0];
            } else e.preventDefault();
            b.isDragging = true;
            b.startDragY = e.pageY;
        },
        switchMouseUp: function(e) {
            if (b.isDragging) {
                b.isDragging = false;

                var $switch = $(this).toggleClass('scene__item-switch_active');
                _s($switch, {
                    height: '56px'
                });
                if (b.stages['principles'].lightOff) {
                    _t($('.darkness'), 0.3, {
                        autoAlpha: 0,
                        ease: Back.easeIn,
                        onComplete: function() {
                            $('.darkness').hide();
                        }
                    });
                } else {
                    _f($('.darkness').show(), 0.3, {
                        autoAlpha: 0
                    }, {
                        autoAlpha: 0.75,
                        ease: Back.easeOut
                    });
                }
                b.stages['principles'].lightOff = !b.stages['principles'].lightOff;
            }
        },
        switchMouseMove: function(e) {
            var mouseY, $switch = $(this);
            if (b.isDragging) {
                e.preventDefault();
                if (e && e.touches) {
                    e = e.touches[0];
                }
                mouseY = e.pageY;

                var dragDistance = mouseY - b.startDragY;
                _s($switch, {
                    height: 56 + dragDistance + 'px'
                });
                if (dragDistance >= 10) $switch.trigger('mouseup');
            }
        },
        birdMouseDown: function(e) {
            if (e && e.touches) {
                e = e.touches[0];
            } else e.preventDefault();
            b.isDragging = true;
        },
        birdMouseUp: function(e) {
            if (b.isDragging) {
                b.isDragging = false;

                /*var $switch = $(this).toggleClass('scene__item-switch_active');
                _s($switch, { height: '56px' });
                if (b.stages['principles'].lightOff) {
                    _t($('.darkness'), 0.3, { autoAlpha: 0, ease: Back.easeIn, onComplete: function() {
                        $('.darkness').hide();
                    }});
                } else {
                    _f($('.darkness').show(), 0.3, { autoAlpha: 0 }, { autoAlpha: 0.75, ease: Back.easeOut });
                }
                b.stages['principles'].lightOff = !b.stages['principles'].lightOff;*/
            }
        },
        birdMouseMove: function(e) {
            var $bird = $(this);
            if (b.isDragging) {
                e.preventDefault();
                if (e && e.touches) {
                    e = e.touches[0];
                }
                _s($bird, {
                    left: e.pageX - 100
                });

                b.stages['portfolio'].checkActiveImg(e.pageX - 100);
            }
        }
    };

    /* Barracuda popup */
    b.popup = {
        backstage: function() {
            var url = $('.popup__close').attr('href');
            b.ajaxPageLoad(url);
        },
        close: function() {
            _t($('.overlay'), 0.5, {
                autoAlpha: 0,
                delay: 0.5
            });

            _t($('.popup .faded-block'), 0.5, {
                autoAlpha: 0,
                ease: Expo.easeOut
            });
            _t($('.popup'), 0.6, {
                width: 0,
                marginLeft: 0,
                ease: Circ.easeIn,
                delay: 0.5,
                onComplete: function() {
                    $('.overlay,.popup').remove();
                    if (!b.arrows) b.events.checkWidth();
                }
            });

            b.clicked = false;
            b.popup.opened = false;
        },
        open: function($dataContent) {
            b.clicked = true;
            b.popup.opened = true;

            b.popup.prev = $('.popup');
            if (b.popup.prev.length) {
                _t(b.popup.prev.find('.popup__content'), 0.3, {
                    left: (b.popup.left) ? '100%' : '-100%',
                    ease: Quint.easeIn,
                    onComplete: function() {
                        b.popup.prev.find('.popup__content').remove();
                    }
                });
            }

            b.popup.init($dataContent);
        },
        init: function($dataContent) {
            $dataContent.pushImages();
            $('#preloader').appendTo('body');
            if (!$('.overlay').length) {
                _f($('<div class="overlay"></div>').appendTo($('body')), 0.2, {
                    autoAlpha: 0
                }, {
                    autoAlpha: 1
                });
            }
            preloader.start(0.5);
            b.loader(b.popup.images, function() {
                preloader.stop(function() {
                    $('#preloader').prependTo(b.$wrapper);
                    $dataContent = $dataContent.appendTo($('body')).ajaxify();

                    if (b.popup.prev.length) {
                        _s($dataContent.find('.popup__content'), {
                            left: (b.popup.left) ? '-100%' : '100%'
                        });
                        _f($dataContent, 0.1, {
                            autoAlpha: 0
                        }, {
                            autoAlpha: 1
                        });
                        _t($dataContent.find('.popup__content'), 0.3, {
                            left: 0,
                            delay: 0.1,
                            ease: Quint.easeIn,
                            onComplete: function() {
                                b.popup.prev.remove();
                            }
                        });
                        b.popup.attachScroll(0.5);
                    } else {
                        _f($dataContent, 0.8, {
                            width: 0,
                            marginLeft: 0
                        }, {
                            width: '900px',
                            marginLeft: '-450px',
                            ease: Back.easeOut
                        });
                        _sf($dataContent.find('.faded-block'), 0.5, {
                            autoAlpha: 0
                        }, {
                            autoAlpha: 1,
                            ease: Expo.easeIn,
                            delay: 0.8
                        }, 0.2);
                        b.popup.attachScroll(1.3 + 0.2 * $dataContent.find('.faded-block').length);
                    }
                    b.clicked = false;
                });
            });
        },
        attachScroll: function(delay) {
            setTimeout(function() {
                var delta = ($('.news-list').length) ? 238 : 192;
                b.popup.scrollApi = $('.popup .scrollPane')
                    .height($('.popup').height() - delta)
                    .jScrollPane({
                        mouseWheelSpeed: 70
                    })
                    .data('jsp');
            }, delay * 1000);
        },
        ajaxFunc: function() {
            $('.overlay').on('click', function() {
                $('.popup__close').trigger('click');
            });
            $('.popup__close').on('click', b.popup.close)
                .on('mouseenter', function() {
                    var $this = $(this);
                    _f($this, 0.3, {
                        rotation: '0deg'
                    }, {
                        rotation: '180deg'
                    });
                    _f($this.find('.popup__close_inr'), 0.3, {
                        scale: 1
                    }, {
                        scale: 2.3
                    });
                    _f($this.find('.popup__close_in'), 0.3, {
                        scale: 1
                    }, {
                        scale: 1.8
                    });
                }).on('mouseleave', function() {
                    var $this = $(this);
                    _t($this.find('.popup__close_in'), 0.4, {
                        scale: 2.3
                    });
                    _s($this.find('.popup__close_inr,.popup__close_in'), {
                        scale: 1,
                        delay: 0.5
                    });
                });

            if ($('.news-list__item').length) $('.news-list__item').on('click', function() {
                $(this).find('a').trigger('click');
            });
            if ($('.popup .slider').length) b.slider.initCarousel($('.popup .slider'), 1);

            if ($('.popup-image').length) {
                var _rnd = Math.round(Math.random() * 3) + 1,
                    _last = $('.popup-image img').attr('src').substr(-5),
                    _src = $('.popup-image img').attr('src').replace(_last, '');
                $('.popup-image img').attr('src', _src + _rnd + '.png');
            }

            if ($('.share-button').length) {
                var $button = $('.share-button'),
                    $icon = $('.share-icon'),
                    $tooltip = $('.case__coloumn .subcontrols-tooltip-container'),
                    $tooltipBg = $('.case__coloumn .subcontrols-tooltip-bg'),
                    shown = false;

                _s([$tooltip, $tooltipBg], {
                    transformOrigin: "50% 100%",
                    perspective: 100
                });
                _s($tooltip.show(), {
                    autoAlpha: 0,
                    rotationX: "90deg"
                });

                $button.on('mouseenter mouseleave', function() {
                    _t($icon, SubControls._iconSwapTime, {
                        rotationY: "-90deg"
                    });
                    _t($icon, SubControls._iconSwapTime, {
                        rotationY: "0deg",
                        delay: SubControls._iconSwapTime,
                        overwrite: false
                    });
                }).on('click', function() {
                    if (shown) {
                        shown = false;
                        _t($tooltip, 0.5, {
                            autoAlpha: 0,
                            rotationX: "90deg",
                            onComplete: SubControls.ToolTip._switchToHidedState
                        });
                        _t($tooltipBg, 0.5, {
                            scale: 0.2,
                            ease: Back.easeOut
                        });
                    } else {
                        shown = true;
                        _t($tooltip, 0.5, {
                            autoAlpha: 1,
                            rotationX: '0deg',
                            ease: Back.easeOut
                        });
                        _t($tooltipBg, 0.5, {
                            scale: 1,
                            ease: Back.easeOut
                        });
                    }
                });
                $tooltip.find('.footer-share__item').on('click', function() {
                    shown = false;
                    _t($tooltip, 0.5, {
                        autoAlpha: 0,
                        rotationX: "90deg",
                        onComplete: SubControls.ToolTip._switchToHidedState
                    });
                    _t($tooltipBg, 0.5, {
                        scale: 0.2,
                        ease: Back.easeOut
                    });
                    return false;
                });
            }
        },
        images: ['/media/img/close.png', '/media/img/popup/about_1.png', '/media/img/popup/about_2.png', '/media/img/popup/about_3.png', '/media/img/popup/about_4.png',
            '/media/img/popup/news_1.png', '/media/img/popup/news_2.png', '/media/img/popup/news_3.png', '/media/img/popup/news_4.png'
        ],
        map: {
            init: function() {
                b.clicked = true;

                if (!this.initialized) this.initMap();
                else {
                    _f($('.overlay').show(), 0.2, {
                        autoAlpha: 0
                    }, {
                        autoAlpha: 1
                    });
                    _f($('.popup').show(), 0.8, {
                        width: 0,
                        marginLeft: 0
                    }, {
                        width: '900px',
                        marginLeft: '-450px',
                        ease: Back.easeOut
                    });
                    _sf($('.faded-block'), 0.5, {
                        autoAlpha: 0
                    }, {
                        autoAlpha: 1,
                        ease: Expo.easeIn,
                        delay: 0.8
                    }, 0.2);
                }
            },
            close: function() {
                _t($('.overlay'), 0.5, {
                    autoAlpha: 0,
                    delay: 0.5
                });

                _t($('.popup .faded-block'), 0.5, {
                    autoAlpha: 0,
                    ease: Expo.easeOut
                });
                _t($('.popup'), 0.6, {
                    width: 0,
                    marginLeft: 0,
                    ease: Circ.easeIn,
                    delay: 0.5,
                    onComplete: function() {
                        if (!b.arrows) b.events.checkWidth();
                    }
                });

                b.clicked = false;
                b.popup.opened = false;
            },
            initMap: function() {

                $('<div class="overlay"></div>').appendTo($('body')).hide();
                $('<div class="popup"><span class="popup__close faded-block"><span class="popup__close_inr"></span><span class="popup__close_in">X</span></span><div id="map" class="faded-block"></div></div>').appendTo($('body')).hide();

                _s($('.popup__close_inr,.popup__close_in'), {
                    scale: 0
                });

                $('.overlay').on('click', function() {
                    $('.popup__close').trigger('click');
                });
                $('.popup__close').on('click', function() {
                        b.popup.map.close();
                    })
                    .on('mouseenter', function() {
                        var $this = $(this);
                        _f($this, 0.3, {
                            rotation: '0deg'
                        }, {
                            rotation: '180deg'
                        });
                        _f($this.find('.popup__close_inr'), 0.3, {
                            scale: 0
                        }, {
                            scale: 2.3
                        });
                        _f($this.find('.popup__close_in'), 0.3, {
                            scale: 0
                        }, {
                            scale: 1.8
                        });
                    }).on('mouseleave', function() {
                        var $this = $(this);
                        _t($this.find('.popup__close_in'), 0.4, {
                            scale: 2.3
                        });
                        _t($this.find('.popup__close_in'), 0.15, {
                            autoAlpha: 0,
                            delay: 0.4
                        });
                        _s($this.find('.popup__close_inr'), {
                            scale: 0,
                            delay: 0.4
                        });
                        _s($this.find('.popup__close_in'), {
                            scale: 0,
                            autoAlpha: 1,
                            delay: 0.6
                        });
                    });

                this.geocoder = new google.maps.Geocoder();
                var mapOptions = {
                    center: new google.maps.LatLng(51.426614, 16.347656),
                    zoom: 5,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    mapTypeControl: false
                };
                this.mapObject = new google.maps.Map(document.getElementById('map'), mapOptions);
                this.initialized = true;

                _f($('.overlay').show(), 0.2, {
                    autoAlpha: 0
                }, {
                    autoAlpha: 1
                });
                _f($('.popup').show(), 0.8, {
                    width: 0,
                    marginLeft: 0
                }, {
                    width: '900px',
                    marginLeft: '-450px',
                    ease: Back.easeOut
                });
                _sf($('.faded-block'), 0.5, {
                    autoAlpha: 0
                }, {
                    autoAlpha: 1,
                    ease: Expo.easeIn,
                    delay: 0.8
                }, 0.2);

                this.codeAddress($('.address').html());
                b.clicked = false;
            },
            codeAddress: function(address) {
                var map = this.mapObject;
                this.geocoder.geocode({
                    'address': address
                }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        map.setCenter(results[0].geometry.location);
                        b.popup.map.marker = new google.maps.Marker({
                            map: map,
                            icon: '/media/img/marker.png',
                            position: results[0].geometry.location
                        });
                        map.setZoom(15);

                        google.maps.event.addListener(b.popup.map.marker, "click", function(e) {
                            b.popup.map.infoBox = new InfoBox({
                                latlng: b.popup.map.marker.getPosition(),
                                map: map
                            });
                        });
                    } else {
                        alert("Geocode was not successful for the following reason: " + status);
                    }
                });
            }
        }
    };

    /* Barracuda stages */
    b.stages = {
        index: {
            man: '.scene__item-man',
            meow: '.scene__item-cat__meow'
        },
        principles: {
            tooltip: '.scene__item-consumer-tooltip',
            creatorBubble: '.scene__item-creator-tooltip',
            lightOff: false
        },
        services: {
            bubble: '.scene__item-creator-bubble',
            listItems: '.services-list__item',
            listItemLinks: '.services-list__item-title',
            bird: '.scene__item-creator-bird'
        },
        portfolio: {
            bird: '.scene__item-fly-bird',
            checkActiveImg: function(left) {
                $('.activate').each(function() {
                    var $this = $(this),
                        width = $this.attr('data-wleft') - $this.attr('data-left');

                    if ($this.hasClass('activate_active') && $this.attr('data-left') >= left) {
                        _t($this.find('.grey'), 0.3, {
                            autoAlpha: 1
                        });
                        _t($this.find('.bright'), 0.3, {
                            autoAlpha: 0
                        });
                        $this.removeClass('activate_active');
                    }
                    if (!$this.hasClass('activate_active') && ($this.attr('data-wleft') - width / 2) <= left) {
                        _t($this.find('.grey'), 0.3, {
                            autoAlpha: 0
                        });
                        _t($this.find('.bright'), 0.3, {
                            autoAlpha: 1
                        });
                        $this.addClass('activate_active');
                    }
                });
            }
        },

        startAnimation: {
            simple: function() {
                _t($('#' + b.currentScene), 0.4, {
                    autoAlpha: 1
                });
            },

            index: function() {
                /* Parallax */
                _f($('.scene__item-index-line1'), 3, {
                    marginLeft: '-=120px'
                }, {
                    marginLeft: '+=120px'
                });
                _f($('.scene__item-index-line2'), 3, {
                    marginLeft: '-=150px'
                }, {
                    marginLeft: '+=150px'
                });
                _f($('.scene__item-index-line3'), 3, {
                    marginLeft: '-=250px'
                }, {
                    marginLeft: '+=250px'
                });
                _f($('.scene__item-front-trees'), 3, {
                    marginLeft: '-=500px'
                }, {
                    marginLeft: '+=500px'
                });

                /* Fading in */
                _s($('#index'), {
                    autoAlpha: 1
                });
                _s($('.scene__item-index-line1,.scene__item-index-line2,.scene__item-index-line3,.scene__item-front-trees'), {
                    autoAlpha: 0
                });
                _t($('.scene__item-front-trees,.scene__item-house'), 1.5, {
                    autoAlpha: 1
                });
                _st([$('.scene__item-way'), $('.news-block'), $('.scene__item-trees_left'), $('.scene__item-fence')], 1.5, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);

                _st($('.scene__item-index-line3.scene__item-cloud'), 1.5, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);
                _st($('.scene__item-index-line2.scene__item-cloud'), 1.5, {
                    autoAlpha: 1,
                    delay: 1
                }, 0.3);
                _st($('.scene__item-index-line1.scene__item-cloud'), 1.5, {
                    autoAlpha: 1,
                    delay: 1.3
                }, 0.3);

                _sf($('.faded'), 0.5, {
                    autoAlpha: 0
                }, {
                    autoAlpha: 1,
                    ease: Back.easeIn,
                    delay: 3
                }, 0.2);

                /* Man */
                var $man = $(b.stages['index'].man);
                _f($man, 0.5, {
                    autoAlpha: 0
                }, {
                    autoAlpha: 1,
                    delay: 3.3
                });
                _f($man, 2, {
                    marginLeft: '-850px'
                }, {
                    marginLeft: '-338px',
                    ease: Linear.easeNone,
                    delay: 3.3,
                    onComplete: function() {
                        _k($man);
                        _s($man, {
                            backgroundPosition: '0 0'
                        });
                    }
                });
                _t($man, 0.6, {
                    backgroundPosition: '0 -1120px',
                    ease: SteppedEase.config(5),
                    delay: 2,
                    repeat: -1
                });
            },
            principles: function() {
                /* Parallax */
                _f($('.scene__item-principles-line2'), 3, {
                    marginLeft: '-=300px'
                }, {
                    marginLeft: '+=300px'
                });
                _f($('.scene__item-principles-line3'), 3, {
                    marginLeft: '-=500px'
                }, {
                    marginLeft: '+=500px'
                });
                _f($('.scene__item-principles-line4'), 3, {
                    marginLeft: '-=700px'
                }, {
                    marginLeft: '+=700px'
                });

                /* Fading in */
                _s($('#principles'), {
                    autoAlpha: 1
                });
                _s($('.scene__item-principles-line2,.scene__item-principles-line3,.scene__item-principles-line4'), {
                    autoAlpha: 0
                });
                _t($('.scene__item-principles-line4'), 1.5, {
                    autoAlpha: 1
                });

                _st([$('.principles'), $('.scene__item-chair-consumer'), $('.scene__item-chair'), $('.scene__item-carpet'), $('.scene__item-table,.scene__item-door'), $('.scene__item-lamp-floor,.scene__item-switch'), $('.scene__item-picture')], 1.5, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);
                _st($('.bounce'), 1.5, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);

                /* Balloons */
                var $tooltip = $(b.stages['principles'].tooltip);
                _f($tooltip.show(), 0.5, {
                    scale: 0
                }, {
                    scale: 1,
                    delay: 3
                });
                _f($tooltip, 0.3, {
                    rotation: '0deg'
                }, {
                    rotation: '2deg',
                    delay: 3.5,
                    yoyo: true,
                    repeat: 3
                });
                _f($('.scene__item-mustache'), 0.1, {
                    rotation: '0deg',
                    top: '68px'
                }, {
                    rotation: '-1deg',
                    top: '67px',
                    delay: 3.5,
                    yoyo: true,
                    repeat: 9
                });
            },
            services: function() {
                $('.scene__item-creator-table').removeClass('scene__item-creator-table-aha');

                /* Parallax */
                _f($('.scene__item-services-line1'), 3, {
                    marginLeft: '-=100px'
                }, {
                    marginLeft: '+=100px'
                });
                _f($('.scene__item-services-line2'), 3, {
                    marginLeft: '-=150px'
                }, {
                    marginLeft: '+=150px'
                });
                _f($('.scene__item-services-line3'), 3, {
                    marginLeft: '-=300px'
                }, {
                    marginLeft: '+=300px'
                });
                _f($('.scene__item-services-line4'), 3, {
                    marginLeft: '-=500px'
                }, {
                    marginLeft: '+=500px'
                });

                /* Fading in */
                _s($('#services'), {
                    autoAlpha: 1
                });
                _s($('.scene__item-services-line1,.scene__item-services-line2,.scene__item-services-line3,.scene__item-services-line4'), {
                    autoAlpha: 0
                });
                _t($('.scene__item-cannon,.services,.scene__item-services-line4.scene__item-cloud'), 1.5, {
                    autoAlpha: 1
                });
                _st([$('.scene__item-creator-table'), $('.scene__item-screen'), $('.scene__item-cord,.scene__item-dynamite'), $('.scene__item-spruce'), $('.scene__item-pictures')], 1.5, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);

                _st([$('.scene__item-lamp1'), $('.scene__item-pipe'), $('.scene__item-lamp2,.scene__item-lamp3'), $('.scene__item-horns')], 1.5, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);

                /* Bubble */
                var $bubble = $(b.stages['services'].bubble),
                    $list = $(b.stages['services'].listItems).getRandoms([]),
                    $bird = $(b.stages['services'].bird),
                    i = 0;
                _s([$bubble, $bird], {
                    scale: 0
                });
                _t($bubble, 1, {
                    scale: 1,
                    delay: 3
                });
                _s($(b.stages['services'].listItems), {
                    perspective: 100
                });
                _st($list, 0.6, {
                    scale: 0.7,
                    delay: 4
                }, 1.1);
                _st($list, 0.1, {
                    rotationY: '-90deg',
                    delay: 4,
                    yoyo: true,
                    repeat: 6,
                    onComplete: function() {
                        i++;
                        var $icon = $(this)[0].target,
                            $item = $icon.parents(b.stages['services'].listItemLinks);
                        _t($icon.prependTo($bubble), 0.2, {
                            rotationY: '0deg'
                        });
                        _t($icon, 0.1, {
                            rotationX: '90deg',
                            delay: 0.2,
                            yoyo: true,
                            repeat: 3,
                            ease: Linear.easeNone
                        });
                        _t($icon, 0.15, {
                            rotationY: '90deg',
                            delay: 0.5,
                            yoyo: true,
                            repeat: 4,
                            ease: Linear.easeNone,
                            onComplete: function() {
                                var _scale = ($item.parents('ul').hasClass('services-list_opened') && !$item.parents('li').hasClass('services-list__item_open')) ? 0.5 : 1,
                                    _left = ($item.parents('ul').hasClass('services-list_opened') && !$item.parents('li').hasClass('services-list__item_open')) ? -65 : -85;
                                _t($icon.appendTo($item), 0.5, {
                                    rotationY: '0deg',
                                    scale: _scale,
                                    left: _left,
                                    ease: Back.easeOut
                                });
                            }
                        });
                    }
                }, 1.1);

                _f($bird, 0.8, {
                    backgroundPosition: '0 0'
                }, {
                    backgroundPosition: '-588px 0',
                    ease: SteppedEase.config(7),
                    delay: 10,
                    repeat: -1
                });
                _t($bird, 0.5, {
                    scale: 1,
                    ease: Back.easeOut,
                    delay: 9.4,
                    onStart: function() {
                        _k($bubble);
                        $('.scene__item-creator-table').addClass('scene__item-creator-table-aha');
                    }
                });
            },
            clients: function() {
                /* Parallax */
                _f($('.scene__item-cloud1'), 1.5, {
                    marginLeft: '-=200px'
                }, {
                    marginLeft: '+=200px'
                });
                _f($('.scene__item-cloud2'), 1.5, {
                    marginLeft: '+=200px'
                }, {
                    marginLeft: '-=200px'
                });
                _f($('.scene__item-bird,.scene__item-bird-cloud'), 1.5, {
                    marginLeft: '-=100px'
                }, {
                    marginLeft: '+=100px'
                });
                _f($('.scene__item-consumer,.scene__item-consumer-cloud'), 1.5, {
                    marginLeft: '+=100px'
                }, {
                    marginLeft: '-=100px'
                });

                /* Fading in */
                _s($('#clients'), {
                    autoAlpha: 1
                });
                _s($('.scene__item-bird,.scene__item-bird-cloud,.scene__item-consumer,.scene__item-consumer-cloud,.scene__item-cloud1,.scene__item-cloud2'), {
                    autoAlpha: 0
                });
                _st([$('.scene__item-bird,.scene__item-bird-cloud'), $('.scene__item-consumer,.scene__item-consumer-cloud'), $('.scene__item-cloud1,.scene__item-cloud2')], 1, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);

                /* Wing */
                _s($('.scene__item-bird__wing'), {
                    rotation: '90deg'
                });
                _t($('.scene__item-bird__wing'), 0.7, {
                    rotation: '0deg',
                    delay: 1.8
                });
                _t($('.scene__item-bird__wing'), 0.3, {
                    rotation: '5deg',
                    yoyo: true,
                    repeat: 5,
                    delay: 2.5
                });
                _t($('.scene__item-bird-tooltip'), 0.3, {
                    autoAlpha: 1,
                    delay: 2.5
                });
            },
            portfolio: function() {
                /* Parallax */
                _f($('.scene__item-portfolio-line1'), 3, {
                    marginLeft: '-=120px'
                }, {
                    marginLeft: '+=120px'
                });
                _f($('.scene__item-portfolio-line2'), 3, {
                    marginLeft: '-=150px'
                }, {
                    marginLeft: '+=150px'
                });
                _f($('.scene__item-portfolio-line3'), 3, {
                    marginLeft: '-=300px'
                }, {
                    marginLeft: '+=300px'
                });
                _f($('.scene__item-portfolio-line4'), 3, {
                    marginLeft: '-=600px'
                }, {
                    marginLeft: '+=600px'
                });

                /* Fading in */
                _s($('#portfolio'), {
                    autoAlpha: 1
                });
                _s($('.scene__item-portfolio-line1,.scene__item-portfolio-line2,.scene__item-portfolio-line3,.scene__item-portfolio-line4'), {
                    autoAlpha: 0
                });
                _st([$('.portfolio'), $('.scene__item-front-trees2'), $('.scene__item-boy,.scene__item-girl'), $('.scene__item-guys'), $('.scene__item-house1'), $('.scene__item-car'), $('.scene__item-trees-left,.scene__item-trees-middle,.scene__item-trees-right'), $('.scene__item-scene,.scene__item-bycicle'), $('.scene__item-house-back')], 1.5, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);

                _st($('.scene__item-cloud'), 1.5, {
                    autoAlpha: 1,
                    delay: 0.1
                }, 0.3);
                _sf($('.faded'), 0.5, {
                    autoAlpha: 0
                }, {
                    autoAlpha: 1,
                    ease: Back.easeIn,
                    delay: 3
                }, 0.2);

                /* Bird */
                var $bird = $(b.stages['portfolio'].bird).show();;
                _f($bird, 1, {
                    left: '-185px'
                }, {
                    left: '5%',
                    delay: 2
                });
            },
            contacts: function() {
                /*  */
                _f($('#contacts'), 2, {
                    autoAlpha: 0
                }, {
                    autoAlpha: 1
                });

                /* Bg */
                _s($('.scene__item-background-b'), {
                    autoAlpha: 0
                });
                _f($('.scene__item-background'), 2, {
                    scale: 0.8
                }, {
                    scale: 1
                });
                _t($('.scene__item-background-b'), 1, {
                    autoAlpha: 1,
                    delay: 1,
                    ease: Linear.none
                });
                _t($('.scene__item-background-a'), 1, {
                    autoAlpha: 0,
                    delay: 1,
                    ease: Linear.none
                });

                _s($('.scene__item-deal'), {
                    transformOrigin: '0 100%'
                });
                _f($('.scene__item-deal'), 0.5, {
                    rotationX: '-90deg'
                }, {
                    rotationX: '0deg',
                    delay: 2
                });
            }
        },

        endAnimation: {
            simple: function(callback, callbackParams) {
                _t($('#' + b.prevScene), 0.3, {
                    autoAlpha: 0,
                    onComplete: callback,
                    onCompleteParams: [callbackParams]
                });
            },

            index: function(callback, callbackParams) {
                var $man = $(b.stages['index'].man);
                _k($man);
                _k($('.scene__item-index-tooltip__slide'));

                /* tooltip */
                _s($('.scene__item-index-tooltip__slide'), {
                    backgroundPosition: '0 0'
                })
                _t($('.scene__item-index-tooltip'), 0.2, {
                    scale: 0
                });

                /* man */
                $('.scene__item-birds,.scene__item-house').css('z-index', 3);
                _t($man.css('z-index', 2), 0.5, {
                    marginLeft: '-200px',
                    ease: Linear.easeNone
                });
                _t($man, 1, {
                    marginLeft: '22px',
                    marginTop: '-164px',
                    ease: Linear.easeNone,
                    delay: 0.5,
                    onComplete: function() {
                        _t($('#index'), 0.2, {
                            autoAlpha: 0,
                            onComplete: callback,
                            onCompleteParams: [callbackParams]
                        });
                    }
                });
                _f($man, 0.6, {
                    backgroundPosition: '0 0'
                }, {
                    backgroundPosition: '0 -1120px',
                    ease: SteppedEase.config(5),
                    repeat: -1
                });
            },
            principles: function(callback, callbackParams) {

                if (b.stages['principles'].lightOff) {
                    b.isDragging = true;
                    b.events.switchMouseUp.call($('.scene__item-switch'));
                }

                var $tooltip = $(b.stages['principles'].tooltip),
                    $creatorBubble = $(b.stages['principles'].creatorBubble);
                _k($tooltip);
                _k($('.scene__item-mustache'));

                _st([$tooltip, $creatorBubble, $tooltip], 0.5, {
                    scale: 0,
                    onComplete: function() {
                        $(this._targets[0]).find('.principles-tooltip__txt').toggle();
                    }
                }, 1.4);
                _sf([$creatorBubble.show(), $tooltip, $creatorBubble], 0.5, {
                    scale: 0
                }, {
                    scale: 1
                }, 1.4);
                _sf([$creatorBubble, $tooltip, $creatorBubble], 0.3, {
                    rotation: '0deg'
                }, {
                    rotation: '2deg',
                    delay: 0.5,
                    yoyo: true,
                    repeat: 3
                }, 1.4);
                _f($('.scene__item-mustache'), 0.1, {
                    rotation: '0deg',
                    top: '68px'
                }, {
                    rotation: '-1deg',
                    top: '67px',
                    delay: 1.9,
                    yoyo: true,
                    repeat: 9
                });

                _t($('#principles'), 1, {
                    autoAlpha: 0,
                    delay: 4.2,
                    onComplete: callback,
                    onCompleteParams: [callbackParams]
                });
            },
            services: function(callback, callbackParams) {
                var w = $(b.contentSelector).width(),
                    h = $(b.contentSelector).height(),
                    $bird = $(b.stages['services'].bird).css({
                        margin: 0
                    }),
                    $bubble = $(b.stages['services'].bubble);

                _t($bubble, 0.5, {
                    autoAlpha: 0
                });
                _f($bird.css('z-index', 10), 2, {
                    top: h / 2 + 30,
                    left: w / 2 - 274
                }, {
                    top: 0,
                    left: w + 10 + 'px',
                    scale: 1.1,
                    ease: Cubic.easeIn
                });
                _t($('#services'), 1, {
                    autoAlpha: 0,
                    delay: 3,
                    onComplete: callback,
                    onCompleteParams: [callbackParams]
                });
            },
            clients: function(callback, callbackParams) {
                _t($('.scene__item-consumer-tooltip2'), 0.3, {
                    autoAlpha: 1
                });
                _t($('.scene__item-consumer-tooltip2'), 0.3, {
                    rotation: '2deg',
                    yoyo: true,
                    repeat: 5
                });
                _t($('.scene__item-bird__wing'), 0.4, {
                    rotation: '90deg',
                    delay: 1.5
                });
                _t($('.scene__item-consumer__face_happy'), 1.5, {
                    autoAlpha: 1
                });
                _t($('.scene__item-consumer__face_n'), 0.3, {
                    autoAlpha: 0,
                    delay: 1
                });
                _t($('.scene__item-consumer__mustache'), 0.3, {
                    rotation: '-2deg',
                    yyoyo: true,
                    repeat: -1
                });

                _t($('#clients'), 1, {
                    autoAlpha: 0,
                    delay: 3.5,
                    onComplete: callback,
                    onCompleteParams: [callbackParams]
                });
            },
            portfolio: function(callback, callbackParams) {
                var $bird = $(b.stages['portfolio'].bird),
                    w = $(b.contentSelector).width();

                _t($bird, 1.5, {
                    left: w + 'px',
                    ease: Linear.easeNone
                });
                var t = 0,
                    _int = setInterval(function() {
                        t++;
                        var bl = +$bird.css('left').replace('px', '');
                        b.stages['portfolio'].checkActiveImg(bl);
                        if (t >= 15) clearInterval(_int);
                    }, 100);
                _t($('#portfolio'), 1, {
                    autoAlpha: 0,
                    delay: 1.2,
                    onComplete: callback,
                    onCompleteParams: [callbackParams]
                });
            },
            contacts: function(callback, callbackParams) {
                _t($('#contacts'), 2, {
                    autoAlpha: 0,
                    onComplete: callback,
                    onCompleteParams: [callbackParams]
                });
            }
        },

        showTooltip: {
            simple: function() {
                clearTimeout(b.timer);
                b.stages.showTooltip[b.currentScene]();
            },

            index: function() {
                _f($('.scene__item-index-tooltip').show(), 0.5, {
                    scale: 0
                }, {
                    scale: 1
                });
                _f($('.scene__item-index-tooltip__slide'), 2, {
                    backgroundPosition: '0 0'
                }, {
                    backgroundPosition: '-1330px 0',
                    ease: SteppedEase.config(19),
                    delay: 0.6,
                    repeat: -1
                });
            },
            principles: function() {
                var $tooltip = $(b.stages['principles'].tooltip);
                _f($tooltip, 0.3, {
                    rotation: '0deg'
                }, {
                    rotation: '2deg',
                    yoyo: true,
                    repeat: -1
                });
                _f($tooltip, 0.6, {
                    scale: 1
                }, {
                    scale: 1.05,
                    delay: 2,
                    yoyo: true,
                    repeat: -1
                });
                _f($('.scene__item-mustache'), 0.1, {
                    rotation: '0deg',
                    top: '68px'
                }, {
                    rotation: '-1deg',
                    top: '67px',
                    yoyo: true,
                    repeat: -1
                });
            },
            services: function() {
                var $bird = $(b.stages['services'].bird);
                _t($bird, 3, {
                    scale: 1.1,
                    yoyo: true,
                    repeat: -1
                });
            },
            clients: function() {
                _f($('.scene__item-bird__wing'), 0.3, {
                    rotation: '0deg'
                }, {
                    rotation: '5deg',
                    yoyo: true,
                    repeat: -1
                });
            },
            portfolio: function() {
                var $bird = $(b.stages['portfolio'].bird),
                    w = $(b.contentSelector).width(),
                    l = w / 2 - 100;

                _t($bird, 1.5, {
                    left: l + 'px',
                    ease: Linear.easeNone
                });
                var t = 0,
                    _int = setInterval(function() {
                        t++;
                        var bl = +$bird.css('left').replace('px', '');
                        b.stages['portfolio'].checkActiveImg(bl);
                        if (t >= 15) clearInterval(_int);
                    }, 100);
            },
            contacts: function() {}
        },

        resetAll: {
            index: function() {
                _s($('#index'), {
                    autoAlpha: 0
                });
                $('.scene__item-birds,.scene__item-house').removeAttr('style');
                $('.scene__item-index-tooltip').hide();

                var $man = $(b.stages['index'].man);
                _s($man.removeAttr('style'), {
                    backgroundPosition: '0 0',
                    marginLeft: '-338px',
                    marginTop: '-64px'
                });
            },
            principles: function() {
                _s($('#principles'), {
                    autoAlpha: 0
                });

                var $tooltip = $(b.stages['principles'].tooltip),
                    $creatorBubble = $(b.stages['principles'].creatorBubble);
                _s($tooltip, {
                    scale: 1
                });
                _s($creatorBubble, {
                    scale: 0
                });
                $creatorBubble.find('.principles-tooltip__txt').toggle();
            },
            services: function() {
                _s($('#services'), {
                    autoAlpha: 0
                });
                $('.scene__item-creator-table').removeClass('scene__item-creator-table-aha');

                var $bird = $(b.stages['services'].bird);
                $bird.removeAttr('style');
            },
            clients: function() {
                _s($('#clients'), {
                    autoAlpha: 0
                });
                _k($('.scene__item-consumer__mustache'));
                _s($('.scene__item-bird__wing'), {
                    rotation: '90deg'
                });
                _s($('.scene__item-bird-tooltip'), {
                    autoAlpha: 1
                });
            },
            portfolio: function() {
                _s($('#portfolio'), {
                    autoAlpha: 0
                });

                var $bird = $(b.stages['portfolio'].bird);
                _s($bird, {
                    left: '10px'
                });

                _s($('.activate .bright'), {
                    autoAlpha: 0
                });
                _s($('.activate .grey'), {
                    autoAlpha: 1
                });
                _s($('.activate_active .bright'), {
                    autoAlpha: 1
                });
                _s($('.activate_active .grey'), {
                    autoAlpha: 0
                });
                $('.activate').removeClass('activate_active');
            },
            contacts: function() {
                _s($('#contacts'), {
                    autoAlpha: 0
                });
            }
        },

        ajaxFunc: {
            index: function() {
                $('.bounce').on('mousedown touchstart', b.events.bounceMouseDown)
                    .on('mouseup touchend', b.events.bounceMouseUp)
                    .on('mousemove touchmove', b.events.bounceMouseMove);

                $('.scene__item-cloud_light').on('mousedown touchstart', b.events.bounceMouseDown)
                    .on('mouseup touchend', b.events.bounceLightMouseUp)
                    .on('mousemove touchmove', b.events.bounceMouseMove);

                var $man = $(b.stages['index'].man);
                $man.on('click', function() {
                    b.animated = true;
                    b.$menuChildren.eq(b.currentSceneIndex + 1).find('a').trigger('click');
                });

                /* Window */
                var wi = 0;
                $('.scene__item-house__window').on('click', function() {
                    var $this = $(this);
                    if (wi < 3) {
                        _t($('.scene__item-house__broken-w'), 0.1, {
                            backgroundPosition: '0 -' + (45 * wi) + 'px',
                            ease: SteppedEase.config(1)
                        });
                        wi++;
                    }
                    if (wi == 3) {
                        wi++;
                        var $win = $('.scene__item-house__broken-w'),
                            $clone = $win.clone().appendTo($this);
                        _s($clone, {
                            backgroundPosition: '0 -90px'
                        });
                        _s($win, {
                            backgroundPosition: '0 -45px',
                            delay: 3
                        });

                        _t($clone, 0.5, {
                            autoAlpha: 0,
                            delay: 3,
                            onComplete: function() {
                                _s($clone, {
                                    backgroundPosition: '0 0',
                                    autoAlpha: 1
                                });
                                _t($win, 0.5, {
                                    autoAlpha: 0,
                                    delay: 0.2,
                                    onComplete: function() {
                                        _t($clone, 0.5, {
                                            autoAlpha: 0,
                                            delay: 0.2,
                                            onComplete: function() {
                                                $clone.remove();
                                                _s($win, {
                                                    backgroundPosition: '0 45px',
                                                    autoAlpha: 1
                                                });
                                                wi = 0;
                                            }
                                        });
                                    }
                                });
                            }
                        });
                    }
                });

                var $meow = $(b.stages['index'].meow);
                _s($meow, {
                    scale: 0
                });
                $('.scene__item-cat').on('click', function() {
                    var $cat = $(this).addClass('active');
                    _t($meow, 0.2, {
                        scale: 1
                    });
                    _f($meow, 0.2, {
                        rotation: '0deg'
                    }, {
                        rotation: '2deg',
                        repeat: -1
                    });
                    _t($meow, 0.2, {
                        scale: 0,
                        delay: 0.8,
                        onComplete: function() {
                            $cat.removeClass('active');
                        }
                    });
                });
            },
            principles: function() {
                $('.bounce').on('mousedown touchstart', b.events.bounceMouseDown)
                    .on('mouseup touchend', b.events.bounceMouseUp)
                    .on('mousemove touchmove', b.events.bounceMouseMove);
                $('.scene__item-door__kitty').on('mousedown touchstart', b.events.kittyMouseDown)
                    .on('mouseup mouseout touchend', b.events.kittyMouseUp)
                    .on('mousemove touchmove', b.events.kittyMouseMove);
                var $tooltip = $(b.stages['principles'].tooltip).show();
                $('.scene__item-switch').on('mousedown touchstart', b.events.switchMouseDown)
                    .on('mouseup mouseout touchend', b.events.switchMouseUp)
                    .on('mousemove touchmove', b.events.switchMouseMove);
            },
            services: function() {
                $('.scene__item-creator-table').addClass('scene__item-creator-table-aha');

                var $bird = $(b.stages['services'].bird).show(),
                    $bubble = $(b.stages['services'].bubble).show();

                _s($bubble, {
                    autoAlpha: 1,
                    scale: 1
                });
                $('.bounce').on('mousedown touchstart', b.events.bounceMouseDown)
                    .on('mouseup touchend', b.events.bounceMouseUp)
                    .on('mousemove touchmove', b.events.bounceMouseMove);
                _f($bird, 0.8, {
                    backgroundPosition: '0 0'
                }, {
                    backgroundPosition: '-588px 0',
                    ease: SteppedEase.config(7),
                    repeat: -1
                });

                /* Services layout */
                var _top = 0,
                    _left = 160;
                $(b.stages['services'].listItems).each(function(i) {
                    $(this).css({
                        left: _left,
                        top: _top * 100
                    });
                    _top++;
                    if (i == 3) {
                        _left = 300;
                        _top = 0;
                    }
                });
                $('.services-list_opened').removeClass('services-list_opened');
                $('.services-list__item_open').removeClass('services-list__item_open');
                $('.services-list__item-descr').hide();
                _s($(b.stages['services'].listItems).find('i'), {
                    scale: 1,
                    left: -85
                });

                /* Services open */
                b.stages['services'].opened = false;
                $(b.stages['services'].listItemLinks).off('click').on('click', function() {

                    var $link = $(this),
                        $item = $link.parents(b.stages['services'].listItems),
                        $list = $link.parents('ul'),
                        $descr = $item.find('.services-list__item-descr'),
                        $btn = $descr.find('[data-modal]');

                    /*####*/

                    $btn.on('click', function() {
                        var
                            modal_window = $(this).attr('data-modal'),
                            modal_what = $(this).attr('data-value');

                        closeModals();

                        $('#' + modal_window + ' .caption').html(modal_what);
                        $('#' + modal_window + ' input[name="what"]').val(modal_what);

                        showModal(modal_window);
                    });

                    $('.modal-close').on('click', function() {
                        var modal_window = $(this).closest('modal-form').attr('id');
                        closeModals();
                        $('#' + modal_window).trigger('modal_window:off');
                    });

                    $('.modal-form').on('modal_window:on', function() {
                        $(document).bind('mouseup', function(e) {
                            var container = $('.modal-form, #ui-datepicker-div');
                            if (!container.is(e.target) && container.has(e.target).length === 0) {
                                closeModals();
                                container.trigger('modal_window:off');
                            }
                        });
                    });

                    function showModal(id) {
                        if ($('#' + id).length) {
                            $('#modal-back').addClass('active');
                            $('#' + id).addClass('active').trigger('modal_window:on');
                        }
                        return false;
                    }

                    function closeModals() {
                        $('#modal-back, .modal-form').removeClass('active');
                        $('#modal-order .caption').html('Форма обратной связи');
                        $('input[name="what"]').val('');
                        $('.modal-form .btn').css({ 'visibility': 'visible' });
                        return false;
                    }

                    $('.form-container').on('submit', function() {
                        var form = document.getElementById('form-feedback');
                        var formData = new FormData(form);

                        $(this).find('.btn').css({ 'visibility': 'hidden' });

                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', '/media/forms/send.php');
                        xhr.onreadystatechange = function() {
                            if (xhr.readyState === 4) {
                                if (xhr.status === 200) {
                                    var data = xhr.responseText;

                                    $(form)[0].reset();

                                    closeModals();

                                    if (data == 'ok') {
                                        showModal('modal-success');
                                    } else {
                                        showModal('modal-error');
                                    }

                                    $(this).find('.btn').css({ 'visibility': 'visible' });
                                }
                            }
                        };
                        xhr.send(formData);

                        return false;
                    });

                    /*####*/

                    if (b.stages['services'].opened) {

                        if ($item.hasClass('services-list__item_open')) {
                            $list.removeClass('services-list_opened');
                            $item.removeClass('services-list__item_open');

                            _top = 0, _left = 160;
                            $(b.stages['services'].listItems).each(function(i) {
                                _t($(this), 0.4, {
                                    left: _left,
                                    top: _top * 100
                                });
                                _top++;
                                if (i == 3) {
                                    _left = 300;
                                    _top = 0;
                                }
                            });

                            _t($(b.stages['services'].listItems).find('i'), 0.2, {
                                scale: 1,
                                left: -85
                            });

                            $descr.slideUp(100);
                            b.stages['services'].opened = false;

                        } else {
                            $('.services-list__item_open').removeClass('services-list__item_open');
                            $('.services-list__item-descr').slideUp(100);

                            _t($item.addClass('services-list__item_open'), 0.3, {
                                top: 0,
                                left: 0
                            });
                            _t($item.find('i'), 0.2, {
                                scale: 1,
                                left: -85
                            });

                            var $otherItems = $(b.stages['services'].listItems).not('.services-list__item_open'),
                                _nTop = 0;

                            $otherItems.each(function(i) {
                                _t($(this), 0.4, {
                                    top: i * 60,
                                    left: 300
                                });
                            });

                            _t($otherItems.find('i'), 0.2, {
                                scale: 0.5,
                                left: -65
                            });

                            $descr.slideDown(100);
                            b.stages['services'].opened = true;
                        }

                    } else {
                        $list.addClass('services-list_opened');
                        _t($item.addClass('services-list__item_open'), 0.3, {
                            top: 0,
                            left: 0
                        });

                        var $otherItems = $(b.stages['services'].listItems).not('.services-list__item_open'),
                            _nTop = 0;

                        $otherItems.each(function(i) {
                            _t($(this), 0.4, {
                                top: i * 60,
                                left: 300
                            });
                        });

                        _t($otherItems.find('i'), 0.2, {
                            scale: 0.5,
                            left: -65
                        });

                        $descr.slideDown(100);
                        b.stages['services'].opened = true;
                    }
                });

                /* Legs */
                _s($('.scene__item-screen__leg1,.scene__item-screen__leg2'), {
                    rotation: '-110deg'
                });
                $('.scene__item-screen__item').off('click').on('click', function() {
                    var rnd = Math.round(Math.random() * 2);
                    if (!b.stages['services'].screen) {
                        if (rnd == 1) {
                            _f($('.scene__item-screen__leg2'), 0.6, {
                                rotation: '-110deg'
                            }, {
                                rotation: '0deg',
                                onComplete: function() {
                                    _t($('.scene__item-screen__leg2'), 0.3, {
                                        rotation: '-2deg',
                                        yoyo: true,
                                        repeat: -1
                                    });
                                }
                            });
                            _f($('.scene__item-screen__leg1'), 0.8, {
                                rotation: '-110deg'
                            }, {
                                rotation: '0deg',
                                delay: 0.3,
                                onComplete: function() {
                                    _t($('.scene__item-screen__leg1'), 0.2, {
                                        rotation: '-2deg',
                                        yoyo: true,
                                        repeat: -1
                                    });
                                }
                            });

                            _t($('.scene__item-screen__leg1,.scene__item-screen__leg2'), 0.3, {
                                rotation: '-110deg',
                                delay: 5,
                                onComplete: function() {
                                    _k($('.scene__item-screen__leg1,.scene__item-screen__leg2'));
                                    b.stages['services'].screen = false;
                                }
                            });
                        } else {
                            _t($('.scene__item-screen__hat'), 0.1, {
                                rotation: '-2deg',
                                yoyo: true,
                                repeat: -1
                            });
                            _f($('.scene__item-screen__hat'), 0.5, {
                                left: '+=10px'
                            }, {
                                left: '-=60px',
                                delay: 0.1,
                                yoyo: true,
                                repeat: -1
                            });
                            _t($('.scene__item-screen__hat'), 2.5, {
                                top: '410px',
                                onComplete: function() {
                                    _k($('.scene__item-screen__hat'));
                                    _s($('.scene__item-screen__hat'), {
                                        rotation: '20deg'
                                    });

                                    _t($('.scene__item-screen__hat'), 0.1, {
                                        autoAlpha: 0,
                                        delay: 5,
                                        onComplete: function() {
                                            _t($('.scene__item-screen__hat').removeAttr('style'), 0.1, {
                                                autoAlpha: 1
                                            });
                                            b.stages['services'].screen = false;
                                        }
                                    });
                                }
                            });
                        }
                    }
                    b.stages['services'].screen = true;
                });

                /* Bang */
                b.stages['services'].sparkSpeed = 0.025;
                $('.scene__item-cord-button').one('click', function() {
                    var $button = $(this).addClass('scene__item-cord-button_down'),
                        $spark = $('.scene__item-cord-spark'),
                        s = b.stages['services'].sparkSpeed;
                    _f($spark, 0.3, {
                        backgroundPosition: '0 0'
                    }, {
                        backgroundPosition: '0 -32px',
                        ease: SteppedEase.config(2),
                        repeat: -1
                    });
                    _t($spark, 3 * s, {
                        left: '903px',
                        top: '75px',
                        ease: Linear.easeNone
                    });
                    _t($spark, 5 * s, {
                        left: '922px',
                        top: '89px',
                        ease: Linear.easeNone,
                        delay: 3 * s
                    });
                    _t($spark, 6 * s, {
                        left: '950px',
                        top: '98px',
                        ease: Linear.easeNone,
                        delay: 8 * s
                    });
                    _t($spark, 4 * s, {
                        left: '965px',
                        top: '108px',
                        ease: Linear.easeNone,
                        delay: 14 * s
                    });
                    _t($spark, 3 * s, {
                        left: '965px',
                        top: '121px',
                        ease: Linear.easeNone,
                        delay: 18 * s
                    });
                    _t($spark, 3 * s, {
                        left: '955px',
                        top: '131px',
                        ease: Linear.easeNone,
                        delay: 21 * s
                    });
                    _t($spark, 9 * s, {
                        left: '910px',
                        top: '134px',
                        ease: Linear.easeNone,
                        delay: 24 * s
                    });
                    _t($spark, 3 * s, {
                        left: '898px',
                        top: '142px',
                        ease: Linear.easeNone,
                        delay: 33 * s
                    });
                    _t($spark, 2 * s, {
                        left: '893px',
                        top: '151px',
                        ease: Linear.easeNone,
                        delay: 36 * s
                    });
                    _t($spark, 4 * s, {
                        left: '877px',
                        top: '159px',
                        ease: Linear.easeNone,
                        delay: 38 * s
                    });
                    _t($spark, s, {
                        left: '877px',
                        top: '163px',
                        ease: Linear.easeNone,
                        delay: 42 * s
                    });
                    _t($spark, 4 * s, {
                        left: '898px',
                        top: '171px',
                        ease: Linear.easeNone,
                        delay: 43 * s
                    });
                    _t($spark, 8 * s, {
                        left: '938px',
                        top: '176px',
                        ease: Linear.easeNone,
                        delay: 47 * s
                    });
                    _t($spark, 4 * s, {
                        left: '958px',
                        top: '179px',
                        ease: Linear.easeNone,
                        delay: 55 * s
                    });
                    _t($spark, 2 * s, {
                        left: '968px',
                        top: '183px',
                        ease: Linear.easeNone,
                        delay: 59 * s
                    });
                    _t($spark, 2 * s, {
                        left: '978px',
                        top: '176px',
                        ease: Linear.easeNone,
                        delay: 61 * s
                    });
                    _t($spark, s, {
                        left: '973px',
                        top: '179px',
                        ease: Linear.easeNone,
                        delay: 63 * s
                    });
                    _t($spark, s, {
                        left: '968px',
                        top: '179px',
                        ease: Linear.easeNone,
                        delay: 64 * s
                    });
                    _t($spark, s, {
                        left: '968px',
                        top: '183px',
                        ease: Linear.easeNone,
                        delay: 65 * s
                    });
                    _t($spark, 2 * s, {
                        left: '978px',
                        top: '179px',
                        ease: Linear.easeNone,
                        delay: 66 * s
                    });
                    _t($spark, s, {
                        left: '973px',
                        top: '179px',
                        ease: Linear.easeNone,
                        delay: 68 * s
                    });
                    _t($spark, s, {
                        left: '973px',
                        top: '183px',
                        ease: Linear.easeNone,
                        delay: 69 * s
                    });
                    _t($spark, 2 * s, {
                        left: '978px',
                        top: '190px',
                        ease: Linear.easeNone,
                        delay: 70 * s
                    });
                    _t($spark, 2 * s, {
                        left: '978px',
                        top: '200px',
                        ease: Linear.easeNone,
                        delay: 72 * s
                    });
                    _t($spark, 4 * s, {
                        left: '963px',
                        top: '215px',
                        ease: Linear.easeNone,
                        delay: 74 * s
                    });
                    _t($spark, 5 * s, {
                        left: '941px',
                        top: '223px',
                        ease: Linear.easeNone,
                        delay: 78 * s
                    });
                    _t($spark, 4 * s, {
                        left: '922px',
                        top: '228px',
                        ease: Linear.easeNone,
                        delay: 83 * s
                    });
                    _t($spark, s, {
                        left: '921px',
                        top: '232px',
                        ease: Linear.easeNone,
                        delay: 87 * s
                    });
                    _t($spark, 6 * s, {
                        left: '950px',
                        top: '236px',
                        ease: Linear.easeNone,
                        delay: 88 * s
                    });
                    _t($spark, s, {
                        left: '953px',
                        top: '239px',
                        ease: Linear.easeNone,
                        delay: 94 * s
                    });
                    _t($spark, 3 * s, {
                        left: '941px',
                        top: '245px',
                        ease: Linear.easeNone,
                        delay: 95 * s
                    });
                    _t($spark, 3 * s, {
                        left: '924px',
                        top: '248px',
                        ease: Linear.easeNone,
                        delay: 98 * s
                    });
                    _t($spark, 3 * s, {
                        left: '910px',
                        top: '245px',
                        ease: Linear.easeNone,
                        delay: 101 * s
                    });
                    _t($spark, 4 * s, {
                        left: '891px',
                        top: '245px',
                        ease: Linear.easeNone,
                        delay: 104 * s
                    });
                    _t($spark, s, {
                        left: '887px',
                        top: '248px',
                        ease: Linear.easeNone,
                        delay: 108 * s
                    });
                    _t($spark, 2 * s, {
                        left: '898px',
                        top: '252px',
                        ease: Linear.easeNone,
                        delay: 109 * s
                    });
                    _t($spark, 3 * s, {
                        left: '912px',
                        top: '250px',
                        ease: Linear.easeNone,
                        delay: 111 * s
                    });
                    _t($spark, s, {
                        left: '912px',
                        top: '247px',
                        ease: Linear.easeNone,
                        delay: 114 * s
                    });
                    _t($spark, 4 * s, {
                        left: '893px',
                        top: '240px',
                        ease: Linear.easeNone,
                        delay: 115 * s
                    });
                    _t($spark, 5 * s, {
                        left: '868px',
                        top: '239px',
                        ease: Linear.easeNone,
                        delay: 119 * s
                    });
                    _t($spark, 3 * s, {
                        left: '853px',
                        top: '241px',
                        ease: Linear.easeNone,
                        delay: 124 * s
                    });
                    _t($spark, 6 * s, {
                        left: '825px',
                        top: '238px',
                        ease: Linear.easeNone,
                        delay: 127 * s
                    });
                    _t($spark, s, {
                        left: '831px',
                        top: '234px',
                        ease: Linear.easeNone,
                        delay: 133 * s
                    });
                    _t($spark, 3 * s, {
                        left: '845px',
                        top: '234px',
                        ease: Linear.easeNone,
                        delay: 134 * s
                    });
                    _t($spark, 2 * s, {
                        left: '852px',
                        top: '238px',
                        ease: Linear.easeNone,
                        delay: 137 * s
                    });
                    _t($spark, 2 * s, {
                        left: '846px',
                        top: '244px',
                        ease: Linear.easeNone,
                        delay: 139 * s
                    });
                    _t($spark, 6 * s, {
                        left: '815px',
                        top: '250px',
                        ease: Linear.easeNone,
                        delay: 141 * s
                    });
                    _t($spark, 11 * s, {
                        left: '759px',
                        top: '248px',
                        ease: Linear.easeNone,
                        delay: 147 * s
                    });
                    _t($spark, 8 * s, {
                        left: '719px',
                        top: '237px',
                        ease: Linear.easeNone,
                        delay: 158 * s
                    });
                    _t($spark, 9 * s, {
                        left: '676px',
                        top: '230px',
                        ease: Linear.easeNone,
                        delay: 166 * s
                    });
                    _t($spark, 7 * s, {
                        left: '639px',
                        top: '228px',
                        ease: Linear.easeNone,
                        delay: 175 * s
                    });
                    _t($spark, 4 * s, {
                        left: '619px',
                        top: '234px',
                        ease: Linear.easeNone,
                        delay: 182 * s
                    });
                    _t($spark, 2 * s, {
                        left: '613px',
                        top: '240px',
                        ease: Linear.easeNone,
                        delay: 186 * s
                    });
                    _t($spark, s, {
                        left: '615px',
                        top: '246px',
                        ease: Linear.easeNone,
                        delay: 188 * s
                    });
                    _t($spark, 2 * s, {
                        left: '627px',
                        top: '249px',
                        ease: Linear.easeNone,
                        delay: 189 * s
                    });
                    _t($spark, 4 * s, {
                        left: '647px',
                        top: '247px',
                        ease: Linear.easeNone,
                        delay: 191 * s
                    });
                    _t($spark, 3 * s, {
                        left: '658px',
                        top: '239px',
                        ease: Linear.easeNone,
                        delay: 195 * s
                    });
                    _t($spark, 2 * s, {
                        left: '657px',
                        top: '228px',
                        ease: Linear.easeNone,
                        delay: 198 * s
                    });
                    _t($spark, s, {
                        left: '650px',
                        top: '224px',
                        ease: Linear.easeNone,
                        delay: 200 * s
                    });
                    _t($spark, 2 * s, {
                        left: '639px',
                        top: '224px',
                        ease: Linear.easeNone,
                        delay: 201 * s
                    });
                    _t($spark, s, {
                        left: '639px',
                        top: '221px',
                        ease: Linear.easeNone,
                        delay: 203 * s
                    });
                    _t($spark, s, {
                        left: '643px',
                        top: '219px',
                        ease: Linear.easeNone,
                        delay: 204 * s
                    });
                    _t($spark, 3 * s, {
                        left: '635px',
                        top: '209px',
                        ease: Linear.easeNone,
                        delay: 205 * s
                    });
                    _t($spark, 4 * s, {
                        left: '614px',
                        top: '200px',
                        ease: Linear.easeNone,
                        delay: 208 * s
                    });
                    _t($spark, 8 * s, {
                        left: '577px',
                        top: '194px',
                        ease: Linear.easeNone,
                        delay: 212 * s
                    });
                    _t($spark, 8 * s, {
                        left: '540px',
                        top: '194px',
                        ease: Linear.easeNone,
                        delay: 220 * s
                    });
                    _t($spark, 9 * s, {
                        left: '493px',
                        top: '202px',
                        ease: Linear.easeNone,
                        delay: 228 * s
                    });
                    _t($spark, s, {
                        left: '488px',
                        top: '206px',
                        ease: Linear.easeNone,
                        delay: 237 * s
                    });
                    _t($spark, s, {
                        left: '492px',
                        top: '213px',
                        ease: Linear.easeNone,
                        delay: 238 * s
                    });
                    _t($spark, 8 * s, {
                        left: '533px',
                        top: '213px',
                        ease: Linear.easeNone,
                        delay: 239 * s
                    });
                    _t($spark, s, {
                        left: '537px',
                        top: '216px',
                        ease: Linear.easeNone,
                        delay: 247 * s
                    });
                    _t($spark, 2 * s, {
                        left: '527px',
                        top: '223px',
                        ease: Linear.easeNone,
                        delay: 248 * s
                    });
                    _t($spark, 6 * s, {
                        left: '499px',
                        top: '229px',
                        ease: Linear.easeNone,
                        delay: 250 * s
                    });
                    _t($spark, 5 * s, {
                        left: '474px',
                        top: '234px',
                        ease: Linear.easeNone,
                        delay: 256 * s
                    });
                    _t($spark, 4 * s, {
                        left: '456px',
                        top: '243px',
                        ease: Linear.easeNone,
                        delay: 261 * s
                    });
                    _t($spark, 3 * s, {
                        left: '443px',
                        top: '252px',
                        ease: Linear.easeNone,
                        delay: 265 * s
                    });
                    _t($spark, 7 * s, {
                        left: '409px',
                        top: '263px',
                        ease: Linear.easeNone,
                        delay: 268 * s
                    });
                    _t($spark, 11 * s, {
                        left: '358px',
                        top: '278px',
                        ease: Linear.easeNone,
                        delay: 275 * s
                    });
                    _t($spark, 10 * s, {
                        left: '311px',
                        top: '282px',
                        ease: Linear.easeNone,
                        delay: 286 * s
                    });
                    _t($spark, 4 * s, {
                        left: '291px',
                        top: '282px',
                        ease: Linear.easeNone,
                        delay: 296 * s
                    });
                    _t($spark, 2 * s, {
                        left: '281px',
                        top: '278px',
                        ease: Linear.easeNone,
                        delay: 300 * s
                    });
                    _t($spark, 2 * s, {
                        left: '290px',
                        top: '273px',
                        ease: Linear.easeNone,
                        delay: 302 * s
                    });
                    _t($spark, 3 * s, {
                        left: '303px',
                        top: '271px',
                        ease: Linear.easeNone,
                        delay: 304 * s
                    });
                    _t($spark, 3 * s, {
                        left: '316px',
                        top: '275px',
                        ease: Linear.easeNone,
                        delay: 307 * s
                    });
                    _t($spark, 2 * s, {
                        left: '310px',
                        top: '282px',
                        ease: Linear.easeNone,
                        delay: 310 * s
                    });
                    _t($spark, 7 * s, {
                        left: '276px',
                        top: '293px',
                        ease: Linear.easeNone,
                        delay: 312 * s
                    });
                    _t($spark, 7 * s, {
                        left: '241px',
                        top: '301px',
                        ease: Linear.easeNone,
                        delay: 319 * s
                    });
                    _t($spark, 12 * s, {
                        left: '179px',
                        top: '302px',
                        ease: Linear.easeNone,
                        delay: 326 * s
                    });
                    _t($spark, 14 * s, {
                        left: '108px',
                        top: '291px',
                        ease: Linear.easeNone,
                        delay: 338 * s
                    });
                    _t($spark, 11 * s, {
                        left: '58px',
                        top: '274px',
                        ease: Linear.easeNone,
                        delay: 352 * s
                    });
                    _t($spark, 3 * s, {
                        left: '43px',
                        top: '265px',
                        ease: Linear.easeNone,
                        delay: 363 * s
                    });
                    _t($spark, 3 * s, {
                        left: '34px',
                        top: '256px',
                        ease: Linear.easeNone,
                        delay: 366 * s
                    });
                    _t($spark, 3 * s, {
                        left: '24px',
                        top: '244px',
                        ease: Linear.easeNone,
                        delay: 369 * s
                    });
                    _t($spark, 4 * s, {
                        left: '6px',
                        top: '237px',
                        ease: Linear.easeNone,
                        delay: 372 * s
                    });
                    _t($spark, 2 * s, {
                        left: '0px',
                        top: '230px',
                        ease: Linear.easeNone,
                        delay: 376 * s
                    });
                    _t($spark, 2 * s, {
                        left: '10px',
                        top: '225px',
                        ease: Linear.easeNone,
                        delay: 378 * s
                    });
                    _t($spark, 2 * s, {
                        left: '10px',
                        top: '225px',
                        ease: Linear.easeNone,
                        delay: 380 * s
                    });
                    _t($spark, 2 * s, {
                        left: '-1px',
                        top: '222px',
                        ease: Linear.easeNone,
                        delay: 382 * s
                    });
                    _t($spark, 3 * s, {
                        left: '-8px',
                        top: '209px',
                        ease: Linear.easeNone,
                        delay: 384 * s
                    });
                    _t($spark, s, {
                        left: '-13px',
                        top: '209px',
                        ease: Linear.easeNone,
                        delay: 387 * s,
                        onComplete: function() {
                            var $white = $('<div class="white"></div>').appendTo(b.$wrapper);
                            _k($spark.hide());
                            $('.scene__item-dynamite').hide();
                            $('.scene__item-creator-table-bang').show();
                            $('.scene__item-spruce').addClass('scene__item-spruce_bang');
                            _sf([$('.scene__item-creator-table'), $('.scene__item-spruce')], 0.3, {
                                marginLeft: '-=5px'
                            }, {
                                marginLeft: '+=5px'
                            }, 0.15);
                            _t($white, 1, {
                                autoAlpha: 0,
                                delay: 0.2,
                                onComplete: function() {
                                    $white.remove();
                                }
                            });
                        }
                    });
                });
            },
            clients: function() {
                $('.tooltip').show();
                _s($('.scene__item-bird-tooltip'), {
                    autoAlpha: 0
                });
                _s($('.scene__item-bird__wing'), {
                    rotation: '90deg'
                });
                _s($('.scene__item-bird-tooltip'), {
                    autoAlpha: 1
                });
                _s($('.scene__item-consumer__face_n'), {
                    autoAlpha: 1
                });
                _s($('.scene__item-consumer__face_happy, .scene__item-consumer-tooltip2'), {
                    autoAlpha: 0
                });

                $('.clients-scroll').jScrollPane({
                    contentWidth: 1400
                });
            },
            portfolio: function() {
                $('.bounce').on('mousedown touchstart', b.events.bounceMouseDown)
                    .on('mouseup touchend', b.events.bounceMouseUp)
                    .on('mousemove touchmove', b.events.bounceMouseMove);
                b.slider.initCarousel($('.portfolio-holder .slider'), 3);

                _s($('.activate .bright'), {
                    autoAlpha: 0
                });
                _s($('.activate .grey'), {
                    autoAlpha: 1
                });
                _s($('.activate_active .bright'), {
                    autoAlpha: 1
                });
                _s($('.activate_active .grey'), {
                    autoAlpha: 0
                });
                $('.activate').each(function() {
                    var $act = $(this),
                        w = $('body').width(),
                        l = +$act.css('margin-left').replace('px', ''),
                        left = w / 2 + l + (+$act.attr('data-width'));
                    $act.attr('data-left', w / 2 + l).attr('data-wleft', left);
                });

                var $bird = $(b.stages['portfolio'].bird).show();
                _f($bird, 0.8, {
                    backgroundPosition: '0 0'
                }, {
                    backgroundPosition: '-1295px 0',
                    ease: SteppedEase.config(7),
                    repeat: -1
                });
                $bird.on('mousedown touchstart', b.events.birdMouseDown)
                    .on('mouseup touchend', b.events.birdMouseUp)
                    .on('mousemove touchmove', b.events.birdMouseMove);

                _s($('.scene__item-icecream'), {
                    autoAlpha: 0,
                    rotation: '-30deg'
                });
                $('.scene__item-car').on('click', function() {
                    _t($('.scene__item-icecream'), 0.3, {
                        autoAlpha: 1,
                        rotation: '-80deg',
                        left: '-20px'
                    });
                    _t($('.scene__item-icecream'), 0.2, {
                        autoAlpha: 0,
                        delay: 1
                    });
                    _s($('.scene__item-icecream'), {
                        rotation: '-30deg',
                        left: '10px',
                        delay: 1.2
                    });
                });

                /* Portfolio list */
                $('.portfolio-list__link').on('mouseenter', function() {
                    var $link = $(this),
                        $desc = $(this).find('.portfolio-list__item-desc').removeAttr('style'),
                        $item = $link.parent();

                    $item.css('z-index', 5);
                    _t($desc, 0.3, {
                        left: 0,
                        right: 0
                    });

                    if ($item.is($('.slider').jcarousel('first'))) {
                        _t($link, 0.2, {
                            top: '-35px',
                            left: '0',
                            width: '310px',
                            height: '170px',
                            delay: 0.3
                        });
                    } else {
                        _t($link, 0.2, {
                            top: '-35px',
                            left: '-69px',
                            width: '310px',
                            height: '170px',
                            delay: 0.3
                        });
                    }
                }).on('mouseleave', function() {
                    var $link = $(this),
                        $desc = $(this).find('.portfolio-list__item-desc'),
                        $item = $link.parent();

                    _t($link, 0.2, {
                        top: '0',
                        left: '0',
                        width: '172px',
                        height: '100px'
                    });
                    _t($desc, 0.3, {
                        left: '50%',
                        right: '50%',
                        delay: 0.2,
                        onComplete: function() {
                            $item.removeAttr('style');
                            $desc.removeAttr('style');
                            _s($link, {
                                top: '0',
                                left: '0',
                                width: '172px',
                                height: '100px'
                            });
                        }
                    });
                });
            },
            contacts: function() {
                $('.contacts-map').click(function() {
                    b.popup.map.init();
                    return false;
                });
            }
        },

        images: {
            index: ['/media/img/scene1/trees.png', '/media/img/scene1/trees_grass1.png', '/media/img/scene1/trees_grass2.png',
                '/media/img/scene1/trees_apple1.png', '/media/img/scene1/trees_apple2.png', '/media/img/scene1/trees_apple3.png',
                '/media/img/scene1/fence.png', '/media/img/scene1/news.png', '/media/img/scene1/way.png',
                '/media/img/scene1/man_sprite.png', '/media/img/scene1/way_grass.png', '/media/img/scene1/way_bird.png',
                '/media/img/scene1/house.png', '/media/img/scene1/trees_front.png', '/media/img/scene1/broken_w.png',
                '/media/img/scene1/cat.png', '/media/img/scene1/cat_active.png', '/media/img/scene1/cat_meow.png',
                '/media/img/scene1/tooltip.png', '/media/img/scene1/tooltip_sprite.png', '/media/img/scene1/birds_grass1.png',
                '/media/img/scene1/birds_bird1.png', '/media/img/scene1/birds_bird2.png', '/media/img/scene1/birds_grass2.png',
                '/media/img/scene1/birds_grass3.png', '/media/img/scene1/light.png'
            ],
            principles: ['/media/img/scene2/door.png', '/media/img/scene2/picture.png', '/media/img/scene2/table.png',
                '/media/img/scene2/chair.png', '/media/img/scene2/chair_consumer.png', '/media/img/scene2/lamp_floor.png',
                '/media/img/scene2/carpet.png', '/media/img/scene2/creator.png', '/media/img/scene2/kitty.png',
                '/media/img/scene2/kitty_drag.png', '/media/img/scene2/mustache.png', '/media/img/scene2/tooltip.png',
                '/media/img/scene2/tooltip_ico.png', '/media/img/scene2/switch.png', '/media/img/scene2/switch_red.png',
                '/media/img/scene2/switch_act.png'
            ],
            services: ['/media/img/scene3/horns.png', '/media/img/scene3/pipe.png', '/media/img/scene3/screen.png',
                '/media/img/scene3/screen_hat.png', '/media/img/scene3/pictures.png', '/media/img/scene3/spruce.png',
                '/media/img/scene3/creator_table.png', '/media/img/scene3/cord.png', '/media/img/scene3/cannon.png',
                '/media/img/scene3/icons.png', '/media/img/scene3/bubble.png', '/media/img/scene3/bird.png',
                '/media/img/scene3/creator_table_aha.png', '/media/img/scene3/dynamite.png', '/media/img/scene3/cord_button.png',
                '/media/img/scene3/spark.png', '/media/img/scene3/bang.png', '/media/img/scene3/spruce_bang.png',
                '/media/img/scene3/screen_leg1.png', '/media/img/scene3/screen_leg2.png', '/media/img/scene3/tooltip_ico.png'
            ],
            clients: ['/media/img/scene4/cloud1.png', '/media/img/scene4/cloud2.png', '/media/img/scene4/bird_cloud.png',
                '/media/img/scene4/bird_shadow.png', '/media/img/scene4/consumer_shadow.png',
                '/media/img/scene4/note.png', '/media/img/scene4/bird.png', '/media/img/scene4/bird_wing.png',
                '/media/img/scene4/consumer_cloud.png', '/media/img/scene4/consumer.png', '/media/img/scene4/consumer_face.png',
                '/media/img/scene4/consumer_bubble.png', '/media/img/scene4/excl.png', '/media/img/scene4/bird_bubble.png'
            ],
            portfolio: ['/media/img/scene5/trees_front.png', '/media/img/scene5/house_back.png', '/media/img/scene5/flag.png',
                '/media/img/scene5/grass.png', '/media/img/scene5/scene_balloons.png', '/media/img/scene5/scene_notes.png',
                '/media/img/scene5/bycicle.png', '/media/img/scene5/bird.png', '/media/img/scene5/car_sound.png',
                '/media/img/scene5/icecream.png'
            ],
            contacts: ['/media/img/scene6/bg.png', '/media/img/scene6/bg_blur.png', '/media/img/scene6/deal.png']
        }
    };

    /* Barracuda slider */
    b.slider = {
        initCarousel: function(holder, item) {
            holder.jcarousel({
                wrap: 'circular'
            });
            item = (item) ? item : 1;
            holder.parent().find('.slider-next').on('click', function() {
                holder.jcarousel('scroll', '+=' + item)
                return false;
            });
            holder.parent().find('.slider-prev').on('click', function() {
                holder.jcarousel('scroll', '-=' + item)
                return false;
            });
        }
    };

    /* Arrows show */
    b.initArrows = function() {
        b.arrows = true;

        _s($('.nav-arrows').show(), {
            width: 0
        });
        $('.nav-arrows__s').hide();
        _t($('.nav-arrows'), 0.6, {
            width: '55px',
            ease: Back.easeOut
        });

        if (b.currentSceneIndex == 0) $('.nav-arrows_left').hide();
        else if (b.currentSceneIndex == b.$menuChildren.length - 1) $('.nav-arrows_right').hide();
        $('.nav-arrows_left').on('click', function() {
            b.animated = false;
            b.$menuChildren.eq(b.currentSceneIndex - 1).find('a').trigger('click');
        });
        $('.nav-arrows_right').on('click', function() {
            b.animated = true;
            b.$menuChildren.eq(b.currentSceneIndex + 1).find('a').trigger('click');
        });

        $('.nav-arrows').on('mouseenter', function() {
            var $this = $(this),
                left = $this.hasClass('nav-arrows_left');
            $this.find('.nav-arrows__p').removeAttr('style');

            _t($this, 0.4, {
                width: '60px'
            });

            if (left) _f($this.find('.nav-arrows__s').show(), 0.4, {
                top: '30px',
                right: '30px',
                width: 0,
                height: 0
            }, {
                top: '5px',
                right: '5px',
                width: '50px',
                height: '50px'
            });
            else _f($this.find('.nav-arrows__s').show(), 0.4, {
                top: '30px',
                left: '30px',
                width: 0,
                height: 0
            }, {
                top: '5px',
                left: '5px',
                width: '50px',
                height: '50px'
            });

        }).on('mouseleave', function() {
            var $this = $(this),
                left = $this.hasClass('nav-arrows_left');

            _t($this, 0.4, {
                width: '55px'
            });

            if (left) {
                _f($this.find('.nav-arrows__p').css('z-index', 2), 0.4, {
                    top: '30px',
                    right: '30px',
                    width: 0,
                    height: 0
                }, {
                    top: '5px',
                    right: '5px',
                    width: '50px',
                    height: '50px',
                    onComplete: function() {
                        _s($this.find('.nav-arrows__s'), {
                            top: '30px',
                            right: '30px',
                            width: 0,
                            height: 0
                        });
                        $this.find('.nav-arrows__p').removeAttr('style');
                    }
                });
            } else {
                _f($this.find('.nav-arrows__p').css('z-index', 2), 0.4, {
                    top: '30px',
                    left: '30px',
                    width: 0,
                    height: 0
                }, {
                    top: '5px',
                    left: '5px',
                    width: '50px',
                    height: '50px',
                    onComplete: function() {
                        _s($this.find('.nav-arrows__s'), {
                            top: '30px',
                            left: '30px',
                            width: 0,
                            height: 0
                        });
                        $this.find('.nav-arrows__p').removeAttr('style');
                    }
                });
            }
        });
    };
    b.hideArrows = function() {
        b.arrows = false;
        $('.nav-arrows').off('mouseenter mouseleave');
        _t($('.nav-arrows_left'), 0.3, {
            left: '-55px',
            onComplete: function() {
                _s($('.nav-arrows_left'), {
                    left: 0,
                    width: 0
                });
            }
        });
        _t($('.nav-arrows_right'), 0.3, {
            right: '-55px',
            onComplete: function() {
                _s($('.nav-arrows_right'), {
                    right: 0,
                    width: 0
                });
            }
        });
    };

    /* Footer show */
    b.showFooter = function(callback) {
        _t($('.footer'), 0.8, {
            marginTop: '-63px',
            height: '63px',
            delay: 0.5,
            ease: Cubic.easeInOut,
            onComplete: callback
        });
        _s($('.footer'), {
            overflow: 'visible',
            delay: 1.3
        });
        _sf($('.menu__item'), 0.4, {
            marginLeft: '-50px',
            scale: 0,
            overflow: 'hidden'
        }, {
            marginLeft: '0',
            scale: 1,
            ease: Back.easeOut,
            delay: 1.3
        }, 0.1);
        _f($('.footer-phone'), 0.3, {
            autoAlpha: 0
        }, {
            autoAlpha: 1,
            delay: 2.3
        });
        //SubControls.init($('.footer-icons-top'), 2.7);
        SubControls.init($('.footer-icons-bot'), 2.7);
    };

    /* Barracuda preloader */
    b.loader = function() {
        if (typeof arguments[arguments.length - 1] == 'function') {
            var callback = arguments[arguments.length - 1];
        } else {
            var callback = false;
        }
        if (typeof arguments[0] == 'object') {
            var images = arguments[0];
            var n = images.length;
        } else {
            var images = arguments;
            var n = images.length - 1;
        }
        var not_loaded = n;
        for (var i = 0; i < n; i++) {
            $(new Image()).load(function() {
                if (--not_loaded < 1 && typeof callback == 'function') {
                    callback();
                }
            }).error(function() {
                console.log('Error loading some images');
                if (--not_loaded < 1 && typeof callback == 'function') {
                    callback();
                }
            }).attr('src', images[i]);
        }
    };

    /* Barracuda initialisation */
    b.init = function() {
        'use strict';
        b.clicked = false;
        b.$wrapper = $('.wrapper');
        b.$menuChildren = $('.menu li');

        b.isIE = $('html').hasClass('ie678');
        b.handheld = b.isHandheld();

        b.currentScene = $(b.contentSelector).attr('id');

        b.updateDOM.menu();

        b.events.checkHeight();

        var images = [];
        if (!b.currentScene) {
            b.popup.backstage();
            $(b.popupSelector).pushImages();
            images = b.popup.images;
        } else if (b.currentScene !== 'error') {
            _s($(b.contentSelector).pushImages().show(), {
                autoAlpha: 0
            });
            images = b.stages.images[b.currentScene];
        }

        _s($('.footer').show(), {
            marginTop: 0,
            height: 0,
            overflow: 'hidden'
        });

        if (b.currentScene === 'error') {
            b.loader(['/media/img/error/ghost.png', '/media/img/error/ghost_shadow.png', '/media/img/error/text.png'], function() {
                $('body').ajaxify();
                b.showFooter(b.error.init)
                $(window).bind('resize', b.events.checkSize);

                preloader.stop(function() {});
            });
        } else {
            b.loader(images, function() {
                $('body').ajaxify();
                b.showFooter(b.stages.startAnimation[b.currentScene]);

                if ($(b.popupSelector).length) b.popup.attachScroll(0);

                $(window).bind('statechange', b.events.stateChange);
                $(window).bind('resize', b.events.checkSize);

                preloader.stop(function() {});
            });
        }

        if (document.addEventListener) {
            if ('onwheel' in document) {
                document.addEventListener("wheel", onWheel);
            } else if ('onmousewheel' in document) {
                document.addEventListener("mousewheel", onWheel);
            } else {
                document.addEventListener("MozMousePixelScroll", onWheel);
            }
        } else {
            document.attachEvent("onmousewheel", onWheel);
        }

        var animInProgrees = false;

        function onWheel(e) {
            e = e || window.event;

            var delta = e.deltaY || e.detail || e.wheelDelta;

            if (!$('.popup').length) {
                if (delta > 0) {
                    if (!animInProgrees) {
                        delay();

                        b.animated = true;
                        b.$menuChildren.eq(b.currentSceneIndex + 1).find('a').trigger('click');
                    }
                } else {
                    if (!animInProgrees) {
                        delay();

                        b.animated = false;
                        b.$menuChildren.eq(b.currentSceneIndex - 1).find('a').trigger('click');
                    }
                }
            }

        }

        function delay(argument) {
            animInProgrees = true;
            setTimeout(function() {
                animInProgrees = false;
            }, 2000)
        }
    };

    $(function() {
        preloader.init();
    });
})(jQuery);