jQuery('#filter-container').on('click', function() {
	var $imgs = jQuery('.lazy-img');
	$imgs.each(function() {
		jQuery(this).attr('src', jQuery(this).data('src'));
	});
	jQuery("#gallery-content").show();
});

jQuery('#gallery').each(function(){
	var $isotope = jQuery('#gallery-content-center', this);
	var $filters = jQuery('.filter-button', this);

	var filter = function(){
		jQuery(".filter-button.active").removeClass("active")
		jQuery(this).addClass('active');
		var type = $filters.filter('.active').data('type') || '*';
		
		if(type !== '*') type = '.' + type;
		$isotope.isotope({ filter: type });
	};

	$isotope.isotope({
		itemSelector: '.pic',
		layoutMode: 'masonry'
	});

	jQuery(this).on('click', '.filter-button', filter);
	filter();
});