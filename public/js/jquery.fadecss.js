/* jquery plugin to replace fadeIn and fadeOut with CSS transitions for better performances using the jquery transit plugin */

jQuery.fn.fadeOut = function(speed, callback) {
    var transitionSpeed = typeof(speed) == 'undefined' ? 500 : speed;
    $(this).transition({ opacity: 0 }, transitionSpeed, function(){
        $(this).css('display', 'none');
        if (typeof(callback) == 'function')
            callback(this);
    });
};

jQuery.fn.fadeIn = function(speed, callback) {
    var transitionSpeed = typeof(speed) == 'undefined' ? 500 : speed;
    $(this).css('opacity', '0');
    $(this).css('display', '');
    $(this).transition({ opacity: 1 }, transitionSpeed, function(){
        if (typeof(callback) == 'function')
            callback(this);
    });
};