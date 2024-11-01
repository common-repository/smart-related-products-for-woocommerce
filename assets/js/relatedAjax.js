jQuery(document).ready(function($){
	var relatedProducts = jQuery('<div id="ajax-related-products"></div>').insertAfter('.woocommerce-tabs');
	
	var data = {
		action: 'related_ajax',
		post_id: ajaxData.post_id,
		beforeSend: function(){
			jQuery('#ajax-related-products').append('<center><img src="/wp-includes/js/thickbox/loadingAnimation.gif" id="ajaxSpinnerImage" title="Загрузка..." /></center>');
		}
	};
	jQuery.post(ajaxData.ajaxurl, data, function(response){
		jQuery('#ajax-related-products').html(response);
		jQuery("#product_wrapper3").slick({
			dots: false,
			autoplay:true,
			infinite: true,
			autoplaySpeed: 5000,
			prevArrow: '<div class="arrow left"></div>',
			nextArrow: '<div class="arrow right"></div>',
			slidesToShow: 4,
			slidesToScroll: 1,
			responsive: [
				{
					breakpoint: 768,
					settings: {
					slidesToShow: 3,
					slidesToScroll: 3,
					}
				},
				{
					breakpoint: 600,
					settings: {
					slidesToShow: 2,
					slidesToScroll: 2
					}
				},
				{
					breakpoint: 480,
					settings: {
					slidesToShow: 1,
					slidesToScroll: 1
					}
				}
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
			]
		});
	});
});