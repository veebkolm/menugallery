<?php
$all_category = get_option( 'simple_gallery_categories' . $gallery_id );
$allimages = array(  'p' => $gallery_id, 'post_type' => 'simple_gallery', 'orderby' => 'ASC');
$loop = new WP_Query( $allimages );
$widths = [
	'1' => '100%',
	'2' => 'calc(50% - 10px)',
	'3' => 'calc(33% - 10px)',
	'4' => 'calc(25% - 10px)',
	'5' => 'calc(20% - 10px)',
	'6' => 'calc(25% - 10px)',
	'7' => 'calc(16.66% - 10px)',
	'8' => 'calc(12.5% - 10px)',
];

if( isset($gallery_settings['image-ids'] ) ) {
?>
<style>	
#gallery-content-center .pic {
	width: <?php echo $widths[$gallery_settings['phone']]; ?> !important;
}
@media screen and (min-width: 640px) {
  #gallery-content-center .pic {
    width: <?php echo $widths[$gallery_settings['tablet-portrait']]; ?> !important;
  }
}
@media screen and (min-width: 940px) {
  #gallery-content-center .pic {
    width: <?php echo $widths[$gallery_settings['tablet-landscape']]; ?> !important;
  }
}
@media screen and (min-width: 1200px) {
  #gallery-content-center .pic {
    width: <?php echo $widths[$gallery_settings['desktop']]; ?> !important;
  }
}
@media screen and (min-width: 1800px) {
  #gallery-content-center .pic {
    width: <?php echo $widths[$gallery_settings['big']]; ?> !important;
  }
}



</style>
<div id="gallery">

<div id="gallery-header">
		<div id="filter-container">
			<div class="filter-button" data-type="*"><?php echo $default_category; ?></div>
			<?php foreach( $all_category as $key => $category ) { $key++; ?>
			<div class="filter-button" data-type="<?php echo $key; ?>"><?php echo ucfirst($category); ?></div>
			<?php } ?>
		</div>
	</div> <!-- end #gallery-header  -->
	<div id="gallery-content" style="display: none;">
		<div id="gallery-content-center">


<?php 
while ( $loop->have_posts() ) : $loop->the_post();
$post_id = get_the_ID();

// start looping pictures

foreach($gallery_settings['image-ids'] as $attachment_id) {
	$attachment_id = (string) $attachment_id;
	// $image_link_url =  $gallery_settings['image-link'][$count];
	// $thumb = wp_get_attachment_image_src($attachment_id, 'thumb', true);
	$thumbnail = wp_get_attachment_image_src($attachment_id, 'thumbnail', true);
	$medium = wp_get_attachment_image_src($attachment_id, 'medium', true);
	$large = wp_get_attachment_image_src($attachment_id, 'large', true);
	$full = wp_get_attachment_image_src($attachment_id, 'full', true);
	// $postthumbnail = wp_get_attachment_image_src($attachment_id, 'post-thumbnail', true);
	$attachment_details = get_post( $attachment_id );
	$href = get_permalink( $attachment_details->ID );
	$src = $attachment_details->guid;
	$title = $attachment_details->post_title;
	$description = $attachment_details->post_content;

	$categories = '';
	$filters = isset($gallery_settings['filters'][$attachment_id]) ? $gallery_settings['filters'][$attachment_id] : [];
	foreach($filters as $f) {
		$f++;
		$categories .= $f . ' ';

	}
	
?>
			<div class="pic <?php echo $categories; ?>">
				<?php if ($lightbox): ?>
			 		<a href="<?php echo $full[0]; ?>" data-lightbox="image-1" class="lightbox-link"> 
			 	<?php endif; ?>
					<img data-src="<?php echo $medium[0]; ?>" class="lazy-img"/>
					<div class="img-text">
						<p class="img-title"><?php echo $title; ?></p>
						<p class="img-description"><?php echo $description; ?></p>
					</div>
				<?php if ($lightbox): ?>
			 		</a>
			 	<?php endif; ?>
 			</div>

<?php
}  // end foreach image-ids
endwhile; 
} else {
	?>
	<div><p>Sorry, no images to display!</p></div>
	<?php
}  // end if isset image-ids
?>
		</div> <!-- end #gallery-content-centent -->
	</div> <!-- end #gallery-content -->
</div> <!-- end #gallery -->
