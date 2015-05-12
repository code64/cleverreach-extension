(function ($) {
	'use strict';

	$(document).ready(function () {
		$(cre.selector + cre.container_selector + ' form').on('submit', function () {

			var $cr_container = $(cre.selector + cre.container_selector);

			$.ajax({
				url: cre.ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					action: 'cre_ajax_controller_interaction',
					nonce: cre.nonce,
					cr_form: $(this).serialize()
				},
				beforeSend: function() {
					$cr_container.find(cre.selector + cre.response_selector).remove();
					$cr_container.prepend('<p class="' + cre.loading_selector + '">' + cre.loading + '</p>');
				},
				success: function( response ) {
					$cr_container.find(cre.selector + cre.loading_selector).remove();
					if ( response.type === 'success' ) {
						$cr_container.prepend('<p class="' + cre.response_selector + ' ' + cre.success_selector + '">' + cre.success + '</p>');
					} else {
						$cr_container.prepend('<p class="' + cre.response_selector + ' ' + cre.error_selector + '">' + response.status + '</p>');
					}
				},
				error: function ( data, errorThrown ) {
					$cr_container.find(cre.selector + cre.loading_selector).remove();
					$cr_container.prepend('<p class="' + cre.response_selector + ' ' + cre.error_selector + '">' + cre.error + '</p>');
				}
			});

			return false;

		});
	});

})(jQuery);