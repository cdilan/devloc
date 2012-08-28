<?php

require_once(dirname(__FILE__) . '/widgets.php');

class LanobaSocialWidget extends WP_Widget
{

    public function __construct()
    {
        parent::WP_Widget('lanoba_social', 'Lanoba Social', array(
            'description' => __('Sign in and register users through social networks like Facebook and Twitter, among others.', 'lanoba_social')
        ));
    }

    //The actual widget

    public function widget($args, $instance)
    {
        if ( !is_user_logged_in() || empty($instance ['hideWidgetIfUserLoggedIn']))
        {
            //before the widget
            echo $args ['before_widget'];
            
            //Widget Title
            if ( !empty($instance ['lbWidgetTitle']) )
            {
               echo $args ['before_title'] . apply_filters('lbWidgetTitle', $instance ['lbWidgetTitle']) . $args ['after_title'];
            }

            //Display the login widget 
            lb_LoginWidget();
            
            //After the widget
            echo $args ['after_widget'];
        }
    }

    // Widget Settings

    public function form($instance)
    {
        //Default settings
        $default_settings = array( 'lbWidgetTitle' => __('Sign in using:', 'lanoba_social'), 'hideWidgetIfUserLoggedIn' => '1' );

        foreach ( $instance as $key => $value )
        {
            $instance [$key] = esc_attr($value);
        }

        $instance = wp_parse_args((array) $instance, $default_settings);
        
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('lbWidgetTitle'); ?>"><?php _e('Title', 'lanoba_social'); ?>:</label>
            <input id="<?php echo $this->get_field_id('lbWidgetTitle'); ?>" name="<?php echo $this->get_field_name('lbWidgetTitle'); ?>" type="text" value="<?php echo $instance ['lbWidgetTitle']; ?>" />
        </p>			
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('hideWidgetIfUserLoggedIn', 'lanoba_social'); ?>" name="<?php echo $this->get_field_name('hideWidgetIfUserLoggedIn'); ?>" value="1" <?php echo (!empty($instance ['hideWidgetIfUserLoggedIn']) ? 'checked="checked"' : ''); ?> />
            <label for="<?php echo $this->get_field_id('hideWidgetIfUserLoggedIn'); ?>"><?php _e('Hide widget when user is already logged in', 'hideWidgetIfUserLoggedIn'); ?></label>
        </p>

        <?php
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance ['lbWidgetTitle'] = trim(strip_tags($new_instance ['lbWidgetTitle']));
        $instance ['hideWidgetIfUserLoggedIn'] = (empty($new_instance ['hideWidgetIfUserLoggedIn']) ? 0 : 1);
        return $instance;
    }

}