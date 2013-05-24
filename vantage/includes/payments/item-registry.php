<?php

/**
 * Keeps track of items registered in the theme and
 * stores information about them. 
 *
 * Mainly used in order summaries and reports.
 */
class APP_Item_Registry{

	/**
	 * Stores the item types currently registered
	 * @var array
	 */
	private static $types = array();
	
	/**
	 * Registers an item for later dispaly
	 * @param  string $id    Item type, see APP_Order::add_item()
	 * @param  string $title Title for display to users
	 * @param  array  $meta  Meta information kept for various uses
	 * @return void        
	 */
	public static function register( $id, $title, $meta = array() ){
		self::$types[ $id ] = array(
			'title' => $title,
			'meta' => $meta
		);
	}
	
	/**
	 * Returns the title of the given item
	 * @param  string $id Item type registered in register()
	 * @return string     Item title registered in register()
	 */
	public static function get_title( $id ){
		return self::$types[ $id ]['title'];
	}

	/**
	 * Returns the array of meta information, or part 
	 * of it if specified
	 * @param  string $id  The item type registered in register()
	 * @param  string $key (optional) The part of the array to return
	 * @return array|mixed If specified, the part of the meta array, 
	 * 							or the entire meta array
	 */
	public static function get_meta( $id, $key = '' ){

		if( empty( $key ) )
			return self::$types[ $id ]['meta'];
		else if( isset( self::$types[ $id]['meta'][$key] ) )
			return self::$types[ $id ]['meta'][$key];
		else
			return false;

	}

	public static function is_registered( $id ){
		return isset( self::$types[ $id ] );
	}

}