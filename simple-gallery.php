<?php
if ( ! defined( 'ABSPATH' ) ) exit;  // exit if accessed directly
/**
Plugin Name: Simple Gallery
Description: A simple gallery plugin for Wordpress
Version: 0.0.1
Author: Galilei
License: MIT
Text Domain: SG_TXTDM
Domain Path: /languages
**/

if ( ! class_exists( 'Simple_Gallery' )) {

	class Simple_Gallery {
		
		public function __construct() {
			$this->__constants();
			$this->__hooks();
		}

		protected function __constants() {
			// plugin version
			define( 'SG_PLUGIN_VER', '0.0.1' );

			// plugin text domain
			define( 'SG_TXTDM', 'simple-gallery' );

			// plugin name
			define( 'SG_NAME', __( 'Simple Gallery', SG_TXTDM ) );

			// plugin slug
			define( 'SG_SLUG', 'sg_gallery');

			// plugin directory path
			define( 'SG_PATH', plugin_dir_path( __FILE__ ) );

			// plugin directory URL
			define( 'SG_URL', plugin_dir_url( __FILE__ ) );

			define( 'SG_SECURE_KEY', md5( NONCE_KEY ) );
		}  // end of constants function

		protected function __hooks() {
			// add gallery menu items
			// add_action( 'admin_menu', array($this, 'sg_menu'), 101 );

			// load text domain
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

			// create "Simple Gallery" custom post
			add_action( 'init', array( $this, 'simple_gallery' ) );

			// add meta box to custom box
			add_action( 'add_meta_boxes', array( $this, 'admin_add_meta_box' ) );
			
			// loaded during admin init
			add_action( 'admin_init', array( $this, 'admin_add_meta_box' ) );

			add_action( 'wp_ajax_sg_js', array( &$this, '_ajax_sg' ) );

			add_action( 'save_post', array( &$this, '_sg_save_settings' ) );

		}  // end of hooks function

		// public function sg_menu() { }

		public function load_textdomain() {
			load_plugin_textdomain( 
				SG_TXTDM, 
				false, 
				dirname( plugin_basename( __FILE__ ) ) . '/languages'
			);
		}

		public function simple_gallery() {
			$labels = array(
				'name'                => _x( 'Simple Gallery', 'Post Type General Name', SG_TXTDM ),
				'singular_name'       => _x( 'Simple Gallery', 'Post Type Singular Name', SG_TXTDM ),
				'menu_name'           => __( 'Simple Gallery', SG_TXTDM ),
				'parent_item_colon'   => __( 'Parent Item:', SG_TXTDM ),
				'all_items'           => __( 'All Gallery', SG_TXTDM ),
				'add_new_item'        => __( 'Add New Gallery', SG_TXTDM ),
				'add_new'             => __( 'Add New Gallery', SG_TXTDM ),
				'new_item'            => __( 'New Simple Gallery', SG_TXTDM ),
				'edit_item'           => __( 'Edit Simple Gallery', SG_TXTDM ),
				'update_item'         => __( 'Update Simple Gallery', SG_TXTDM ),
				'search_items'        => __( 'Search Simple Gallery', SG_TXTDM ),
				'not_found'           => __( 'Simple Gallery Not found', SG_TXTDM ),
				'not_found_in_trash'  => __( 'Simple Gallery Not found in Trash', SG_TXTDM ),
			);
			$args = array(
				'label'               => __( 'Simple Gallery', SG_TXTDM ),
				'description'         => __( 'Custom Post Type For Simple Gallery', SG_TXTDM ),
				'labels'              => $labels,
				'supports'            => array('title'),
				'taxonomies'          => array(),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 65,
				'menu_icon'           => 'dashicons-screenoptions',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,		
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
			);
			register_post_type( 'simple_gallery', $args );
		} // end of post type function

		public function admin_add_meta_box() {
			add_meta_box( '2', __('Pictures', SG_TXTDM), array(&$this, 'sg_image_upload'), 'simple_gallery', 'normal', 'default' );
			add_meta_box( '1', __('Settings', SG_TXTDM), array(&$this, 'sg_settings'), 'simple_gallery', 'normal', 'default' );
		}

		public function sg_settings($post) {
			wp_enqueue_style( 'checkbox', SG_URL . 'css/checkbox.css' );

			$all_category = get_option('simple_gallery_categories' . $post->ID);

			$settings = unserialize(base64_decode(
				get_post_meta( $post->ID, 'simple_gallery'.$post->ID, true )
			));
			$default_category = isset($settings['default-category']) ? $settings['default-category'] : '';
			$lightbox = isset($settings['lightbox']) ? $settings['lightbox'] : false;
			$big = isset($settings['big']) ? $settings['big'] : 6 ;
			$desktop = isset($settings['desktop']) ? $settings['desktop'] : 4 ;
			$tablet_landscape = isset($settings['tablet-landscape']) ? $settings['tablet-landscape'] : 3 ;
			$tablet_portrait = isset($settings['tablet-portrait']) ? $settings['tablet-portrait'] : 2 ;
			$phone = isset($settings['phone']) ? $settings['phone'] : 1 ;
			?>
			<table class="settings">
				<tr>
					<td>Categories<span class="add_field_button">+ Add category</span></td>
					<td class="input_fields_wrap">
						<?php for( $i = 0; $i < count( $all_category); $i++ ): ?>
						<div><input value="<?php echo isset($all_category[$i]) ? $all_category[$i] : ''; ?>" type="text" name="categories[]"/><span class="dashicons dashicons-dismiss remove_field" style="margin-top:3px; margin-left: 5px;"></span></div>
						<?php endfor; ?>
					</td>
				</tr>
			  <tr>
			  	<td>Default category name</td>
			  	<td><input type="text" name="default-category" value="<?php echo $default_category;?>"></td>
			  </tr>
			  <tr>
			  	<td>Lightbox</td>
			    <td>
			      <input type="checkbox" name="lightbox" id="lightbox" <?php echo $lightbox ? 'checked' : ''; ?>>
			      <label for="lightbox"><i class="toggle"></i></label>     
			    </td>
			  </tr>
			  <tr>
			  	<td>Grid size | Big 1800px+</td>
			    <td>
			      <select name="big">
			      	<?php for($i = 1; $i < 9; $i++): ?>
								<option value="<?php echo $i; ?>" <?php if ($big == $i) echo 'selected'; ?>><?php echo $i; ?></option>
			      	<?php endfor; ?>
			      </select>
			    </td>
			  </tr>
			  <tr>
			  	<td>Grid size | Desktop 1200px+</td>
			    <td>
			      <select name="desktop">
			      	<?php for($i = 1; $i < 8; $i++): ?>
								<option value="<?php echo $i; ?>" <?php if ($desktop == $i) echo 'selected'; ?>><?php echo $i; ?></option>
			      	<?php endfor; ?>
			      </select>
			    </td>
			  </tr>
			  <tr>
			  	<td>Grid size | Tablet landscape 940px+</td>
			    <td>
			      <select name="tablet-landscape">
			      	<?php for($i = 1; $i < 5; $i++): ?>
								<option value="<?php echo $i; ?>" <?php if ($tablet_landscape == $i) echo 'selected'; ?>><?php echo $i; ?></option>
			      	<?php endfor; ?>
			      </select>
			    </td>
			  </tr>
			  <tr>
			  	<td>Grid size | Tablet portrait 640px+</td>
			    <td>
			      <select name="tablet-portrait">
			      	<?php for($i = 1; $i < 5; $i++): ?>
								<option value="<?php echo $i; ?>" <?php if ($tablet_portrait == $i) echo 'selected'; ?>><?php echo $i; ?></option>
			      	<?php endfor; ?>
			      </select>
			    </td>
			  </tr>
			  <tr>
			  	<td>Grid size | Phone 640px- </td>
			    <td>
			      <select name="phone">
			      	<?php for($i = 1; $i < 5; $i++): ?>
								<option value="<?php echo $i; ?>" <?php if ($phone == $i) echo 'selected'; ?>><?php echo $i; ?></option>
			      	<?php endfor; ?>
			      </select>
			    </td>
			  </tr>
			</table>
			<script type="text/javascript">
				jQuery(document).ready(function() {
						// http://jsfiddle.net/techfoobar/xQqbR/
						jQuery('option').mousedown(function(e) {
						    e.preventDefault();
						    jQuery(this).prop('selected', jQuery(this).prop('selected') ? false : true);
						    return false;
						});
						//
				    var wrapper         = jQuery(".input_fields_wrap"); // Fields wrapper
				    var add_button      = jQuery(".add_field_button"); // Add button ID
				   
				    var x = 1; // initlal text box count
				    jQuery(add_button).click(function(e){ // on add input button click
				        e.preventDefault();
			            x++; // text box increment
			            jQuery(wrapper).append('<div><input type="text" name="categories[]"/><span class="dashicons dashicons-dismiss remove_field" style="margin-top:3px; margin-left: 5px;"></span></div>'); //add input box
				    });
				   
				    jQuery(wrapper).on("click",".remove_field", function(e){ // user click on remove text
				    	if (confirm('Are sure to delete this images?')) {
				    		e.preventDefault(); 
				    		jQuery(this).parent('div').fadeOut(700, function() {
                  jQuery(this).remove();
                });
                x--;
              }
				        
				    })
				});			
			</script>
			<?php
		}

		public function sg_image_upload($post) {
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'media-upload' );  // provides all functions for handling media uploads
			wp_enqueue_script( 'sg-uploader.js' , SG_URL . 'js/sg-uploader.js', array( 'jquery' ) );
			wp_enqueue_style( 'sg-admin', SG_URL . 'css/sg-admin.css' );
			wp_enqueue_media();	

			?>
			<div id="add-new-images" class="new-images">					
					<p style="font-size: 20px;"><i class="menu-icon dashicons dashicons-format-image"></i>
					<?php _e('Add Image', PFG_TXTDM); ?></p>		
			</div>
			<div style="text-align: center; padding: 4px 10px; background: #fefefe;">
				<p>Shortcode: <span id="shortcode"><?php echo "[sg id=".$post->ID."]"; ?></span></p>
			</div>
			<div id="image_upload">
			<ul id="remove-images" class="sbox">
				<?php
				// get all simple_gallery posts				
				$images = unserialize(base64_decode(
					get_post_meta( $post->ID, 'simple_gallery'.$post->ID, true )
				));
		
				$all_category = get_option( 'simple_gallery_categories' . $post->ID );
				$filters = isset($images['filters']) ? $images['filters'] : [];

				// check if there are images already attached
				if ( isset( $images['image-ids'] ) ) {
					$count = 0;

					foreach( $images['image-ids'] as $id ) {
						$thumb 		= wp_get_attachment_image_src( $id, 'medium', true );
						$attachment = get_post( $id ); 

						if( isset( $filters[$id] ) ) {
							$selected_filters_array = $filters[$id];
						} else {
							$selected_filters_array = array();
						}

						?>
						<li class="images">
							<div class="thumb-container">
								<img src="<?php echo $thumb[0]; ?>" alt="">
							</div>
							<input type="hidden" id="image-ids[]" name="image-ids[]" value="<?php echo $id; ?>" />
							<input type="text" name="image-title[]" id="image-title[]" style="width: 100%;" placeholder="Image Title" value="<?php echo get_the_title($id); ?>">
							<input type="text" name="image-description[]" id="image-description[]" style="width: 100%;" placeholder="Image Description" value="<?php echo $attachment->post_content; ?>">
							
							<select name="filters[<?php echo $id; ?>][]" multiple="multiple" id="filters" style="width: 100%;">
								<?php
								foreach ($all_category as $key => $value) {														
									?>
									<option value="<?php echo $key; ?>" 
									<?php 
									  if(isset($selected_filters_array)) { if(in_array($key, $selected_filters_array)) echo "selected=selected"; } ?>><?php echo $value; ?></option>
									<?php
								}							
								?>
							</select>
							<input type="button" name="remove-image" id="remove-image" style="width: 100%;" class="button" value="Delete">
						</li>
						<?php
					}  // end of foreach

				}  // end of if isset( $images['image-ids'] )
				?>
			</ul>
			</div>
			<?php
			require_once('simple-gallery-settings.php');

		}  // end of sg_image_upload

		public function _sg_ajax_callback_function($id) {
			// thumb, thumbnail, medium, large, post-thumbnail
			$thumbnail = wp_get_attachment_image_src($id, 'medium', true);
			$attachment = get_post( $id );  // $id = attachment id
			$all_category = get_option( 'simple_gallery_categories' . $id );	
			?>
			<li class="images">
				<div class="thumb-container">
					<img src="<?php echo $thumbnail[0]; ?>" alt="">
				</div>
				<input type="hidden" id="image-ids[]" name="image-ids[]" value="<?php echo $id; ?>" />
				<input type="text" name="image-title[]" id="image-title[]" style="width: 100%;" placeholder="Image Title" value="<?php echo get_the_title($id); ?>">
				<input type="text" name="image-description[]" id="image-description[]" style="width: 100%;" placeholder="Image Description" value="<?php echo $attachment->post_content; ?>">
				<select name="filters[<?php echo $id; ?>][]" multiple="multiple" id="filters" style="width: 100%;">
					<?php
					foreach ($all_category as $key => $value) {
						if($key != 0) {
						?><strong>
							<option value="<?php echo $key; ?>"><?php echo ucwords($value); ?>
							</option>
						</strong><?php
						}
					}
					?>
				</select>
				<input type="button" name="remove-image" id="remove-image" style="width: 100%;" class="button" value="Delete">
			</li>
			<?php
		}  // end of ajax callback

		public function _ajax_sg() {
			echo $this->_sg_ajax_callback_function($_POST['SGimageid']);
			die;
		}  // end of ajax sg 

		public function _sg_save_settings($id) {			
			if(isset($_POST['sg_save_nonce'])) {
				if (!isset( $_POST['sg_save_nonce'] ) || ! wp_verify_nonce( $_POST['sg_save_nonce'], 'sg_save_settings' ) ) {
				   print 'Sorry, your nonce did not verify.';
				   exit;
				} else {

					$image_ids 				= $_POST['image-ids'];
					$categories 			= $_POST['categories'];
					$image_titles 			= $_POST['image-title'];
					$image_descriptions 	= $_POST['image-description'];

					$i = 0;
					foreach($image_ids as $image_id) {
						$single_image_update = array(
							'ID'           => $image_id,
							'post_title'   => $image_titles[$i],
							'post_content'   => $image_descriptions[$i],						
						);
						wp_update_post( $single_image_update );
						$i++;
					}
					
					update_option('simple_gallery_categories' . $id, $categories);

					$simple_gallery_shortcode_setting = "simple_gallery" . $id;
					update_post_meta(
						$id, 
						$simple_gallery_shortcode_setting, 
						base64_encode(serialize($_POST))
					);
				}
			}
		}

	}
	$sg_object = new Simple_Gallery();
	require_once('simple-gallery-shortcode.php');
}
?>