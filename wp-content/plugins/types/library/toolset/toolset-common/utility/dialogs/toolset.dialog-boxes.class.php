<?php

if ( ! class_exists( 'Toolset_DialogBoxes' ) ) {
	class Toolset_DialogBoxes
	{

		private $screens;

		public function __construct( $screens = array() ) {
			$this->screens = apply_filters( 'toolset_dialog-boxes_screen_ids', $screens );

			add_filter( 'toolset_add_registered_script', array( &$this, 'register_scripts' ), 10, 1 );
			add_filter( 'toolset_add_registered_styles', array( &$this, 'register_styles' ), 10, 1 );

		}

		function init_screen_render() {

			if ( empty( $this->screens ) ) {
				return;
			}

			$screen = get_current_screen();

			if ( ! in_array( $screen->id, $this->screens ) ) {
				return;
			}

			add_action( 'admin_print_scripts',	array( &$this, 'enqueue_scripts'), 999 );
			add_action( 'admin_footer',			array( &$this,'template'));
		}

		function register_scripts( $scripts ) {
			$scripts['ddl-abstract-dialog']	= new Toolset_Script( 'ddl-abstract-dialog', TOOLSET_COMMON_URL . '/utility/dialogs/js/views/abstract/ddl-abstract-dialog.js', array('jquery','wpdialogs'), '0.1', false );
			$scripts['ddl-dialog-boxes']	= new Toolset_Script( 'ddl-dialog-boxes', TOOLSET_COMMON_URL . '/utility/dialogs/js/views/abstract/dialog-view.js', array('jquery','ddl-abstract-dialog', 'underscore', 'backbone'), '0.1', false );

			return $scripts;
		}

		function register_styles( $styles ) {
			$styles['ddl-dialogs-css'] = new Toolset_Style( 'ddl-dialogs-css', TOOLSET_COMMON_URL . '/utility/dialogs/css/dd-dialogs.css', array( 'wp-jquery-ui-dialog' ) );
			$styles['ddl-dialogs-general-css'] = new Toolset_Style( 'ddl-dialogs-general-css', TOOLSET_COMMON_URL . '/utility/dialogs/css/dd-dialogs-general.css', array( 'wp-jquery-ui-dialog' ) );
			$styles['ddl-dialogs-forms-css'] = new Toolset_Style( 'ddl-dialogs-forms-css', TOOLSET_COMMON_URL . '/utility/dialogs/css/dd-dialogs-forms.css' );

			return $styles;
		}

		public function enqueue_scripts() {
			do_action( 'toolset_enqueue_styles',
				array(
					'ddl-dialogs-css',
					'ddl-dialogs-general-css',
					'ddl-dialogs-forms-css'
				)
			);

			do_action(	'toolset_enqueue_scripts', apply_filters( 'ddl-dialog-boxes_enqueue_scripts',array( 'ddl-dialog-boxes' ) ) );
		}

		public function template() {
			ob_start();?>
				<script type="text/html" id="ddl-cell-dialog-tpl">
					<div id="js-dialog-dialog-container">
					<div class="ddl-dialog-content" id="js-dialog-content-dialog">
						<?php printf(__('This is %s cell.', 'ddl-layouts'), '{{{ cell_type }}}'); ?>
					</div>

					<div class="ddl-dialog-footer" id="js-dialog-footer-dialog">
						<?php printf(__('This is %s cell.', 'ddl-layouts'), '{{{ cell_type }}}'); ?>
					</div>
					</div>
				</script>
			<?php
			echo ob_get_clean();
		}
	}

}