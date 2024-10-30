<?php
define('CBTWITTERCARDTOOLTIP', plugins_url('cbtwittercard/admin/assets/img/tool.jpg'));

/**
 * admin init function
 */
function cbtwittercard_admin_init() {

    $sections     = CbTwittercardSettings::cbtwittercard_settings_sections();
    $fields       = CbTwittercardSettings::cbtwittercard_settings_fields();
    $settings_api = new CbtwitterCard_Settings_API();
    $settings_api->set_sections($sections);
    $settings_api->set_fields($fields);
    $settings_api->admin_init();
    // new CbtcCallbacks($sections ,$fields);
}

/**
 * Class CbTwittercardSettings
 */
class CbTwittercardSettings {

    protected $sections;
    protected $fields;
    protected static $pluginsuffix     = '_cbtwittercard_';
    protected static $pluginmetasuffix = '_cbtwittercard_meta_';

    /**
     * @param $cbsections
     * return settings sections
     */
    public static function cbtwittercard_settings_sections($plugin_suffix = '_cbtwittercard_') {

        $sections = array(
            array(
                'id'    => $plugin_suffix . 'general_settings',
                'title' => __('General Settings', 'cbtwittercard'),
                'desc'  => __('Required for all card type, will take image from photo card type', 'cbtwittercard'),
            ), array(
                'id'    => $plugin_suffix . 'summary_settings',
                'title' => __('Summary Card', 'cbtwittercard'),
                'desc'  => __('Required for summary card type, will take image from photo card type. select "summary" as card type if want to use this datas ', 'cbtwittercard'),
            )
                /* array(
                  'id'    => $plugin_suffix.'photo_settings',
                  'title' => __( 'General Photo', 'cbtwittercard' ),
                  'desc'  =>  __( 'Required for summary and photo card type, select "photo" as card type if want to use this datas ', 'cbtwittercard' ),
                  ) */
        );
        $sections = apply_filters('cbtwittercard_add_sections', $sections, $plugin_suffix);

        return $sections;
    }

    /**
     * @param $cbsections
     * return meta settings sections
     */
    public static function cbtwittercard_meta_settings_sections($plugin_suffix = '_cbtwittercard_meta_') {


        $sections = array(
            array(
                'id'    => $plugin_suffix . 'general_settings',
                'title' => __('General Settings', 'cbtwittercard'),
                'desc'  => __('Required for all card type, will take image from photo card type', 'cbtwittercard'),
            ), array(
                'id'    => $plugin_suffix . 'summary_settings',
                'title' => __('Summary Card', 'cbtwittercard'),
                'desc'  => __('Required for summary card type, will take image from photo card type. select "summary" as card type if want to use this datas ', 'cbtwittercard'),
            )
        );
        $sections = apply_filters('cbtwittercard_add_meta_sections', $sections, $plugin_suffix);

        return $sections;
    }

