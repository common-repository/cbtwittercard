<?php

/**
 * @package   cbtwittercard
 * @author    WPBoxr <info@wpboxr.com>
 * @license   GPL-2.0+
 * @link      http://wpboxr.com
 * @copyright 2014-2015 WPBoxr
 */

/**
 * Class CbtwitterCard
 */
class CbtwitterCard {

    const VERSION                          = '1.0.6';

    protected $plugin_slug           = 'cbtwittercard';
    protected static $instance       = null;
    protected static $cbtcsuffix     = '_cbtwittercard_';
    protected static $cbtcmetasuffix = '_cbtwittercard_meta_';

    /**
     * Initialize the plugin by setting localization and loading public scripts
     * and styles.
     *
     * @since     1.0.0
     */
    private function __construct() {

        // Load plugin text domain
        $this->plugin_suffix      = '_cbtwittercard_';
        $this->meta_plugin_suffix = '_cbtwittercard_meta_';
        require_once("includes/cblib/cblib-meta.php");

        add_action('init', array($this, 'load_plugin_textdomain'));

        // Activate plugin when new blog is added
        add_action('wpmu_new_blog', array($this, 'activate_new_site'));

        // Load public-facing style sheet and JavaScript.
        //add_action( 'wp_enqueue_scripts', array( $this, 'cbtwittercard_enqueue_styles' ) );
        //add_action( 'wp_enqueue_scripts', array( $this, 'cbtwittercard_enqueue_scripts' ) );

        $cbtc_settings           = (get_option($this->plugin_suffix . 'general_settings') != false ) ? get_option($this->plugin_suffix . 'general_settings') : array();
        $cbtc_enable_twittercard = isset($cbtc_settings[$this->plugin_suffix . 'enableplugin']) ? $cbtc_settings[$this->plugin_suffix . 'enableplugin'] : '';

        if ($cbtc_enable_twittercard == 'on') {

            add_action('wp_head', array($this, 'cbtwittercard_pushmetadata'));
        }
    }

    /**
     * @param $post_id
     * @param $cbtcmetasuffix
     * update post meta for old post
     */
    public static function cbtwittercard_update_empty_meta($post_id, $cbtcmetasuffix) {
        // $cbtc_meta_settings = get_post_meta( $post_id, $this->meta_plugin_suffix.'general_settings', false );
        $cbtc_meta_settings = array(
            $cbtcmetasuffix . 'sitetitle' => '',
            $cbtcmetasuffix . 'sitedesc'  => '',
            $cbtcmetasuffix . 'image'     => '',
        );

        update_post_meta($post_id, $cbtcmetasuffix . 'summary_settings', $cbtc_meta_settings);
        $cbtc_meta_settings = array(
            $cbtcmetasuffix . 'enableplugin'             => '',
            $cbtcmetasuffix . 'cardselectbox'            => '',
            $cbtcmetasuffix . 'siteadmintwitterusername' => '',
        );

        update_post_meta($post_id, $cbtcmetasuffix . 'general_settings', $cbtc_meta_settings);

        /* $cbtc_meta_settings = array(
          //$cbtcmetasuffix.'image' =>'',
          $cbtcmetasuffix.'imagewidth' =>'',
          $cbtcmetasuffix.'imageheight' =>'',

          ) ;

          update_post_meta($post_id, $cbtcmetasuffix.'photo_settings', $cbtc_meta_settings); */
        do_action('cbtwittercard_update_empty_meta', $post_id, $cbtcmetasuffix);
    }

