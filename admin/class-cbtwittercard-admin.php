<?php

/**
 * @package   cbtwittercard_Admin
 * @author    WPBoxr <info@wpboxr.com>
 * @license   GPL-2.0+
 * @link      http://wpboxr.com
 * @copyright 2014-2015 WPBoxr
 */

/**
 * Class CbtwitterCardAdmin
 */
class CbtwitterCardAdmin {

    protected static $instance           = null;
    protected $plugin_screen_hook_suffix = '_cbtwittercard';

    private function __construct() {

        require_once("includes/class.cbtwittercardsettings.php");
        require_once("functions/cbtwittercardadminfunctions.php");

        $plugin                   = CbtwitterCard::get_instance();
        $this->plugin_suffix      = '_cbtwittercard_';
        $this->meta_plugin_suffix = '_cbtwittercard_meta_';
        $this->plugin_slug        = $plugin->get_plugin_slug();
        $this->iconurl            = plugins_url('cbtwittercard/admin/assets/img/twittercardwp.png');


        // Load admin style sheet and JavaScript.
        add_action('admin_enqueue_scripts', array($this, 'cbtwittercard_enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'cbtwittercard_enqueue_admin_scripts'));

        // Add the options page and menu item.
        add_action('admin_init', 'cbtwittercard_admin_init');
        add_action('admin_menu', array($this, 'cbtwittercard_admin_menu'));

        // Add an action link pointing to the options/setting page.
        $plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_slug . '.php');
        add_filter('plugin_action_links_' . $plugin_basename, array($this, 'cbtwittercard_action_links'));

        //add and save meta box
        add_action('add_meta_boxes', array($this, 'cbtwittercard_add_meta_box'));
        add_action('save_post', array($this, 'cbtwittercard_save_meta_box'));
    }

    /**
     * Callback for activation plugin error check action 'plugin_activated'
     * 
     */
    public static function cbtwittercard_activation_error() {

        update_option('cbtwittercard_activation_error', ob_get_contents());
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        if (null == self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Register and enqueue admin-specific style sheet.
     * @return    null    Return early if no settings page is registered.
     */
    public function cbtwittercard_enqueue_admin_styles($hook) {


        if (!isset($this->plugin_screen_hook_suffix))
            return;

        $screen = get_current_screen();

        if ($this->plugin_screen_hook_suffix == $screen->id || in_array($screen->id, get_post_types())) {
            
            
            wp_register_style($this->plugin_slug . '-chosen-styles', plugins_url('assets/css/chosen.min.css', __FILE__), array(), CbtwitterCard::VERSION);
            wp_register_style($this->plugin_slug . '-toltip-styles', plugins_url('assets/css/tooltipster.css', __FILE__), array(), CbtwitterCard::VERSION);
            wp_register_style($this->plugin_slug . '-toltiptheme-styles', plugins_url('assets/css/themes/tooltipster-light.css', __FILE__), array(), CbtwitterCard::VERSION);
            wp_register_style($this->plugin_slug . '-admin-styles', plugins_url('assets/css/cbtwittercardadmin.css', __FILE__), array(), CbtwitterCard::VERSION);
            
            wp_enqueue_style($this->plugin_slug . '-chosen-styles');
            wp_enqueue_style($this->plugin_slug . '-toltip-styles');
            wp_enqueue_style($this->plugin_slug . '-toltiptheme-styles');
            wp_enqueue_style($this->plugin_slug . '-admin-styles');
        }
    }

    /**
     * Register and enqueue admin-specific JavaScript.
     *

     * @return    null    Return early if no settings page is registered.
     */
    public function cbtwittercard_enqueue_admin_scripts($hook) {
  
        if (!isset($this->plugin_screen_hook_suffix)) {
            return;
        }
        $screen = get_current_screen();
        if ($this->plugin_screen_hook_suffix == $screen->id || in_array($screen->id, get_post_types())) {
            wp_enqueue_media();
            wp_register_script($this->plugin_slug . '-admin-script', plugins_url('assets/js/cbtwittercardadmin.js', __FILE__), array('jquery'), cbtwittercard::VERSION);
            // Localize the script with new data
            $cbtwittercardadmin_array = array(
                'remove' => __('Remove', 'cbtwittercard'),
                'add'    => __('Add New', 'cbtwittercard')
            );
            wp_localize_script($this->plugin_slug . '-admin-script', 'cbtwittercardadmin', $cbtwittercardadmin_array);
            wp_enqueue_script($this->plugin_slug . '-admin-script');

            wp_register_script($this->plugin_slug . '-chosen-script', plugins_url('assets/js/chosen.jquery.min.js', __FILE__), array('jquery'), cbtwittercard::VERSION);
            wp_register_script($this->plugin_slug . '-toltip-script', plugins_url('assets/js/jquery.tooltipster.js', __FILE__), array('jquery'), cbtwittercard::VERSION);
            
            wp_enqueue_script($this->plugin_slug . '-chosen-script');
            wp_enqueue_script($this->plugin_slug . '-toltip-script');
        }
    }

    /**
     * add meta box
     */
    public function cbtwittercard_add_meta_box() {

        $cbtc_settings           = (get_option($this->plugin_suffix . 'general_settings') != false ) ? get_option($this->plugin_suffix . 'general_settings') : array();
        $cbtc_enable_twittercard = isset($cbtc_settings[$this->plugin_suffix . 'enableplugin']) ? $cbtc_settings[$this->plugin_suffix . 'enableplugin'] : '';
        $cbtc_post_types         = isset($cbtc_settings[$this->plugin_suffix . 'postselectbox']) ? $cbtc_settings[$this->plugin_suffix . 'postselectbox'] : array('post' => 'post');
        // var_dump($cbtc_enable_twittercard);
        if ($cbtc_enable_twittercard == 'on') {

            foreach ($cbtc_post_types as $index => $cbtc_post_type) {

                add_meta_box(
                        $this->plugin_suffix . 'meta_box', // $id
                        __('CBX Twitter Card Options', $this->plugin_slug), // $title
                        array($this, 'cbtwittercard_show_meta_box'), // $callback
                        $index, // $page
                        'normal', // $context
                        'high'); // $priority
            }
        }
    }

    /**
     * show meta box
     */
    function cbtwittercard_show_meta_box() {

        global $post;
        $post_id = $post->ID;



        CbTwittercardSettings::cbtwittercard_render_metabox();
    }

    /**
     * @param $post_id
     * save meta boxes
     */
    function cbtwittercard_save_meta_box($post_id) {

        CbTwittercardSettings::cbtwittercard_save_meta($post_id);
    }

    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */
    public function cbtwittercard_admin_menu() {

        $this->plugin_screen_hook_suffix = add_menu_page(
                __('CBX Twitter Card', $this->plugin_slug), __('Twitter Card', $this->plugin_slug), 'manage_options', $this->plugin_slug, array($this, 'cbtwittercard_admin_page'), $this->iconurl
        );
        //  $this->plugin_screen_hook_suffix = add_menu_page('Codeboxr Twitter Card Settings', 'Twitter Cards', 'manage_options', 'twittercards', array($this, 'twitter_cards_options_page'), $this->iconurl );
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */
    public function cbtwittercard_admin_page() {

        $sections     = CbTwittercardSettings::cbtwittercard_settings_sections();
        $fields       = CbTwittercardSettings::cbtwittercard_settings_fields();
        $settings_api = new CbtwitterCard_Settings_API();
        $settings_api->set_sections($sections);
        $settings_api->set_fields($fields);
        $settings_api->admin_init();

        include_once('includes/cb-sidebar.php');
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */
    public function cbtwittercard_action_links($links) {

        return array_merge(
                array(
            'settings' => '<a href="' . admin_url('options-general.php?page=' . $this->plugin_slug) . '">' . __('Settings', $this->plugin_slug) . '</a>'
                ), $links
        );
    }

}

// end of class
