<?php
/**
 * Class CPT_TechEd_CPT_Suppliers
 *
 * Creates the post type.
 *
 * @since {{VERSION}}
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CPT_TechEd_CPT_Suppliers extends RBM_CPT {

	public $post_type = 'teched-suppliers';
	public $label_singular = null;
	public $label_plural = null;
	public $labels = array();
	public $icon = 'networking';
	public $post_args = array(
		'hierarchical' => false,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'custom-fields' ),
		'has_archive' => true,
		'rewrite' => array(
			'slug' => 'supplier',
			'with_front' => false,
			'feeds' => false,
			'pages' => true
		),
		'menu_position' => 11,
		//'capability_type' => 'suppliers',
	);

	/**
	 * CPT_TechEd_CPT_Suppliers constructor.
	 *
	 * @since {{VERSION}}
	 */
	function __construct() {

		// This allows us to Localize the Labels
		$this->label_singular = __( 'Supplier', 'teched-cpt-suppliers' );
		$this->label_plural = __( 'Suppliers', 'teched-cpt-suppliers' );

		$this->labels = array(
			'menu_name' => __( 'Suppliers', 'teched-cpt-suppliers' ),
			'all_items' => __( 'All Suppliers', 'teched-cpt-suppliers' ),
		);

        parent::__construct();
        
        add_action( 'init', array( $this, 'register_taxonomy' ) );
        
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		
    }
    
    /**
	 * Registers our Directory Item Categories Taxonomy
	 *
	 * @access	public
	 * @since	{{VERSION}}
	 * @return  void
	 */
	public function register_taxonomy() {

        $args = array(
            'hierarchical'          => true,
            'labels'                => $this->get_taxonomy_labels( __( 'Subject/Discipline', 'teched-cpt-suppliers' ), __( 'Subjects/Disciplines', 'teched-cpt-suppliers' ) ),
            'show_in_menu'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'suppliers-subject-discipline' ),
        );
    
        register_taxonomy( 'suppliers-subject-discipline', 'teched-suppliers', $args );

        $args = array(
            'hierarchical'          => true,
            'labels'                => $this->get_taxonomy_labels( __( 'Grade Level', 'teched-cpt-suppliers' ), __( 'Grade Levels', 'teched-cpt-suppliers' ) ),
            'show_in_menu'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'suppliers-grade-level' ),
        );
    
        register_taxonomy( 'suppliers-grade-level', 'teched-suppliers', $args );

        $args = array(
            'hierarchical'          => true,
            'labels'                => $this->get_taxonomy_labels( __( 'Industry', 'teched-cpt-suppliers' ), __( 'Industries', 'teched-cpt-suppliers' ) ),
            'show_in_menu'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'suppliers-industry' ),
        );
    
        register_taxonomy( 'suppliers-industry', 'teched-suppliers', $args );

	}

	/**
     * DRYs up the code above a little
     *
     * @param   [string]  $singular   Singular Label
     * @param   [string]  $plural     Plural Label
     * @param   [string]  $menu_name  Menu Label. Defaults to Plural Label
     *
     * @since   {{VERSION}}
     * @return  [array]               Taxonomy Labels
     */
    private function get_taxonomy_labels( $singular, $plural, $menu_name = false ) {

        if ( ! $menu_name ) {
            $menu_name = $plural;
        }

        $labels = array(
            'name'                       => $menu_name,
            'singular_name'              => $singular,
            'search_items'               => sprintf( __( 'Search %', 'teched-cpt-suppliers' ), $plural ),
            'popular_items'              => sprintf( __( 'Popular %s', 'teched-cpt-suppliers' ), $plural ),
            'all_items'                  => sprintf( __( 'All %', 'teched-cpt-suppliers' ), $plural ),
            'parent_item'                => sprintf( __( 'Parent %s', 'teched-cpt-suppliers' ), $singular ),
            'parent_item_colon'          => sprintf( __( 'Parent %s:', 'teched-cpt-suppliers' ), $singular ),
            'edit_item'                  => sprintf( __( 'Edit %s', 'teched-cpt-suppliers' ), $singular ),
            'update_item'                => sprintf( __( 'Update %s', 'teched-cpt-suppliers' ), $singular ),
            'add_new_item'               => sprintf( __( 'Add New %s', 'teched-cpt-suppliers' ), $singular ),
            'new_item_name'              => sprintf( __( 'New %s Name', 'teched-cpt-suppliers' ), $singular ),
            'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'teched-cpt-suppliers' ), $plural ),
            'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'teched-cpt-suppliers' ), $plural ),
            'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'teched-cpt-suppliers' ), $plural ),
            'not_found'                  => sprintf( __( 'No %s found.', 'teched-cpt-suppliers' ), $plural ),
            'menu_name'                  => $menu_name,
        );

        return $labels;

    }

    /**
	 * Enqueues the necessary JS/CSS on the Suppliers Screen
	 *
	 * @access	public
	 * @since	{{VERSION}}
	 * @return  void
	 */
	public function admin_enqueue_scripts() {

		$current_screen = get_current_screen();
		global $pagenow;
		
		if ( $current_screen->post_type == 'teched-suppliers' && 
			( in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) ) {

            wp_enqueue_style( 'teched-cpt-suppliers-admin' );
            
            add_filter( 'rbm_fieldhelpers_load_select2', '__return_true' );

		}

	}

	/**
	 * Registers our Meta Boxes
	 *
	 * @access	public
	 * @since	{{VERSION}}
	 * @return  void
	 */
	public function add_meta_boxes() {

		add_meta_box(
			'suppliers-meta',
			sprintf( __( '%s Meta', 'teched-cpt-suppliers' ), $this->label_singular ),
			array( $this, 'suppliers_metabox_content' ),
			$this->post_type,
			'normal'
        );
        
        add_meta_box(
			'suppliers-featured',
			sprintf( __( 'Featured %s', 'teched-cpt-suppliers' ), $this->label_singular ),
			array( $this, 'suppliers_featured_metabox_content' ),
			$this->post_type,
            'side',
            'high'
		);

	}

	/**
	 * Adds Metabox Content for our Suppliers Item Occurrences Meta Box
	 *
	 * @access	public
	 * @since	{{VERSION}}
	 * @return  void
	 */
	public function suppliers_metabox_content() {
        
        echo '<h3>' . __( 'Address', 'teched-cpt-suppliers' ) . '</h3>';

        teched_suppliers_do_field_text( array(
            'name' => 'street_address_1',
            'group' => 'suppliers_meta',
            'label' => '<strong>' . __( 'Street Address Line 1', 'teched-cpt-suppliers' ) . '</strong>',
            'input_class' => '',
            'input_atts' => array(
                'style' => 'width: 100%;',
            ),
            'wrapper_classes' => array(
                'fieldhelpers-col',
                'fieldhelpers-col-1',
            ),
        ) );

        teched_suppliers_do_field_text( array(
            'name' => 'street_address_2',
            'group' => 'suppliers_meta',
            'label' => '<strong>' . __( 'Street Address Line 2', 'teched-cpt-suppliers' ) . '</strong>',
            'input_class' => '',
            'input_atts' => array(
                'style' => 'width: 100%;',
            ),
            'wrapper_classes' => array(
                'fieldhelpers-col',
                'fieldhelpers-col-1',
            ),
        ) );

        teched_suppliers_do_field_text( array(
            'name' => 'city',
            'group' => 'suppliers_meta',
            'label' => '<strong>' . __( 'City', 'teched-cpt-suppliers' ) . '</strong>',
            'wrapper_classes' => array(
                'fieldhelpers-col',
                'fieldhelpers-col-3',
            ),
        ) );

        teched_suppliers_do_field_select( array(
            'name' => 'state',
            'group' => 'suppliers_meta',
            'label' => '<strong>' . __( 'State', 'teched-cpt-suppliers' ) . '</strong>',
            'options' => array( '' => __( 'Select a State', 'teched-cpt-suppliers' ) ) + teched_suppliers_get_state_list(),
            'placeholder' => __( 'Select a State', 'teched-cpt-suppliers' ),
            'wrapper_classes' => array(
                'fieldhelpers-col',
                'fieldhelpers-col-3',
            ),
        ) );

        teched_suppliers_do_field_text( array(
            'name' => 'zip',
            'group' => 'suppliers_meta',
            'label' => '<strong>' . __( 'ZIP Code', 'teched-cpt-suppliers' ) . '</strong>',
            'wrapper_classes' => array(
                'fieldhelpers-col',
                'fieldhelpers-col-3',
            ),
        ) );

        teched_suppliers_do_field_text( array(
            'name' => 'phone',
            'group' => 'suppliers_meta',
            'label' => '<strong>' . __( 'Phone Number', 'teched-cpt-suppliers' ) . '</strong>',
            'wrapper_classes' => array(
                'fieldhelpers-col',
                'fieldhelpers-col-1',
            ),
        ) );

        teched_suppliers_do_field_text( array(
            'name' => 'fax',
            'group' => 'suppliers_meta',
            'label' => '<strong>' . __( 'Fax Number', 'teched-cpt-suppliers' ) . '</strong>',
            'wrapper_classes' => array(
                'fieldhelpers-col',
                'fieldhelpers-col-1',
            ),
        ) );

        teched_suppliers_do_field_text( array(
            'name' => 'website_url',
            'group' => 'suppliers_meta',
            'label' => '<strong>' . __( 'Website Address', 'teched-cpt-suppliers' ) . '</strong>',
            'wrapper_classes' => array(
                'fieldhelpers-col',
                'fieldhelpers-col-2',
            ),
        ) );

		teched_suppliers_init_field_group( 'suppliers_meta' );

    }
    
    public function suppliers_featured_metabox_content() {

        teched_suppliers_do_field_toggle( array(
            'name' => 'featured',
            'group' => 'suppliers_featured',
            'label' => '<stronge>' . sprintf( __( 'Is this a Featured %s?', 'teched-cpt-suppliers' ), $this->label_singular ) . '</strong>',
        ) );

    }
	
}