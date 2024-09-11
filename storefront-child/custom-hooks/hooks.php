<?php 

/**
 * Cities Table
 *
 * @see  cities_table_header()
 */
add_action( 'cities_before_table', 'cities_table_header', 10 );


if ( ! function_exists( 'cities_table_header' ) ) {
	/**
	 * The cities header
	 */
	function cities_table_header() {
		echo '<div class="container-header">';
            echo '<h3>List of City</h3>';
            echo '<div class="search-bar">';
            echo '<input type="text" id="search-input" placeholder="Search City..." />';
            echo '<button id="search-button">Search</button>';
            echo '</div>';
        echo '</div>';
	}
}