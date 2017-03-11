<?php

$all_category = get_option('simple_gallery_categories');
if(is_array($all_category)){
	if(!isset($all_category[0])) {
		$all_category[0] = "all";
		update_option('simple_gallery_categories' . $post->ID, $all_category);
	}
} else {
	$all_category[0] = 'all';
	update_option('simple_gallery_categories' . $post->ID, $all_category);
}
?>

<div id="categories">
	<fieldset id="add-category">
		<form id="add-form">
			<label for="category">Add new category</label>
			<input type="text" placeholder="cat-name" name="category">
			<button onclick="post_category('add', '')">+</button>
		</form>
	</fieldset>
</div>

<script type="text/javascript">
// add, edit, delete category
function post_category(action, id) {
	if(action == "add") {
		jQuery.ajax({
			type: 'GET',
			url: location.href,
			data: jQuery('#add-form').serialize() + '&action=' + action,
			success:function(response){
				console.log('success');
			}
		});
	}
}
</script>
<?php
if(isset($_GET['action'])){
	//print_r($_POST);
	$action = $_GET['action'];
	
	if($action == "add"){
		$category_name = sanitize_text_field($_POST['name']);
		//$category_slug = strtolower($category_name);
		$new_category = array($category_name);

		$all_category = get_option('awl_portfolio_filter_gallery_categories');
		if(is_array($all_category)) {
			$all_category = array_merge($all_category, $new_category);
		} else {
		$all_category = $new_category;
		}
	}
}
?>