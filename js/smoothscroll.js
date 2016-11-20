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


$(document).ready(function() {

    function checkPosition() {
        if ($(this).scrollTop() > 200) { // Checks if the value of scrollTop is greater than 200
            $('#back-to-top').fadeIn(500); // Fades in if true
        } else {
            $('#back-to-top').fadeOut(300);// Otherwise fades out
        }
    }
    // Checks to see if sticky button is shown or not
    $(window).scroll(checkPosition);

    // Animate the scroll to top
    $('#back-to-top').click(function(event) {
        event.preventDefault();

        $('html, body').animate({scrollTop: 0}, 2000); // sets scrollTop value to 0(top of page) 2000 references speed
    })

});
