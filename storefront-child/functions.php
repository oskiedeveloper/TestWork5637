<?php 
	add_action( 'wp_enqueue_scripts', 'storefront_child_enqueue_styles' );
	function storefront_child_enqueue_styles() {
 		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' ); 
		wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), null, true );

		// Pass the AJAX URL to the script
		wp_localize_script( 'custom-js', 'ajax_object', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ) // AJAX URL for WordPress
		));
 	}
	
	// Include custom post types
	foreach (glob(get_stylesheet_directory() . '/custom-post-types/*.php') as $filename) {
		require_once $filename;
	}
	// Include custom widgets
	foreach (glob(get_stylesheet_directory() . '/custom-widgets/*.php') as $filename) {
		require_once $filename;
	}
	// Include custom hooks
	foreach (glob(get_stylesheet_directory() . '/custom-hooks/*.php') as $filename) {
		require_once $filename;
	}

	add_action( 'wp_loaded', 'remove_theme_hooks_on_load' );
	function remove_theme_hooks_on_load() {
		// Remove a theme hook after everything is loaded
		remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10 );
	}



	// Fetch Weather Data from OpenWeather API
	function get_weather_data( $lat, $lon, $api_key ) {
		$api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric";

		// Send the HTTP request to the API
		$response = wp_remote_get( $api_url );
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );

		if ( isset( $data->cod ) && $data->cod == 200 ) {
			return $data;
		} else {
			return false;
		}
	}

	//Search City function
	function ajax_search_function() {
	
		// Get the search term from the AJAX request
		$search_term = isset( $_POST['search'] ) ? sanitize_text_field( $_POST['search'] ) : '';

		$args = array(
			'post_type' => 'cities', 
			'post_status' => 'publish',
			's' => $search_term, 
			'posts_per_page' => -1
		);

		$query = new WP_Query( $args );
		if ( $query->have_posts() ) {
			$output = '';
			while ( $query->have_posts() ) {
				$query->the_post();
				$cityName = esc_html( get_the_title() );
				$country = get_the_terms( get_the_ID(), 'countries' );
				if ( $country && ! is_wp_error( $country ) ) {
					$country_name = wp_list_pluck( $country, 'name' );
					$country_name = implode( ', ', $country_name );
				}

				$cityLat = get_post_meta( get_the_ID(), '_city_latitude', true );
				$cityLon = get_post_meta( get_the_ID(), '_city_longitude', true );

				$api_key = 'ced07a41a942bffd5775529590b711b6';
				$weather_data = get_weather_data( $cityLat, $cityLon, $api_key );
				$cityTemp = esc_html( $weather_data->main->temp );
				?>
				<div class="table-row">
					<div class="table-data"><?php echo $cityName; ?></div>
					<div class="table-data"><?php echo $country_name; ?></div>
					<div class="table-data"><?php echo $cityTemp; ?></div>
					<div class="table-data"><?php echo $cityLat; ?></div>
					<div class="table-data"><?php echo $cityLon; ?></div>
				</div>
			<?php
			}
		} else {
			$output = '<tr><td colspan="3">No results found</td></tr>';
		}

		// Return the results to the AJAX call
		echo $output;
		wp_die();

	}

	add_action( 'wp_ajax_ajax_search', 'ajax_search_function' );
	add_action( 'wp_ajax_nopriv_ajax_search', 'ajax_search_function' );

 ?>