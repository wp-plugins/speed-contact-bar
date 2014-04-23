(function ( $ ) {
	"use strict";

	$(function () {

	// JS for the background color wheel

	// bg color
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

    // text color
	$('#colorpicker-text_color').hide();
    $('#colorpicker-text_color').farbtastic('#text_color');

    $('#text_color').click(function() {
        $('#colorpicker-text_color').fadeIn();
    });

    $(document).mousedown(function() {
        $('#colorpicker-text_color').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });

    // link color
	$('#colorpicker-link_color').hide();
    $('#colorpicker-link_color').farbtastic('#link_color');

    $('#link_color').click(function() {
        $('#colorpicker-link_color').fadeIn();
    });

    $(document).mousedown(function() {
        $('#colorpicker-link_color').each(function() {
            var display = $(this).css('display');
            if ( display == 'block' )
                $(this).fadeOut();
        });
    });

});

}(jQuery));
