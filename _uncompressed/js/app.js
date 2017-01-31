/* ~~ Public Vars ~~
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
var worthWatching = $(".worth-watching"),
    scoreboard = $(".scoreboard");

/* ~~ Init ~~
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */

function init() {

    worthWatching.prependTo('.scoreboard');

    // Smooth scrolling anchors
    $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
        var target = $(this.hash);
        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top -77
            }, 1000);
            return false;
        }
    }
    });

}

function resizer() {


}

$(window).resize(function(){
    resizer();
});

$(document).ready(function(){

    init();

});
