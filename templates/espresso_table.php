<?php
/*
Shortcode Name: Espresso Table
Author: Seth Shoultes
Contact: seth@eventespresso.com
Website: http://www.eventespresso.com
Description: Only show events in a CATEGORY within a certain number number of days into the future and a qty. The example below only shows events in a certain category that start within 30 days from the current date.
Usage Example: [ESPRESSO_TABLE max_days="30" qty="3" category_identifier="gracecard" order_by="state"]
Custom CSS for the table display
Notes: This file should be stored in your "/wp-content/uploads/espresso/templates/" folder and you should have the custom_includes.php files installed in your "/wp-content/uploads/espresso/" directory.
*/

/**
 * Espresso Table CSS
 * @author Chris Reynolds
 * adds some minor styles to the tables. To override, just comment out the line that begins with add_action
 */
function espresso_table_css() {
  /* add some css for styling */
  ?>
    <style type="text/css">
      .espresso-table-row .event-name {
        font-weight: bold;
      }
      .espresso-table-row .address {
        font-size: 0.8em;
      }
      .espresso-table-row .status span {
        padding: 2px 5px;
        border-radius: 4px;
        font-size: 0.8em;
        border: 1px solid;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
      }
      .espresso-table-row .status .full {
        color: #b94a48;
        background-color: #f2dede;
        border-color: #eed3d7;
      }
      .espresso-table-row .status .available {
        color: #468847;
        background-color: #dff0d8;
        border-color: #d6e9c6;
      }
    </style>
  <?php
}
add_action('wp_head','espresso_table_css');

function espresso_display_table($atts){
 	global $wpdb;
	$org_options = get_option('events_organization_settings');
	$event_page_id =$org_options['event_page_id'];
  $type = '';

	global $load_espresso_scripts;
		$load_espresso_scripts = true;//This tells the plugin to load the required scripts
    extract(shortcode_atts(array('event_category_id'=>'NULL','category_identifier' => 'NULL','show_expired' => 'false', 'show_secondary'=>'false','show_deleted'=>'false','show_recurrence'=>'true', 'limit' => '0', 'order_by' => 'NULL', 'max_days'=>''),$atts));

    if ( !empty($category_identifier) ){
      $type = 'category';
    }
    if ( !empty($event_category_id) ){
		  $type = 'category';
      $category_identifier = $event_category_id;
		}

		$show_expired = $show_expired == 'false' ? " AND e.start_date >= '".date ( 'Y-m-d' )."' " : '';
		$show_secondary = $show_secondary == 'false' ? " AND e.event_status != 'S' " : '';
		$show_deleted = $show_deleted == 'false' ? " AND e.event_status != 'D' " : '';
		$show_recurrence = $show_recurrence == 'false' ? " AND e.recurrence_id = '0' " : '';
		$limit = $limit > 0 ? " LIMIT 0," . $limit . " " : '';
		$order_by = $order_by != 'NULL'? " ORDER BY ". $order_by ." ASC " : " ORDER BY date(start_date), id ASC ";

		if ($type == 'category'){
			$sql = "SELECT e.* FROM " . EVENTS_CATEGORY_TABLE . " c ";
			$sql .= " JOIN " . EVENTS_CATEGORY_REL_TABLE . " r ON r.cat_id = c.id ";
			$sql .= " JOIN " . EVENTS_DETAIL_TABLE . " e ON e.id = r.event_id ";
			$sql .= " WHERE c.category_identifier = '" . $category_identifier . "' ";
			$sql .= " AND e.is_active = 'Y' ";
		}else{
			$sql = "SELECT e.* FROM " . EVENTS_DETAIL_TABLE . " e ";
			$sql .= " WHERE e.is_active = 'Y' ";
		}
		if ($max_days != ""){
				$sql  .= " AND ADDDATE('".date ( 'Y-m-d' )."', INTERVAL ".$max_days." DAY) >= e.start_date AND e.start_date >= '".date ( 'Y-m-d' )."' ";
		}
		$sql .= $show_expired;
		$sql .= $show_secondary;
		$sql .= $show_deleted;
		$sql .= $show_recurrence;
		$sql .= $order_by;
		$sql .= $limit;

		echo espresso_get_table($sql);

}

/**
 * get slug
 * @author Chris Reynolds
 * @link http://www.wprecipes.com/wordpress-function-to-get-postpage-slug
 * gets the slug of the event registration page. To disable comment out the $ee_base_reg_url line and uncomment the $ee_base_reg_url line that is commented out now
 */
function espresso_get_ee_slug() {
  $org_options = get_option('events_organization_settings');
  $event_page_id =$org_options['event_page_id'];
  $post_data = get_post( $event_page_id, ARRAY_A );
  $slug = $post_data['post_name'];
  return $slug;
}

