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
	}

	/** 
	 * Overrides the session ID management done by WP Bouncer to implement a different use case
	 *
	 * @return Array
	 */
	public function manage_sessions($session_ids, $old_session_ids, $user_id) {
		$num_allowed = apply_filters('wp_bouncer_number_simultaneous_logins', 1);
		if(empty($num_allowed))
			return $session_ids;
		// If we have more sessions than allowed, remove from the _tail_!
		while(count($old_session_ids) > $num_allowed) {
			unset($old_session_ids[$num_allowed-1]; // Remove newest session
		}
		return $old_session_ids;
	}
}

$WP_Bouncer_Extended = new WP_Bouncer_Extended();