    /**
     * Push meta data in wp head, adding meta data starts from here
     */
    function cbtwittercard_pushmetadata() {


        global $wpdb, $wp_query, $post;
        $cbtc_metaTagHelper = new CBTwitterCardMetaTagHelper();
        $cbtc_metaarray     = array();
        $cbtc_meta          = '';
        $cbtc_settings      = (get_option($this->plugin_suffix . 'general_settings') != false ) ? get_option($this->plugin_suffix . 'general_settings') : array();


        //archive page
        if (!is_singular()):

            $cbtc_cardtype   = self::cbtwittercard_post_card(''); //$cbtc_meta_settings[0][$this->meta_plugin_suffix.'cardselectbox'];
            $cbtc_post_title = self::cbtwittercard_post_title('');
            $cbtc_post_desc  = self::cbtwittercard_post_desc('');

            $cbtc_post_image   = self::cbtwittercard_post_image('');
            $cbtc_post_creator = self::cbtwittercard_post_creator('');
            $cbtc_site_url     = self::cbtwittercard_post_siteurl('');
            $cbtc_site_name    = self::cbtwittercard_post_siteusername('');

            $metaarray = array('card'        => $cbtc_cardtype,
                'site'        => $cbtc_site_url,
                'creator'     => $cbtc_post_creator,
                'url'         => $cbtc_site_name,
                'title'       => $cbtc_post_title,
                'description' => $cbtc_post_desc,
                'image'       => $cbtc_post_image,
            );

            switch ($cbtc_cardtype) {
                /* case 'photo':
                  $cbtc_image_d              = self::cbtwittercard_post_imagedimension('');
                  $metaarray['image:width']  = $cbtc_image_d[0];
                  $metaarray['image:height'] = $cbtc_image_d[1];

                  break; */

                default:

                    break;
            }

            $metaarray = apply_filters('cbtwittercard_edit_metavalue', $metaarray, '');
            //$cbtc_meta .= $cbtc_metaTagHelper->cbtwittercard_addmeta($metaarray);
            $cbtc_meta .= $cbtc_metaTagHelper->cbtwittercard_addtcmeta($metaarray);

        else:

            global $post;
            $post_id                = $post->ID;
            $cbtc_enable_post_types = $cbtc_post_types        = isset($cbtc_settings[$this->plugin_suffix . 'postselectbox']) ? $cbtc_settings[$this->plugin_suffix . 'postselectbox'] : array('post' => 'post', 'page' => 'page');
            //if in selected post types

            if (in_array($post->post_type, $cbtc_enable_post_types)) {

                $cbtc_meta_settings = get_post_meta($post_id, $this->meta_plugin_suffix . 'general_settings', false);

                if (empty($cbtc_meta_settings)) {
                    self:: cbtwittercard_update_empty_meta($post_id, $this->meta_plugin_suffix);
                }
                $cbtc_meta_settings = get_post_meta($post_id, $this->meta_plugin_suffix . 'general_settings', false);


                if (is_array($cbtc_meta_settings) && !empty($cbtc_meta_settings)) {

                    if ((array_key_exists($this->meta_plugin_suffix . 'enableplugin', $cbtc_meta_settings[0]) && $cbtc_meta_settings[0][$this->meta_plugin_suffix . 'enableplugin'] != 'on')) {

                        $cbtc_cardtype   = self::cbtwittercard_post_card($post_id); //$cbtc_meta_settings[0][$this->meta_plugin_suffix.'cardselectbox'];
                        $cbtc_post_title = self::cbtwittercard_post_title($post_id);
                        $cbtc_post_desc  = self::cbtwittercard_post_desc($post_id);
                        $cbtc_post_desc  = strip_shortcodes($cbtc_post_desc);

                        $cbtc_post_image   = self::cbtwittercard_post_image($post_id);
                        $cbtc_post_creator = self::cbtwittercard_post_creator($post_id);
                        $cbtc_site_name    = self::cbtwittercard_post_siteusername($post_id);

                        $metaarray = array('card'        => $cbtc_cardtype,
                            'site'        => $cbtc_site_name,
                            'creator'     => $cbtc_post_creator,
                            'url'         => get_permalink($post_id),
                            'title'       => $cbtc_post_title,
                            'description' => $cbtc_post_desc,
                            'image'       => $cbtc_post_image,
                        );

                        switch ($cbtc_cardtype) {
                            /* case 'photo':
                              $cbtc_image_d   = self::cbtwittercard_post_imagedimension($post_id);
                              $metaarray['image:width']  = $cbtc_image_d[0];
                              $metaarray['image:height'] = $cbtc_image_d[1];

                              break; */

                            default:
                                break;
                        }

                        $metaarray = apply_filters('cbtwittercard_edit_metavalue', $metaarray, $post_id);

                        //$cbtc_meta .= $cbtc_metaTagHelper->cbtwittercard_addmeta($metaarray);
                        $cbtc_meta .= $cbtc_metaTagHelper->cbtwittercard_addtcmeta($metaarray);
                    }
                }
            }

        endif;

        //render the meta tags
        echo "\n<!-- CBX Twitter Card for Wordpress plugin by wpboxr.com : start -->\n";
        echo $cbtc_meta;
        echo "<!-- CBX Twitter Card for Wordpress plugin by wpboxr.com : end -->\n";
    }

