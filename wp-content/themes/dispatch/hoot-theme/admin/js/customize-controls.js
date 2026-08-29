/**
 * Theme Customizer
 */


( function( api ) {

	// Extends our custom "hoot-theme" section.
	api.sectionConstructor['hoot-premium'] = api.Section.extend( {

		// No events for this type of section.
		attachEvents: function () {},

		// Always make the section active.
		isContextuallyActive: function () {
			return true;
		}
	} );

	api.bind('ready', function () {

		jQuery(document).ready(function($) {
			$('a[rel="focuslink"]').click(function(e) {
				e.preventDefault();
				var id = $(this).data('href'),
					type = $(this).data('focustype');
				if(api[type].has(id)) {
					api[type].instance(id).focus();
				}
			});

			var areaIds = ['area_a', 'area_b', 'area_c', 'area_d', 'area_e', 'area_f', 'area_g', 'area_h', 'area_i', 'area_j', 'area_k', 'area_l', 'content'];
			function updateBgVisibility($input,areaId,initial=false) {
				var selectedValue = $input.val();
				var $parentli = $input.closest('li');
				var $colorli = $parentli.siblings("#customize-control-frontpage_sectionbg_" + areaId + "-color");
				var $imageli = $parentli.siblings("#customize-control-frontpage_sectionbg_" + areaId + "-image");
				var $parallaxli = $parentli.siblings("#customize-control-frontpage_sectionbg_" + areaId + "-parallax");
				if (selectedValue === "none") {
					if ( initial ) {
						$colorli.hide(); $imageli.hide(); $parallaxli.hide();
					} else {
						$colorli.slideUp('fast'); $imageli.slideUp('fast'); $parallaxli.slideUp('fast');
					}
				} else if (selectedValue === "color" || selectedValue === "highlight") {
					if ( initial ) {
						$colorli.show(); $imageli.hide(); $parallaxli.hide();
					} else {
						$colorli.slideDown('fast'); $imageli.slideUp('fast'); $parallaxli.slideUp('fast');
					}
				} else if (selectedValue === "image") {
					if ( initial ) {
						$colorli.hide(); $imageli.show(); $parallaxli.show();
					} else {
						$colorli.slideUp('fast'); $imageli.slideDown('fast'); $parallaxli.slideDown('fast');
					}
				}
			}
			function updateFontVisibility($input,areaId,initial=false) {
				var selectedValue = $input.val();
				var $parentli = $input.closest('li');
				var $colorli = $parentli.siblings("#customize-control-frontpage_sectionbg_" + areaId + "-fontcolor");
				if (selectedValue === "theme") {
					if ( initial ) {
						$colorli.hide();
					} else {
						$colorli.slideUp('fast');
					}
				} else {
					if ( initial ) {
						$colorli.show();
					} else {
						$colorli.slideDown('fast');
					}
				}
			}
			areaIds.forEach(function(areaId) {
				var $typeinput = $("#customize-control-frontpage_sectionbg_"+areaId+"-type input[type='radio']");
				if( $typeinput.length ) {
					$typeinput.filter(':checked').each(function() {
						updateBgVisibility($(this), areaId, true);
					});
					$typeinput.on('change', function() {
						updateBgVisibility($(this), areaId);
					});
				}
				var $typeinput = $("#customize-control-frontpage_sectionbg_"+areaId+"-font input[type='radio']");
				if( $typeinput.length ) {
					$typeinput.filter(':checked').each(function() {
						updateFontVisibility($(this), areaId, true);
					});
					$typeinput.on('change', function() {
						updateFontVisibility($(this), areaId);
					});
				}
			});

		});

	});

} )( wp.customize );


jQuery(document).ready(function($) {
	"use strict";

});
