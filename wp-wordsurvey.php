<?php
/*
Plugin Name: WordSurvey
Description: The plugin allow you to create & publish complete surveys with a few mouse clicks and to see the statistics in the backend.
Version: 3.2
Author: Sersis - Servizi e Sistemi
Author URI: http://www.sersis.com/
Text Domain: wp-wordsurvey
*/


// VERSION
define( 'WP_WORDSURVEY_VERSION', 1.0 );

// TEXT DOMAIN
add_action( 'plugins_loaded', 'wordsurvey_textdomain' );
function wordsurvey_textdomain() {
	load_plugin_textdomain( 'wp-wordsurvey', false, dirname( plugin_basename( __FILE__ ) ).'/languages');
}


// TABLE
global $wpdb;
$wpdb->wordsurvey   = $wpdb->prefix.'wordsurvey';
$wpdb->wordsurvey_answer   = $wpdb->prefix.'wordsurvey_answer';
$wpdb->wordsurvey_question  = $wpdb->prefix.'wordsurvey_question';
$wpdb->wordsurvey_vote  = $wpdb->prefix.'wordsurvey_vote';


include(plugin_dir_path( __FILE__ ).'inc/frontendsurvey.php');

// MENU
add_action( 'admin_menu', 'wordsurvey_menu' );
function wordsurvey_menu() {
	global $wp_version;
	$w = '';
	if($wp_version < 4.6) {$w = 'wp-';}
	add_menu_page( __( 'WordSurvey', 'wp-wordsurvey' ), __( 'WordSurvey', 'wp-wordsurvey' ), 'manage_options', $w.'wordsurvey/wordsurvey-manager.php', '', 'dashicons-chart-pie' );
	add_submenu_page( $w.'wordsurvey/wordsurvey-manager.php', __( 'Add Survey', 'wp-wordsurvey'), __( 'Add Survey', 'wp-wordsurvey' ), 'manage_options', $w.'wordsurvey/wordsurvey-add.php' );
}

//SCRIPT ADMIN
add_action('admin_enqueue_scripts', 'wordsurvey_register_script');
function wordsurvey_register_script($hook){
	global $wp_version;
	$w = '';
	if($wp_version < 4.6) {$w = 'wp-';}
	if ( $w.'wordsurvey/wordsurvey-manager.php' != $hook && $w.'wordsurvey/wordsurvey-add.php' != $hook ) {
        return;
    }
	
	wp_enqueue_style('wordsurvey-style-admin', plugins_url('/css/wordsurvey-admin-css.css',__FILE__), false, WP_WORDSURVEY_VERSION, 'all');
	wp_enqueue_script('wordsurvey-scripts-admin', plugins_url('/js/wordsurvey-scripts-admin.js',__FILE__), array('json2','jquery'), WP_WORDSURVEY_VERSION, true);
	
	wp_localize_script( 'wordsurvey-scripts-admin', 'objectL10n', array(
	'ajaxurl'   => admin_url( 'admin-ajax.php' ),
	'wrdsrv_image_path' => plugins_url('/images/loading.gif',__FILE__),
	'Add_Answer'  => __('Aggiungi Risposta',$w.'wordsurvey'),
	'Question' => __( 'Domanda', $w.'wordsurvey' ),
	'Save_Question' => __( 'Salva Domanda', $w.'wordsurvey' ),
	'Type' => __( 'Tipologia', $w.'wordsurvey' ),
	'Obligatory_Question' => __( 'Domanda Obbligatoria', $w.'wordsurvey' ),
	'text' => __( 'testo', $w.'wordsurvey' ),
	'select' => __( 'seleziona...', $w.'wordsurvey' ),
	'selection' => __( 'selezione', $w.'wordsurvey' ),
	'Reply' => __( 'Risposta', $w.'wordsurvey' ),
	'Reply_n_1' => __( 'Risposta n. 1', $w.'wordsurvey' ),
	'Number_of_reply' => __( 'Numero di risposte', $w.'wordsurvey' ),
	'Edit_Sounding' => __( 'Modifica Sondaggio', $w.'wordsurvey' ),
	'Delete_Sounding' => __( 'Elimina Sondaggio', $w.'wordsurvey' ),
	'Edit_Question' => __( 'Modifica Domanda', $w.'wordsurvey' ),
	'Delete_Question' => __( 'Elimina Domanda', $w.'wordsurvey' ),
	'Create_Sounding' => __( 'Crea Sondaggio', $w.'wordsurvey' ),
	) );
	
	wp_enqueue_style('bootstrap-css', plugins_url('/js/bootstrap/css/bootstrap.css',__FILE__));
	wp_enqueue_style('bootstrap-css', plugins_url('/js/bootstrap/css/bootstrap-theme.min.css',__FILE__));
	wp_enqueue_style('bootstrap-datetimepicker-css', plugins_url('/js/bootstrap/css/bootstrap-datetimepicker.min.css',__FILE__));
	wp_enqueue_script('bootstrap-js', plugins_url('/js/bootstrap/js/bootstrap.js',__FILE__),array('jquery'));
	wp_enqueue_script('bootstrap-datetimepicker-js', plugins_url('/js/bootstrap/js/bootstrap-datetimepicker.min.js',__FILE__));
	
	//wp_enqueue_script('chart-wordsurvey-stats-js', plugins_url('/js/chart/Chart.js',__FILE__));

}
//SCRIPT FRONTEND
add_action('wp_enqueue_scripts', 'wordsurvey_regfront_script');
function wordsurvey_regfront_script(){

	wp_enqueue_style('wordsurvey-style-wp', plugins_url('/css/wordsurvey-wp-css.css',__FILE__), false, WP_WORDSURVEY_VERSION, 'all');
	wp_enqueue_script('wordsurvey-scripts-wp', plugins_url('/js/wordsurvey-scripts-wp.js',__FILE__), array('json2','jquery'), WP_WORDSURVEY_VERSION, true);
	
	wp_localize_script( 'wordsurvey-scripts-wp','wrdsrvajfe', array('ajaxurl'   => admin_url( 'admin-ajax.php' ),'wrdsrv_image_path' => plugins_url('/images/loading.gif',__FILE__)));

}