    /**
     * @param $post_id
     *  return post card type meta data
     * @return string
     */
    public static function cbtwittercard_post_card($post_id) {

        $cbtccard  = 'summary';
        $cbtc_card = '';

        if ($post_id != '') {

            $cbtc_meta_settings = get_post_meta($post_id, self::$cbtcmetasuffix . 'general_settings', true);
            $cbtc_card          = (!empty($cbtc_meta_settings) && isset($cbtc_meta_settings[self::$cbtcmetasuffix . 'cbtwittercard']) ) ? $cbtc_meta_settings[self::$cbtcmetasuffix . 'cbtwittercard'] : '';
        }

        $cbtc_global_settings = get_option(self::$cbtcsuffix . 'general_settings');

        if ($cbtc_card == '') {
            $cbtc_card = isset($cbtc_global_settings[self::$cbtcsuffix . 'cardselectbox']) ? $cbtc_global_settings[self::$cbtcsuffix . 'cardselectbox'] : $cbtccard;
        }

        if ($post_id != '') {
            //do_action('cbtwittercard_update_card_type' , $post_id ,$cbtc_meta_settings , self::$cbtcmetasuffix, $cbtc_card );
        }


        return $cbtc_card;
    }

    /**
     * @param $post_id
     *  return site url meta data
     * @return mixed|string|void
     */
    public static function cbtwittercard_post_siteurl($post_id) {

        $cbtc_global_settings = get_option(self::$cbtcsuffix . 'general_settings');
        $siteurl              = get_site_url();
        $cbtc_site            = isset($cbtc_global_settings[self::$cbtcsuffix . 'siteurl']) ? $cbtc_global_settings[self::$cbtcsuffix . 'siteurl'] : $siteurl;
        return $cbtc_site;
    }

    /**
     * @param $post_id
     *  return global site name meta data
     * @return string
     */
    public static function cbtwittercard_post_siteusername($post_id) {

        $cbtc_global_settings = get_option(self::$cbtcsuffix . 'general_settings');
        $siteurl              = '';
        $cbtc_site            = isset($cbtc_global_settings[self::$cbtcsuffix . 'sitetwitterusername']) ? $cbtc_global_settings[self::$cbtcsuffix . 'sitetwitterusername'] : $siteurl;

        return '@' . $cbtc_site;
    }

    /**
     * @param $post_id
     *  return summary type post title meta data
     * @return string
     */
    public static function cbtwittercard_post_title($post_id) {

        $cbtc_title = '';

        if ($post_id != '') {
            $cbtc_meta_summary_settings = get_post_meta($post_id, self::$cbtcmetasuffix . 'summary_settings', true);
            $cbtc_title                 = (!empty($cbtc_meta_summary_settings) && $cbtc_meta_summary_settings[self::$cbtcmetasuffix . 'sitetitle'] != '') ? $cbtc_meta_summary_settings[self::$cbtcmetasuffix . 'sitetitle'] : get_the_title($post_id);
        }


        if ($cbtc_title == '') {
            $cbtc_global_summary_settings = get_option(self::$cbtcsuffix . 'summary_settings');
            $cbtc_title                   = ( isset($cbtc_global_summary_settings[self::$cbtcsuffix . 'sitetitle']) && $cbtc_global_summary_settings[self::$cbtcsuffix . 'sitetitle'] != '') ? $cbtc_global_summary_settings[self::$cbtcsuffix . 'sitetitle'] : _('No Title Found', 'cbtwittercard');
        }

        if ($post_id != '') {
            //
            //do_action('cbtwittercard_update_sitetitle', $post_id , $cbtc_meta_summary_settings, self::$cbtcmetasuffix , $cbtc_title);
        }
        return $cbtc_title;
    }

