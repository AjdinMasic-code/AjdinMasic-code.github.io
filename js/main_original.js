$(document).ready(function() {
    $(".navbar-collapse ul li a[href^='#']").on('click', function(e) {
        e.preventDefault();
        var hash = this.hash;
        $('html, body').animate({
            scrollTop: $(hash).offset().top
        }, 800, function() {
            window.location.hash = hash;
        });
    });
    $("a.navbar-brand[href^='#']").on('click', function(e) {
        e.preventDefault();
        var hash = this.hash;
        $('html, body').animate({
            scrollTop: $(hash).offset().top
        }, 800, function() {
            window.location.hash = hash;
        });
    });

    function showImages(el) {
        var windowHeight = jQuery(window).height();
        $(el).each(function() {
            var thisPos = $(this).offset().top;
            var topOfWindow = $(window).scrollTop();
            if (topOfWindow + windowHeight - 500 > thisPos) {
                $(this).addClass("fadeIn");
            }
        });
    }
    var timeout = null,
        divider = 2,
        minimumScrollbarPosition = 900,
        minimumNavbarPosition = 300;
    $(window).scroll(function() {
        if (!timeout) {
            timeout = setTimeout(function() {
                clearTimeout(timeout);
                timeout = null;
                checkPosition();
                checkNavbarPosition();
                $('.delay').each(function(i) {
                    var bottom_of_object = $(this).position().top + $(this).outerHeight();
                    var bottom_of_window = $(window).scrollTop() + $(window).height() - 3500;
                    console.log(bottom_of_window);
                    if (bottom_of_window > bottom_of_object) {
                        $(this).animate({
                            'opacity': '1'
                        }, 1500);
                    }
                });
                $('.left-slide').each(function(i) {
                    var bottom_of_object = $(this).position().top + $(this).outerHeight();
                    var bottom_of_window = $(window).scrollTop() + $(window).height() - 900;
                    console.log(bottom_of_window);
                    if (bottom_of_window > bottom_of_object) {
                        $(this).animate({
                            'left': '0',
                            'opacity': '1'
                        }, 1200);
                    }
                });
                $('.right-slide').each(function(i) {
                    var bottom_of_object = $(this).position().top + $(this).outerHeight();
                    var bottom_of_window = $(window).scrollTop() + $(window).height() - 900;
                    console.log(bottom_of_window);
                    if (bottom_of_window > bottom_of_object) {
                        $(this).animate({
                            'right': '0',
                            'opacity': '1'
                        }, 1200);
                    }
                });
                
                  $(".jumbotron").css({
                        'opacity': 1 - (($(this).scrollTop()) / 720)
                  });
                showImages('.star');
            }, 200);
        }
    });

    function checkPosition() {
        if ($(this).scrollTop() >= minimumScrollbarPosition) {
            $('#back-to-top').fadeIn(500);
        } else {
            $('#back-to-top').fadeOut(300);
        }
    };

    function checkNavbarPosition() {
        if ($(this).scrollTop() >= 650) {
            $('.navbar-fixed-top').addClass("opaque");
        } else {
            $('.navbar-fixed-top').removeClass("opaque");
        }
    };
    $('#back-to-top').click(function(event) {
        event.preventDefault();
        var scrollSpeed = $(window).scrollTop() / divider;
        $('html, body').animate({
            scrollTop: 0
        }, scrollSpeed);
    });
    $(".services-section.null .info-container img.first").mouseenter(function() {
        $(".text-capt.first").slideDown(250, 'swing');
    });
    $(".services-section.null .info-container img.second").mouseenter(function() {
        $(".text-capt.second").slideDown(250, 'swing');
    });
    $(".services-section.null .info-container img.third").mouseenter(function() {
        $(".text-capt.third").slideDown(250, 'swing');
    });
    $(".services-section.null .info-container img.first").mouseleave(function() {
        $(".text-capt.first").slideUp(250, 'swing');
    });
    $(".services-section.null .info-container img.second").mouseleave(function() {
        $(".text-capt.second").slideUp(250, 'swing');
    });
    $(".services-section.null .info-container img.third").mouseleave(function() {
        $(".text-capt.third").slideUp(250, 'swing');
    });
    
    var TxtType = function(el, toRotate, period) {
        this.toRotate = toRotate;
        this.el = el;
        this.loopNum = 0;
        this.period = parseInt(period, 10) || 2000;
        this.txt = '';
        this.tick();
        this.isDeleting = false;
    };

    TxtType.prototype.tick = function() {
        var i = this.loopNum % this.toRotate.length;
        var fullTxt = this.toRotate[i];

        if (this.isDeleting) {
        this.txt = fullTxt.substring(0, this.txt.length - 1);
        } else {
        this.txt = fullTxt.substring(0, this.txt.length + 1);
        }

        this.el.innerHTML = '<span class="wrap">Web '+this.txt+'</span>';

        var that = this;
        var delta = 200 - Math.random() * 100;

        if (this.isDeleting) { delta /= 2; }

        if (!this.isDeleting && this.txt === fullTxt) {
        delta = this.period;
        this.isDeleting = true;
        } else if (this.isDeleting && this.txt === '') {
        this.isDeleting = false;
        this.loopNum++;
        delta = 500;
        }

        setTimeout(function() {
        that.tick();
        }, delta);
    };

    window.onload = function() {
        var elements = document.getElementsByClassName('typewrite');
        for (var i=0; i<elements.length; i++) {
            var toRotate = elements[i].getAttribute('data-type');
            var period = elements[i].getAttribute('data-period');
            if (toRotate) {
              new TxtType(elements[i], JSON.parse(toRotate), period);
            }
        }
    };
    
});