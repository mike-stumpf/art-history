<?php


class Types_Information_Message {

	protected $id;
	protected $type = false;
	protected $conditions = array();

	public $priority;
	public $title;
	public $description;

	/**
	 * Type Set & Get
	 * @param $type
	 */
	public function set_id( $id ) {
		$this->id = $id;
	}

	public function get_id() {
		return $this->id;
	}

	/**
	 * Type Set & Get
	 * @param $type
	 */
	public function set_type( $type ) {
		switch( $type ) {
			case 'information':
			case 'template':
			case 'archive':
			case 'views':
			case 'forms':
				$this->type = $type;
				break;
		}
	}

	public function get_type() {
		return $this->type;
	}

	/**
	 * Use this to add multiple conditions at ounce.
	 *
	 * @param $conditions
	 *
	 * @return bool
	 */
	public function add_conditions( $conditions ) {
		if( $conditions === false )
			return false;

		if( is_array( $conditions ) ) {
			foreach( $conditions as $condition ) {
				$this->add_condition( $condition );
			}
		} else {
			$this->add_condition( $conditions );
		}
	}


	/**
	 * Add a condition to show the message.
	 *
	 * @param Types_Helper_Condition $condition
	 *
	 * @return bool
	 */
	public function add_condition( $condition ) {
		if( $condition = Types_Helper_Type_Hinting::valid( $condition, 'Types_Helper_Condition' ) )
			$this->conditions[] = $condition;

		return $this;
	}

	/**
	 * Check if all assigned conditions match
	 *
	 * @return bool
	 */
	public function valid() {

		foreach( $this->conditions as $condition ) {
			if( ! $condition->valid() )
				return false;
		}

		return true;
	}

	/**
	 * Title Set & Get
	 * @param $title
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	public function get_title() {
		return $this->title;
	}

	/**
	 * Description Set & Get
	 * @param $description
	 */
	public function set_description( $description ) {
		if( !is_array( $description ) ) {
			$this->description = array(
				array(
					'type' => 'paragraph',
					'content' => $description
				)
			);

			return;
		}

		// @todo Remove once placeholder replace comes before Twig rendering
		foreach( $description as &$element ) {
			if( isset( $element['target'] ) ) {
				switch( $element['target'] ) {
					case '%POST-PERMALINK%':
						$element['target'] = Types_Helper_Placeholder::get_permalink();
						break;
					case '%POST-ARCHIVE-PERMALINK%':
						$element['target'] = Types_Helper_Placeholder::get_archive_permalink();
						break;
				}
			}

		}

		$this->description = $description;
	}

	public function get_description() {
		return $this->description;
	}


	/**
	 * Import data
	 * see /application/data/information
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	public function data_import( $data ) {
		if( ! is_array( $data ) )
			return false;

		$default = array(
			'id'            => false,
			'type'          => false,
			'conditions'    => false,
			'title'         => false,
			'description'   => false,
			'priority'      => false
		);

		$cfg = array_replace_recursive( $default, $data );

		$this->set_id( $cfg['id'] );
		$this->set_type( $cfg['type'] );
		$this->add_conditions( $cfg['conditions'] );
		$this->set_title( $cfg['title'] );
		$this->set_description( $cfg['description'] );
		$this->priority = $cfg['priority'];
	}


	/**
	 * Add link, used for example, documentation and how to resolve links
	 *
	 * @param $target
	 * @param $link
	 * @param bool $in_array
	 *  false for $target   = $link
	 *  true  for $target[] = $link
	 */
	protected function add_link( &$target, $link, $in_array = false ) {

		if( isset( $link['label'] ) && isset( $link['link'] ) ) {
			$add = array(
				'label' => $link['label'],
				'link' => $link['link']
			);
		} elseif( isset( $link['label'] ) && isset( $link['dialog'] ) ) {
			$add = array(
				'label'  => $link['label'],
				'dialog' => $link['dialog']
			);
		} elseif( count( $link, COUNT_RECURSIVE ) == 2 ) {
			$add = array(
				'label' => $link[0],
				'link' => $link[1]
			);
		}

		if( isset( $link['class'] ) )
			$add['class'] = $link['class'];

		if( isset( $add ) ) {
			if( $in_array ) {
				$target[] = $add;
			} else {
				$target = $add;
			}
		}

	}
}