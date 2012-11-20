<?php 
//This is the recurring dropdown table template page.
//This is a template file for displaying a table of recurring events on a page.
//There should be a copy of this file in your wp-content/uploads/espresso/templates/recurring-dropdown folder.
/*
* use the following shortcodes in a page or post:
* [RECURRING_DROPDOWN]
* [RECURRING_DROPDOWN limit=1]
* [RECURRING_DROPDOWN show_expired=true]
* [RECURRING_DROPDOWN show_deleted=true]
* [RECURRING_DROPDOWN show_secondary=true]
* [RECURRING_DROPDOWN show_recurrence=true]
* [RECURRING_DROPDOWN button_text="Select a date"]
* [RECURRING_DROPDOWN category_identifier=your_category_identifier]
*
* Example:
* [RECURRING_DROPDOWN button_text="View date selections" category_identifier=your_category_identifier]
*
*
*/
$first_event_instance = $events_group[0];
$first_event_excerpt = array_shift(explode('<!--more-->', html_entity_decode($first_event_instance['event_desc'])));

?>

<tr id="event_data-<?php echo $first_event_instance['event_id']?>" class="event_data subpage_excerpt r <?php echo $css_class; ?> <?php echo $category_identifier; ?> event-data-display event-list-display">
    
    <td id="event_title-<?php echo $first_event_instance['event_id']?>" class="event_title">
    
			<?php echo stripslashes_deep($first_event_instance['event_name'])?></td>
	
	 <td id="venue_title-<?php echo $first_event_instance['venue_title']?>" class="venue_title">
    
			<?php echo stripslashes_deep($first_event_instance['venue_title'])?></td>
	
	<td id="start_time-<?php echo $first_event_instance['start_time']?>" class="start_time">
    
			<?php echo stripslashes_deep($first_event_instance['start_time'])?></td>
			
	
	<td id="price-<?php echo $first_event_instance['price']?>" class="price">
    
			<?php echo $currency_symbol.stripslashes_deep($first_event_instance['price'])?></td>
			
			
	    <?php 
		//Group the recurring events
		if (count($events_group) > 1) : 
		?>
        
       <td>       
      
      <input type="button" value="<?php echo esc_html ( $button_text ) ?>" data-dropdown="#date_picker_<?php echo $first_event_instance['event_id']?>">
	      
       
       
     <div class="dropdown-menu has-tip has-scroll" id="date_picker_<?php echo $first_event_instance['event_id']?>"> 
       
            
            <ul>
        	    <?php foreach ($events_group as $e) :
                    $num_attendees = get_number_of_attendees_reg_limit($e['event_id'], 'num_attendees');//Get the number of attendees. Please visit http://eventespresso.com/forums/?p=247 for available parameters for the get_number_of_attendees_reg_limit() function.
                    echo '<li>';
                    if ($num_attendees >= $e['reg_limit']) : 
                        echo '<span class="error">';
                    else :
                        echo '<a href="'.$e['registration_url'].'">';
                    endif;
                    if ($e['start_date'] != $e['end_date']) : 
                        echo event_date_display($e['start_date'], get_option('date_format')).'–'.event_date_display($e['end_date'], get_option('date_format')); 
                    else : 
                        echo event_date_display($e['start_date'], get_option('date_format'));
                    endif;
                    if ($num_attendees >= $e['reg_limit']) : 
                        echo ' Sold Out</span> <a href="'.get_option('siteurl').'/?page_id='.$e['event_page_id'].'&e_reg=register&event_id='.$e['overflow_event_id'].'&name_of_event='.stripslashes_deep($e['event_name']).'">'.__('(Join Waiting List)').'</a>';
                    else :
                        echo '</a>';
                    endif;
                    echo '</li>';
                endforeach; ?>
            </ul>
              
       </div>
       
        </td>
    <?php else : ?>
        
        
        <?php $num_attendees = get_number_of_attendees_reg_limit($first_event_instance['event_id'], 'num_attendees'); ?>
        <?php if ($num_attendees >= $events_group[0]['reg_limit']) : ?>
            
          <td>  <p><span class="error">Sold Out</span> <a href="<?php echo get_option('siteurl')?>/?page_id=<?php echo $first_event_instance['event_page_id']?>&e_reg=register&event_id=<?php echo $first_event_instance['overflow_event_id']?>&name_of_event=<?php echo stripslashes_deep($first_event_instance['event_name'])?>" title="<?php echo stripslashes_deep($first_event_instance['event_name'])?>"><?php _e('Join Waiting List', 'event_espresso'); ?></a></p> <td>
            
        <?php else : ?>
            
          <td class="event-links">  <a href="<?php echo $first_event_instance['registration_url']; ?>" title="<?php echo stripslashes_deep($first_event_instance['event_name'])?>"><?php _e('Register', 'event_espresso'); ?></a><td>
        
        <?php endif; ?>
    <?php endif; ?>

</tr>