    /**
     * @param $post_id
     *  return summary type post desc meta data
     * @return string
     */
    public static function cbtwittercard_post_desc($post_id) {

        $cbtc_desc = '';

        if ($post_id != '') {

            $cbtc_meta_summary_settings = get_post_meta($post_id, self::$cbtcmetasuffix . 'summary_settings', true);
            $cbtccontent                = get_post_field('post_content', $post_id);
            $cbtccontent                = strip_shortcodes($cbtccontent);
            $cbtccontent                = substr(strip_tags($cbtccontent), 0, 200); //do we need another

            $cbtccontent = str_replace("\r", "", $cbtccontent);
            $cbtccontent = str_replace("\n", "", $cbtccontent);

            $cbtc_desc = (!empty($cbtc_meta_summary_settings) && array_key_exists(self::$cbtcmetasuffix . 'sitedesc', $cbtc_meta_summary_settings) && $cbtc_meta_summary_settings[self::$cbtcmetasuffix . 'sitedesc'] != '') ? $cbtc_meta_summary_settings[self::$cbtcmetasuffix . 'sitedesc'] : $cbtccontent;
        }



        if ($cbtc_desc == '') {
            $cbtc_global_summary_settings = get_option(self::$cbtcsuffix . 'summary_settings');
            $cbtc_desc                    = (isset($cbtc_global_summary_settings[self::$cbtcsuffix . 'sitedesc']) && $cbtc_global_summary_settings[self::$cbtcsuffix . 'sitedesc'] != '') ? $cbtc_global_summary_settings[self::$cbtcsuffix . 'sitedesc'] : __('No Desc Found', 'cbtwittercard');
        }

        if ($post_id != '') {
            //do_action('cbtwittercard_update_sitedesc', $post_id , $cbtc_meta_summary_settings, self::$cbtcmetasuffix , $cbtc_desc);
        }

        return $cbtc_desc;
    }

    /**
     * @param $post_id
     *  return image meta data and save if necessary
     * @return bool|string
     */
    public static function cbtwittercard_post_image($post_id) {

        $cbtc_photo         = '';
        $cbtc_default_thumb = plugins_url('cbtwittercard/public/assets/css/default-thumbnail.png');

        if (intval($post_id) > 0) {

            $cbtc_meta_general_settings = get_post_meta($post_id, self::$cbtcmetasuffix . 'general_settings', true);
            //var_dump($cbtc_meta_general_settings);
            $cbtc_thumb                 = (wp_get_attachment_url(get_post_thumbnail_id($post_id)));
            $cbtc_photo                 = (!empty($cbtc_meta_general_settings) && isset($cbtc_meta_general_settings[self::$cbtcmetasuffix . 'image']) ) ? $cbtc_meta_general_settings[self::$cbtcmetasuffix . 'image'] : $cbtc_thumb;



            $cbtc_global_photo_settings = get_option(self::$cbtcsuffix . 'general_settings');

            if ($cbtc_photo == '') {
                $cbtc_photo = ( isset($cbtc_global_photo_settings[self::$cbtcsuffix . 'image']) && isset($cbtc_global_photo_settings[self::$cbtcsuffix . 'image'])) ? $cbtc_global_photo_settings[self::$cbtcsuffix . 'image'] : $cbtc_default_thumb;
            }



            do_action('cbtwittercard_update_image', $post_id, $cbtc_meta_general_settings, self::$cbtcmetasuffix, $cbtc_photo);
        }

        return $cbtc_photo;
    }

