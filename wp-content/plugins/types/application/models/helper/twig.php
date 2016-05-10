<?php


class Types_Helper_Twig {

	private $filesystem;
	private $twig;

	public function __construct() {
		$this->filesystem = new Twig_Loader_Filesystem();
		$this->filesystem->addPath( TYPES_ABSPATH . '/application/views' );
		$this->twig = new Twig_Environment( $this->filesystem );
	}

	public function render( $file, $data ) {
		if( $this->filesystem->exists( $file ) )
			return $this->twig->render( $file, $data );

		return false;
	}
}