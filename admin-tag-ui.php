<?php
/*
* Plugin Name:       Admin Tag UI
* Plugin URI:        https://wordpress.org/plugins/admin-tag-ui/
* Description:       Changes appearance of tag sections in the admin backend post pages.
* Version:           1.1.4
* Author:            DivSpark
* Author URI:        https://profiles.wordpress.org/divspark/#content-plugins
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       admin-tag-ui
*/

if ( ! defined( 'WPINC' ) ) { exit; }

class Admin_Tag_UI_Plugin
{
    const version = '1.1.4';

    private $settings;

	public function __construct()
    {
        $this->settings = get_option( 'atui_settings' );

        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ) );

        add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );

        register_activation_hook( __FILE__, array( $this, 'install_plugin' ) );

        if ( $this->settings['number_to_show'] == 'all' ) {
            add_filter( 'get_terms_args',  array( $this, 'show_all_tags' ) );
        }

        $current_page = basename( $_SERVER['PHP_SELF'] );
        
        // only run on add/edit post pages
        if ( ( $current_page == 'post.php' || $current_page == 'post-new.php' ) && ( ! isset( $_GET['post_type'] ) || $_GET['post_type'] != 'page' ) )
        {
            add_action( 'admin_head', array( $this, 'add_style_to_head' ) );
            add_action( 'admin_footer-post.php', array( $this, 'add_script_to_footer' ) );
            add_action( 'admin_footer-post-new.php', array( $this, 'add_script_to_footer' ) );
        }

        // only run on settings page
        if ( ( $current_page == 'plugins.php' && isset( $_GET['page'] ) && $_GET['page'] == 'atui-page' ) || $current_page == 'options.php' )
        {
            require_once ( plugin_dir_path( __FILE__ ) . 'admin-tag-ui-settings.php' );
            $settings_page = new Admin_Tag_UI_Settings_Page( $this );
        }
	}

    /**
     * Removes the limit on the number of tags to show
     * - filter get_terms_args - __construct()
     * @param $args
     * @return mixed
     */
    public function show_all_tags( $args )
    {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && $_POST['action'] === 'get-tagcloud' )
        {
            $args['number'] = 0;
            $args['hide_empty'] = 0;
        }

        return $args;
    }

    /**
     * Adds a settings link to the plugin's actions under plugins.php
     * - filter plugin_action_links_ . plugin_basename( __FILE__ ) - __construct()
     * @param $links
     * @return array
     */
    public function add_plugin_action_links( $links )
    {
        $add_links = array();
        $add_links[] = '<a href="' . admin_url( 'plugins.php?page=atui-page' ) . '">Settings</a>';
        return array_merge( $add_links, $links );
    }

    /**
     * Adds a view more link to the plugin's meta under plugins.php
     * - filter plugin_row_meta - __construct()
     * @param $links
     * @param $file
     * @return array
     */
    public function add_plugin_row_meta( $links, $file )
    {
        $plugin = plugin_basename( __FILE__ );
        $add_links = array();

        if ( $file == $plugin ) {
            $add_links[] = '<a href="https://profiles.wordpress.org/divspark/#content-plugins">View more plugins</a>';
        }

        return array_merge( $links, $add_links );
    }

    /**
     * Default settings for the plugin. Returns an array if no parameters passed in. Pass in a name to get the default for that setting.
     * @param string $setting
     * @return array|mixed
     */
    public function default_settings( $setting = '' )
    {
        $default = array(
	        'number_to_show'    => 'all',
            'number_of_columns' => 2,
            'tag_size'          => 'same',
            'auto_reveal'       => 1,
        );

        if ( empty( $setting ) ) {
            return $default;
        }
        else {
            return $default[ $setting ];
        }
    }

    /**
     * Adds css <style> to the head section of the page
     * - action admin_head - __construct()
     */
    public function add_style_to_head()
    {
        $columns = $this->settings['number_of_columns']; // 1 or 2

        /** the tag size setting changes whether the tags should be larger or smaller based on usage */
        $font_size = $this->settings['tag_size'] == 'same'      ? 'font-size: 15px !important;' : '';
        $margin    = $this->settings['tag_size'] == 'same'      ? 'margin: 5px 0;'              : 'margin: 3px 0;';
        $visible   = $this->settings['number_to_show'] == 'all' ? 'hidden'                      : 'visible';
        
        $output = <<<HTML
        
        <style type="text/css">
            /**
             * Multi columns
             * http://stackoverflow.com/questions/5314726/
             * CSS multi-column layout of list items does not align properly in Chrome
             * The inline-block style causes some items to bunch up on one line within a column. A width
             * of 100% prevents anything else on the line from appearing.
             */
             
             /** .the-tagcloud - Unselected tags */
            .the-tagcloud { 
                column-count: {$columns};
                -webkit-column-count: {$columns};
                -moz-column-count: {$columns};
            }
            .the-tagcloud a {
                display: inline-block;
                width: 100%;
                word-wrap: break-word;
            }
            .the-tagcloud ul li {
                display: block;
            }
            
            /** .tagchecklist - Currently selected tags */ 
            .tagchecklist {
                column-count: {$columns};
                -webkit-column-count: {$columns};
                -moz-column-count: {$columns};
                overflow: hidden; /* horizontal scrollbar appears in firefox */
            } 
            .tagchecklist > span,
            .tagchecklist > li {
                display: inline-block;
                width: 100%;
                word-wrap: break-word;
            }
            
            /**
             * List style - suppress tag cloud look    
             */
            .the-tagcloud a {
                {$font_size}
                {$margin}                               
                word-spacing: 1px; /* default is 3px */
                line-height: 1.3em;
            }
             
            .tagchecklist > span,
            .tagchecklist > li {
                /* default is left. However, the float causes problesm in firefox */
                float: none; 
                font-size: 15px;
                line-height: 1.3em;
                /*margin: 4px 0;*/
            }
            .tagchecklist .ntdelbutton {
                /* raises the delete tag button to match the larger font size */
                /* tried to find the right balance between chrome (5px) and firefox (2px) */
                padding-bottom: 4px;
            }
        
            /** Used when hovering over delete tag button */
            .atui-delete-hover {
                color: #c00;
            }
            
            /** Used to show a tag has been selected */
            .atui-tag-selected {
                font-weight: bold;
                color: #124964;
            }
            
            .tagcloud-link {
                visibility: {$visible};
            }
        </style>
HTML;

        echo $output;
    }

    /**
     * Adds jquery to the footer
     * Needs to be in the footer otherwise the click() command will run too early.
     *
     * - action admin_footer-post.php - __construct()
     * - action admin_footer-post-new.php - __construct()
     */
    public function add_script_to_footer()
    {
        $auto_reveal = $this->settings['auto_reveal'] ? 'jQuery( ".tagcloud-link" ).click();' : '';

        if ( $this->settings['number_to_show'] == 'all' )
        {
            $all = __( 'all', 'admin-tag-ui' );
            $tag_link_wording = "jQuery( '.tagcloud-link' ).each( function() {\n";
                $tag_link_wording .= "var tag_wording = jQuery( this ).text().replace( 'the most used', '{$all}' );\n";
                $tag_link_wording .= "jQuery( this ).text( tag_wording );\n";
                $tag_link_wording .= "jQuery( this ).css( 'visibility', 'visible' );\n";
            $tag_link_wording .= "});";
        }
        else
        {
            $tag_link_wording  = '';
        }

        $output = <<<HTML
        
        <script>
            jQuery( document ).ready( function() {
                
                function atui_check_tag_selection() 
                {
                    // Removes the selected class from all tags
                    jQuery( '.the-tagcloud a[class*=tag-link-]' ).each( function() {     
                            jQuery( this ).removeClass( 'atui-tag-selected' );
                    });
                    
                    // find the tag boxes within the screen. ^= is starts with. Also needs .postbox otherwise will incorrectly match other elements
                    jQuery( 'div[id^="tagsdiv-"].postbox' ).each( function( i, tagsdiv ) 
                    {
                        // Adds the selected class back to tags which have been selected
                        jQuery( tagsdiv ).find( '.tagchecklist span.screen-reader-text' ).each( function() 
                        {
                            selected_tag_text = jQuery( this ).text().replace( 'Remove term: ', '' ).trim().toLowerCase();
                            
                            // console.log( 'selected tag: ' + jQuery( this ).text().replace( 'Remove term: ', '' ).trim().toLowerCase() + ' ' );
                            
                            jQuery( tagsdiv ).find( '.the-tagcloud a[class*=tag-link-]' ).filter( function() { 
                                      
                                // console.log( 'selectable tag: ' + jQuery( this ).text().trim().toLowerCase() + ' ' );
                                
                                if ( jQuery( this ).text().trim().toLowerCase() === selected_tag_text ) {
                                    
                                    jQuery( this ).addClass( 'atui-tag-selected' );
                                    
                                    // console.log( 'matching tag: ' + jQuery( this ).text().trim().toLowerCase() + ' ' );
                                }
                            });
    
                        }); 
                    });
                }
                
                
                jQuery( '.tagchecklist' ).on(
                {
                    mouseenter: function() {
                        jQuery( this ).parent( 'span' ).addClass( 'atui-delete-hover' ); // prior to wordpress 4.9
                        jQuery( this ).parent( 'li' ).addClass( 'atui-delete-hover' );   // wordpress 4.9+
                    },
                    
                    mouseleave: function() {
                        jQuery( this ).parent( 'span' ).removeClass( 'atui-delete-hover' ); // prior to wordpress 4.9
                        jQuery( this ).parent( 'li' ).removeClass( 'atui-delete-hover' );   // wordpress 4.9+
                    },
                    
                    mouseup: function() {
                       setTimeout( function() {
                            atui_check_tag_selection();
                       }, 100);
                    }
                    
                }, '.ntdelbutton' );
                                
                /** Disabling the newtag input prevents focus from jumping to it */
                function atui_disable_newtag() {
                    jQuery( 'input.newtag' ).prop( 'disabled', true );
                }
                
                /**
                 * setTimeout() re-queues the statement at the end of the execution queue
                 * Allows other events to finish (e.g. showing the newly selected tag) before re-enabling input
                 */
                function atui_enable_newtag() 
                { 
                    setTimeout( function() {
                        jQuery( 'input.newtag' ).prop( 'disabled', false );
                        jQuery( this ).blur();
                    }, 100);
                }
                
                jQuery( '.postbox-container' ).on(
                {
                    mousedown: function() {
                       atui_disable_newtag();
                    },
                    
                    keydown: function(e) {
                        // 13 is the enter key
                        if( e.which == 13 ) {
                            atui_disable_newtag();
                        }  
                    },
                                       
                    mouseup: function() {
                        atui_enable_newtag();
                        setTimeout( function() {
                            atui_check_tag_selection();
                        }, 100);
                    },
                    
                    keyup: function(e) {
                        // 13 is the enter key
                        if( e.which == 13 ) {
                            atui_enable_newtag();
                            setTimeout( function() {
                                atui_check_tag_selection();
                            }, 100);
                        }  
                    }
                    
                }, '.the-tagcloud a[class*=tag-link-]' );
                
                // highlights tags after clicking "Choose from all tags" 
                jQuery( '.postbox-container' ).on(
                { 
                    mouseup: function() {
                       setTimeout( function() {
                            atui_check_tag_selection();
                       }, 900); 
                    }
                }, '.tagcloud-link' );
                
                // highlights tags after clicking "Add" button 
                jQuery( '.postbox-container' ).on(
                { 
                    mouseup: function() {
                       setTimeout( function() {
                            atui_check_tag_selection();
                       }, 100); 
                    }
                }, '.tagadd' );    
                
                {$auto_reveal}
             
                {$tag_link_wording}
                                
                setTimeout( function() {
                    atui_check_tag_selection();
                }, 900);
            });
                     
        </script>

HTML;

        echo $output;
    }

    /**
     * Adds array of default settings to an option in the wp options table
     * - register_activation_hook __FILE__
     */
    public function install_plugin()
    {
        add_option( 'atui_settings', $this->default_settings() );
    }
}

if ( is_admin() ) {
    $admin_tag_ui_plugin = new Admin_Tag_UI_Plugin();
}