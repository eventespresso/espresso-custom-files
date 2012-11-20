Installation instructions:

Use the following shortcode in a page or post:
 [RECURRING_DROPDOWN]
 [RECURRING_DROPDOWN limit=1]
 [RECURRING_DROPDOWN show_expired=true] (default=false)
 [RECURRING_DROPDOWN show_deleted=true] (default=false)
 [RECURRING_DROPDOWN show_secondary=true] (waitlist events default=false)
 [RECURRING_DROPDOWN show_recurrence=true] (default=true)
 [RECURRING_DROPDOWN button_text="Select a date"] (default="Select a Date")
 [RECURRING_DROPDOWN category_identifier=your_category_identifier]
 [RECURRING_DROPDOWN staff_id=1] (default=NULL, allows you to filter event list by assigned staff)

 Example:
 [RECURRING_DROPDOWN button_text="View date selections" category_identifier=your_category_identifier]

* Changing the date format

The date display format uses the date format selected in WordPress>Settings>General

* Customizing the CSS

You can remove the included stylesheet by adding this function to your theme's stylesheet:

<?php
add_action( 'wp_footer', 'espresso_remove_recurring_dropdown_stylesheet' );
function espresso_remove_recurring_dropdown_stylesheet() {
wp_dequeue_style( 'espresso_recurring_dropdown_stylesheet' );
}
?>

Then you can copy and paste into then modify the styles in your theme's stylesheet.