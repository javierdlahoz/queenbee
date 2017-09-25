//jQuery(window).load(function () {
//    equalheight('.top .wrap .widget-area');
//});
//
//
//jQuery(window).resize(function () {
//    equalheight('.top .wrap .widget-area');
//});


jQuery(document).ready(function($) {
    
    /* // EQUAL HEIGHT ELEMENTS // */
    equalheight = function (container) {

        var currentTallest = 0,
            currentRowStart = 0,
            rowDivs = new Array(),
            $el,
            topPosition = 0;

        $(container).each(function () {

            $el = $(this);
            $($el).height('auto');
            topPostion = $el.position().top;

            if (currentRowStart != topPostion) {
                for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
                    rowDivs[currentDiv].height(currentTallest);
                }
                rowDivs.length = 0; // empty the array
                currentRowStart = topPostion;
                currentTallest = $el.height();
                rowDivs.push($el);
            } else {
                rowDivs.push($el);
                currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
            }
            for (currentDiv = 0; currentDiv < rowDivs.length; currentDiv++) {
                rowDivs[currentDiv].height(currentTallest);
            }
        });
    };
    
    
    
    // Optimization: Store the references outside the event handler:
    var $window = $(window);
    var $targetElement = $('.top .wrap .widget-area');

    function checkWidth() {
        var windowsize = $window.width();
        if (windowsize > 768) {
            equalheight( $targetElement );
        }else{
            $targetElement.height("auto");
        }
    }
    
    // Fade in the image
    function fadeInImage() {
        $('.top .wrap .home-image .textwidget').addClass('show');
    }
    
    
//    // on Scroll
//    $(window).scroll(function() {
//       checkWidth();
//    });
    
    // Bind event listener
    $(window).resize(checkWidth);
    
    
    // Execute on load (after 0.5 sec)
    setTimeout( checkWidth, 500 );
    
    // Fade in image after 1 sec
    setTimeout( fadeInImage, 1000 );
});