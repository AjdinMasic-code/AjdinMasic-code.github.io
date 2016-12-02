$(document).ready(function() {

  $(".navbar-collapse ul li a[href^='#']").on('click', function(e) {

     // prevent default anchor click behavior
     e.preventDefault();

     // store hash
     var hash = this.hash;

     // animate
     $('html, body').animate({
         scrollTop: $(hash).offset().top
       }, 800, function(){

         // when done, add hash to url
         // (default click behaviour)
         window.location.hash = hash;
       });

  });


  $("a.navbar-brand[href^='#']").on('click', function(e) {

     // prevent default anchor click behavior
     e.preventDefault();

     // store hash
     var hash = this.hash;

     // animate
     $('html, body').animate({
         scrollTop: $(hash).offset().top
       }, 800, function(){

         // when done, add hash to url
         // (default click behaviour)
         window.location.hash = hash;
       });

  });

/* The start of back to top button */
      var timeout = null,
          divider =2,
          minimumScrollbarPosition = 900;

    $(window).scroll(function () {
        if (!timeout) {
            timeout = setTimeout(function () {            
                clearTimeout(timeout);
                timeout = null;
                checkPosition();

            }, 200);
        }
    });

    function checkPosition() {
        if ($(this).scrollTop() >= minimumScrollbarPosition) { // Checks if the value of scrollTop is greater than 200
            $('#back-to-top').fadeIn(500); // Fades in if true
        } else {
            $('#back-to-top').fadeOut(300);// Otherwise fades out
        }
    }

        // Animate the scroll to top
        $('#back-to-top').click(function(event) {
            event.preventDefault();
            var scrollSpeed = $(window).scrollTop() / divider;

            $('html, body').animate({scrollTop: 0}, scrollSpeed); // sets scrollTop value to 0(top of page) scrollSpeed references speed
        })

});
