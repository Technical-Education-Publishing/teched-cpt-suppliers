<?php
/**
 * Handles plugin upgrades.
 *
 * @since {{VERSION}}
 */

defined( 'ABSPATH' ) || die();

/**
 * Class TechEd_CPT_Suppliers_Upgrade
 *
 * Handles plugin upgrades.
 *
 * @since {{VERSION}}
 */
class TechEd_CPT_Suppliers_Upgrade {

	/**
	 * TechEd_CPT_Suppliers_Upgrade constructor.
	 *
	 * @since {{VERSION}}
	 *
	 * @return bool True if needs to upgrade, false if does not.
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'check_upgrades' ) );

		if ( isset( $_GET['teched_cpt_suppliers_upgrade'] ) ) {

			add_action( 'admin_init', array( $this, 'do_upgrades' ) );
        }
        
        if ( isset( $_GET['teched_cpt_suppliers_upgraded'] ) ) {
            add_action( 'admin_init', array( $this, 'show_upgraded_message' ) );
        }

	}

	/**
	 * Checks for upgrades and migrations.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function check_upgrades() {

		$version = get_option( 'teched_cpt_suppliers_version', 0 );

		if ( version_compare( $version, TechEd_CPT_Suppliers_VER ) === - 1 ) {
			update_option( 'teched_cpt_suppliers_version', TechEd_CPT_Suppliers_VER );
		}

		$last_upgrade = get_option( 'teched_cpt_suppliers_last_upgrade', 0 );

		foreach ( $this->get_upgrades() as $upgrade_version => $upgrade_callback ) {

			if ( version_compare( $last_upgrade, $upgrade_version ) === - 1 ) {

				add_action( 'admin_notices', array( $this, 'show_upgrade_nag' ) );
				break;
			}
		}
	}

	/**
	 * Runs upgrades.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function do_upgrades() {

		$last_upgrade = get_option( 'teched_cpt_suppliers_last_upgrade', 0 );

		foreach ( $this->get_upgrades() as $upgrade_version => $upgrade_callback ) {

			if ( version_compare( $last_upgrade, $upgrade_version ) === - 1 ) {

				call_user_func( $upgrade_callback );
				update_option( 'teched_cpt_suppliers_last_upgrade', $upgrade_version );
			}
		}

		wp_safe_redirect( admin_url( 'index.php?teched_cpt_suppliers_upgraded=true' ) );
		exit();
	}

	/**
	 * Returns an array of all versions that require an upgrade.
	 *
	 * @since {{VERSION}}
	 * @access private
	 *
	 * @return array
	 */
	function get_upgrades() {

		return array(
			'1.0.0' => array( $this, 'upgrade_1_0_0' ),
		);
	}

