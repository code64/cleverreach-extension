/* global cre_admin:false */

(function ($) {
	'use strict';

	$.fn.parseSelectOptions = function ($container, $selector, $response, $empty) {

		if (typeof $response !== 'undefined' && $response.length > 0) {

			// Reset and append empty option.
			$container.find($selector).empty().append('<option value="">' + $empty + '</option>');

			// Parse options from response.
			$.each($response, function (i, obj) {
				var $current = $container.find($selector);
				$current.append($('<option>', {
					value: obj.id,
					text : obj.name
				}));

				// Select selected option and update shortcode.
				if (obj.selected) {
					$current.find('option[value="' + obj.id + '"]').prop('selected', true);
				}

			});

		} else {

			// Reset
			$container.find($selector).empty();

		}

	};

	$(document).ready(function () {

		var cre_admin_timeout;
		var cre_admin_selector = cre_admin.selector,
			cre_admin_container_selector = cre_admin_selector + cre_admin.container_selector,
			cre_admin_key_selector = cre_admin_selector + cre_admin.key_selector,
			cre_admin_list_selector = cre_admin_selector + cre_admin.list_selector,
			cre_admin_form_selector = cre_admin_selector + cre_admin.form_selector,
			cre_admin_source_selector = cre_admin_selector + cre_admin.source_selector,
			cre_admin_response_selector = cre_admin_selector + cre_admin.response_selector,
			cre_admin_shortcode_selector = cre_admin_selector + cre_admin.shortcode_selector;

		$(cre_admin_container_selector + ' form').on('input propertychange change submit', function () {

			clearTimeout(cre_admin_timeout);
			cre_admin_timeout = setTimeout(function () {

				var $cr_container = $(cre_admin_container_selector),
					cre_admin_form_data = {
						api_key: $cr_container.find(cre_admin_key_selector).val(),
						list_id: $cr_container.find(cre_admin_list_selector).val(),
						form_id: $cr_container.find(cre_admin_form_selector).val(),
						source : $cr_container.find(cre_admin_source_selector).val()
					};

				$.ajax({
					url       : cre_admin.ajaxurl,
					type      : 'POST',
					dataType  : 'JSON',
					data      : {
						action       : 'cre_admin_ajax_controller_interaction',
						nonce        : cre_admin.nonce,
						cr_admin_form: cre_admin_form_data // User input
					},
					beforeSend: function () {

						$cr_container.find(cre_admin_response_selector).removeClass('confirmed invalid'); // Cleanup status
						$cr_container.find(cre_admin_response_selector).addClass('updating animate'); // Loading

					},
					success   : function (response) {

						$cr_container.find(cre_admin_response_selector).removeClass('updating animate'); // Cleanup loading

						if (response.status === 'success') {
							$cr_container.find(cre_admin_response_selector).addClass('confirmed'); // Confirmed
						} else {
							$cr_container.find(cre_admin_response_selector).addClass('invalid'); // Invalid
						}

						// Parse response options.
						$(this).parseSelectOptions($cr_container, cre_admin_list_selector, response.list_options, cre_admin.list_empty);
						$(this).parseSelectOptions($cr_container, cre_admin_form_selector, response.form_options, cre_admin.form_empty);

						if (typeof response.shortcode_id !== 'undefined' && response.shortcode_id.length > 0) {
							$(cre_admin_shortcode_selector).text(response.shortcode_id);
						} else {
							$(cre_admin_shortcode_selector).text('');
						}

					},
					error     : function () {

						$cr_container.find(cre_admin_response_selector).removeClass('updating animate'); // Cleanup loading
						$cr_container.find(cre_admin_list_selector).empty(); // Reset
						$cr_container.find(cre_admin_form_selector).empty(); // Reset
						$cr_container.find(cre_admin_response_selector).addClass('invalid'); // Invalid

					}
				});

			}, 1000); // Save one second after the last change.

			return false;

		});

	});

})(jQuery);