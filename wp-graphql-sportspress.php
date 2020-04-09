<?php
/**
 * Plugin Name: WPGraphQL for SportPress
 * Plugin URI: http://omukiguy.com
 * Author Name: Laurence bahiirwa
 * Author URI: https://omukiguy.com
 * Description: Expose the SportsPress sports Data to GraphQL Endpoint - Access sports data via domain.com/graphql
 * Version: 0.1.0
 * License: GPL2 or Later
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: sportspress-graphql
*/

// namespace omukiguy\sportspress_graphql;

add_action( 'graphql_register_types', function() {

	register_graphql_field( 'Event', 'matchday', [
	   'type' => 'String',
	   'description' => __( 'The color of the post', 'wp-graphql' ),
	   'resolve' => function( $post ) {
		 $color = get_post_meta( $post->ID, 'sp_day', true );
		 return ! empty( $color ) ? $color : 'blue';
	   }
	] );

	//Register connection
	register_graphql_connection(
		[
			'fromType' 		=> 'Event',
			'toType'		=> 'Club',
			'fromFieldName' => 'Clubs', //Name of the field - can be whatever
			'resolve'		=> function( $event, $args, $context, $info ){
									$connection = new \WPGraphQL\Data\Connection\PostObjectConnectionResolver( $event, $args, $context, $info, 'sp_team' );
									$clubs = get_post_meta( $event->ID, 'sp_team', false );
									// wp_send_json( $clubs );
									$connection->setQueryArg( 'post_parent', 0 );
									$connection->setQueryArg( 'post__in', $clubs );
									return $connection->get_connection();
			}
		]
	);

});


// sp_player, sp_event, sp_team, sp_staff

add_filter( 'register_taxonomy_args', function( $args, $taxonomy ) {

	if ( 'sp_position' === $taxonomy ) {
		$args['show_in_graphql'] = true;
		$args['graphql_single_name'] = 'PlayerPosition';
		$args['graphql_plural_name'] = 'PlayerPositions';
	}
	if ( 'sp_list' === $taxonomy ) {
		$args['show_in_graphql'] = true;
		$args['graphql_single_name'] = 'PlayerList';
		$args['graphql_plural_name'] = 'PlayerLists';
	}

	return $args;

}, 10, 2 );



add_filter( 'register_post_type_args', function( $args, $post_type ) {

	if ( 'sp_player' === $post_type ) {
		$args['show_in_graphql'] = true;
		$args['description'] = 'SportsPress Exposed Player Custom Post Type';
		$args['graphql_single_name'] = 'Player';
		$args['graphql_plural_name'] = 'Players';
    }
    
	if ( 'sp_event' === $post_type ) {
		$args['show_in_graphql'] = true;
		$args['graphql_single_name'] = 'Event';
		$args['graphql_plural_name'] = 'Events';
    }
    
	if ( 'sp_team' === $post_type ) {
		$args['show_in_graphql'] = true;
		$args['graphql_single_name'] = 'Club';
		$args['graphql_plural_name'] = 'Clubs';
	}
    
	if ( 'sp_staff' === $post_type ) {
		$args['show_in_graphql'] = true;
		$args['graphql_single_name'] = 'Staff';
		$args['graphql_plural_name'] = 'StaffPersons';
	}

	return $args;

}, 10, 2 );
