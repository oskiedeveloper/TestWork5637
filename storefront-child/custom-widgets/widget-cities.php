<?php

class Cities_Widget extends WP_Widget {

    // Constructor
    public function __construct() {
        parent::__construct(
            'cities_widget', // Base ID
            __( 'City Widget', 'storefront-child' ), // Name
            array( 'description' => __( 'Displays City', 'storefront-child' ) ) // Args
        );
    }

    // Fetch Weather Data from OpenWeather API
    public function get_weather_data( $lat, $lon, $api_key ) {
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

    // Widget Output on the Frontend
    public function widget( $args, $instance ) {
        // Get selected post ID from the widget settings
        $selected_post = ! empty( $instance['selected_post'] ) ? $instance['selected_post'] : '';

        echo $args['before_widget'];


        if ( $selected_post ) {  
            $post = get_post( $selected_post );
            if( $post ){
                $api_key = 'ced07a41a942bffd5775529590b711b6';
                $lat = get_post_meta( $post->ID, '_city_latitude', true );
                $lon = get_post_meta( $post->ID, '_city_longitude', true ); 
                $weather_data = $this->get_weather_data( $lat, $lon, $api_key );

                $featured_image_url = get_the_post_thumbnail_url( $post->ID );
                $content = apply_filters( 'the_content', $post->post_content );
                $excerpt = get_the_excerpt($post->ID);

                ?>
                <div class="widget-city">
                    <div class="city-wrap">
                        <div class="city-banner" style="background-image:url('<?php echo esc_url( $featured_image_url ); ?>');"></div>
                        <div class="city-info">
                            <h3><?php echo esc_html( get_the_title( $selected_post ) ); ?></h3>
                            <p class="text"><?php echo esc_html( $excerpt ); ?></p>
                            <p><strong>Latitude:</strong> <?php echo $lat; ?></p>
                            <p><strong>Longitude:</strong> <?php echo $lon; ?></p>
                            <p><strong>Temperature:</strong> <?php echo esc_html( $weather_data->main->temp ); ?></p>
                        </div>
                    </div>
                </div>
                <?php
            } else {
                echo '<p>Post not found!</p>';
            }
        }

        echo $args['after_widget'];
    }

    // Backend Form for Widget Settings
    public function form( $instance ) {
        $selected_post = ! empty( $instance['selected_post'] ) ? $instance['selected_post'] : '';

        $post_type = 'cities';

        $query_args = array(
            'post_type'      => $post_type,
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        );
        $custom_posts = get_posts( $query_args );

        if ( ! empty( $custom_posts ) ) {
            echo '<select id="'. esc_attr( $this->get_field_id( 'selected_post' ) ) .'" name="' . esc_attr( $this->get_field_name( 'selected_post' ) ) . '">';
            echo '<option value="0">Select a city</option>'; 
            foreach ( $custom_posts as $post ) {
                echo '<option value="' . esc_attr( $post->ID ) . '"' . selected( $selected_post, $post->ID ) . '>' . esc_html( get_the_title( $post->ID ) ) . '</option>';
            }
            echo '</select>';
        }
    }

    // Save Widget Settings
    public function update( $new_instance, $old_instance ) {
        $instance = array();    
        $instance['selected_post'] = sanitize_text_field( $new_instance['selected_post'] );
        return $instance;
    }
    
}

// Register the widget
function register_cities_widget() {
    register_widget( 'Cities_Widget' );
}
add_action( 'widgets_init', 'register_cities_widget' );
