(function ( $ ) {
	"use strict";

	$(function () {

	// JS for the background color wheel

    $('#colorpicker-bg_color').hide();
    $('#colorpicker-bg_color').farbtastic('#bg_color');

    $('#bg_color').click(function() {
        $('#colorpicker-bg_color').fadeIn();
    });

    $(document).mousedown(function() {
        $('#colorpicker-bg_color').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });

    $('#colorpicker-text_color_dl').hide();
    $('#colorpicker-text_color_dl').farbtastic('#text_color_dl');

    $('#text_color_dl').click(function() {
        $('#colorpicker-text_color_dl').fadeIn();
    });

    $(document).mousedown(function() {
        $('#colorpicker-text_color_dl').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });

});

}(jQuery));
