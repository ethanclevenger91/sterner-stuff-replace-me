<?php
/**
 * Plugin Name: Replace Me
 * Description: Core functionality for the Replace Me site.
 * Author: Sterner Stuff
 * Author URI: https://sternerstuff.dev
 */

class ReplaceMe
{
    private static $self = false;

    public function __construct()
    {
        $this->registerPostTypes();
        $this->registerTaxonomies();
        $this->configureACF();
    }

    private function registerPostTypes()
    {
        // Register custom post types. See https://wp-cli.org/commands/scaffold/post-type/ for generation instructions
        $post_types = glob(dirname(__FILE__) . '/post-types/*.php');
        foreach ($post_types as $post_type) {
            require_once($post_type);
        }
    }

    private function registerTaxonomies()
    {
        // Register custom taxonomies. See https://wp-cli.org/commands/scaffold/taxonomy/ for generation instructions.
        $taxonomies = glob(dirname(__FILE__) . '/taxonomies/*.php');
        foreach ($taxonomies as $tax) {
            require_once($tax);
        }
    }

    private function configureACF()
    {
        // Set the acf-json desintation to here
        add_filter( 'acf/settings/save_json', function ($path) {
            return dirname(__FILE__) . '/acf-json';
        } );
        // Include the /acf-json folder in the places to look for ACF Local JSON files
        add_filter( 'acf/settings/load_json', function ($paths) {
            $paths[] = dirname(__FILE__) . '/acf-json';
            return $paths;
        } );

        add_action( 'wp_loaded', [ $this, 'addACFOptions' ] );
    }

    public function addACFOptions()
    {
        //Add an ACF Options Page
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page(array(
                'page_title' => 'Site General Settings',
                'menu_title' => 'Site Settings',
                'menu_slug' => 'site-general-settings',
                'capability' => 'edit_site_settings',
                'icon_url' => 'dashicons-admin-site'
            ));
        }
    }

    public static function acfSettingsCaps()
    {
        $role = get_role('administrator');
        $role->add_cap('edit_site_settings');
        $role = get_role('editor');
        $role->add_cap('edit_site_settings');
    }

    //Keep this method at the bottom of the class
    public static function getInstance()
    {
        if (!self::$self) {
            self::$self = new self();
        }
        return self::$self;
    }
}

ReplaceMe::getInstance();

register_activation_hook(__FILE__, ['ReplaceMe', 'acfSettingsCaps']);