    /**
     * @param $type   meta as string or empty
     *  return settings fields
     * @return array|mixed|void
     */
    public static function cbtwittercard_settings_fields($plugin_suffix = '_cbtwittercard_') {


        $cbtc_args = array(
            'public'   => true,
            '_builtin' => true
        );


        $cbtc_post_types = get_post_types($cbtc_args);
        $cbtc_post_types = apply_filters('cbtwittercard_add_custom_post_types', $cbtc_post_types);

        $cbtc_card_types = array(
            'summary' => __('Summary', 'cbtwittercard')
        );

        $cbtc_card_types = apply_filters('cbtwittercard_add_custom_card_types', $cbtc_card_types);

        $cbtc_enable = __('Enable Twitter Card', 'cbtwittercard');
        $cbtc_owner  = __('Admin', 'cbtwittercard');
        $tool_cbtc   = __('Enable Site wide', 'cbtwittercard');

        $cbtc_general_settings = array(
            array(
                'name'        => $plugin_suffix . 'enableplugin',
                'label'       => __('' . $cbtc_enable, 'cbtwittercard'),
                'desc'        => __('', 'cbtwittercard'),
                'type'        => 'checkbox',
                'tooltip'     => $tool_cbtc,
                'placeholder' => '',
                'default'     => 'on'
            ),
            array(
                'name'        => $plugin_suffix . 'postselectbox',
                'label'       => __('Post Types', 'cbtwittercard'),
                'desc'        => __('Select post types where you want to enable twitter card', 'cbtwittercard'),
                'type'        => 'postselectbox',
                'options'     => $cbtc_post_types,
                'tooltip'     => 'Select post types, default post and page',
                'placeholder' => ''
            ),
            array(
                'name'        => $plugin_suffix . 'cardselectbox',
                'label'       => __('Default Card Type', 'cbtwittercard'),
                'desc'        => __(apply_filters('cbtwittercard_card_select_desc', 'Select default card type'), 'cbtwittercard'),
                'type'        => 'select',
                'default'     => 'summary',
                'options'     => $cbtc_card_types,
                'tooltip'     => 'Select your desired card type ,default is summary ',
                'placeholder' => ''
            ),
            array(
                'name'        => $plugin_suffix . 'sitetwitterusername',
                'label'       => __('Site Twitter Username', 'cbtwittercard'),
                'desc'        => __('Example-WPBoxr', 'cbtwittercard'),
                'type'        => 'text',
                'default'     => '',
                'tooltip'     => 'Enter site twitter user name , "@" will automatically prepend',
                'placeholder' => '',
                'beforeinput' => 'on'
            ),
            array(
                'name'        => $plugin_suffix . 'siteadmintwitterusername',
                'label'       => __($cbtc_owner . ' Twitter Username', 'cbtwittercard'),
                'desc'        => __('Twitter username of the content creator/admin.', 'cbtwittercard'),
                'type'        => 'text',
                'default'     => 'admin',
                'tooltip'     => 'Enter your admin twitter user name , "@ will automatically prepend "',
                'placeholder' => '',
                'beforeinput' => 'on'
            ),
            array(
                'name'        => $plugin_suffix . 'siteurl',
                'label'       => __('Site URL', 'cbtwittercard'),
                'desc'        => __('', 'cbtwittercard'),
                'type'        => 'text',
                'default'     => get_bloginfo('url'),
                'tooltip'     => 'Enter site url ,for single post link of that post will be send',
                'placeholder' => ''
            ),
            array(
                'name'        => $plugin_suffix . 'image',
                'label'       => __('Default Photo', 'cbtwittercard'),
                'desc'        => __('Upload image or insert image url', 'cbtwittercard'),
                'type'        => 'file',
                'tooltip'     => 'URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. The image must be a minimum size of 120px by 120px and must be less than 1MB in file size. For an expanded tweet and its detail page, the image will be cropped to a 4:3 aspect ratio and resized to be displayed at 120px by 90px. The image will also be cropped and resized to 120px by 120px for use in embedded tweets.',
                'placeholder' => '',
                'preview'     => true
            )
        );

        $cbtc_summary_settings = array(
            array(
                'name'        => $plugin_suffix . 'sitetitle',
                'label'       => __('Title', 'cbtwittercard'),
                'desc'        => __('Title of youe page /article', 'cbtwittercard'),
                'type'        => 'text',
                'default'     => get_bloginfo('name'),
                'tooltip'     => 'Title should be concise and will be truncated at 70 characters.If no title set for single post post title will be taken',
                'placeholder' => ''
            ),
            array(
                'name'        => $plugin_suffix . 'sitedesc',
                'label'       => __('Description', 'cbtwittercard'),
                'desc'        => __('Concisely summarizes the content of the page', 'cbtwittercard'),
                'type'        => 'textarea',
                'tooltip'     => 'A description that concisely summarizes the content of the page, as appropriate for presentation within a Tweet. Do not re-use the title text as the description, or use this field to describe the general services provided by the website. Description text will be truncated at the word to 200 characters.',
                'default'     => get_bloginfo('description'),
                'placeholder' => ''
            ),
        );


        $fields = array(
            $plugin_suffix . 'general_settings' => apply_filters('cbtwittercard_add_general_settings', $cbtc_general_settings, $plugin_suffix),
            $plugin_suffix . 'summary_settings' => apply_filters('cbtwittercard_add_summary_settings', $cbtc_summary_settings, $plugin_suffix)
        );
        $fields = apply_filters('cbtwittercard_add_fields', $fields, $plugin_suffix);

        return $fields;
    }

