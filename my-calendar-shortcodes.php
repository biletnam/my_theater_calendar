<?php


function my_calendar_insert($atts) {
	extract(shortcode_atts(array(
				'name' => 'all',
				'format' => 'calendar',
				'category' => 'all',
				'showkey' => 'yes',
				'shownav' => 'yes',
				'toggle' => 'no',
				'time' => 'month'
			), $atts));
	if ( isset($_GET['format']) ) {
		$format = mysql_real_escape_string($_GET['format']);
	}	
	return my_calendar($name,$format,$category,$showkey,$shownav,$toggle,$time);
}

function my_calendar_insert_upcoming($atts) {
	extract(shortcode_atts(array(
				'before' => 'default',
				'after' => 'default',
				'type' => 'default',
				'category' => 'default',
				'template' => 'default',
				'fallback' => '',
				'order' => 'asc'
			), $atts));
	return my_calendar_upcoming_events($before, $after, $type, $category, $template, $fallback, $order);
}

function my_calendar_insert_today($atts) {
	extract(shortcode_atts(array(
				'category' => 'default',
				'template' => 'default',
				'fallback' => '',
			), $atts));
	return my_calendar_todays_events($category, $template, $fallback);
}

function my_calendar_locations($atts) {
	extract(shortcode_atts(array(
				'show' => 'list',
				'type' => 'saved',
				'datatype' => 'name'
			), $atts));
	return my_calendar_locations_list($show,$type,$datatype);
}

function my_calendar_categories($atts) {
	extract(shortcode_atts(array(
				'show' => 'list'
			), $atts));
	return my_calendar_categories_list( $show );
}


?>