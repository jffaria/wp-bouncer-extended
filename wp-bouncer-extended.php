<?php
/*
 * Plugin Name: WP Bouncer Extended
 * Plugin URI: https://www.jffaria.com
 * Description: Extends WP Bouncer to support more flexible use cases.
 * Version: 1.0
 * Author: jffaria
 * Author URI: https://www.jffaria.com
 *
 *     Copyright 2019 João Faria
 *
 */

class WP_Bouncer_Extended {
	/**
	 * Constructor
	 *
	 * @return WP_Bouncer_Extended
	 */
	public function __construct() {
		add_filter( 'wp_bouncer_session_ids',  array($this, 'manage_sessions'), 10, 3);
		add_filter( 'wp_bouncer_number_simultaneous_logins', array($this, 'num_sessions_allowed'), 10, 1);
		add_filter( 'wp_bouncer_ignore_admins', '__return_false' ); 
	}

	/** 
	 * Overrides the session ID management done by WP Bouncer to implement a different use case
	 *
	 * @return Array
	 */
	public function manage_sessions($session_ids, $old_session_ids, $user_id) {
		error_log( '[WP Bouncer Ext] wp_bouncer_session_ids ( ' . print_r($session_ids, true) .', ' . print_r($old_session_ids, true) . ', ' . $user_id . ' )' );
		$num_allowed = apply_filters('wp_bouncer_number_simultaneous_logins', 1);
		if(empty($num_allowed))
			return $session_ids;

		// If we have more sessions than allowed, remove from the _tail_!
		//make sure it's an array, as this cleanup is done after $session_ids is copied in WP Bouncer
		if(empty($old_session_ids))
			$old_session_ids = array();
		elseif(!is_array($old_session_ids))
			$old_session_ids = array($old_session_ids);
		while(count($old_session_ids) > $num_allowed) {
			unset($old_session_ids[count($old_session_ids)-1]); // Remove newest session
			$old_session_ids = array_values($old_session_ids); // Fix keys
		}
		error_log( '[WP Bouncer Ext] wp_bouncer_session_ids <- ' . print_r( $old_session_ids, true ) );
		return $old_session_ids;
	}

	/** 
	 * Overrides the session ID management done by WP Bouncer to allow more sessions then the default
	 *
	 * @return Array
	 */
	public function num_sessions_allowed($num_allowed) {
		$num_allowed = 4;
		error_log( '[WP Bouncer Ext] wp_bouncer_number_simultaneous_logins <- ' . $num_allowed );
		return $num_allowed;
	}

}

$WP_Bouncer_Extended = new WP_Bouncer_Extended();
