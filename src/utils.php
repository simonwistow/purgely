<?php

/**
 * Sanitize a surrogate key to only allow hash-like keys.
 * 
 * This function will allow surrogate keys to be a-z, A-Z, 0-9, -, and _. This will hopefully ward off any weird issues
 * that might occur with unusual characters.
 * 
 * @param  string    $key    The key to sanitize.
 * @return string            The sanitized key.
 */
function purgely_sanitize_surrogate_key( $key ) {
	return preg_replace( '/[^a-zA-Z0-9_\-]/', '', $key );
}

/**
 * Purge a URL.
 *
 * @since  1.0.0.
 *
 * @param  string                 $url           The URL to purge.
 * @param  array                  $purge_args    Additional args to pass to the purge request.
 * @return array|bool|WP_Error                   The purge response.
 */
function purgely_purge_url( $url, $purge_args = array() ) {
	if ( isset( $purge_args['related'] ) && true === $purge_args['related'] ) {
		$purgely = new Purgely_Purge_Request_Collection( $url, $purge_args );
		$purgely->purge_related( $purgely->get_purge_args() );
	} else {
		$purgely = new Purgely_Purge();
		$purgely->purge( 'url', $url, $purge_args );
	}

	return $purgely->get_result();
}

/**
 * Purge a surrogate key.
 *
 * @since  1.0.0.
 *
 * @param  string                 $key           The surrogate key to purge.
 * @param  array                  $purge_args    Additional args to pass to the purge request.
 * @return array|bool|WP_Error                   The purge response.
 */
function purgely_purge_surrogate_key( $key, $purge_args = array() ) {
	$purgely = new Purgely_Purge();
	$purgely->purge( 'surrogate-key', $key, $purge_args );
	return $purgely->get_result();
}

/**
 * Purge the whole cache.
 *
 * @since  1.0.0.
 *
 * @param  array                  $purge_args    Additional args to pass to the purge request.
 * @return array|bool|WP_Error                   The purge response.
 */
function purgely_purge_all( $purge_args = array() ) {
	$purgely    = new Purgely_Purge();
	$purge_args = array_merge( array( 'allow-all' => PURGELY_ALLOW_PURGE_ALL ), $purge_args );

	$purgely->purge( 'all', '', $purge_args );
	return $purgely->get_result();
}

/**
 * Add a key to the list.
 *
 * @param  string    $key    The key to add to the list.
 * @return array             The full list of keys.
 */
function purgely_add_surrogate_key( $key ) {
	return get_purgely_instance()->add_key( $key );
}

/**
 * Set the TTL for the current request.
 *
 * @param  int    $seconds    The amount of seconds to cache the object for.
 * @return int                The amount of seconds to cache the object for.
 */
function purgely_set_ttl( $seconds ) {
	if ( ! empty( $seconds ) ) {
		$seconds = absint( $seconds );
		$seconds = get_purgely_instance()->set_ttl( $seconds );
	}

	return $seconds;
}

/**
 * Set the stale while revalidate directive.
 *
 * @param  int      $seconds    The TTL for stale while revalidate.
 * @return array                All of the cache control headers.
 */
function purgely_set_stale_while_revalidate( $seconds ) {
	$purgely = get_purgely_instance();
	return $purgely->add_cache_control_header( $seconds, 'stale-while-revalidate' );
}

/**
 * Set the stale while error directive.
 *
 * @param  int      $seconds    The TTL for stale while error.
 * @return array                All of the cache control headers.
 */
function purgely_set_stale_while_error( $seconds ) {
	$purgely = get_purgely_instance();
	return $purgely->add_cache_control_header( $seconds, 'stale-while-error' );
}