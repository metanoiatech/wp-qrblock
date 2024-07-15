<?php

if ( !class_exists( 'ACF_Custom' ) ) {

	class ACF_Custom {
		
        private $_param_enable_key = "params_enable";
        private $_general_settings_key = "qr_general_settings";
        private $_same_all_instances_key = "same_all_instances";

        public function __construct() {
			add_action( 'admin_init', array( &$this, 'add_acf_form_head') );
			add_action( 'admin_menu', array( &$this, 'add_qr_reader_menu' ) );
			add_filter( 'acf/load_field/name=pages', array( &$this, 'set_pages_field' ), 10, 1 );//hook for loading a field of which name is pages
			add_action( 'current_screen', array( &$this, 'qr_reader_settings_page_init' ) );
			add_action( 'acf/save_post', array( &$this, 'save_param_enable_fields' ), 10, 1 );

		}

        private function _get_acf_field_key($field_name) {
			$field = acf_get_field($field_name);
			if ($field) {
				return $field['key'];
			}
			return '';
		}

        private function _get_acf_field_group_key($title) {
			if (!function_exists('acf_get_field_groups')) {
				return false;
			}
			
			$field_groups = acf_get_field_groups();
			foreach ($field_groups as $field_group) {
				if ($field_group['title'] === $title) {
					return $field_group['key'];
				}
			}
			
			return false;
		}

        function add_qr_reader_menu() {
			add_menu_page(
				'QR Reader', // Page title
				'QR Reader', // Menu title
				'manage_options', // Capability
				'qr-reader',  // Menu slug
				array(&$this, 'display_qr_reader_general_settings'), 
				'dashicons-admin-generic', // Icon URL or Dashicon class
				6                    // Position
			);

			add_submenu_page(
				'qr-reader',  // Parent slug
				'QR Reader Settings',  // Page title
				'General Settings',           // Menu title
				'manage_options',    // Capability
				'qr-reader-general-settings', // Menu slug
				array(&$this, 'display_qr_reader_general_settings')
			);

			add_submenu_page(
				'qr-reader',  // Parent slug
				'QR Reader Settings',  // Page title
				'Page Settings',           // Menu title
				'manage_options',    // Capability
				'qr-reader-page-settings', // Menu slug
				array(&$this, 'display_qr_reader_page_settings')
			);

			remove_submenu_page('qr-reader', 'qr-reader');
		}

        function add_acf_form_head() {
            if (!isset($_GET['page'])) {
                return;
            }
			if ($_GET['page'] === 'qr-reader-general-settings' || $_GET['page'] === 'qr-reader-page-settings') {
				acf_form_head();
			}
		}

        //load acf fields for "QR General Settings" acf group
        function display_qr_reader_general_settings() {
			$field_group_key = $this->_get_acf_field_group_key("QR General Settings");
			?>
				<div class="wrap">
					<h1>QR Reader Settings</h1>
					<?php
						acf_form([
							'post_id' => 'qr_general_settings', 
							'field_groups' => [$field_group_key],
							'submit_value' => 'Save Settings', 
						]);  
					?>
				</div>
			<?php
		}

        //load acf fields for "QR Page Settings" acf group
		function display_qr_reader_page_settings() {
			$field_group_key = $this->_get_acf_field_group_key("QR Page Settings");
			?>
				<div class="wrap">
					<h1>QR Reader Settings</h1>
					<?php
						acf_form([
							'post_id' => 'param_enable_settings', 
							'field_groups' => [$field_group_key],
							'submit_value' => 'Save Settings', 
						]);  
					?>
				</div>
			<?php
		}

        //set options to 'pages' field of select type by pages info
        function set_pages_field( $field ) {
            $field['choices'] = [];

            $pages = get_pages();
            foreach ($pages as $page) {
                $value = $page->ID;
                $label = $page->post_title;
                $field['choices'][ $value ] = $label;
            }

            return $field;
        }

        //add js to user qr reader settings pages
		function qr_reader_settings_page_init() {
            if (!is_admin()) {
                return;
            }

			$current_screen = get_current_screen();
			if ($current_screen && $current_screen->base == 'qr-reader_page_qr-reader-general-settings') {
				$show_debug_data_field_key = $this->_get_acf_field_key('show_debug_data');
                $header_text_field_key = $this->_get_acf_field_key('header_text');
                $info_text_field_key = $this->_get_acf_field_key('info_text');

                $plugin_dir_path = plugin_dir_url(qr_reader_plugin_file);
                wp_register_script('qrReaderGeneralSettings_js', $plugin_dir_path . 'src/asset/js/qrReaderGeneralSettings.js', [], qr_reader_version, true);
                wp_enqueue_script('qrReaderGeneralSettings_js');
                wp_localize_script('qrReaderGeneralSettings_js', 'general_settings', [
                    'show_debug_data_field_key' => $show_debug_data_field_key,
                    'header_text_field_key' => $header_text_field_key,
                    'info_text_field_key' => $info_text_field_key,
                ]);
			} else if ($current_screen && $current_screen->base == 'qr-reader_page_qr-reader-page-settings') {
                $same_all_instances_field_key = $this->_get_acf_field_key('same_for_all_instances');
				$pages_field_key = $this->_get_acf_field_key('pages');
                $team_id_enable_field_key = $this->_get_acf_field_key('team_id_enable');
                $minecraft_id_enable_field_key = $this->_get_acf_field_key('minecraft_id_enable');
                $server_id_enable_field_key = $this->_get_acf_field_key('server_id_enable');
                $game_id_enable_field_key = $this->_get_acf_field_key('game_id_enable');
                $group_id_enable_field_key = $this->_get_acf_field_key('group_id_enable');
                $gamipress_ranks_enable_field_key = $this->_get_acf_field_key('gamipress_ranks_enable');
                $gamipress_points_enable_field_key = $this->_get_acf_field_key('gamipress_points_enable');

                $plugin_dir_path = plugin_dir_url(qr_reader_plugin_file);
                wp_register_script('qrReaderPageSettings_js', $plugin_dir_path . 'src/asset/js/qrReaderPageSettings.js', array('jquery'), qr_reader_version, true);
                wp_enqueue_script('qrReaderPageSettings_js');
                wp_localize_script('qrReaderPageSettings_js', 'param_enable', [
                    'same_all_instances_field_key' => $same_all_instances_field_key,
                    'pages_field_key' => $pages_field_key,
                    'team_id_enable_field_key' => $team_id_enable_field_key,
                    'minecraft_id_enable_field_key' => $minecraft_id_enable_field_key,
                    'server_id_enable_field_key' => $server_id_enable_field_key,
                    'game_id_enable_field_key' => $game_id_enable_field_key,
                    'group_id_enable_field_key' => $group_id_enable_field_key,
                    'gamipress_ranks_enable_field_key' => $gamipress_ranks_enable_field_key,
                    'gamipress_points_enable_field_key' => $gamipress_points_enable_field_key,
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('ajax_nonce')
                ]);
			}
		}

        //update param enable settings
		function save_param_enable_fields($post_id) {
			// delete_option($this->_param_enable_key);
            // delete_option($this->_general_settings_key);
			// return false;

            if (!is_admin()) {
                return;
            }

			if ($post_id === 'param_enable_settings') {
                $same_all_instances = get_field('same_for_all_instances');
                $page_id = get_field('pages', $post_id);
                $team_id_enable = get_field('team_id_enable', $post_id);
                $minecraft_id_enable = get_field('minecraft_id_enable', $post_id);
                $server_id_enable = get_field('server_id_enable', $post_id);
                $game_id_enable = get_field('game_id_enable', $post_id);
                $group_id_enable = get_field('group_id_enable', $post_id);
                $gamipress_ranks_enable = get_field('gamipress_ranks_enable', $post_id);
                $gamipress_points_enable = get_field('gamipress_points_enable', $post_id);

                $param_enable = get_option($this->_param_enable_key, []);
                if ($same_all_instances === "1") {
                    $page_id = 'all';
                }
                $param_enable[$page_id] = array(
                    'team_id_enable' => $team_id_enable,
                    'minecraft_id_enable' => $minecraft_id_enable,
                    'server_id_enable' => $server_id_enable,
                    'game_id_enable' => $game_id_enable,
                    'group_id_enable' => $group_id_enable,
                    'gamipress_ranks_enable' => $gamipress_ranks_enable,
                    'gamipress_points_enable' => $gamipress_points_enable,
                );

                update_option($this->_param_enable_key, $param_enable);
                update_option($this->_same_all_instances_key, $same_all_instances);
			} else if ($post_id === 'qr_general_settings') {
                $header_text = get_field('header_text', $post_id);
                $show_debug_data = get_field('show_debug_data', $post_id);
                $info_text = get_field('info_text', $post_id);

                $general_settings = array(
                    'header_text' => $header_text,
                    'show_debug_data' => $show_debug_data,
                    'info_text' => $info_text,
                );

                update_option($this->_general_settings_key, $general_settings);
            }
		}
		
	}

}