// SHORT CODE
add_shortcode( 'wordsurvey', 'wordsurvey_shortcode' );
function wordsurvey_shortcode( $atts, $content = null ) {
	//$attributes = shortcode_atts( array( 'id' => 0), $atts );
	//$id = intval( $attributes['id'] );
	
	extract( shortcode_atts( array(
				'id' =>0
			), $atts 
		) 
	);
	$bd = WRDsrv_printSurvey($id);
	return $bd;

}

// TINYMCE
add_action('init', 'wordsurvey_tinymce_buttons');
function wordsurvey_tinymce_buttons() {
	if(!current_user_can('edit_posts') && ! current_user_can('edit_pages')) {
		return;
	}
	if(get_user_option('rich_editing') == 'true') {
		add_filter('mce_external_plugins', 'wordsurvey_tinymce_addplugin');
		add_filter('mce_buttons', 'wordsurvey_tinymce_registerbutton');
		add_filter('wp_mce_translation', 'wordsurvey_tinymce_translation');
	}
}
function wordsurvey_tinymce_registerbutton($buttons) {
	array_push($buttons, 'separator', 'wordsurvey');
	return $buttons;
}
function wordsurvey_tinymce_addplugin($plugin_array) {
	$plugin_array['wordsurvey'] = plugins_url( '/tinymce/plugin.js?v=' . WP_WORDSURVEY_VERSION ,__FILE__);
	return $plugin_array;
}
function wordsurvey_tinymce_translation($mce_translation) {
	global $wp_version;
	$w = '';
	if($wp_version < 4.6) {$w = 'wp-';}
	$mce_translation['Enter ID'] = esc_js(__('Enter ID', $w.'wordsurvey'));
	$mce_translation['Error: ID must be numeric'] = esc_js(__('Error: ID must be numeric', $w.'wordsurvey'));
	$mce_translation['Please enter ID again'] = esc_js(__('Please enter ID again', $w.'wordsurvey'));
	$mce_translation['Insert Survey'] = esc_js(__('Insert Survey', $w.'wordsurvey'));
	return $mce_translation;
}


// REMOVE
register_uninstall_hook(__FILE__,'wordsurvey_remove_from_database');
function wordsurvey_remove_from_database(){
	global $wpdb;
	$table = array('wordsurvey_vote','wordsurvey_answer','wordsurvey_question','wordsurvey');
	for($t=0; $t<count($table); $t++){
		$tab = $wpdb->prefix . $table[$t];
		$wpdb->query( "DROP TABLE IF EXISTS $tab" );
	}
}


// ACTIVATE
register_activation_hook( __FILE__, 'wordsurvey_activation' );
function wordsurvey_activation( $network_wide )
{
	if ( is_multisite() && $network_wide )
	{
		$ms_sites = wp_get_sites();

		if( 0 < sizeof( $ms_sites ) )
		{
			foreach ( $ms_sites as $ms_site )
			{
				switch_to_blog( $ms_site['blog_id'] );
				wordsurvey_activate();
				restore_current_blog();
			}
		}
	}
	else
	{
		wordsurvey_activate();
	}
}