    /**
     * @param $post_id
     *  return width and height of photo type image data
     * @return array
     */
    /*
      public static function cbtwittercard_post_imagedimension($post_id){

      $cbtc_photo_width           = '';
      $cbtc_default_thumb         = 200;
      $cbtc_photo_height          = '';

      if($post_id != ''){
      $cbtc_meta_photo_settings              = get_post_meta( $post_id, self::$cbtcmetasuffix.'photo_settings', false );
      $cbtc_photo_width                      = (!empty($cbtc_meta_photo_settings) && $cbtc_meta_photo_settings[0][self::$cbtcmetasuffix.'imagewidth'] != '') ? $cbtc_meta_photo_settings[0][self::$cbtcmetasuffix.'imagewidth'] : '';
      $cbtc_photo_height                     = (!empty($cbtc_meta_photo_settings) && $cbtc_meta_photo_settings[0][self::$cbtcmetasuffix.'imageheight'] != '') ? $cbtc_meta_photo_settings[0][self::$cbtcmetasuffix.'imageheight'] : '';
      }

      $cbtc_global_photo_settings   = get_option(self::$cbtcsuffix.'photo_settings');

      if($cbtc_photo_width == ''){
      $cbtc_photo_width                  = ( isset($cbtc_global_photo_settings[self::$cbtcsuffix.'imagewidth'] )&& $cbtc_global_photo_settings[self::$cbtcsuffix.'imagewidth'] != '') ? $cbtc_global_photo_settings[self::$cbtcsuffix.'imagewidth'] : $cbtc_default_thumb;
      }

      if($cbtc_photo_height == ''){
      $cbtc_photo_height                 = ( isset($cbtc_global_photo_settings[self::$cbtcsuffix.'imageheight'] )&& $cbtc_global_photo_settings[self::$cbtcsuffix.'imageheight'] != '') ? $cbtc_global_photo_settings[self::$cbtcsuffix.'imageheight'] : $cbtc_default_thumb;
      }


      if($post_id != ''){

      do_action('cbtwittercard_update_image_dimension', $post_id , $cbtc_meta_photo_settings, self::$cbtcmetasuffix , $cbtc_photo_width , $cbtc_photo_height );

      }
      return array($cbtc_photo_width, $cbtc_photo_height);

      }
     */

    /**
     * @param $post_id
     *  return creator /admin name meta data
     * @return string
     */
    public static function cbtwittercard_post_creator($post_id) {

        $cbtc_creator = '';
        if ($post_id != '') {
            $creator            = get_post_meta($post_id, 'creator', true);
            $cbtc_meta_settings = get_post_meta($post_id, self::$cbtcmetasuffix . 'general_settings', true);
            $cbtc_creator       = (!empty($cbtc_meta_settings) && ( array_key_exists(self::$cbtcmetasuffix . 'siteadmintwitterusername', $cbtc_meta_settings) && $cbtc_meta_settings[self::$cbtcmetasuffix . 'siteadmintwitterusername'] != '' )) ? $cbtc_meta_settings[self::$cbtcmetasuffix . 'siteadmintwitterusername'] : $creator;
        }
        $cbtc_global_settings = get_option(self::$cbtcsuffix . 'general_settings');

        if ($cbtc_creator == '') {

            $cbtc_creator = (isset($cbtc_global_settings[self::$cbtcsuffix . 'siteadmintwitterusername']) && $cbtc_global_settings[self::$cbtcsuffix . 'siteadmintwitterusername'] != '') ? $cbtc_global_settings[self::$cbtcsuffix . 'siteadmintwitterusername'] : '';
        }

        if ($post_id != '') {

            do_action('cbtwittercard_update_image_creator', $post_id, $cbtc_meta_settings, self::$cbtcmetasuffix, $cbtc_creator);
        }

        return '@' . $cbtc_creator;
    }

    /**
     * Return the plugin slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if (null == self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Fired when the plugin is activated.
     *
     * @since    1.0.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Activate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       activated on an individual blog.
     */
    public static function activate($network_wide) {

        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_activate();
                }