    /**
     * Use for creating meta field
     *
     * @param $type   meta as string or empty
     *  return settings fields
     * @return array|mixed|void
     */
    public static function cbtwittercard_meta_settings_fields($plugin_suffix = '_cbtwittercard_meta_') {

        $cbtc_args = array(
            'public'   => true,
            '_builtin' => true
        );

        $cbtc_post_types = get_post_types($cbtc_args);
        $cbtc_post_types = apply_filters('cbtwittercard_add_custom_post_types', $cbtc_post_types);

        $cbtc_card_types = array(
            'summary' => __('Summary', 'cbtwittercard')
        );

        $cbtc_card_types = apply_filters('cbtwittercard_add_custom_card_types', $cbtc_card_types);

        $cbtc_enable = __('Disable', 'cbtwittercard');
        $cbtc_owner  = __('Creator', 'cbtwittercard');
        $tool_cbtc   = __('Disable', 'cbtwittercard');

        $global_settings = get_option('_cbtwittercard_general_settings');


        if (is_array($global_settings) && isset($global_settings['_cbtwittercard_cardselectbox']) && $global_settings['_cbtwittercard_cardselectbox'] != '') {
            $global_settings_cardtype = $global_settings['_cbtwittercard_cardselectbox'];
        } else {
            $global_settings_cardtype = 'summary';
        }

        $cbtc_general_settings = array(
            array(
                'name'        => $plugin_suffix . 'enableplugin',
                'label'       => __('' . $cbtc_enable, 'cbtwittercard'),
                'desc'        => __('', 'cbtwittercard'),
                'type'        => 'checkbox',
                'tooltip'     => $tool_cbtc,
                'placeholder' => ''
            ),
            array(
                'name'        => $plugin_suffix . 'cbtwittercard',
                'label'       => __('Card Type', 'cbtwittercard'),
                'desc'        => __(apply_filters('cbtwittercard_card_select_desc', 'Select default card type'), 'cbtwittercard'),
                'type'        => 'select',
                'default'     => apply_filters('cbtwittercard_default_meta_cardtype', $global_settings_cardtype),
                'options'     => $cbtc_card_types,
                'tooltip'     => 'Select your desired card type ,default is summary ',
                'placeholder' => ''
            ),
            array(
                'name'        => $plugin_suffix . 'siteadmintwitterusername',
                'label'       => __($cbtc_owner . ' Twitter Username', 'cbtwittercard'),
                'desc'        => __('Twitter username of the content creator/admin.', 'cbtwittercard'),
                'type'        => 'text',
                'default'     => 'admin',
                'tooltip'     => 'Enter Creator twitter user name , default is admin ',
                'placeholder' => '',
                'beforeinput' => 'on'
            ),
            array(
                'name'        => $plugin_suffix . 'image',
                'label'       => __('Photo', 'cbtwittercard'),
                'desc'        => __('Upload image or insert image url', 'cbtwittercard'),
                'type'        => 'file',
                'tooltip'     => 'URL to a unique image representing the content of the page. Do not use a generic image such as your website logo, author photo, or other image that spans multiple pages. The image must be a minimum size of 120px by 120px and must be less than 1MB in file size. For an expanded tweet and its detail page, the image will be cropped to a 4:3 aspect ratio and resized to be displayed at 120px by 90px. The image will also be cropped and resized to 120px by 120px for use in embedded tweets.',
                'placeholder' => '',
                'preview'     => true
            )
        );

        $cbtc_summary_settings = array(
            array(
                'name'        => $plugin_suffix . 'sitetitle',
                'label'       => __('Title', 'cbtwittercard'),
                'desc'        => __('Title of youe page /article', 'cbtwittercard'),
                'type'        => 'text',
                'default'     => 'Title',
                'tooltip'     => 'Title should be concise and will be truncated at 70 characters.If no title set for single post post title will be taken',
                'placeholder' => ''
            ),
            array(
                'name'        => $plugin_suffix . 'sitedesc',
                'label'       => __('Description', 'cbtwittercard'),
                'desc'        => __('Concisely summarizes the content of the page', 'cbtwittercard'),
                'type'        => 'textarea',
                'tooltip'     => 'A description that concisely summarizes the content of the page, as appropriate for presentation within a Tweet. Do not re-use the title text as the description, or use this field to describe the general services provided by the website. Description text will be truncated at the word to 200 characters.',
                'placeholder' => ''
            ),
        );

        $fields = array(
            $plugin_suffix . 'general_settings' => apply_filters('cbtwittercard_add_general_settings', $cbtc_general_settings, $plugin_suffix),
            $plugin_suffix . 'summary_settings' => apply_filters('cbtwittercard_add_summary_settings', $cbtc_summary_settings, $plugin_suffix)
        );

        $fields = apply_filters('cbtwittercard_add_meta_fields', $fields, $plugin_suffix);

        return $fields;
    }

