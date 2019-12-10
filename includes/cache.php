<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists('pdc_cache') ) :

class pdc_cache {
	
	// vars
	var $reference = array(),
		$active = true;
		
		
	/*
	*  __construct
	*
	*  This function will setup the class functionality
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.4.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function __construct() {
		
		// prevent PDC from persistent cache
		wp_cache_add_non_persistent_groups('pdc');
		
	}
	
	
	/*
	*  is_active
	*
	*  This function will return true if caching is enabled
	*
	*  @type	function
	*  @date	26/6/17
	*  @since	5.6.0
	*
	*  @param	n/a
	*  @return	(bool)
	*/
	
	function is_active() {
		
		return $this->active;
		
	}
	
	
	/*
	*  enable
	*
	*  This function will enable PDC caching
	*
	*  @type	function
	*  @date	26/6/17
	*  @since	5.6.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function enable() {
		
		$this->active = true;
		
	}
	
	
	/*
	*  disable
	*
	*  This function will disable PDC caching
	*
	*  @type	function
	*  @date	26/6/17
	*  @since	5.6.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function disable() {
		
		$this->active = false;
		
	}
	
	
	/*
	*  get_key
	*
	*  This function will check for references and modify the key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @return	$key
	*/
	
	function get_key( $key = '' ) {
		
		// check for reference
		if( isset($this->reference[ $key ]) ) {
			
			$key = $this->reference[ $key ];
				
		}
		
		
		// return
		return $key;
		
	}
	
	
	
	/*
	*  isset_cache
	*
	*  This function will return true if a cached data exists for the given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @return	(boolean)
	*/
	
	function isset_cache( $key = '' ) {
		
		// bail early if not active
		if( !$this->is_active() ) return false;
		
		
		// vars
		$key = $this->get_key($key);
		$found = false;
		
		
		// get cache
		$cache = wp_cache_get($key, 'pdc', false, $found);
		
		
		// return
		return $found;
		
	}
	
	
	/*
	*  get_cache
	*
	*  This function will return cached data for a given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @return	(mixed)
	*/
	
	function get_cache( $key = '' ) {
		
		// bail early if not active
		if( !$this->is_active() ) return false;
		
		
		// vars
		$key = $this->get_key($key);
		$found = false;
		
		
		// get cache
		$cache = wp_cache_get($key, 'pdc', false, $found);
		
		
		// return
		return $cache;
		
	}
	
	
	/*
	*  set_cache
	*
	*  This function will set cached data for a given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @param	$data (mixed)
	*  @return	n/a
	*/
	
	function set_cache( $key = '', $data = '' ) {
		
		// bail early if not active
		if( !$this->is_active() ) return false;
		
		
		// set
		wp_cache_set($key, $data, 'pdc');
		
		
		// return
		return $key;
		
	}
	
	
	/*
	*  set_cache_reference
	*
	*  This function will set a reference to cached data for a given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @param	$reference (string)
	*  @return	n/a
	*/
	
	function set_cache_reference( $key = '', $reference = '' ) {
		
		// bail early if not active
		if( !$this->is_active() ) return false;
		
		
		// add
		$this->reference[ $key ] = $reference;	
		
		
		// resturn
		return $key;
		
	}
	
	
	/*
	*  delete_cache
	*
	*  This function will delete cached data for a given key
	*
	*  @type	function
	*  @date	30/06/2016
	*  @since	5.4.0
	*
	*  @param	$key (string)
	*  @return	n/a
	*/
	
	function delete_cache( $key = '' ) {
		
		// bail early if not active
		if( !$this->is_active() ) return false;
		
		
		// delete
		return wp_cache_delete( $key, 'pdc' );
		
	}
	
}


// initialize
pdc()->cache = new pdc_cache();

endif; // class_exists check


/*
*  pdc_is_cache_active
*
*  alias of pdc()->cache->is_active()
*
*  @type	function
*  @date	26/6/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_is_cache_active() {
	
	return pdc()->cache->is_active();
	
}


/*
*  pdc_disable_cache
*
*  alias of pdc()->cache->disable()
*
*  @type	function
*  @date	26/6/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_disable_cache() {
	
	return pdc()->cache->disable();
	
}


/*
*  pdc_enable_cache
*
*  alias of pdc()->cache->enable()
*
*  @type	function
*  @date	26/6/17
*  @since	5.6.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_enable_cache() {
	
	return pdc()->cache->enable();
	
}


/*
*  pdc_isset_cache
*
*  alias of pdc()->cache->isset_cache()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_isset_cache( $key = '' ) {
	
	return pdc()->cache->isset_cache( $key );
	
}


/*
*  pdc_get_cache
*
*  alias of pdc()->cache->get_cache()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_get_cache( $key = '' ) {
	
	return pdc()->cache->get_cache( $key );
	
}


/*
*  pdc_set_cache
*
*  alias of pdc()->cache->set_cache()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_set_cache( $key = '', $data ) {
	
	return pdc()->cache->set_cache( $key, $data );
	
}


/*
*  pdc_set_cache_reference
*
*  alias of pdc()->cache->set_cache_reference()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_set_cache_reference( $key = '', $reference = '' ) {
	
	return pdc()->cache->set_cache_reference( $key, $reference );
	
}


/*
*  pdc_delete_cache
*
*  alias of pdc()->cache->delete_cache()
*
*  @type	function
*  @date	30/06/2016
*  @since	5.4.0
*
*  @param	n/a
*  @return	n/a
*/

function pdc_delete_cache( $key = '' ) {
	
	return pdc()->cache->delete_cache( $key );
	
}

?>