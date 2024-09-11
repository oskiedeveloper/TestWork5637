<?php
/*
*  Template name: Cities template
*/

get_header(); ?>

    <div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">  

			<?php
			while ( have_posts() ) :
				the_post();

                global $wpdb;

                $custom_post_type = 'cities';
                $meta_key_1 = '_city_latitude'; 
                $meta_key_2 = '_city_longitude';
                $taxonomy = 'countries';

                $results = $wpdb->get_results( $wpdb->prepare(
                    "SELECT p.ID, p.post_title, t.name AS term_name, pm1.meta_value AS field_1_value, pm2.meta_value AS field_2_value
                    FROM {$wpdb->posts} AS p
                    LEFT JOIN {$wpdb->postmeta} AS pm1 ON p.ID = pm1.post_id AND pm1.meta_key = %s
                    LEFT JOIN {$wpdb->postmeta} AS pm2 ON p.ID = pm2.post_id AND pm2.meta_key = %s
                    LEFT JOIN {$wpdb->term_relationships} AS tr ON p.ID = tr.object_id
                    LEFT JOIN {$wpdb->term_taxonomy} AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
                    LEFT JOIN {$wpdb->terms} AS t ON tt.term_id = t.term_id
                    WHERE p.post_type = %s
                    AND p.post_status = 'publish'
                    ORDER BY p.post_title ASC",
                    $meta_key_1, $meta_key_2, $custom_post_type, $taxonomy
                ) );

                if ( !empty( $results ) ) { ?>
                    <div class="container-wrap">
                        
                        <?php do_action( 'cities_before_table' ); ?>

                        <div class="table">
                            <div class="table-header">  
                                <div class="header__item"><a id="name" class="filter__link" href="#">City</a></div>
                                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">Country</a></div>
                                <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">Temperature</a></div>
                                <div class="header__item"><a id="losses" class="filter__link filter__link--number" href="#">Latitude</a></div>
                                <div class="header__item"><a id="total" class="filter__link filter__link--number" href="#">Longitude</a></div>
                            </div>
                            <div id="city-table" class="table-content">
                                <?php 
                                foreach ( $results as $post ) { 
                                    $cityName = esc_html( $post->post_title );
                                    $country= esc_html( $post->term_name );
                                    $cityLat = esc_html( $post->field_1_value );
                                    $cityLon = esc_html( $post->field_2_value );

                                    $api_key = 'ced07a41a942bffd5775529590b711b6';
                                    $weather_data = get_weather_data( $cityLat, $cityLon, $api_key );
                                    $cityTemp = esc_html( $weather_data->main->temp );

                                ?>  
                                    <div class="table-row">
                                        <div class="table-data"><?php echo $cityName; ?></div>
                                        <div class="table-data"><?php echo $country; ?></div>
                                        <div class="table-data"><?php echo $cityTemp; ?></div>
                                        <div class="table-data"><?php echo $cityLat; ?></div>
                                        <div class="table-data"><?php echo $cityLon; ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <?php do_action( 'cities_after_table' ); ?>
                        
                    </div>
                <?php
                } else {
                    echo 'No city found.';
                }

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