	/**
	 * Displays upgrade nag.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function show_upgrade_nag() {
		?>
        <div class="notice notice-warning">
            <p>
				<?php printf( __( '%s needs to upgrade the database. It is strongly recommended you backup your database first.', 'teched-cpt-suppliers' ), TECHEDCPTSUPPLIERS()->plugin_data['Name'] ); ?>
                <a href="<?php echo add_query_arg( 'teched_cpt_suppliers_upgrade', '1' ); ?>"
                   class="button button-primary">
					<?php _e( 'Upgrade', 'teched-cpt-suppliers' ); ?>
                </a>
            </p>
        </div>
		<?php
	}

	/**
	 * Displays the upgrade complete message.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function show_upgraded_message() {
		?>
        <div class="notice notice-success">
            <p>
				<?php printf( __( '%s has successfully upgraded!', 'teched-cpt-suppliers' ), TECHEDCPTSUPPLIERS()->plugin_data['Name'] ); ?>
            </p>
        </div>
		<?php
	}

	/**
	 * 1.0.0 upgrade script.
	 *
	 * @since {{VERSION}}
	 * @access private
	 */
	function upgrade_1_0_0() {

		if ( ! class_exists( 'TablePress' ) ) return;

		$table = TablePress::$model_table->load( 'TE-Producers', true, true );

		foreach ( $table['data'] as $index => $row ) {

			// Skip headers row
			if ( $index == 0 ) continue;

			$post_title = preg_replace( '/[\t|\n|\r]*/is', '', trim( strip_tags( $row[0] ) ) );
			$post_title = preg_replace( '/\s{2,}/is', ' ', $post_title );
			$post_content = trim( $row[2] );

			$post_id = wp_insert_post( array(
                'ID' => 0,
                'post_type' => 'teched-suppliers',
                'post_title' => $post_title,
                'post_status' => 'publish',
                'post_content' => $post_content,
            ), true );

            if ( is_wp_error( $post_id ) ) {
                $errors = implode( ';', $post_id->get_error_messages() );
                error_log( $errors );
			}

			// Get the Featured Image
			$match = preg_match( '/\<img.*?src="(.*?)"/is', $row[0], $matches );

			$attachment_id = false;
			if ( $match ) {

				$dir = wp_upload_dir();

				$featured_image = str_replace( $dir['baseurl'], $dir['basedir'], $matches[1] );
				$attachment_id = teched_media_file_exists( str_replace( trailingslashit( $dir['basedir'] ), '', $featured_image ) );

				if ( $attachment_id ) {

					update_post_meta( $post_id, '_thumbnail_id', $attachment_id );

				}

			}

			// Sometimes Phone/Fax/Web gets put one line after the label
			$row[1] = preg_replace( '/((?:Phone:|Fax:|Web:)[ ]*)\n/is', '$1', trim( $row[1] ) );

			$url = false;
			$match = preg_match( '/\<a.*?href="(.*?)"/is', $row[0], $matches );

			// Get the website URL
			if ( $match ) {

				$url = trim( $matches[1] );

			}
			else {

				// Try grabbing it a different way
				$match = preg_match( '/Web:\s*?(.*?)\n/is', $row[1], $matches );
				
				if ( $match ) {

					$url = trim( $matches[1] );

				}

			}

			if ( $url ) {
				$url = preg_replace( '/^\p{Z}+|\p{Z}+$/u', '', $url );
			}

			$phone_number = false;
			$match = preg_match( '/Phone:\s*?(.*?)\n/is', $row[1], $matches );

			// Get the Phone Number
			if ( $match ) {

				// https://stackoverflow.com/a/10859208
				$phone_number = preg_replace( '/[a-z]*/i', '', trim( $matches[1] ) );
				$phone_number = preg_replace( '/^\p{Z}+|\p{Z}+$/u', '', $phone_number );

			}

			$fax_number = false;
			$match = preg_match( '/Fax:\s*?(.*?)\n/is', $row[1], $matches );

			// Get the Fax Number
			if ( $match ) {

				// https://stackoverflow.com/a/10859208
				$fax_number = preg_replace( '/[a-z]*/i', '', trim( $matches[1] ) );
				$fax_number = preg_replace( '/^\p{Z}+|\p{Z}+$/u', '', $fax_number );

			}

			// This gets a lot more complicated. We need to be sanitize this a lot
			$sanitized = preg_replace( '/[ ]{2,}/i', '', $row[1] );
			$sanitized = preg_replace( '/\t/i', '', trim( $sanitized ) );

			// Why do you do this to me, table maker
			// This removes a non-breaking space which made my matches fail
			$sanitized = preg_replace( '/\x{00A0}*\n/isu', "\n", $sanitized );

			if ( preg_match( '/\n\n/is', $sanitized ) ) {
				$sanitized_split = preg_split( '/\n\n/is', $sanitized );
			}
			else {
				$sanitized_split = preg_split( '/(?:Phone:|Fax:|Web:).*?\n/is', $sanitized );
			}

			$address_text = $sanitized_split[ count( $sanitized_split ) - 1 ];

			// Split into an Array
			$address_array = preg_split( '/\r\n|\r|\n/', $address_text );

			// Remove empty lines
			$address_array = array_filter( $address_array, function( $item ) {
				return ! empty( preg_replace( '/^\p{Z}+|\p{Z}+$/u', '', $item ) );
			} );

			foreach ( $address_array as $index => $line ) {

				if ( $index > 1 ) {

					$words = explode( ' ', trim( $line ) );

					if ( count( $words ) == 1 ) {

						if ( preg_match( '/[a-z]/i', $line ) ) {

							unset ( $address_array[ $index ] ); // This is just some single word, like putting the State on a separate line. Remove

						}

					}

				}

			}

			// Re-index
			$address_array = array_values( $address_array );

			// This is gross, but I want to be able to easily move the ZIP up if I have to
			foreach ( $address_array as $index => $line ) {

				if ( $index > 1 ) {

					$line = str_replace( ' - ', '-', $line );

					$words = explode( ' ', trim( $line ) );

					if ( count( $words ) == 1 ) {

						if ( ! preg_match( '/[a-z]/i', $line ) ) {

							$address_array[ $index - 1 ] .= ' ' . $line; // Bump the ZIP up one line
							unset( $address_array[ $index ] );

						}

					}
					else if ( preg_match( '/Canada/i', $line ) ) {

						$address_array[ $index - 1 ] = trim( $address_array[ $index - 1 ] );

						$address_array[ $index - 1 ] .= ', ' . $line; // Canadian ZIPs are weird
						unset( $address_array[ $index ] );

					}

				}

			}

			// Re-index
			$address_array = array_values( $address_array );

			// Some addresses have unnecessary things at the front, such as "University of such-and-such"
			// The actual address is the important part, Google will tell them that it is the University
			foreach ( $address_array as $index => $line ) {

				if ( ! preg_match( '/^\d/', $line ) && 
					! preg_match( '/^P\.?O/i', $line ) ) {

					// First character is not a number or a PO Box, this is not Address Line 1
					unset( $address_array[ $index ] );

				}
				else {
					// We found Address Line 1
					break;
				}

			}

			// Re-index
			$address_array = array_values( $address_array );

			// Some addresses have unnecessary things _after_ Address Line 2 
			// Further clarification on location, etc.
			// These will get bumped up into Address Line 2
			if ( count( $address_array ) > 2 ) {

				$backwards_index = 1;
				$address_array_count = count( $address_array );
				foreach ( $address_array as $index => $line ) {

					if ( $index <= 1 ) continue;

					if ( $index == ( $address_array_count - 1 ) ) break;

					$address_array[ $index - $backwards_index ] = trim( $address_array[ $index - $backwards_index ] );

					$address_array[ $index - $backwards_index ] .= ', ' . trim( $line ); // Bump the line up one one
					unset( $address_array[ $index ] );

					$backwards_index++;

				}

			}

			// Re-index
			$address_array = array_values( $address_array );

			// Save line 1
			update_post_meta( $post_id, 'suppliers_street_address_1', ( isset( $address_array[0] ) && $address_array[0] ) ? trim( $address_array[0] ) : '' ); 

			// Assume it is on index 1
			$city_state_zip_index = 1;

			// There's a second Street Address Line
			if ( count( $address_array ) > 2 ) {

				update_post_meta( $post_id, 'suppliers_street_address_2', trim( $address_array[1] ) );

				$city_state_zip_index = 2;

			}

			// Grab the City/State/ZIP line
			$city_state_zip = ( isset( $address_array[ $city_state_zip_index ] ) && $address_array[ $city_state_zip_index ] ) ? $address_array[ $city_state_zip_index ] : '';

			// Store here for later
			$zip = preg_replace( '/[^\d|-]/', '', $city_state_zip );

			// Remove ZIP since we already have that data and will be setting it later
			$city_state_zip = preg_replace( '/\s?[\d|-]*$/', '', $city_state_zip );

			$city_state_array = preg_split( '/\s?,\s?/', $city_state_zip );

			$city = ( isset( $city_state_array[0] ) && $city_state_array[0] ) ? trim( $city_state_array[0] ) : '';

			update_post_meta( $post_id, 'suppliers_city', $city );

			$state = ( isset( $city_state_array[1] ) && $city_state_array[1] ) ? $city_state_array[1] : '';
			$trimmed_state = trim( preg_replace( '/[^a-z]/i', '', $state ) );

			if ( $state ) {

				$all_states = teched_suppliers_get_state_list();

				// Check if we have the Key or the Value
				if ( array_key_exists( strtoupper( $trimmed_state ), $all_states ) ) {

					$trimmed_state = strtoupper( $trimmed_state );

				}
				elseif ( $key = array_search( $trimmed_state, $all_states ) ) {

					$trimmed_state = $key;

				}

				update_post_meta( $post_id, 'suppliers_state', $trimmed_state );

			}

			// This mainly is just for handling Canadian ZIPs
			if ( count( $city_state_array ) > 2 ) {

				$index = array_search( $city, $city_state_array );

				if ( $index !== false ) {
					unset( $city_state_array[ $index ] );
				}

				$index = array_search( $state, $city_state_array );

				if ( $index !== false ) {
					unset( $city_state_array[ $index ] );
				}

				$zip = implode( ' ', $city_state_array );

			}

			if ( $zip ) {
				update_post_meta( $post_id, 'suppliers_zip', trim( $zip ) );
			}

			if ( $phone_number ) {
				update_post_meta( $post_id, 'suppliers_phone', trim( $phone_number ) );
			}

			if ( $fax_number ) {
				update_post_meta( $post_id, 'suppliers_fax', trim( $fax_number ) );
			}

			if ( $url ) {
				update_post_meta( $post_id, 'suppliers_website_url', trim( $url ) );
			}

			$featured = array(
				'ABS Activity Based Supplies',
				'American Technical Publishers',
				'CNC Software Inc. (Mastercam)',
				'Computer Comfort, Inc',
				'Design Assistance Corporation',
				'Elenco Electronics Inc.',
				'Forest Scientific',
				'Snap Circuits',
				'TII - Technical Education Systems',
			);

			if ( in_array( $post_title, $featured ) ) {
				update_post_meta( $post_id, 'suppliers_featured', '1' );
			}
			else {
				update_post_meta( $post_id, 'suppliers_featured', '0' );
			}

		}
		
	}
	
}