function wordsurvey_activate() {
	global $wpdb;
	
	if(@is_file(ABSPATH.'/wp-admin/includes/upgrade.php')) {
		include_once(ABSPATH.'/wp-admin/includes/upgrade.php');
	} elseif(@is_file(ABSPATH.'/wp-admin/upgrade-functions.php')) {
		include_once(ABSPATH.'/wp-admin/upgrade-functions.php');
	} else {
		die('We have problem finding your \'/wp-admin/upgrade-functions.php\' and \'/wp-admin/includes/upgrade.php\'');
	}
	
	$charset_collate = '';
	if( $wpdb->has_cap( 'collation' ) ) {
		if(!empty($wpdb->charset)) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if(!empty($wpdb->collate)) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
	}
	$create_table = array();
	$create_table['wordsurvey_question'] = "CREATE TABLE $wpdb->wordsurvey_question (".
									"sounding_question_id int(10) NOT NULL auto_increment,".
									"sounding_question_question varchar(200) character set utf8 NOT NULL default '',".
									"sounding_question_type int(5) NOT NULL default 0,".
									"sounding_question_obbl int(5) NOT NULL default 0,".
									"sounding_question_num int(10) NOT NULL default 0,".
									"sounding_question_soundid int(10),".
									"sounding_question_totvote int(10),".
									"sounding_question_ts timestamp,".
									"PRIMARY KEY (sounding_question_id)) $charset_collate;";
	$create_table['wordsurvey_answer'] = "CREATE TABLE $wpdb->wordsurvey_answer (".
									"sounding_answer_id int(10) NOT NULL auto_increment,".
									"sounding_answer_questid int(10) NOT NULL default '0',".
									"sounding_answer_answers varchar(200) character set utf8 NOT NULL default '',".
									"sounding_answer_votes int(10) NOT NULL default '0',".
									"PRIMARY KEY (sounding_answer_id)) $charset_collate;";
	$create_table['wordsurvey'] = "CREATE TABLE IF NOT EXISTS $wpdb->wordsurvey (".
									"sounding_id int(10) NOT NULL auto_increment,".
									"sounding_name varchar(200) NOT NULL default '',".
									"sounding_expiration_date varchar(20) NOT NULL default '0000-00-00 00:00:00',".
									"sounding_user tinytext NOT NULL,".
									"sounding_userid int(10) NOT NULL default '0',".
									"sounding_ts timestamp,".
									"PRIMARY KEY (sounding_id)) $charset_collate;";
	$create_table['wordsurvey_vote'] = "CREATE TABLE	$wpdb->wordsurvey_vote (".
									"sounding_vote_id int(10) NOT NULL auto_increment,".
									"sounding_vote_answerid int(10) NOT NULL default '0',".
									"sounding_vote_text varchar(255) character set utf8 NOT NULL default '',".
									"sounding_vote_ts timestamp,".
									"PRIMARY KEY (sounding_vote_id)) $charset_collate;";
	maybe_create_table($wpdb->wordsurvey_question, $create_table['wordsurvey_question']);
	maybe_create_table($wpdb->wordsurvey_answer, $create_table['wordsurvey_answer']);
	maybe_create_table($wpdb->wordsurvey, $create_table['wordsurvey']);
	maybe_create_table($wpdb->wordsurvey_vote, $create_table['wordsurvey_vote']);
	add_option('wrdsrv_surveylimit',1);

}

/*FUNZIONI PER L'AJAX BACKEND*/
add_action( 'wp_ajax_wrdsrva_save_sounding', 'wrdsrv_save_sounding' );
add_action( 'wp_ajax_wrdsrva_save_question_sounding', 'wrdsrv_save_sounding' );
add_action( 'wp_ajax_wrdsrva_preview', 'wrdsrv_preview' );
add_action( 'wp_ajax_wrdsrva_edit_sounding', 'wrdsrv_edit_sounding' );
add_action( 'wp_ajax_wrdsrva_delete_sounding', 'wrdsrv_edit_sounding' );
add_action( 'wp_ajax_wrdsrva_edit_question_sounding', 'wrdsrv_edit_sounding' );
add_action( 'wp_ajax_wrdsrva_delete_question_sounding', 'wrdsrv_edit_sounding' );
add_action( 'wp_ajax_wrdsrva_delete_answer_sounding', 'wrdsrv_edit_sounding' );
add_action( 'wp_ajax_wrdsrva_delete_answercod_sounding', 'wrdsrv_edit_sounding' );
add_action( 'wp_ajax_wrdsrva_masterdelete_sounding', 'wrdsrv_masteredit_sounding' );
add_action( 'wp_ajax_wrdsrva_mastersave_question_sounding', 'wrdsrv_mastersave_question' );

/*FUNZIONI AJAX FRONTEND*/
add_action( 'wp_ajax_wrdsrva_frontend_save_vote', 'wrdsrv_frontend_save_vote' );
add_action( 'wp_ajax_nopriv_wrdsrva_frontend_save_vote', 'wrdsrv_frontend_save_vote' );

include(plugin_dir_path( __FILE__ ).'/inc/ajax_function.php');
include(plugin_dir_path( __FILE__ ).'/inc/preview.php');

include(plugin_dir_path( __FILE__ ).'/inc/ajax_front.php');
