<?php
/*
Plugin Name: WordPress Publication Repository
Plugin URI: http://wordpress.org/extend/plugins/wordpress-publication-repository/
Description: It allows to manage a publication repository and exposes metadata in order to be indexed by Google Scholar
Version: 0.0.2
Author: Joan Junyent
Compatible: 3.0
Tags: Dublin Core, metadata, Google Scholar, DMS, Document Manager, Publication Manager

Released under the GNU General Public License (GPL) http://www.gnu.org/licenses/gpl.txt
This is a WordPress plugin (http://www.wordpress.org/).
*/


/*======== Set up a custom post type ==========*/
add_theme_support( 'post-thumbnails' );

add_action('init', 'WPPR_init');

function WPPR_init()
{
  $labels = array(
	'name' => _x('Publications', 'post type general name'),
	'singular_name' => _x('Publication', 'post type singular name'),
	'add_new' => _x('Add New', 'publication'),
	'add_new_item' => __('Add New publication'),
	'edit_item' => __('Edit publication'),
	'new_item' => __('New publication'),
	'view_item' => __('View publication'),
	'search_items' => __('Search publications'),
	'not_found' =>  __('No publications found'),
	'not_found_in_trash' => __('No publication found in Trash'),
	'parent_item_colon' => ''
  );
  $args = array(
	'labels' => $labels,
	'public' => true,
	'publicly_queryable' => true,
	'show_ui' => true,
	'query_var' => true,
	'rewrite' => true,
	'has_archive' => true,
	'capability_type' => 'post',
	'hierarchical' => false,
	'menu_position' => null,
	'supports' => array('title','editor','author','thumbnail','excerpt','comments'),
	'register_meta_box_cb' => 'WPPR_add_meta_boxes',
	'taxonomies' => array('category','post_tag','pub_serie','pub_author','pub_publisher','pub_lang')
  );
  register_post_type('publication',$args);

  // Add new taxonomies, NOT hierarchical (like tags)
	  $pub_serie_labels = array(
		'name' => _x( 'Series', 'taxonomy general name' ),
		'singular_name' => _x( 'Serie', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search series' ),
		'popular_items' => __( 'Popular series' ),
		'all_items' => __( 'All series' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit serie' ),
		'update_item' => __( 'Update serie' ),
		'add_new_item' => __( 'Add New serie' ),
		'new_item_name' => __( 'New serie Name' ),
		'separate_items_with_commas' => __( 'Separate series with commas' ),
		'add_or_remove_items' => __( 'Add or remove series' ),
		'choose_from_most_used' => __( 'Choose from the most used series' )
	  );

	  register_taxonomy('pub_serie','publication',array(
		'hierarchical' => false,
		'labels' => $pub_serie_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'serie' ),
	  ));

	  $pub_author_labels = array(
		'name' => _x( 'Authors', 'taxonomy general name' ),
		'singular_name' => _x( 'Author', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search authors' ),
		'popular_items' => __( 'Popular authors' ),
		'all_items' => __( 'All authors' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit author' ),
		'update_item' => __( 'Update author' ),
		'add_new_item' => __( 'Add New author' ),
		'new_item_name' => __( 'New author Name' ),
		'separate_items_with_commas' => __( 'Separate authors with commas' ),
		'add_or_remove_items' => __( 'Add or remove authors' ),
		'choose_from_most_used' => __( 'Choose from the most used authors' )
	  );

	  register_taxonomy('pub_author','publication',array(
		'hierarchical' => false,
		'labels' => $pub_author_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'author' ),
	  ));
	  $pub_publisher_labels = array(
		'name' => _x( 'Publishers', 'taxonomy general name' ),
		'singular_name' => _x( 'Publisher', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search publishers' ),
		'popular_items' => __( 'Popular publisher' ),
		'all_items' => __( 'All publishers' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit publisher' ),
		'update_item' => __( 'Update publisher' ),
		'add_new_item' => __( 'Add New publisher' ),
		'new_item_name' => __( 'New publisher Name' ),
		'separate_items_with_commas' => __( 'Separate publishers with commas' ),
		'add_or_remove_items' => __( 'Add or remove publishers' ),
		'choose_from_most_used' => __( 'Choose from the most used publishers' )
	  );

	  register_taxonomy('pub_publisher','publication',array(
		'hierarchical' => false,
		'labels' => $pub_publisher_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'publisher' ),
	  ));

	  $pub_lang_labels = array(
		'name' => _x( 'Languages', 'taxonomy general name' ),
		'singular_name' => _x( 'Language', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search languages' ),
		'popular_items' => __( 'Popular languages' ),
		'all_items' => __( 'All languages' ),
		'parent_item' => null,
		'parent_item_colon' => null,
		'edit_item' => __( 'Edit language' ),
		'update_item' => __( 'Update language' ),
		'add_new_item' => __( 'Add New language' ),
		'new_item_name' => __( 'New language Name' ),
		'separate_items_with_commas' => __( 'Separate languages with commas' ),
		'add_or_remove_items' => __( 'Add or remove languages' ),
		'choose_from_most_used' => __( 'Choose from the most used languages' )
	  );

	  register_taxonomy('pub_lang','publication',array(
		'hierarchical' => false,
		'labels' => $pub_lang_labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => array( 'slug' => 'language' ),
	  ));


}

//add filter to insure the text Publication, or publication, is displayed when user updates a book
add_filter('post_updated_messages', 'publication_updated_messages');
function publication_updated_messages( $messages ) {
  global $post, $post_ID;

  $messages['publication'] = array(
	0 => '', // Unused. Messages start at index 1.
	1 => sprintf( __('Publication updated. <a href="%s">View publication</a>'), esc_url( get_permalink($post_ID) ) ),
	2 => __('Custom field updated.'),
	3 => __('Custom field deleted.'),
	4 => __('Publication updated.'),
	/* translators: %s: date and time of the revision */
	5 => isset($_GET['revision']) ? sprintf( __('Publication restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
	6 => sprintf( __('Publication published. <a href="%s">View publication</a>'), esc_url( get_permalink($post_ID) ) ),
	7 => __('Book saved.'),
	8 => sprintf( __('Publication submitted. <a target="_blank" href="%s">Preview publication</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
	9 => sprintf( __('Publication scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview publication</a>'),
	  // translators: Publish box date format, see http://php.net/date
	  date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
	10 => sprintf( __('Publication draft updated. <a target="_blank" href="%s">Preview publication</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

//display contextual help for Publications
add_action( 'contextual_help', 'WPPR_add_help_text', 10, 3 );

function WPPR_add_help_text($contextual_help, $screen_id, $screen) {
  //$contextual_help .= var_dump($screen); // use this to help determine $screen->id
  if ('publication' == $screen->id ) {
	$contextual_help =
	  '<p>' . __('Things to remember when adding or editing a publication:') . '</p>' .
	  '<ul>' .
	  '<li>' . __('Specify the correct genre.') . '</li>' .
	  '<li>' . __('Specify the correct author of the book.  Remember that the Author module refers to you, the author of this book review.') . '</li>' .
	  '</ul>' .
	  '<p>' . __('If you want to schedule the publication to be published in the future:') . '</p>' .
	  '<ul>' .
	  '<li>' . __('Under the Publish module, click on the Edit link next to Publish.') . '</li>' .
	  '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.') . '</li>' .
	  '</ul>' .
	  '<p><strong>' . __('For more information:') . '</strong></p>' .
	  '<p>' . __('<a href="http://codex.wordpress.org/Posts_Edit_SubPanel" target="_blank">Edit Posts Documentation</a>') . '</p>' .
	  '<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>' ;
  } elseif ( 'edit-book' == $screen->id ) {
	$contextual_help =
	  '<p>' . __('This is the help screen blah blah blah.') . '</p>' ;
  }
  return $contextual_help;
}



function WPPR_add_meta_boxes() {
	add_meta_box("publications-meta-box", "Publication Details", "WPPR_publication_meta_box",
	"publication", "normal", "low");

 }

$WPPR_publication_meta_box_array = array(
  	'journal_title' => array(
  		'label' => 'WPPR_journal_title',
		'description' => 'Journal Title'),
	'date' => array(
		'label' => 'WPPR_date',
		'description' => 'Date'),
	'year' => array(
		'label' => 'WPPR_year',
		'description' => 'Year'),
	'volume' => array(
		'label' => 'WPPR_volume',
		'description' => 'Volume'),
	'issue' => array(
		'label' => 'WPPR_issue',
		'description' => 'Issue'),
	'firstpage' => array(
		'label' => 'WPPR_firstpage',
		'description' => 'First page'),
	'lastpage' => array(
		'label' => 'WPPR_lastpage',
		'description' => 'Last page'),
	'issn' => array(
		'label' => 'WPPR_issn',
		'description' => 'ISSN'),
	'isbn' => array(
		'label' => 'WPPR_isbn',
		'description' => 'ISBN'),
	'pdf_url' => array(
		'label' => 'WPPR_pdf_url',
		'description' => 'PDF URL'),
	'pmid' => array(
		'label' => 'WPPR_pmid',
		'description' => 'PMID'),
	'license' => array(
		'label' => 'WPPR_license',
		'description' => 'License'),
	'license_url' => array(
		'label' => 'WPPR_license_url',
		'description' => 'License URL')
	);

/* Prints the box content */
function WPPR_publication_meta_box() {
  // Use nonce for verification
  wp_nonce_field( plugin_basename(__FILE__), 'WPPR_noncemetabox' );
  // The actual fields for data entry

  	global $post, $WPPR_publication_meta_box_array;

	foreach ($WPPR_publication_meta_box_array as $meta_field) {
		$meta_box_value = get_post_meta($post->ID, $meta_field['label'], true);

  		echo '<label for="'.$meta_field['label'].'" class="WPPR_meta_box_label">' . $meta_field['description'] . '</label> ';
  		echo '<input type="text" id= "'.$meta_field['label'].'" name="'.$meta_field['label'].'" value="'.$meta_box_value.'" size="25" class="WPPR_meta_box_input" /><br />';
	}

}

/* Do something with the data entered */
add_action('save_post', 'WPPR_save_metadata');


/* When the post is saved, saves our custom data */
function WPPR_save_metadata( $post_id ) {
  global $post, $WPPR_publication_meta_box_array;
  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['WPPR_noncemetabox'], plugin_basename(__FILE__) )) {
	return $post_id;
  }

  // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
  // to do anything
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
	return $post_id;

  // Check permissions
  if ( 'post' == $_POST['post_type'] ) {
	if ( !current_user_can( 'edit_page', $post_id ) )
	  return $post_id;
  } else {
	if ( !current_user_can( 'edit_post', $post_id ) )
	  return $post_id;
  }

  // OK, we're authenticated: we need to find and save the data

	foreach($WPPR_publication_meta_box_array as $meta_field) {
		$data = $_POST[$meta_field['label']];

		if(get_post_meta($post_id, $meta_field['label']) == "")
		add_post_meta($post_id, $meta_field['label'], $data, true);
		elseif($data != get_post_meta($post_id, $meta_field['label'], true))
		update_post_meta($post_id, $meta_field['label'], $data);
		elseif($data == "")
		delete_post_meta($post_id, $meta_field['label'], get_post_meta($post_id, $meta_field['label'], true));
	}
}

function WPPR_metabox_css() {
	echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl') .'/wp-content/plugins/WPPR/style.css" />' . "\n";
}

add_action('admin_print_styles', 'WPPR_metabox_css');


//----------------edit custom columns display for back-end
add_action("manage_posts_custom_column", "WPPR_custom_columns");
add_filter("manage_edit-publication_columns", "WPPR_publication_columns");

function WPPR_publication_columns($columns) //this function display the columns headings
{
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => "Title",
		"pub_author" => "Author",
		"abstract" => "Abstract",
		"thumbnail" => "Thumbnail"
	);
	return $columns;
}

function WPPR_custom_columns($column)
{
	global $post;
	if ("ID" == $column) echo $post->ID; //displays title
	elseif ("pub_author" == $column) echo get_the_term_list( $post->ID, 'pub_author', '', ', ', '' );
	elseif ("abstract" == $column) echo $post->post_excerpt; //displays the content excerpt
	elseif ("thumbnail" == $column) echo $post->post_thumbnail; //shows up our post thumbnail that we previously created.
}

function WPPR_get_the_terms($ID,$taxonomy){	// Based on http://www.murraypicton.com/plugins/tags-to-meta
	$tags = get_the_terms($post->ID, $taxonomy);
	$tagList = array();
	if(is_array($tags) && count($tags) > 0) {
		foreach($tags as $tag) {
			$tagList[] = $tag->name;
		}
	}
	$tagStr = implode(",", $tagList);
	return $tagStr;
}

/*========================*
 * Generate HTML metadata *
 *========================*/
function WPPR_write_metadata(){
	global $post;
	if(is_single() && get_post_type() == 'publication'){
		echo '<meta name="citation_journal_title" content="'.get_post_meta($post->ID, 'WPPR_journal_title', true).'">'."\n";
		echo '<meta name="citation_publisher" content="'.WPPR_get_the_terms($post->ID,'pub_publisher').'">'."\n";
		echo '<meta name="citation_authors" content="'.WPPR_get_the_terms($post->ID,'pub_author').'">'."\n";
		echo '<meta name="citation_title" content="'.get_the_title().'">'."\n";
		echo '<meta name="citation_date" content="'.get_post_meta($post->ID, 'WPPR_date', true).'">'."\n";
		echo '<meta name="citation_year" content="'.get_post_meta($post->ID, 'WPPR_year', true).'">'."\n";
		echo '<meta name="citation_volume" content="'.get_post_meta($post->ID, 'WPPR_volume', true).'">'."\n";
		echo '<meta name="citation_issue" content="'.get_post_meta($post->ID, 'WPPR_issue', true).'">'."\n";
		echo '<meta name="citation_firstpage" content="'.get_post_meta($post->ID, 'WPPR_firstpage', true).'">'."\n";
		echo '<meta name="citation_lastpage" content="'.get_post_meta($post->ID, 'WPPR_lastpage', true).'">'."\n";
		echo '<meta name="citation_issn" content="'.get_post_meta($post->ID, 'WPPR_issn', true).'">'."\n";
		echo '<meta name="citation_isbn" content="'.get_post_meta($post->ID, 'WPPR_isbn', true).'">'."\n";
		echo '<meta name="citation_language" content="'.WPPR_get_the_terms($post->ID,'pub_language').'">'."\n";
		echo '<meta name="citation_keywords" content="'.WPPR_get_the_terms($post->ID,'post_tag').'">'."\n";
		echo '<meta name="citation_abstract" content="'.get_the_excerpt().'">'."\n";
		echo '<meta name="citation_pdf_url" content="'.get_post_meta($post->ID, 'WPPR_pdf_url', true).'">'."\n";
		echo '<meta name="citation_abstract_html_url" content="'.get_permalink().'">'."\n";
		echo '<meta name="citation_pmid" content="'.get_post_meta($post->ID, 'WPPR_pmid', true).'">'."\n";
		echo '<meta name="DC.rights.license" content="'.get_post_meta($post->ID, 'WPPR_license', true).'">'."\n";
		echo '<meta name="DC.license" content="'.get_post_meta($post->ID, 'WPPR_license_url', true).'">'."\n";

	}
	else {	}

}

add_action('wp_head', 'WPPR_write_metadata');



?>
