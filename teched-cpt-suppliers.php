<?php
/**
 * Plugin Name: CPT Suppliers
 * Plugin URI: https://github.com/realbig/teched-cpt-suppliers
 * Description: Holds the Suppliers Post Type
 * Version: 1.1.0
 * Text Domain: teched-cpt-suppliers
 * Author: Real Big Marketing
 * Author URI: https://realbigmarketing.com/
 * Contributors: d4mation
 * GitHub Plugin URI: realbig/teched-cpt-suppliers
 * GitHub Branch: master
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'TechEd_CPT_Suppliers' ) ) {

	/**
	 * Main TechEd_CPT_Suppliers class
	 *
	 * @since	  1.0.0
	 */
	final class TechEd_CPT_Suppliers {
		
		/**
		 * @var			array $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			array $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.0.0
		 */
		private $admin_errors;

		/**
		 * @var			object $field_helpers RBM_Field_Helpers object
		 * @since		1.0.0
		 */
		public $field_helpers;

		/**
		 * @var			object $upgrade TechEd_CPT_Suppliers_Upgrade object
		 * @since		1.0.0
		 */
		public $upgrade;

		/**
		 * Get active instance
		 *
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  object self::$instance The one true TechEd_CPT_Suppliers
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			if ( version_compare( get_bloginfo( 'version' ), '4.4' ) < 0 ) {
				
				$this->admin_errors[] = sprintf( _x( '%s requires v%s of %sWordPress%s or higher to be installed!', 'First string is the plugin name, followed by the required WordPress version and then the anchor tag for a link to the Update screen.', 'teched-cpt-suppliers' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '4.4', '<a href="' . admin_url( 'update-core.php' ) . '"><strong>', '</strong></a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}

			// Check for the CPT class, to ensure that RBM CPTs has successfully loaded
			if ( ! class_exists( 'RBM_CPT' ) ) {
				
				$this->admin_errors[] = sprintf( __( 'To use the %s Plugin, %s must be installed!', 'teched-cpt-suppliers' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '<a href="//github.com/realbig/rbm-cpts/" target="_blank">' . __( 'RBM Custom Post Types', 'gscr-cpt-radio-shows' ) . '</a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
				
			}

			// RBM CPTs will complain about this too, but that's OK
			if ( ! class_exists( 'RBM_FieldHelpers' ) ) {
				$this->admin_errors[] = sprintf( __( 'To use the %s Plugin, %s must be installed!', 'teched-cpt-suppliers' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '<a href="//github.com/realbig/rbm-field-helpers-wrapper/" target="_blank">' . __( 'RBM Field Helpers', 'teched-cpt-suppliers' ) . '</a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;
			}
			
			$this->require_necessities();
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'TechEd_CPT_Suppliers_VER' ) ) {
				// Plugin version
				define( 'TechEd_CPT_Suppliers_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'TechEd_CPT_Suppliers_DIR' ) ) {
				// Plugin path
				define( 'TechEd_CPT_Suppliers_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'TechEd_CPT_Suppliers_URL' ) ) {
				// Plugin URL
				define( 'TechEd_CPT_Suppliers_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'TechEd_CPT_Suppliers_FILE' ) ) {
				// Plugin File
				define( 'TechEd_CPT_Suppliers_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language suppliers
			$lang_dir = TechEd_CPT_Suppliers_DIR . '/languages/';
			$lang_dir = apply_filters( 'teched_cpt_suppliers_languages_suppliers', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'teched-cpt-suppliers' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'teched-cpt-suppliers', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/teched-cpt-suppliers/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/teched-cpt-suppliers/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'teched-cpt-suppliers', $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/teched-cpt-suppliers/languages/ folder
				load_textdomain( 'teched-cpt-suppliers', $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( 'teched-cpt-suppliers', false, $lang_dir );
			}

		}
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access	  private
		 * @since	  1.0.0
		 * @return	  void
		 */
		private function require_necessities() {

			$this->field_helpers = new RBM_FieldHelpers( array(
				'ID'   => 'suppliers',
				'l10n' => array(
					'field_table'    => array(
						'delete_row'    => __( 'Delete Row', 'teched-cpt-suppliers' ),
						'delete_column' => __( 'Delete Column', 'teched-cpt-suppliers' ),
					),
					'field_select'   => array(
						'no_options'       => __( 'No select options.', 'teched-cpt-suppliers' ),
						'error_loading'    => __( 'The results could not be loaded', 'teched-cpt-suppliers' ),
						/* translators: %d is number of characters over input limit */
						'input_too_long'   => __( 'Please delete %d character(s)', 'teched-cpt-suppliers' ),
						/* translators: %d is number of characters under input limit */
						'input_too_short'  => __( 'Please enter %d or more characters', 'teched-cpt-suppliers' ),
						'loading_more'     => __( 'Loading more results...', 'teched-cpt-suppliers' ),
						/* translators: %d is maximum number items selectable */
						'maximum_selected' => __( 'You can only select %d item(s)', 'teched-cpt-suppliers' ),
						'no_results'       => __( 'No results found', 'teched-cpt-suppliers' ),
						'searching'        => __( 'Searching...', 'teched-cpt-suppliers' ),
					),
					'field_repeater' => array(
						'collapsable_title' => __( 'New Row', 'teched-cpt-suppliers' ),
						'confirm_delete'    => __( 'Are you sure you want to delete this element?', 'teched-cpt-suppliers' ),
						'delete_item'       => __( 'Delete', 'teched-cpt-suppliers' ),
						'add_item'          => __( 'Add', 'teched-cpt-suppliers' ),
					),
					'field_media'    => array(
						'button_text'        => __( 'Upload / Choose Media', 'teched-cpt-suppliers' ),
						'button_remove_text' => __( 'Remove Media', 'teched-cpt-suppliers' ),
						'window_title'       => __( 'Choose Media', 'teched-cpt-suppliers' ),
					),
					'field_checkbox' => array(
						'no_options_text' => __( 'No options available.', 'teched-cpt-suppliers' ),
					),
				),
			) );

			require_once trailingslashit( TechEd_CPT_Suppliers_DIR ) . 'core/cpt/class-teched-cpt-suppliers-cpt.php';
			$this->cpt = new CPT_TechEd_CPT_Suppliers();

			require_once trailingslashit( TechEd_CPT_Suppliers_DIR ) . 'core/class-teched-cpt-suppliers-upgrade.php';
			$this->upgrade = new TechEd_CPT_Suppliers_Upgrade();
			
		}
		
		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  HTML
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access	  public
		 * @since	  1.0.0
		 * @return	  void
		 */
		public function register_scripts() {
			
			wp_register_style(
				'teched-cpt-suppliers',
				TechEd_CPT_Suppliers_URL . 'dist/assets/css/app.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : TechEd_CPT_Suppliers_VER
			);
			
			wp_register_script(
				'teched-cpt-suppliers',
				TechEd_CPT_Suppliers_URL . 'dist/assets/js/app.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : TechEd_CPT_Suppliers_VER,
				true
			);
			
			wp_localize_script( 
				'teched-cpt-suppliers',
				'techEdCPTSuppliers',
				apply_filters( 'teched_cpt_suppliers_localize_script', array() )
			);
			
			wp_register_style(
				'teched-cpt-suppliers-admin',
				TechEd_CPT_Suppliers_URL . 'dist/assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : TechEd_CPT_Suppliers_VER
			);
			
			wp_register_script(
				'teched-cpt-suppliers-admin',
				TechEd_CPT_Suppliers_URL . 'dist/assets/js/admin.js',
				array( 'jquery' ),
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : TechEd_CPT_Suppliers_VER,
				true
			);
			
			wp_localize_script( 
				'teched-cpt-suppliers-admin',
				'techEdCPTSuppliers',
				apply_filters( 'teched_cpt_suppliers_localize_admin_script', array() )
			);
			
		}
		
	}
	
} // End Class Exists Check

/**
 * The main function responsible for returning the one true TechEd_CPT_Suppliers
 * instance to functions everywhere
 *
 * @since	  1.0.0
 * @return	  \TechEd_CPT_Suppliers The one true TechEd_CPT_Suppliers
 */
add_action( 'plugins_loaded', 'teched_cpt_suppliers_load' );
function teched_cpt_suppliers_load() {

	require_once __DIR__ . '/core/teched-cpt-suppliers-functions.php';
	TECHEDCPTSUPPLIERS();

}