                restore_current_blog();
            } else {
                self::single_activate();
            }
        } else {
            self::single_activate();
        }
    }

    /**
     * Fired when the plugin is deactivated.
     *
     * @since    1.0.0
     *
     * @param    boolean    $network_wide    True if WPMU superadmin uses
     *                                       "Network Deactivate" action, false if
     *                                       WPMU is disabled or plugin is
     *                                       deactivated on an individual blog.
     */
    public static function deactivate($network_wide) {

        if (function_exists('is_multisite') && is_multisite()) {

            if ($network_wide) {

                // Get all blog ids
                $blog_ids = self::get_blog_ids();

                foreach ($blog_ids as $blog_id) {

                    switch_to_blog($blog_id);
                    self::single_deactivate();
                }

                restore_current_blog();
            } else {
                self::single_deactivate();
            }
        } else {
            self::single_deactivate();
        }
    }

    /**
     * Fired when a new site is activated with a WPMU environment.
     *
     * @since    1.0.0
     *
     * @param    int    $blog_id    ID of the new blog.
     */
    public function activate_new_site($blog_id) {

        if (1 !== did_action('wpmu_new_blog')) {
            return;
        }

        switch_to_blog($blog_id);
        self::single_activate();
        restore_current_blog();
    }

    /**
     * Get all blog ids of blogs in the current network that are:
     * - not archived
     * - not spam
     * - not deleted
     *
     * @since    1.0.0
     *
     * @return   array|false    The blog ids, false if no matches.
     */
    private static function get_blog_ids() {

        global $wpdb;

        // get an array of blog ids
        $sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

        return $wpdb->get_col($sql);
    }

    /**
     * Fired for each blog when the plugin is activated.
     *
     * @since    1.0.0
     */
    private static function single_activate() {

        $check_settings_group = (get_option('_cbtwittercard_general_settings'));
        if (!is_array($check_settings_group) || empty($check_settings_group)) {
            $check_settings_group['_cbtwittercard_enableplugin']  = 'on';
            $check_settings_group['_cbtwittercard_siteurl']       = get_site_url();
            $check_settings_group['_cbtwittercard_cardselectbox'] = 'summary';
        }
        if (is_array($check_settings_group['_cbtwittercard_postselectbox']) && empty($check_settings_group['_cbtwittercard_postselectbox'])) {

            $check_settings_group['_cbtwittercard_postselectbox'] = array('post' => 'post', 'page' => 'page');
            //_cbtwittercard_postselectbox
        } else {
            $check_settings_group['_cbtwittercard_postselectbox'] = array('post' => 'post', 'page' => 'page');
        }

        //update_option('smart_poll_global_settings',$check_poll_user_group);
        update_option('_cbtwittercard_general_settings', $check_settings_group);

        $check_settings_group = (get_option('_cbtwittercard_summary_settings'));
        if (!is_array($check_settings_group) || empty($check_settings_group)) {
            $check_settings_group['_cbtwittercard_sitetitle'] = get_bloginfo('name');
            $check_settings_group['_cbtwittercard_sitedesc']  = get_bloginfo('description');
        }
        update_option('_cbtwittercard_summary_settings', $check_settings_group);
    }

    /**
     * Fired for each blog when the plugin is deactivated.
     *
     * @since    1.0.0
     */
    private static function single_deactivate() {
        
    }

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {

        $domain = $this->plugin_slug;

        load_plugin_textdomain($domain, false, dirname(plugin_basename(__FILE__)) . '/../languages');
    }

    /**
     * Register and enqueue public-facing style sheet.
     *
     * @since    1.0.0
     */
    public function cbtwittercard_enqueue_styles() {
        //not in used
        //wp_enqueue_style($this->plugin_slug . '-plugin-styles', plugins_url('assets/css/cbtwittercard.css', __FILE__), array(), self::VERSION);
    }

    /**
     * Register and enqueues public-facing JavaScript files.
     *
     * @since    1.0.0
     */
    public function cbtwittercard_enqueue_scripts() {
        //note in used
        //wp_enqueue_script($this->plugin_slug . '-plugin-script', plugins_url('assets/js/cbtwittercard.js', __FILE__), array('jquery'), self::VERSION);
    }

}
