(function ( $ ) {
	"use strict";

	$(function () {

	// JS for the background color wheel

    $('#colorpicker').hide();
    $('#colorpicker').farbtastic('#bg_color');

    $('#bg_color').click(function() {
        $('#colorpicker').fadeIn();
    });

    $(document).mousedown(function() {
        $('#colorpicker').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });

});

}(jQuery));
