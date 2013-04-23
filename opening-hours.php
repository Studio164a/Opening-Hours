<?php
/*
Plugin Name: Opening Hours
Plugin URI: 
Description: A simple image plugin to enable you to add your opening hours as a widget or shortcode.
Author: Studio164a
Version: 0.1
Author URI: http://164a.com
*/

class OSFA_Opening_Hours {

    /**
     * OSFA_Opening_Hours instance
	 *
     * @static
     * @access private
     * @var OSFA_Opening_Hours|null
     */
    private static $instance = null;

    /**
     * Plugin settings
     *
	 * @static
	 * @access private
	 * @var array
	 */
    private static $settings = null;
	
    /**
     * Create object. OSFA_Flexslider instance should be retrieved through OSFA_Flexslider::get_instance() method.
     *
     * @access private
     */
    private function __construct() {
        // echo memory_get_usage();
        // die;
    	require_once('widget.php');

    	// Set up multi-lingualism
    	load_plugin_textdomain( 'osfa_opening_hours', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    	add_action( 'admin_menu', array( &$this, 'admin_menu' ));
    	add_action( 'admin_init', array( &$this, 'admin_init' ));
    	add_action( 'widgets_init', array( &$this, 'widgets_init' ));
    	add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_scripts') );
    }

    /**
     * Retrieve object instance
     *
     * @return OSFA_Flexslider
     */
    public static function get_instance() {
        if ( is_null(self::$instance) ) {
            self::$instance = new OSFA_Opening_Hours();
        }
        return self::$instance;
    }   

    /** 
     * Admin menu hook
     * 
     * @return void
     */
    public function admin_menu() {
        add_options_page( 
            __( 'Opening Hours', 'osfa_opening_hours' ), 
            __( 'Opening Hours', 'osfa_opening_hours' ),
            'activate_plugins',
            'osfa-opening-hours',
            array( 'OSFA_Opening_Hours', 'options_page' ) 
        );
    }

    /**
     * Admin init hook
     * 
     * @return void
     */
    public function admin_init() {
        add_settings_section('osfa_opening_hours_main',
            __( 'Opening hours', 'osfa_opening_hours' ),
            create_function('', 'return;'),
            'osfa-opening-hours');
        
        add_settings_field('osfa_opening_hours_hours',
            __( 'Hours by day', 'osfa_opening_hours' ),
            array( 'OSFA_Opening_Hours', 'hours_setting' ),
            'osfa-opening-hours',
            'osfa_opening_hours_main');
        
        add_settings_field('osfa_opening_hours_comment', 
            'Extra comment', 
            array( 'OSFA_Opening_Hours', 'extra_comment_setting' ), 
            'osfa-opening-hours', 
            'osfa_opening_hours_main' );

        // Register our setting so that $_POST handling is done for us and
        // our callback function just has to echo the <input>
        register_setting('osfa-opening-hours', 'osfa_opening_hours');           
    }  

    /**
     * Run on widgets_init hook
     * 
     * @return void
     */
    public function widgets_init() {
        register_widget( 'OSFA_Opening_Hours_Widget' );
    }

    /** 
     * Enqueue scripts & stylesheets
     * 
     * @return void
     */
    public function wp_enqueue_scripts() {
        wp_register_style( 'osfa_opening_hours', plugins_url( 'style.css', __FILE__ ));
        wp_enqueue_style( 'osfa_opening_hours' );
    }

    /**
     * Get value for setting with key
     * 
     * @return mixed
	 */
    public static function get_plugin_setting($key) {
    	if ( is_null( self::$settings ) ) {
    		self::$settings = get_option('osfa_opening_hours');
    	}

    	return is_array(self::$settings) && array_key_exists($key, self::$settings) ? self::$settings[$key] : false;
    }

    /**
     * Options page
     * 
     * @return void
     */
    public function options_page() {
    	?>

    	<div class="wrap">

            <div id="icon-settings" class="icon32"><br /></div>   
            <h2><?php _e( 'Opening Hours', 'osfa_opening_hours' ) ?></h2>         

            <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
                <div class='updated'><p><?php _e( 'Settings updated successfully.', 'osfa_opening_hours' ) ?></p></div>
            <?php endif ?>

            <!-- Start form -->
            <form action="options.php" method="post">

                <?php settings_fields( 'osfa-opening-hours' ) ?>
                <?php do_settings_sections( 'osfa-opening-hours' ) ?>

                <p class="submit">
                    <button name="osfa_opening_hours[do-action]" value="submit" class="button-primary"><?php esc_attr_e( 'Save Settings', 'osfa_opening_hours' ) ?></button>
                    <button name="osfa_opening_hours[do-action]" value="reset" class="button-secondary"><?php esc_attr_e( 'Reset', 'osfa_opening_hours' ) ?></button>
                </p>

            </form>
            <!-- End form -->

        </div>

    	<?php
    }

    /**
     * Following area functions that make up the individual settings on the options page.
     * 
     * @return void 
     */
    public function hours_setting() {
    	$values = self::get_plugin_setting('hours');

    	if ( $values === false || !is_array( $values ) ) 
    		$values = array(); 
    	
    	$days = array(
    		__( 'Monday', 'osfa_opening_hours' ),
    		__( 'Tuesday', 'osfa_opening_hours' ),
    		__( 'Wednesday', 'osfa_opening_hours' ),
    		__( 'Thursday', 'osfa_opening_hours' ),
    		__( 'Friday', 'osfa_opening_hours' ),
    		__( 'Saturday', 'osfa_opening_hours' ),
    		__( 'Sunday', 'osfa_opening_hours' )
		);

    	foreach ( $days as $key => $day ) : ?>

    	<?php $day_values = array_key_exists( $key, $values ) ? $values[$key] : array( 'closed' => false, 'from' => '', 'to' => '' ) ?>

    	<h4><?php echo $day ?></h4>
    	<p>
    		<input type="checkbox" id="osfa_opening_hours_<?php echo $key ?>_closed" name="osfa_opening_hours[hours][<?php echo $key ?>][closed]" <?php checked( $day_values['closed'], 'on' ) ?> />
    		<label for="osfa_opening_hours_<?php echo $key ?>_closed"><?php printf( __( 'Are you closed on %s?', 'osfa_opening_hours' ), $day ) ?></label>
    	</p>
    	
    	<p>
    		<label for="osfa_opening_hours_<?php echo $key ?>_from"><?php _e( 'From:', 'osfa_opening_hours' ) ?> <input type="text" id="osfa_opening_hours_<?php echo $key ?>_from" name="osfa_opening_hours[hours][<?php echo $key ?>][from] ?>" value="<?php echo $day_values['from'] ?>" /></label>
			<label for="osfa_opening_hours_<?php echo $key ?>_to"><?php _e( 'To:', 'osfa_opening_hours' ) ?> <input type="text" name="osfa_opening_hours[hours][<?php echo $key ?>][to] ?>" value="<?php echo $day_values['to'] ?>" /></label>
    	</p>

    	<?php endforeach;
    }

    public function extra_comment_setting() {
    	$value = self::get_plugin_setting('comment') ? self::get_plugin_setting('comment') : '';
    	?>
    	<p>    		
    		<textarea id="osfa_opening_hours_extra_comment" name="osfa_opening_hours[comment]" cols="30" rows="6"><?php echo $value ?></textarea>
    	</p>
    	<?php
    }
}

OSFA_Opening_Hours::get_instance();