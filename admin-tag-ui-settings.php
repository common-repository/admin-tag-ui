<?php
if ( ! defined( 'WPINC' ) ) { exit; }

/**
 * Prepares and renders the settings page (plugins.php?page=atui-page)
 */
class Admin_Tag_UI_Settings_Page
{
    private $plugin;
    private $option;
    private $page_slug = 'atui-page';

    public function __construct( $plugin )
    {
        $this->plugin = $plugin;
        $this->option = get_option( 'atui_settings' );

        add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
        add_action( 'admin_init', array( $this, 'admin_settings_init' ) );
        add_action( 'admin_head', array( $this, 'add_settings_style_to_head' ) );
    }


    /**
     * Creates admin page. Pass in null to avoid menu item from appearing
     * - action admin_menu - __construct()
     */
    public function register_settings_page()
    {
        add_submenu_page(
            null,
            'Admin Tag UI Settings',
            'Admin Tag UI Settings',
            'manage_options',
            $this->page_slug,
            array( $this, 'display_settings_page' )
        );
    }


    /**
     * Uses settings API to register fields, section and setting(option)
     * - action admin_init - __construct()
     */
    public function admin_settings_init()
    {
        // add_settings_field( $id, $title, $callback, $page, $section, $args);
        add_settings_field( 'number_to_show',    'Number of tags to show',    array( $this, 'field_number_to_show' ),    $this->page_slug, 'settings_section' );
        add_settings_field( 'number_of_columns', 'Number of columns',         array( $this, 'field_number_of_columns' ), $this->page_slug, 'settings_section' );
        add_settings_field( 'tag_size',          'Tag size based on usage',   array( $this, 'field_tag_size' ),          $this->page_slug, 'settings_section' );
        add_settings_field( 'auto_reveal',       'Automatically reveal tags', array( $this, 'field_auto_reveal' ),       $this->page_slug, 'settings_section' );

        // add_settings_section( $id, $title, $callback, $page );
        add_settings_section( 'settings_section', '', array( $this, 'settings_section_display' ), $this->page_slug );

        //register_setting( $option_group, $option_name, $callback );
        register_setting( 'atui_settings_group', 'atui_settings', array( $this, 'sanitize_values' ) );
    }


    public function field_number_to_show()
    {
        echo "<input type='radio' id='number_to_show_all' name='atui_settings[number_to_show]' value='all' " . checked( $this->option['number_to_show'], 'all', false ) . " />";
        echo "<label for='number_to_show_all'>Show all tags</label><br>";

        echo "<input type='radio' id='number_to_show_wp_default' name='atui_settings[number_to_show]' value='wp_default' " . checked( $this->option['number_to_show'], 'wp_default', false ) . " />";
        echo "<label for='number_to_show_wp_default'>Only show most used (the WordPress default)</label>";

        echo "<p class='description'>By default, the admin tag section only shows the top used tags. This setting allows all tags to be shown.</p>";
    }


    public function field_number_of_columns()
    {
        echo "<input type='radio' id='number_of_columns_1' name='atui_settings[number_of_columns]' value='1' " . checked( $this->option['number_of_columns'], '1', false ) . " />";
        echo "<label for='number_of_columns_1'>1</label><br>";

        echo "<input type='radio' id='number_of_columns_2' name='atui_settings[number_of_columns]' value='2' " . checked( $this->option['number_of_columns'], '2', false ) . " />";
        echo "<label for='number_of_columns_2'>2</label>";

        echo "<p class='description'>The tags are shown within a list. The number specifies how many columns the tag list should be.</p>";
    }


    public function field_tag_size()
    {
        echo "<input type='radio' id='tag_size_same' name='atui_settings[tag_size]' value='same' " . checked( $this->option['tag_size'], 'same', false ) . " />";
        echo "<label for='tag_size_same'>Make tags the same size</label><br>";

        echo "<input type='radio' id='tag_size_change' name='atui_settings[tag_size]' value='change' " . checked( $this->option['tag_size'], 'change', false ) . " />";
        echo "<label for='tag_size_change'>Change tag size based on usage (the WordPress default)</label>";

        echo "<p class='description'>By default, WordPress makes the tag smaller or larger based on usage. The plugin makes the tags stay the same size. You may re-enable the size change here.</p>";
    }


    public function field_auto_reveal()
    {
        echo "<input type='checkbox' id='atui_settings[auto_reveal]' name='atui_settings[auto_reveal]' value='1' " . checked( $this->option['auto_reveal'], 1, false ) . " />";
        echo "<label for='atui_settings[auto_reveal]'>Enable</label><br>";
        echo "<p class='description'>The 'choose from' tags will be automatically revealed. Otherwise, the 'Choose from most used tags' must be clicked.</p>";
    }


    public function settings_section_display()
    {
        echo '<section id="settings-section">';
        echo "<p>Please use the following settings to tweak how the plugin changes the admin tag section</p>";
    }


    /**
     * Callback function which sanitizes the values before saving to the options table in the database.
     * If checkbox has not been selected, then store 0 in the db. Otherwise the value
     * is deleted in the db and causes a PHP notice upon showing the page again.
     *
     * @param $value
     * @return mixed
     */
    public function sanitize_values( $value )
    {
        $value['number_to_show']    = isset( $value['number_to_show'] )    ? $value['number_to_show']    : $this->plugin->default_settings( 'number_to_show' );
        $value['number_of_columns'] = isset( $value['number_of_columns'] ) ? $value['number_of_columns'] : $this->plugin->default_settings( 'number_of_columns' );
        $value['tag_size']          = isset( $value['tag_size'] )          ? $value['tag_size']          : $this->plugin->default_settings( 'tag_size' );
        $value['auto_reveal']       = isset( $value['auto_reveal'] )       ? $value['auto_reveal']       : 0;

        return $value;
    }


    /**
     * Renders the settings page
     */
    public function display_settings_page()
    {
        echo '<div class="wrap">';

            echo "<h1>Admin Tag UI - Settings</h1>";

            settings_errors();

            echo '<div class="atui-admin-settings-main">';
                echo '<form method="post" action="options.php">';

                do_settings_sections( $this->page_slug );
                settings_fields( 'atui_settings_group' );

                echo '</section>';

                submit_button();

                echo '</form>';
            echo '</div>';

            echo '<div class="atui-admin-settings-support">';
                echo '<h2>Support us</h2>';
                echo '<p>Please consider supporting us</p>';
                echo '<p><span class="dashicons dashicons-star-filled"></span> <a href="https://wordpress.org/support/plugin/admin-tag-ui/reviews/?filter=5" target="_blank">Rate</a> on WordPress</p>';
                echo '<p><span class="dashicons dashicons-admin-plugins"></span> <a href="https://profiles.wordpress.org/divspark/#content-plugins" target="_blank">View more</a> plugins</p>';
            echo '</div>';
        echo '</div>';
    }

    public function add_settings_style_to_head()
    {
        $output = <<<HTML
            
        <style type="text/css">                   
            .atui-admin-settings-main {       
                
                padding: 0 12px 12px 12px;
                border: 1px solid #ccc;
                margin-top: 10px;

                float: left;
                width: 65%;
                max-width: 1200px;
                
                background-color: white;
            }
            
            .atui-admin-settings-support {
        
                padding: 0 12px 12px 12px;
                border: 1px solid #ccc;
                margin-top: 10px;
        
                float:right;
                width: 25%;
                background-color: white;
            }
        </style>
HTML;

        echo $output;
    }
}
