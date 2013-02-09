$(function() { 
	placeholderHint();	//Add placeholder hints to all inputs that have class="placeholder", @see placeholderHint
	
	$("img[rel=tooltip]").tooltip({
		placement: 'right',
		offset: 5
	});	
	
	//Called anytime ajax request is sent
	$('body').bind("ajaxStart", function(){
		$(".spinner").spin("tiny");
		$('.ajax-alert').hide();
	}).bind("ajaxStop", function(){
		$(".spinner").delay(800).spin(false);
	}).bind("ajaxError", function(){
		$(".spinner").delay(800).spin(false);
	});
	
	$('body').bind('ajaxSuccess',function(event,request,settings){
		if (403 == request.status){
			window.location = '/users/login';
		}
	}).bind('ajaxError',function(event,request,settings){
		if (403 == request.status){
			window.location = '/users/login';
		}
	});
});

$.fn.exists = (function () {
    return $(this).length !== 0;
});

/**
 * Does the browser support placeholder HTML5? $.support.placeholder
 */
jQuery.support.placeholder = (function(){
    var i = document.createElement('input');
    return 'placeholder' in i;
})();

/**
 * To use set class of input to placeholder and it will use the title attribute for the placeholder hint
 * 
 * I know tihs is not a jQuery plugin..
 */
function placeholderHint(){
	if($.support.placeholder) return;	//if browser supports placeholder, dont need to do the rest 

	$('input.placeholder').focus(function() {
		var input = $(this);
		if (input.val() == input.attr('title')) {
			input.val('');
		}
	}).blur(function() {
		var input = $(this);
		if (input.val() == '' || input.val() == input.attr('title')) {
			input.val(input.attr('title'));
		}
	})
	.blur()
	.parents('form').submit(function() {		
		$(this).find('.hint').each(function() {
			var input = $(this);
			if (input.val() == input.attr('title')) {
				input.val('');
			}
		});
	});
}

/*

You can now create a spinner using any of the variants below:

$("#el").spin(); // Produces default Spinner using the text color of #el.
$("#el").spin("small"); // Produces a 'small' Spinner using the text color of #el.
$("#el").spin("large", "white"); // Produces a 'large' Spinner in white (or any valid CSS color).
$("#el").spin({ ... }); // Produces a Spinner using your custom settings.

$("#el").spin(false); // Kills the spinner.

*/
(function($) {
	$.fn.spin = function(opts, color) {
		var presets = {
			"tiny": { lines: 8, length: 2, width: 2, radius: 3 },
			"small": { lines: 8, length: 4, width: 3, radius: 5 },
			"large": { lines: 10, length: 8, width: 4, radius: 8 }
		};
		if (Spinner) {
			return this.each(function() {
				var $this = $(this),
					data = $this.data();

				if (data.spinner) {
					data.spinner.stop();
					delete data.spinner;
				}
				if (opts !== false) {
					if (typeof opts === "string") {
						if (opts in presets) {
							opts = presets[opts];
						} else {
							opts = {};
						}
						if (color) {
							opts.color = color;
						}
					}
					data.spinner = new Spinner($.extend({color: $this.css('color')}, opts)).spin(this);
				}
			});
		} else {
			throw "Spinner class not available.";
		}
	};
})(jQuery);

(function($) {
    $.fn.toggleDisabled = function(){
        return this.each(function(){
            this.disabled = !this.disabled;
        });
    };
})(jQuery);