    /**
     * called from admin page to render meta boxs
     */
    public static function cbtwittercard_render_metabox() {

        $cbsections  = self::cbtwittercard_meta_settings_sections();
        $cbtc_fields = self::cbtwittercard_meta_settings_fields();

        self::cbtwittercard_show_nav($cbsections);
        self::cbtwittercard_show_groups($cbsections, $cbtc_fields);
    }

    /**
     * @param $cbsections
     * show nav links for meta box
     */
    public static function cbtwittercard_show_nav($cbsections) {
        $html = '<h2 class="cbtwittercard-meta-nav-tab-wrapper">';
        foreach ($cbsections as $tab) {
            $html .= sprintf('<a href="#%1$s" class="nav-tab cbtwittercard-meta-nav-tab" id="%1$s-tab">%2$s</a>', $tab['id'], $tab['title']);
        }
        $html .= '</h2>';
        echo $html;
    }

    /**
     * @param $cbsections
     * @param $cbtc_fields
     * show groups by calling call back
     */
    public static function cbtwittercard_show_groups($cbsections, $cbtc_fields) {


        echo ' <div class="metabox-holder">
                <div class="postbox">';
        foreach ($cbsections as $index => $form) {
            ?>
            <div id="<?php echo $form['id']; ?>" style="padding: 20px" class="cbtwittercard-meta-group">
            <?php if (array_key_exists('desc', $form)): ?>
                    <p><?php echo $form['desc']; ?></p>
                <?php endif; ?>
                <table class="table form-table">

            <?php
            foreach ($cbtc_fields[$form['id']] as $cbtc_field) {

                $cbtc_callback         = 'cbtwittercard_callback_' . $cbtc_field['type'];
                $cbtc_field['section'] = $form['id'];
                $cbtc_field['id']      = $cbtc_field['name'];

                echo '<tr>';
                    CbTwittercardSettings::$cbtc_callback($cbtc_field);
                echo '</tr>';
            }
            ?>
                </table>
            </div>

            <?php
        }
        echo '</div>
            </div>';
        wp_nonce_field('cbtwittercard_save', 'cbtwittercard_meta_box_nonce');
    }

    /**
     * @param $post_id
     * save all meta boxs of our system
     */
    public static function cbtwittercard_save_meta($post_id) {

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        if (!isset($_POST['cbtwittercard_meta_box_nonce']) || !wp_verify_nonce($_POST['cbtwittercard_meta_box_nonce'], 'cbtwittercard_save'))
            return;

        if (!current_user_can('edit_post'))
            return;

        $cbsections  = self::cbtwittercard_meta_settings_sections();
        $cbtc_fields = self::cbtwittercard_meta_settings_fields();

        foreach ($cbsections as $cbsection) {

            if (isset($_POST[$cbsection['id']])) {

                //var_dump($cbsection['id']); exit();
                update_post_meta($post_id, $cbsection['id'], $_POST[$cbsection['id']]);
            }
        }
    }

