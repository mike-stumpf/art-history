<?php

/**
 * Autoloader for Types classes.
 *
 * See:
 * @link https://git.onthegosystems.com/toolset/layouts/wikis/layouts-theme-integration#wpddl_theme_integration_autoloader
 *
 * @since 2.0
 */
final class Toolset_Autoloader {

	private static $instance;

	protected $paths = array();
	protected $prefixes = array();

	protected function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	public static function get_instance() {
		if( self::$instance === null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}


	/**
	 * Add base path to search for class files.
	 *
	 * @param string $path
	 * @return bool True if path was added
	 * 
	 * @since 2.0
	 */
	public function add_path( $path ) {

		// abort if already set
		if( in_array( $path, $this->paths ) )
			return true;

		// check if path is readable
		if( is_readable( $path ) ) {
			array_push( $this->paths, $path );
			return true;
		}

		return false;
	}

	
	/**
	 * Add multiple base paths.
	 *
	 * @param array $paths
	 * @since 2.0
	 */
	public function add_paths( array $paths ) {
		// run this->addPath for each value
		foreach( $paths as $path ) {
			$this->add_path( $path );
		}
	}

	
	public function get_paths() {
		return $this->paths;
	}

	
	public function add_prefix( $prefix ) {
		// abort if already exists
		if( in_array( $prefix, $this->prefixes ) )
			return $this;

		array_push( $this->prefixes, $prefix );

		// We assume that most specific (longest) prefixes will have higher probability of a match.
		// This is useful when one prefix is substring of another.
		rsort( $this->prefixes );

		return $this;
	}
	

	public function get_prefixes() {
		return $this->prefixes;
	}
	
	

	public function autoload( $class ) {

		if( class_exists( $class ) ) {
			return true;
		}

		foreach( $this->prefixes as $prefix ) {

			// Will be equal to $class if no replacement happens.
			$class_without_prefix = preg_replace( '#^'.$prefix.'_#', '', $class );

			if( $class != $class_without_prefix ) {

				$result = $this->try_autoload_without_prefix( $class, $class_without_prefix );

				// false means we should try with other prefixes
				if( false !== $result ) {
					return $result;
				}
			}
		}

		// no prefix used
		$result = $this->try_autoload_without_prefix( $class, $class );
		if( false !== $result )
			return $result;


		return false;
	}


	/**
	 * Try to load class after matching it's prefix.
	 *
	 * @param string $full_class_name Full name of the class.
	 * @param string $class_name_without_prefix Name of the class without the registered prefix.
	 * @return bool|mixed include_once() result or false if the file was not found.
	 */
	private function try_autoload_without_prefix( $full_class_name, $class_name_without_prefix ) {

		// explode class by _
		$explode_class = explode( '_' , $class_name_without_prefix );

		// get class filename
		$class_filename = array_pop( $explode_class );
		$class_filename = strtolower( $class_filename ) . '.php';

		// get class path
		$class_path = '';
		foreach( $explode_class as $path ) {
			$class_path .= strtolower( $path ) . '/';
		}

		$file = $class_path . $class_filename;

		// check for file in path
		foreach( $this->get_paths() as $path ) {

			$next_filename = $file;

			while( true ) {
				$candidate_filename = $next_filename;

				$candidate_path = $path . '/' . $candidate_filename;

				$result = $this->try_load_file( $candidate_path, $full_class_name );
				if( false !== $result ) {
					return $result;
				}

				// Replace the last slash by underscore.
				// This allows to use underscores in class filename instead of subfolders
				$next_filename = preg_replace( '/(\/(?!.*\/))/', '_', $candidate_filename );

				// If there was no change, we have tried all possibilities for this filename.
				if( $next_filename == $candidate_filename ) {
					break;
				}

			}

		}

		// Last attempt for legacy classes.
		return false;
	}


	/**
	 * Try loading a file and check if given class exists afterwards.
	 *
	 * @param string $full_path Absolute path to the file.
	 * @param string $full_class_name Full class name to be checked.
	 *
	 * @return bool|mixed include_once() result or false if the class couldn't be loaded. It may give false negatives.
	 */
	private function try_load_file( $full_path, $full_class_name ) {
		if( is_file( $full_path ) ) {
			/** @noinspection PhpIncludeInspection */
			$result = include_once( $full_path ) ;

			// Do not stop trying if we load the file but it doesn't contain the requested class.
			if( class_exists( $full_class_name ) ) {
				return $result;
			}
		}

		return false;
	}
}