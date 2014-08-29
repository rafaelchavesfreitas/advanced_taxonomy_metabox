<?php

if ( ! class_exists( 'Taxonomy_Single_Term_Walker' ) && class_exists( 'Walker' ) ) :

/**
 * Walker to output an unordered list of taxonomy radio <input> elements.
 *
 * @see Walker
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 * @since 0.1.2
 */
class Taxonomy_Single_Term_Walker extends Walker {
	var $tree_type = 'category';
	var $db_fields = array ('parent' => 'parent', 'id' => 'term_id'); //TODO: decouple this

	function __construct( $hierarchical, $input_el, $force_selection ) {
		$this->hierarchical = $hierarchical;
		$this->input_el = $input_el;
		$this->force_selection = is_bool( $force_selection ) ? $force_selection : true;
	}

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker:start_lvl()
	 *
	 * @since 0.1.2
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'radio' == $this->input_el ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent<ul class='children'>\n";
		}
	}

	/**
	 * Ends the list of after the elements are added.
	 *
	 * @see Walker::end_lvl()
	 *
	 * @since 0.1.2
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of category. Used for tab indentation.
	 * @param array  $args   An array of arguments. @see wp_terms_checklist()
	 */
	function end_lvl( &$output, $depth = 0, $args = array() ) {
		if ( 'radio' == $this->input_el ) {
			$indent = str_repeat("\t", $depth);
			$output .= "$indent</ul>\n";
		}
	}

	/**
	 * Start the element output.
	 *
	 * @see Walker::start_el()
	 *
	 * @since 0.1.2
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 * @param int    $id       ID of the current term.
	 */
	function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		extract($args);

		if ( empty( $taxonomy ) )
			$taxonomy = 'category';

		if ( $taxonomy == 'category' )
			$name = 'post_category';
		else
			$name = 'tax_input['.$taxonomy.']';

		// input name
		$name = $this->hierarchical ? $name .'[]' : $name;
		// input value
		$val  = $this->hierarchical ? $category->term_id : $category->slug;

		$class = in_array( $category->term_id, $popular_cats ) ? ' class="popular-category"' : '';

		if ( 'radio' == $this->input_el ) {
			$output .= "\n<li id='{$taxonomy}-{$category->term_id}'$class>" . '<label class="selectit"><input value="' . $val . '" type="radio" name="'.$name.'" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters('the_category', $category->name )) . '</label>';
		} else {
			$output .= "\n" . '<option ';
			$output .= selected( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false );
			$output .= 'id="' . $taxonomy . '-' . $category->term_id . '"';
			$output .= $class;
			$output .= 'value="' . $val . '"';
			$output .= ">";
			$output .= esc_html( apply_filters( 'the_category', $category->name ) );
		}

	}

	/**
	 * Ends the element output, if needed.
	 *
	 * @see Walker::end_el()
	 *
	 * @since 0.1.2
	 *
	 * @param string $output   Passed by reference. Used to append additional content.
	 * @param object $category The current term object.
	 * @param int    $depth    Depth of the term in reference to parents. Default 0.
	 * @param array  $args     An array of arguments. @see wp_terms_checklist()
	 */
	function end_el( &$output, $category, $depth = 0, $args = array() ) {
		$output .= "</option>\n";
	}
}

endif; // class_exists check
