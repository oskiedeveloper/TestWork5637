<?php
function cpt_register_cities() {
    $labels = array(
        'name'               => 'Cities',
        'singular_name'      => 'City',
        'menu_name'          => 'Cities',
        'name_admin_bar'     => 'City',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New City',
        'new_item'           => 'New City',
        'edit_item'          => 'Edit City',
        'view_item'          => 'View City',
        'all_items'          => 'All Cities',
        'search_items'       => 'Search Cities',
        'parent_item_colon'  => 'Parent Cities:',
        'not_found'          => 'No cities found.',
        'not_found_in_trash' => 'No cities found in Trash.',
    );

    $args = array(
        'labels'             => $labels,
        'description'        => 'A custom post type for Cities',
        'public'             => true,
        'menu_icon'          => 'dashicons-location-alt',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'has_archive'        => false,
        'rewrite'            => array('slug' => 'city'),
        'show_in_rest'       => false, // For Gutenberg support
    );

    register_post_type('cities', $args);
}
add_action('init', 'cpt_register_cities');



//CUSTOM FIELDS FOR CITIES

function cities_add_meta_box() {
    add_meta_box(
        'cities_meta_box',
        'City Coordinates',
        'cities_meta_box_html',
        'cities', 
        'normal', // Context (side, normal, advanced)
        'default' // Priority
    );
}
add_action( 'add_meta_boxes', 'cities_add_meta_box' );


function cities_meta_box_html( $post ) {
    // Use nonce for security
    wp_nonce_field( 'cities_save_meta_box_data', 'cities_meta_box_nonce' );

    // Retrieve current values from the database
    $city_latitude = get_post_meta( $post->ID, '_city_latitude', true );
    $city_longitude = get_post_meta( $post->ID, '_city_longitude', true );

    // Custom Field Inputs
    ?>
    <p>
        <label for="city_latitude">Latitude :</label>
        <input type="text" id="city_latitude" name="city_latitude" value="<?php echo esc_attr( $city_latitude ); ?>" size="50" />
    </p>
    <p>
        <label for="city_longitude">Longitude :</label>
        <input type="text" id="city_longitude" name="city_longitude" value="<?php echo esc_attr( $city_longitude ); ?>" size="50" />
    </p>
    <?php
}


function cities_save_meta_box_data( $post_id ) {
    // Check if nonce is set
    if ( ! isset( $_POST['cities_meta_box_nonce'] ) ) {
        return;
    }

    // Verify the nonce
    if ( ! wp_verify_nonce( $_POST['cities_meta_box_nonce'], 'cities_save_meta_box_data' ) ) {
        return;
    }

    // If this is an autosave, don’t do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions
    if ( isset( $_POST['post_type'] ) && 'cities' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }   

    // Sanitize and save custom fields
    if ( isset( $_POST['city_latitude'] ) ) {
        $city_latitude = sanitize_text_field( $_POST['city_latitude'] );
        update_post_meta( $post_id, '_city_latitude', $city_latitude );
    }
    if ( isset( $_POST['city_longitude'] ) ) {
        $city_longitude = sanitize_text_field( $_POST['city_longitude'] );
        update_post_meta( $post_id, '_city_longitude', $city_longitude );
    }

}
add_action( 'save_post', 'cities_save_meta_box_data' );


//CUSTOM TAXONOMY FOR CITIES

function register_countries_taxonomy() {
    $labels = array(
        'name'              => _x( 'Countries', 'taxonomy general name', 'storefront-child' ),
        'singular_name'     => _x( 'Country', 'taxonomy singular name', 'storefront-child' ),
        'search_items'      => __( 'Search Countries', 'storefront-child' ),
        'all_items'         => __( 'All Countries', 'storefront-child' ),
        'parent_item'       => __( 'Parent Country', 'storefront-child' ),
        'parent_item_colon' => __( 'Parent Country:', 'storefront-child' ),
        'edit_item'         => __( 'Edit Country', 'storefront-child' ),
        'update_item'       => __( 'Update Country', 'storefront-child' ),
        'add_new_item'      => __( 'Add New Country', 'storefront-child' ),
        'new_item_name'     => __( 'New Country Name', 'storefront-child' ),
        'menu_name'         => __( 'Countries', 'storefront-child' ),
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => false, // Set to false for non-hierarchical (like tags)
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => false, // Enable Gutenberg editor support
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'countries' ),
    );

    // Register the taxonomy and attach it to the 'book' post type
    register_taxonomy( 'countries', array( 'cities' ), $args );
}
add_action( 'init', 'register_countries_taxonomy' );