    /**
     * @param $args
     * call back for text
     */
    public static function cbtwittercard_callback_text($args) {

        global $post;
        $post_id           = $post->ID;
        $cbtc_meta         = get_post_meta($post_id, $args['section'], false);
        $value             = '';
        $cbtc_before_input = '';
        $cbtc_tooltip      = '';
        $cbtc_placeholder  = '';


        if (is_array($cbtc_meta) && !empty($cbtc_meta) && array_key_exists($args['id'], $cbtc_meta[0])) {

            $value = $cbtc_meta[0][$args['id']];
        }
        if (array_key_exists('beforeinput', $args)) {

            $cbtc_before_input = '@';
        }

        if (array_key_exists('tooltip', $args) && $args['tooltip'] != '') {
            $cbtc_tooltip = '<span title="' . $args["tooltip"] . '" class="cbtwittercard_tooltip"><img src="' . CBTWITTERCARDTOOLTIP . '"/></span>';
        }
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $html = sprintf('<td><span style="" class="cbtwittercard_label"><strong> %s</strong></span>', $args['label']);
        $html .= $cbtc_tooltip . '</td>';
        $html .= '<td>' . $cbtc_before_input;
        $html .= sprintf('<input type="text" style="height: 30px;" class="%3$s %1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
        $html .= sprintf('<br><span style="" class="description"> %s</span></td>', $args['desc']);

        echo $html;
    }

    /**
     * @param $args
     * callback for number
     */
    public static function cbtwittercard_callback_number($args) {

        global $post;
        $post_id      = $post->ID;
        $cbtc_meta    = get_post_meta($post_id, $args['section'], false);
        $value        = '';
        $cbtc_tooltip = '';

        if (is_array($cbtc_meta) && !empty($cbtc_meta) && array_key_exists($args['id'], $cbtc_meta[0])) {
            $value = $cbtc_meta[0][$args['id']];
        }

        if (array_key_exists('tooltip', $args) && $args['tooltip'] != '') {
            $cbtc_tooltip = '<span title="' . $args["tooltip"] . '" class="cbtwittercard_tooltip"><img src="' . CBTWITTERCARDTOOLTIP . '"/></span>';
        }
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $html = sprintf('<td><span style="" class="cbtwittercard_label"><strong> %s</strong></span>', $args['label']);
        $html .= $cbtc_tooltip . '</td>';

        $html .= sprintf('<td><input type="number" style="height: 30px;" class="%1$s-text" id="%2$s-%3$s" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
        $html .= sprintf('<br><span style="" class="description"> %s</span></td>', $args['desc']);

        echo $html;
    }

    /**
     * @param $args
     * call back for check bo x
     */
    public static function cbtwittercard_callback_checkbox($args) {

        global $post;
        $post_id   = $post->ID;
        $cbtc_meta = get_post_meta($post_id, $args['section'], false);
        $value     = '';

        if (is_array($cbtc_meta) && !empty($cbtc_meta) && array_key_exists($args['id'], $cbtc_meta[0])) {
            $value = $cbtc_meta[0][$args['id']];
        }
        if (array_key_exists('tooltip', $args) && $args['tooltip'] != '') {
            $cbtc_tooltip = '<span title="' . $args["tooltip"] . '" class="cbtwittercard_tooltip"><img src="' . CBTWITTERCARDTOOLTIP . '"/></span>';
        } else {
            $cbtc_tooltip = '';
        }

        $html = sprintf('<td><span style="" class="cbtwittercard_label"><strong> %s</strong></span>', $args['label']);
        $html .= $cbtc_tooltip . '</td>';
        $html .= sprintf('<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id']);
        $html .= sprintf('<td><input type="checkbox" class="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" value="on"%4$s />', $args['section'], $args['id'], $value, checked($value, 'on', false));
        $html .= sprintf('<span for="%1$s[%2$s]"> %3$s</span></td>', $args['section'], $args['id'], $args['desc']);

        echo $html;
    }

    /**
     * @param $args
     * call back for text area
     */
    public static function cbtwittercard_callback_textarea($args) {

        global $post;
        $post_id   = $post->ID;
        $cbtc_meta = get_post_meta($post_id, $args['section'], false);
        $value     = '';

        if (is_array($cbtc_meta) && !empty($cbtc_meta) && array_key_exists($args['id'], $cbtc_meta[0])) {
            $value = $cbtc_meta[0][$args['id']];
        }

        if (array_key_exists('tooltip', $args) && $args['tooltip'] != '') {
            $cbtc_tooltip = '<span title="' . $args["tooltip"] . '" class="cbtwittercard_tooltip"><img src="' . CBTWITTERCARDTOOLTIP . '"/></span>';
        } else {
            $cbtc_tooltip = '';
        }
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $html = sprintf('<td><span style="" class="cbtwittercard_label"><strong> %s</strong></span>', $args['label']);
        $html .= $cbtc_tooltip . '</td>';
        $html .= sprintf('<td><textarea style="width: 350px;" rows="5" cols="140" class="%3$s %1$s-text" id="%2$s[%3$s]" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value);
        $html .= sprintf('<br><span class="description"> %s</span></td>', $args['desc']);

        echo $html;
    }

    /**
     * @param $args
     * callback for multiselect box
     */
    public static function cbtwittercard_callback_postselectbox($args) {

        global $post;
        $post_id   = $post->ID;
        $cbtc_meta = get_post_meta($post_id, $args['section'], false);
        $value     = array();

        if (is_array($cbtc_meta) && !empty($cbtc_meta) && array_key_exists($args['id'], $cbtc_meta[0])) {
            $value = $cbtc_meta[0][$args['id']];
        }
        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $html = sprintf('<td><span style="" class="cbtwittercard_label"><strong> %s</strong></span></td>', $args['label']);
        $html .= sprintf('<td><select multiple="yes" style="width: 200px" class="multiselect chosen-select %1$s"  name="%2$s[%3$s][]" id="%2$s[%3$s]">', $size, $args['section'], $args['id']);

        foreach ($args['options'] as $key => $label) {

            $selectedpost = '';
            if (in_array($key, $value)) {

                $selectedpost = 'selected="selected"';
            }
            $html .= sprintf('<option value="%s"%s>%s</option>', $key, $selectedpost, $label);
        }
        $html .= sprintf('</select>');
        $html .= sprintf('<span class="description"> %s</span></td>', $args['desc']);

        echo $html;
    }

    /**
     * @param $args
     * callback for file field
     */
    public static function cbtwittercard_callback_file($args) {

        global $post;
        $post_id   = $post->ID;
        $cbtc_meta = get_post_meta($post_id, $args['section'], false);
        $value     = '';

        if (is_array($cbtc_meta) && !empty($cbtc_meta) && array_key_exists($args['id'], $cbtc_meta[0])) {

            $value = $cbtc_meta[0][$args['id']];
        }

        if (array_key_exists('tooltip', $args) && $args['tooltip'] != '') {
            $cbtc_tooltip = '<span title="' . $args["tooltip"] . '" class="cbtwittercard_tooltip"><img src="' . CBTWITTERCARDTOOLTIP . '"/></span>';
        } else {
            $cbtc_tooltip = '';
        }

        $size         = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $id           = $args['section'] . '[' . $args['id'] . ']';
        $js_id        = $args['section'] . '\\\\[' . $args['id'] . '\\\\]';
        $size         = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $html         = sprintf('<td><span style="" class="cbtwittercard_label"><strong> %s</strong></span>', $args['label']);
        $html .= $cbtc_tooltip . '</td>';
        $html .= sprintf('<td><input type="text" style ="height:30px;margin-right:3px;" class="%1$s-text" id="%2$s-%3$s" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value);
        $dataid       = $args['section'] . '-' . $args['id'];
        $html .= '<input type="button" data-id ="' . $dataid . '" class="button cbtwittercard-wpsa-browse" id="' . $id . '_button" value="Browse" />';
        $preview_html = '';
        //show image preview is set
        if (isset($args['preview']) && $args['preview']) {
            if ($value != '') {
                $preview_html = '<br/><span class = "cbtwittercard-wpsa-review" id="' . $dataid . '_review" ><img style="width:200px; height:200px;" src="' . $value . '"/></span>';
            } else {
                $preview_html = '<br/><span class = "cbtwittercard-wpsa-review" id="' . $dataid . '_review" ></span>';
            }
        }


        $html .= $preview_html;

        echo $html;
    }

    /**
     * Used for meta fields
     *
     * @param $args
     * callback for select field
     */
    public static function cbtwittercard_callback_select($args) {

        /* echo '<pre>';
          print_r($args);
          echo '</pre>'; */

        global $post;
        $post_id   = $post->ID;
        $cbtc_meta = get_post_meta($post_id, $args['section'], false);
        $value     = $args['default'];

        if (is_array($cbtc_meta) && !empty($cbtc_meta) && array_key_exists($args['id'], $cbtc_meta[0])) {
            $value = $cbtc_meta[0][$args['id']];
        }
        if (array_key_exists('tooltip', $args) && $args['tooltip'] != '') {
            $cbtc_tooltip = '<span title="' . $args["tooltip"] . '" class="cbtwittercard_tooltip"><img src="' . CBTWITTERCARDTOOLTIP . '"/></span>';
        } else {
            $cbtc_tooltip = '';
        }

        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $html = sprintf('<td><span style="" class="cbtwittercard_label"><strong> %s</strong></span>', $args['label']);
        $html .= $cbtc_tooltip . '</td>';
        $html .= sprintf('<td><select class="%1$s"  name="%2$s[%3$s]" id="%2$s[%3$s]">', $size, $args['section'], $args['id']);

        foreach ($args['options'] as $key => $label) {

            $html .= sprintf('<option value="%s"%s>%s</option>', $key, selected($value, $key, false), $label);
        }

        $html .= sprintf('</select>');


        $html .= sprintf('<br><span class="description"> %s</span></td>', $args['desc']);

        echo $html;
    }

    /**
     * @param $args
     * call back fo r repeat field
     */
    public static function cbtwittercard_callback_repeat($args) {

        global $post;
        $post_id   = $post->ID;
        $cbtc_meta = get_post_meta($post_id, $args['section'], false);
        $value     = array();

        if (is_array($cbtc_meta) && !empty($cbtc_meta) && array_key_exists($args['id'], $cbtc_meta[0])) {
            $value = $cbtc_meta[0];
        }

        $html_repeat = '';
        $html        = '';
        if (array_key_exists('tooltip', $args) && $args['tooltip'] != '') {
            $cbtc_tooltip = '<span title="' . $args["tooltip"] . '" class="cbtwittercard_tooltip"><img src="' . CBTWITTERCARDTOOLTIP . '"/></span>';
        } else {
            $cbtc_tooltip = '';
        }

        $size = isset($args['size']) && !is_null($args['size']) ? $args['size'] : 'regular';
        $html = sprintf('<span style="" class="cbtwittercard_label"><strong> %s</strong></span>', $args['label']);
        $html .= $cbtc_tooltip . '';

        if (!empty($value)) {

            $value_of_ids = ($value[$args['id']]);

            $html .= '<div class="cbtwittercard-repeatfields-wrapper">';

            foreach ($args['options'] as $index => $option) {

                $html .= '<input class="cbtwittercard-repeatfields-groups" type="hidden" value="' . $option['name'] . '"/>';
            }
            $countlabel = 'count';

            $html .= '<input class="cbtwittercard-repeatfields-count" type="hidden" name="' . $args['section'] . '[' . $args['id'] . '][' . $countlabel . ']" value="' . $value_of_ids['count'] . '"/>';
            $html .= '<ul class="cbtwittercard-repeatfields">';

            if (is_array($value_of_ids) && !empty($value_of_ids)) {

                for ($i = 0; $i < $value_of_ids['count']; $i++) {

                    $html .= '<li>';

                    foreach ($args['options'] as $index => $option) {

                        $value = $value_of_ids[$option['name']][$i];
                        $html .= '<input type="text" style="width:100px;height: 30px;margin-bottom: 5px;" class="regular-text ' . $option['name'] . '" id="' . $args['section'] . $args['id'] . $option['name'] . '[' . $i . ']" name="' . $args['section'] . '[' . $args['id'] . '][' . $option['name'] . '][]" value="' . $value . '"/></td>';
                    }
                    $html .= '<a href="#" class="button cbtwittercard-remove">' . __('Remove', 'cbtwittercard') . '</a>';

                    $html .= '</li>';
                }
            }

            $html .= '</ul>';
            // $html .= '<span class="description">'.$args['desc'] .' </span>';
            $html .= '</div>';

            $html .= '<a href="#" data-section = "' . $args['section'] . '" data-count = "' . $value_of_ids['count'] . '" data-id = "' . $args['id'] . '" class=" button cbtwittercard-add-new">Add New </a>';
            echo $html;
        } else {

            $html .= '<div class="cbtwittercard-repeatfields-wrapper">';

            foreach ($args['options'] as $index => $option) {
                $html .= '<input class="cbtwittercard-repeatfields-groups" type="hidden" value="' . $option['name'] . '"/>';
            }

            $countlabel = 'count';


            $html .= '<input class="cbtwittercard-repeatfields-count" type="hidden" name="' . $args['section'] . '[' . $args['id'] . '][' . $countlabel . ']" value="1" />';

            $html .= '<ul class="cbtwittercard-repeatfields"><li>';
            foreach ($args['options'] as $index => $option) {
                $html .= '<input type="text" style="width:100px;height: 30px;margin-bottom: 5px;" class="regular-text ' . $option['name'] . '" id="' . $args['section'] . $args['id'] . $option['name'] . '[0]" name="' . $args['section'] . '[' . $args['id'] . '][' . $option['name'] . '][]" value=" "/>';
            }
            $html .= '<a href="#" class="button cbtwittercard-remove">' . __('Remove', 'cbtwittercard') . '</a></li>';

            $html .= '</ul>';

            $html .= '</div>';

            $html .= '<a href="#" data-section = "' . $args['section'] . '" data-id = "' . $args['id'] . '" data-count = "0" class=" button cbtwittercard-add-new">' . __('Add New', 'cbtwittercard') . ' </a>';
            echo $html;
        }
    }

}

// end of class

/**
 *
 */
