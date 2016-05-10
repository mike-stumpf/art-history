<?php


class Types_Information_Table extends Types_Information_Container {

	protected $template;
	protected $archive;
	protected $views;
	protected $forms;

	/**
	 * @param $message
	 *
	 * @return bool
	 */
	public function add_message( $message ) {
		if( ! $message = Types_Helper_Type_Hinting::valid( $message, 'Types_Information_Message' ) )
			return false;

		if( $message->get_type() )
			switch( $message->get_type() ) {
				case 'template':
					if( $this->template === null && $message->valid() )
						$this->template[] = $message;
					break;
				case 'archive':
					if( $this->archive === null && $message->valid() )
						$this->archive[] = $message;
					break;
				case 'views':
					if( $this->views === null && $message->valid() )
						$this->views[] = $message;
					break;
				case 'forms':
					if( $this->forms === null && $message->valid() )
						$this->forms[] = $message;
					break;
			}
	}

	public function get_template() {
		return $this->template;
	}

	public function get_archive() {
		$post_type = Types_Helper_Condition::get_post_type();
		if( $post_type->name == 'post' ||  $post_type->name == 'page' )
			return false;

		return $this->archive;
	}

	public function get_views() {
		return $this->views;
	}

	public function get_forms() {
		return $this->forms;
	}

}