//Events Custom Table Listing - Shows the events on your page in matching table.
function espresso_get_table($sql){
   global $wpdb, $org_options;
  //echo 'This page is located in ' . get_option( 'upload_path' );
  $event_page_id = $org_options['event_page_id'];
  $currency_symbol = $org_options['currency_symbol'];
  $events = $wpdb->get_results($sql);

  $category_name = !empty($wpdb->last_result[0]->category_name) ?  $wpdb->last_result[0]->category_name : '';
  $category_desc = !empty($wpdb->last_result[0]->category_desc) ?  $wpdb->last_result[0]->category_desc : '';
  $display_desc = !empty($wpdb->last_result[0]->display_desc) ? $wpdb->last_result[0]->display_desc : '';
  if ($display_desc == 'Y'){
    echo '<p>' . stripslashes_deep($category_name) . '</p>';
    echo '<p>' . stripslashes_deep($category_desc) . '</p>';
  }
?>
<table class="espresso-table" width="100%">

      <thead class="espresso-table-header-row">
      <tr>
          <?php /* this is the table header row. any of these lines can be commented out or replaced with your own column headers */ ?>
          <?php /* Event name */ ?>
          <th class="th-group"><?php _e('Class','event_espresso'); ?></th>
          <?php /* Venue */ ?>
          <th class="th-group"><?php _e('Location','event_espresso'); ?></th>
          <?php /* Venue address */ ?>
          <th class="th-group"><?php _e( 'Address', 'event_espresso' ); ?></th>
          <?php /* Date and time */ ?>
          <th class="th-group"><?php _e('Date / Time','event_espresso'); ?></th>
          <?php /* Cost */ ?>
          <th class="th-group"><?php _e('Cost','event_espresso'); ?></th>
          <?php /* event status (available or full) */ ?>
          <th class="th-group"><?php _e('Status','event_espresso'); ?></th>
          <?php /* staff */ ?>
          <!-- <th class="th-group"><?php _e( 'Staff', 'event_espresso' ); ?></th> -->
          <?php /* register link */ ?>
          <th class="th-group"><?php _e('Register','event_espresso'); ?></th>
     </tr>
      </thead>
	<tbody>

      <?php

      foreach ($events as $event){
	      $reg_limit = $event->reg_limit;
        $break = '<br>';
        $event_desc = wpautop($event->event_desc);
        $cost = do_shortcode('[EVENT_PRICE event_id="'.$event->id.'" number="0"]');
        $open_spots = do_shortcode('[ATTENDEE_NUMBERS event_id="' . $event->id . '" type="available_spaces"]'); //Check to see how many open spots are available
        $staff = do_shortcode('[ESPRESSO_STAFF event_id="'.$event->id.'" show_image="false" show_staff_titles="false" show_staff_details="false" show_description="false"]');
        $cart_link = do_shortcode('[ESPRESSO_CART_LINK event_id="'.$event->id.'" anchor="' . __( 'Add to cart', 'event_espresso' ) . '"]')
        // uncomment this line if your site is not using pretty permalinks
        //$ee_base_reg_url = home_url() . '/?page_id=' . $event_page_id;
        // comment out this line if your site is not using pretty permalinks
        $ee_base_reg_url = home_url() . '/' . espresso_get_ee_slug();
        $register_url = $ee_base_reg_url . '/?ee=' . $event->id;

        // check if the venue manager is being used
        if ( $org_options['use_venue_manager'] == 'Y' ) {
          $venue = do_shortcode( '[ESPRESSO_VENUE event_id="'.$event->id.'" show_image="false" show_description="false" show_address="false" show_additional_details="false" show_google_map_link="false" show_map_image="false" title_wrapper="span"]' );
          $address = do_shortcode( '[ESPRESSO_VENUE event_id="' . $event->id . '" show_image="false" show_description="false" show_title="false" show_address="true" show_additional_details="false" show_google_map_link="false" show_map_image="false"]' );
        } else {
          $venue = $event->city;
          $address = $event->address . $break . $event->city . $break . $event->state;
        }

        if ( $open_spots < 1 && $open_spots != 'Unlimited' ) {
          $status = '<span class="full">' . __('Full', 'event_espresso') . '</span>';
          if ( $event->overflow_event_id ) {
            $overflow_event_id = $event->overflow_event_id;
          }
          $register_button = '<a class="a_register_link ui-button ui-button-big ui-priority-primary ui-state-default ui-state-hover ui-state-focus ui-corner-all" id="a_register_link-' . $overflow_event_id . '" href="' . $ee_base_reg_url . '/?ee=' . $overflow_event_id . '" title="' . stripslashes_deep($event->event_name) . '">' . __('Join Waiting List', 'event_espresso') . '</a>';
        } else {
          $status = '<span class="available">' . __('Available', 'event_espresso') . '</span>';
          $register_button = '<a id="a_register_link-'.$event->id.'" href="'.$register_url.'">' . __('Register', 'event_espresso') . '</a>';
          // if you want to use the cart link here, use the following line and comment out the line above
          // $register_button = $cart_link;
        } ?>

      <tr class="espresso-table-row">
       	<td class="td-group event-name">
            <?php echo $event->event_name ?>
          </td>
          <td class="td-group venue">
            <?php echo $venue ?>
          </td>
          <td class="td-group address">
            <?php echo $address; ?>
          </td>
      	  <td class="td-group date-time">
            <?php $date = date( get_option('date_format'), strtotime($event->start_date) ); ?>
            <?php echo espresso_event_time($event->id, 'start_time', get_option('time_format')) . $break . $date; ?>
          </td>
          <td class="td-group cost">
            <?php echo $currency_symbol . $cost; ?>
          </td>
          <td class="td-group status">
            <?php echo $status; ?>
          </td>
          <!--<td class="td-group staff">
            <?php echo $status; ?>
          </td>-->
          <td class="td-group register">
            <?php echo $register_button ?>
          </td>

      </tr>
      <?php } //close foreach ?>
</tbody>
</table>

<?php
}