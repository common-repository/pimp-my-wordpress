<?php

/*
Plugin Name: Pimp My Wordpress
Plugin URI: http://www.bytesite.co.uk/pimp-my-wordpress
Description: List and showoff your installed wordpress plugins, just add [pimpmywordpress seperator=', '] to your body text
Version: 1.0
Author: Bytesite
Author URI: http://www.bytesite.co.uk/pimp-my-wordpress
*/

add_shortcode('pimpmywordpress', 'list_plugins');

function list_plugins($atts)
{
	global $wpdb;

	$seperator = $atts['seperator'] ? $atts['seperator'] : ", ";

	$sql = "SELECT option_value FROM ".$wpdb->prefix."options WHERE option_name ='active_plugins'";

	$rs = $wpdb->get_row($sql);

	$plugin_root = WP_PLUGIN_DIR;

	foreach ( unserialize($rs->option_value) as $plugin_file)
	{
		$pl_data = get_local_plugin_data($plugin_root . "/" . $plugin_file);
		$out[] = $pl_data['Title'] . " by " . $pl_data['Author'];
	}

	return implode($out, $seperator);
}

function get_local_plugin_data(  $plugin_file )
{
	/*
	 *  This is pretty much the original WP code
	 */


	$plugin_data = implode( '', file( $plugin_file ));

	preg_match( '|Plugin Name:(.*)$|mi', $plugin_data, $plugin_name );
	preg_match( '|Plugin URI:(.*)$|mi', $plugin_data, $plugin_uri );
	preg_match( '|Description:(.*)$|mi', $plugin_data, $description );
	preg_match( '|Author:(.*)$|mi', $plugin_data, $author_name );
	preg_match( '|Author URI:(.*)$|mi', $plugin_data, $author_uri );

	if ( preg_match( "|Version:(.*)|i", $plugin_data, $version ))
		$version = trim( $version[1] );
	else
		$version = '';

	$description = wptexturize( trim( $description[1] ));

	$name = $plugin_name[1];
	$name = trim( $name );
	$plugin = $name;
	if ('' != trim($plugin_uri[1]) && '' != $name ) {
		$plugin = '<a href="' . trim( $plugin_uri[1] ) . '" title="'.__( 'Visit plugin homepage' ).'">'.$plugin.'</a>';
	}

	if ('' == $author_uri[1] ) {
		$author = trim( $author_name[1] );
	} else {
		$author = '<a href="' . trim( $author_uri[1] ) . '" title="'.__( 'Visit author homepage' ).'">' . trim( $author_name[1] ) . '</a>';
	}

	return array('Name' => $name, 'Title' => $plugin, 'Description' => $description, 'Author' => $author, 'Version' => $version);
}

?>