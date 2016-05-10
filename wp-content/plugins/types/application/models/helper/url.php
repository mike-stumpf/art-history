<?php


class Types_Helper_Url {

	private static $utm_source = 'typesplugin';
	private static $utm_campaign = 'types';

	private static $utm_medium;
	private static $utm_term;
	private static $utm_content;

	private static $urls = array();

	public static function set_medium( $medium = false ) {
		if( $medium ) {
			self::$utm_medium = $medium;
			return;
		}

		global $pagenow;
		if( ! empty( $pagenow ) ) {
			self::$utm_medium = $pagenow;
			return;
		}

		self::$utm_medium = false;
	}

	private static function get_medium() {
		if( self::$utm_medium === null )
			self::set_medium();

		return self::$utm_medium;
	}

	public static function add_urls( $urls ) {
		if( is_array( $urls ))
			self::$urls = array_merge( self::$urls, $urls );
	}

	private static function apply_analytics_arguments_to_url( $url ) {
		if( ! self::get_medium() )
			return $url;

		$url_parts = parse_url( $url );

		if( isset( $url_parts['fragment'] ) && !empty( $url_parts['fragment'] ) )
			$url = str_replace( '#'.$url_parts['fragment'], '', $url );

		$url .= !isset( $url_parts['query'] ) || empty( $url_parts['query'] ) ? '?' : '&';
		$url .= 'utm_source='.self::$utm_source
		        .'&utm_campaign='.self::$utm_campaign
		        .'&utm_medium='.self::$utm_medium
				.'&utm_term='.self::$utm_term
				.'&utm_content='.self::$utm_content;

		if( isset( $url_parts['fragment'] ) && !empty( $url_parts['fragment'] ) )
			$url .= '#' . $url_parts['fragment'];

		return $url;
	}

	public static function get_url( $key, $utm_content = false, $utm_term = false, $utm_medium = false ) {
		if( !isset( self::$urls[$key] ) )
			return '';

		$url = self::$urls[$key];

		// return url if no arguments
		if( ! $utm_content )
			return $url;

		// add utm content
		self::$utm_content = $utm_content;

		// use key for term, if no term isset
		if( ! $utm_term )
			$utm_term = $key;

		self::$utm_term = $utm_term;

		// apply medium only if medium isset
		if( $utm_medium )
			self::set_medium( $utm_medium );

		// apply arguments
		$url = self::apply_analytics_arguments_to_url( $url );
		return $url;

	}
}