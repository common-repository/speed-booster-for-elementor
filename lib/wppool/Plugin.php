<?php

/**
 * @WPPool Plugin 
 * @author 		WPPool.dev
 * @version 	3.0.0 
 */

/**
 * Namespace 
 */

namespace WPPOOL;

/**
 * Check ABSOLUTE PATH 
 */

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Plugin Class
 */
if (!class_exists('\WPPOOL\Plugin')) : 

    /**
     * Class Plugin 
     */
    final class Plugin
    {
        /**
         * Plugin slug
         * @var string
         * Example: wp-dark-mode
         */

        protected $slug = 'wp-dark-mode';

        /**
         * User Data
         * @var array 
         */

        protected $user_data = [];
 

        /**
         * Modes
         */
        protected $modes = [
            'development' => [
                'sheet_id' => "1G0aRZ2FExads_2L2Ub44K_dvgHlz0znDnyiLAA4wJxs",
                'tab_id' => 0
            ],
            'production' => [
                "sheet_id" => "1G0aRZ2FExads_2L2Ub44K_dvgHlz0znDnyiLAA4wJxs",
                "tab_id" => 0,
            ]
        ];


        /**
         * CRM Endpoint
         */
        protected $crm_endpoint = 'https://fluent.wppool.dev/wp-json/contact/sync';

        /**
         * CRM Secret token
         */
        protected $crm_token = '66E6D9A59A5A948B';

        /**
         * $Tags
         * @var array 
         */
        protected $tags = [
            'free'          => 11,
            'paid'          => 12,
            'pro'           => 13,
            'ultimate'      => 14,
            'lifetime'      => 15,
            'cancelled'     => 23,
        ];

        /**
         * Lists
         */
        protected $lists =   [
            'wp_markdown_editor'                                => 19,
            'wp_dark_mode'                                      => 20,
            'sheets_to_wp_table_live_sync'                      => 21,
            'easy_video_reviews'                                => 22,
            'jitsi_meet'                                        => 23,
            'zero_bs_accounting'                                => 24,
            'stock_sync_with_google_sheet_for_woocommerce'      => 46,
            'stock_notifier_for_woocommerce'                    => 47,
            'chat_widgets_for_multivendor_marketplaces'         => 26,
            'social_contact_form'                               => 49,
            'elementor_speed_optimizer'                         => 54,
            'easy_email_integration'                            => 55,
            'easy_cloudflare_trunstile'                         => 56,
        ];

        /**
         * Custom Tags
         * @var array  
         */

        protected $custom_tags = [];

        /**
         * Custom Lists
         * @var array 
         */

        protected $custom_lists = [];

        /**
         * Constructor
         * @param string $product_slug
         * @param array $user_data
         */
        public function __construct($product_slug = "", $user_data = [])
        {
            $this->slug = $this->to_slug($product_slug); 
            $this->user_data = is_array($user_data) ? $user_data : [];
        }

        /**
         * To Slug
         * @param string $string
         * @return string 
         */
        public function to_slug($string = "")
        {
            $string = sanitize_title($string);
            $string = str_replace('-', '_', $string);
            return $string;
        }

        /**
         * Get mode
         */
        protected function getMode()
        {
            return defined("WP_DEBUG") && WP_DEBUG ? 'development' : 'production';
        }

        /**
         * Register Hooks 
         */
        public function register_hooks()
        {
            add_action("admin_enqueue_scripts", array($this, 'enqueue_scripts'), 0);
            add_action("admin_footer", [$this, 'load_popup_template'], 10);
        }

        /**
         * Load Popup Template
         */
        public function load_popup_template()
        {
            if (file_exists(__DIR__ . '/Popup.php')) {
                include_once __DIR__ . '/Popup.php';
            }
        }


        /**
         * Enqueue Scripts 
         */
        public function enqueue_scripts()
        {
            $pluginData = $this->getPluginsData();

            wp_register_script('wppool-plugins', '');

            // localize script
            wp_localize_script('wppool-plugins', 'WPPOOL_Plugins', array(
                'plugins'   => $pluginData,
                'debug'      => $this->getMode() == 'development' ? true : false,
            ));

            wp_enqueue_script('wppool-plugins');
        }

        /**
         * Get Sheet URL
         * @return array   
         */
        protected function getSheetURL()
        {
            $sheetId = $this->modes[$this->getMode()]['sheet_id'];
            $tabId = $this->modes[$this->getMode()]['tab_id'];
            return "https://docs.google.com/spreadsheets/export?format=csv&id={$sheetId}&gid={$tabId}";
        }


        /**
         * Get Sheet Data
         * @return array    
         */
        protected function getSheetData()
        {
            $url = $this->getSheetURL();

            $response = wp_remote_get($url);

            if (is_wp_error($response)) {
                return false;
            }

            $response = wp_remote_retrieve_body($response);

            if (!$response) {
                return false;
            }

            $data = array_map("str_getcsv", explode("\n", $response));

            $header = array_shift($data);

            $data = array_map(function (array $row) use ($header) {
                return array_combine($header, $row);
            }, $data);

            // filter plugin is not empty
            $data = array_filter($data, function ($row) {
                return !empty($row['name']);
            });

            return $data;
        }

        /**
         * Get Plugin Data
         * @param string $plugin
         * @return object
         */
        public function getPluginsData()
        {
            $transient = "wppool_plugins_data";

            $data = get_transient($transient);

            if (!$data || $this->getMode() === 'development') {
                $data = $this->getSheetData();

                if (!$data) {
                    return false;
                } 
    
                $transient_time = HOUR_IN_SECONDS;
                set_transient($transient, $data, $transient_time);
            }

            return $data;
        }


        /**
         * FluentCRM Operations
         */

        /**
         * set tag 
         * @param int $tag_id
         * @return $this
         */
        public function setTag(Int $tag_id = null)
        {
            if ($tag_id) {
                $this->custom_tags[] = $tag_id;
            }

            return $this;
        }

        /**
         * set list 
         * @param int $list_id
         * @return $this
         */
        public function setList(Int $list_id = null)
        {
            if ($list_id) {
                $this->custom_lists[] = $list_id;
            }

            return $this;
        }

        /**
         * Remove custom tag
         * @param int $tag_id
         * @return $this
         */

        public function removeTag(Int $tag_id = null)
        {
            if ($tag_id) {
                $this->custom_tags = array_diff($this->custom_tags, [$tag_id]);
            }

            return $this;
        }

        /**
         * Remove custom list
         * @param int $list_id
         * @return $this
         */

        public function removeList(Int $list_id = null)
        {
            if ($list_id) {
                $this->custom_lists = array_diff($this->custom_lists, [$list_id]);
            }

            return $this;
        }

        /**
         * Get Tag id
         * @param string $tag
         * @return int 
         */
        public function getTagId($tag = '')
        {
            $tags = $this->tags;

            if (!isset($tags[$tag])) {
                return false;
            }

            return $tags[$tag];
        }

        /**
         * Get current list 
         * @return int 
         */
        public function getCurrentList()
        {
            $lists = $this->lists;

            if (!isset($lists[$this->slug])) {
                return false;
            }

            return $lists[$this->slug];
        }

        /**
         * Get Lists
         * @return array  
         */
        public function getLists()
        {
            return $this->lists;
        }

        /**
         * Send data to CRM
         * @param array $data
         * @return array    
         */
        protected function makeRequest(array $data = [])
        {
            // check if email isset and email is valid
            if (!isset($data['email']) || !is_email($data['email'])) {
                throw new \Exception("Email is not valid");
            }

            $data = $data ?? $this->user_data;

            $response = wp_remote_post($this->crm_endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->crm_token,
                ],
                'body' => $data,
            ]);

            if (is_wp_error($response)) {
                throw new \Exception($response->get_error_message());
            }

            $response = json_decode(wp_remote_retrieve_body($response), true);

            return $response ?? false;
        }


        /**
         * Subscribe to CRM
         * @param array $data
         * @param string $tag
         * @return array 
         */

        public function subscribe(String $tag = 'free')
        {
            $data = $this->user_data;

            $tag = $this->getTagId($tag);
            $list = $this->getCurrentList();

            $data = array_merge($data, [
                'tags' => [$tag],
                'lists' => [$list],
            ]);

            return $this->makeRequest($data);
        }

        /**
         * Unsubscribe from list
         * @param array $data
         * @return array  
         */
        public function unsubscribePlugin()
        {
            $data = $this->user_data;

            $list = $this->getCurrentList();

            $data = array_merge($data, [
                'remove_lists' => [$list],
            ]);

            return $this->makeRequest($data);
        }

        /**
         * Unsubscribe from tag 
         * @param array $data
         * @param string $tag
         * @return array  
         */
        public function unsubscribeTag(String $tag = 'free')
        {
            $data = $this->user_data;

            $tag = $this->getTagId($tag);

            $data = array_merge($data, [
                'remove_tags' => [$tag],
            ]);

            return $this->makeRequest($data);
        }

        /**
         * Unsubscribe from the CRM
         * @param array $data
         * @return array  
         */
        public function unsubscribe()
        {
            $data = $this->user_data;

            $data = array_merge($data, [
                'status' => 'unsubscribed',
            ]);

            return $this->makeRequest($data);
        }
    }

    /**
     * Instantiate the class 
     */
    // plugins_loaded 
    add_action("plugins_loaded", function(){
        $plugin = new Plugin();
        $plugin->register_hooks(); 
    });
    

endif;

