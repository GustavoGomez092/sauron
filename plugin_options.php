<?php

class struck_plugin_options
{

  protected $plugin_options_page = '';

  /**
   * Initialize hooks.
   */
  public function init()
  {
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    add_action('admin_init', array($this, 'register_plugin_settings'));
    add_action('admin_menu', array($this, 'create_admin_menu_page'));
    add_action('acf/init', array($this, 'add_struck_settings'));
    add_action('acf/include_fields', array($this, 'settings_acf_fields'));
    add_action('admin_enqueue_scripts', array($this, 'acf_settings_css'));
  }



  public function register_plugin_settings()
  {
    register_setting('struck-settings-group', 'struck-plugin');
  }

  /**
   *
   * Create new plugin options page under the Settings menu.
   */
  public function create_admin_menu_page()
  {
    $svg_logo = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCA5MjUgMTI4MCI+CiAgPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI5LjIuMSwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDIuMS4wIEJ1aWxkIDExNikgIC0tPgogIDxkZWZzPgogICAgPHN0eWxlPgogICAgICAuc3QwIHsKICAgICAgICBmaWxsOiAjZmZmOwogICAgICB9CiAgICA8L3N0eWxlPgogIDwvZGVmcz4KICA8cGF0aCBjbGFzcz0ic3QwIiBkPSJNNDU4LjUsMjIwLjZjLTMuMi4zLTEwLjYuOS0xNi40LDEuMy02OS41LDUuMS0xMjguOCwyNy4zLTE3OC41LDY2LjgtOS41LDcuNS0zMC40LDI4LjItMzcuMiwzNi45LTIzLjUsMjkuNy0zOC4xLDYxLjQtNDUuNCw5OC41LTYuNywzNC02LjcsNzMuNSwwLDEwNi4zLDExLjIsNTQuOSw0Mi41LDEwMCw5Mi4yLDEzMi43LDI1LjEsMTYuNSw3NC4yLDM2LDE2NS4xLDY1LjUsMzcuOSwxMi4zLDQ4LjQsMTYuMiw2Mi4zLDIzLjEsMjIsMTAuOSwzNS40LDIyLjksMzkuOCwzNS42LDIuNiw3LjUuOSwyNS4xLTMuNSwzNS42LTguOSwyMS4zLTMxLDM0LjEtNjcuNSwzOS4xLTE2LjEsMi4yLTQxLjQsMi01Ny41LS40LTM4LjQtNS43LTc0LjEtMjUuOS0xMTkuNy02Ny41LTMuOC0zLjQtNy4yLTYuMy03LjYtNi4zcy0yNi4xLDMxLjQtNTcuMSw2OS43Yy0zMSwzOC40LTU5LjEsNzMuMS02Mi41LDc3LjJsLTYsNy40djkuNmwxMy4zLDEwLjJjNDEuMywzMS42LDcyLjcsNTEuMiwxMDUuOCw2Ni4yLDQyLjcsMTkuMiw4Mi44LDI3LjQsMTUyLjUsMzEuMiwyMSwxLjEsMjkuNSwxLjIsNDAsLjQsNC4yLS40LDExLjgtMSwxNi45LTEuNCw3My01LjcsMTM2LjQtMzAuOSwxOTItNzYsOC4xLTYuNiwyOS41LTI3LjgsMzUuMi0zNC45LDI4LjItMzQuOSw0NC4yLTcyLjksNDktMTE2LjIsMi41LTIyLjMsMi43LTI4LjYsMS40LTQ1LjUtLjYtOC43LTEuNi0xOS4zLTIuMS0yMy40LTctNTUuNy0yNy05OS42LTYxLjktMTM1LjQtMTIuOS0xMy4zLTMwLjUtMjcuNC00NC0zNS40LTI3LjEtMTYuMS03OS4yLTM2LTE1NS4zLTU5LjUtMzkuMy0xMi4xLTQ5LjctMTUuOS02NS4yLTIzLjgtMjEuNi0xMS0zMy40LTIyLjYtMzUuOC0zNS40LS45LTQuNy4xLTEzLjMsMi4yLTE5LjYsNi4yLTE4LjIsMjYuNy0zMC45LDU3LjQtMzUuMiwxMC40LTEuNCwzMy40LTEuNCw0NS4yLDAsMjguNCwzLjYsNTMuMiwxMi4zLDc3LjUsMjcsOS41LDUuOCwxOC4zLDEyLjIsMjkuOSwyMS45LDUuMiw0LjMsOS44LDcuOCwxMC4xLDcuNy40LS4xLDI5LjgtMzUuMyw2NS41LTc4LjIsNTAuOS02MS4zLDY0LjYtNzguMiw2NC03OC44LTMuOS0zLjctMzYtMjUuNS00OS44LTMzLjktNDQuNC0yNy05Ny4yLTQ2LjktMTQ4LjMtNTUuOS0xOC43LTMuMy0zMC4xLTQuNi01NC44LTYuMy0xOS0xLjMtMzEuMS0xLjUtNDEuNC0uN1oiLz4KPC9zdmc+';
    $this->plugin_options_page = add_menu_page('Plugin options', 'Struck Logger', 'manage_options', 'struck-logs', array($this, 'render_plugin_options_page'), $svg_logo);
  }

  function add_struck_settings()
  {

    // Check function exists.
    if (function_exists('acf_add_options_sub_page')) {

      acf_add_options_page(array(
        'page_title' => 'Settings',
        'menu_title' => 'Settings',
        'menu_slug' => 'struck-settings',
        'parent_slug' => 'struck-logs',
        'position' => '',
        'redirect' => false,
      ));
    }
  }

  public function settings_acf_fields()
  {

    if (!function_exists('acf_add_local_field_group')) {
      return;
    }

    acf_add_local_field_group(array(
      'key' => 'group_67928ede51966',
      'title' => 'Setting Options',
      'fields' => array(
        [
          'key' => 'field_be4c7146a2c3df',
          'label' => 'General settings',
          'name' => 'gneral_settings',
          'graphql_field_name' => 'gneral_settings',
          'show_in_graphql' => 1,
          'graphql_description' => '',
          'graphql_non_null' => 0,
          'type' => 'tab',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
          ],
          'placement' => 'top',
          'endpoint' => 0,
        ],
        [
          'key' => 'field_bed26625a645f6',
          'label' => 'Visual identity',
          'name' => 'visual_identity',
          'graphql_field_name' => 'visual_identity',
          'show_in_graphql' => 1,
          'graphql_description' => '',
          'graphql_non_null' => 0,
          'type' => 'group',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
          ],
          'layout' => 'block',
          'sub_fields' => [
            array(
              'key' => 'field_b6662c545d19c5',
              'label' => 'Company Logo',
              'name' => 'company_logo',
              'graphql_field_name' => 'company_logo',
              'show_in_graphql' => 1,
              'graphql_description' => '',
              'graphql_non_null' => 0,
              'type' => 'image',
              'instructions' => '',
              'required' => 1,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '50',
                'class' => '',
                'id' => '',
              ),
              'return_format' => 'array',
              'preview_size' => 'medium',
              'library' => 'all',
              'min_width' => '',
              'min_height' => '',
              'min_size' => '',
              'max_width' => '',
              'max_height' => '',
              'max_size' => '',
              'mime_types' => '.png',
            ),

          ],
        ],
        [
          'key' => 'field_578de34247e371',
          'label' => 'Export settings',
          'name' => 'export_settings',
          'graphql_field_name' => 'export_settings',
          'show_in_graphql' => 1,
          'graphql_description' => '',
          'graphql_non_null' => 0,
          'type' => 'group',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
          ],
          'layout' => 'block',
          'sub_fields' => [
            [
              'key' => 'field_2453d6036045c7',
              'label' => 'Add email address column on export?',
              'name' => 'email_on_export',
              'graphql_field_name' => 'email_on_export',
              'show_in_graphql' => 1,
              'graphql_description' => '',
              'graphql_non_null' => 0,
              'type' => 'true_false',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
              ],
              'message' => '',
              'default_value' => 0,
              'ui' => 1,
              'ui_on_text' => '',
              'ui_off_text' => '',
            ],
            [
              'key' => 'field_b5159a59c9508f',
              'label' => 'Add PageSpeed results on export?',
              'name' => 'pageSpeed_on_export',
              'graphql_field_name' => 'pageSpeed_on_export',
              'show_in_graphql' => 1,
              'graphql_description' => '',
              'graphql_non_null' => 0,
              'type' => 'true_false',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => [
                'width' => '',
                'class' => '',
                'id' => '',
              ],
              'message' => '',
              'default_value' => 0,
              'ui' => 1,
              'ui_on_text' => '',
              'ui_off_text' => '',
            ],
          ],
        ],
        // [
        //   'key' => 'field_7eea8c57e49dd2',
        //   'label' => 'Company Information',
        //   'name' => 'company_information',
        //   'graphql_field_name' => 'company_information',
        //   'show_in_graphql' => 1,
        //   'graphql_description' => '',
        //   'graphql_non_null' => 0,
        //   'type' => 'tab',
        //   'instructions' => '',
        //   'required' => 0,
        //   'conditional_logic' => 0,
        //   'wrapper' => [
        //     'width' => '',
        //     'class' => '',
        //     'id' => '',
        //   ],
        //   'placement' => 'top',
        //   'endpoint' => 0,
        // ],
        // [
        //   'key' => 'field_05221226c93c6c',
        //   'label' => 'Company Name',
        //   'name' => 'company_name',
        //   'graphql_field_name' => 'company_name',
        //   'show_in_graphql' => 1,
        //   'graphql_description' => '',
        //   'graphql_non_null' => 0,
        //   'type' => 'group',
        //   'instructions' => '',
        //   'required' => 0,
        //   'conditional_logic' => 0,
        //   'wrapper' => [
        //     'width' => '',
        //     'class' => '',
        //     'id' => '',
        //   ],
        //   'layout' => 'block',
        //   'sub_fields' => [
        //     [
        //       'key' => 'field_d880844396754b',
        //       'label' => 'Name',
        //       'name' => 'company_name',
        //       'graphql_field_name' => 'company_name',
        //       'show_in_graphql' => 1,
        //       'graphql_description' => '',
        //       'graphql_non_null' => 0,
        //       'type' => 'text',
        //       'instructions' => '',
        //       'required' => 1,
        //       'conditional_logic' => 0,
        //       'wrapper' => [
        //         'width' => '',
        //         'class' => '',
        //         'id' => '',
        //       ],
        //       'default_value' => '',
        //       'placeholder' => '',
        //       'prepend' => '',
        //       'append' => '',
        //       'maxlength' => '',
        //     ],
        //   ],
        // ],
        [
          'key' => 'field_e9767a46de2ec8',
          'label' => 'Contact information',
          'name' => 'contact_information',
          'graphql_field_name' => 'contact_information',
          'show_in_graphql' => 1,
          'graphql_description' => '',
          'graphql_non_null' => 0,
          'type' => 'tab',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => [
            'width' => '',
            'class' => '',
            'id' => '',
          ],
          'placement' => 'top',
          'endpoint' => 0,
        ],
        array(
          'key' => 'field_c764544247877f',
          'label' => 'Contact Information',
          'name' => 'contact_information',
          'graphql_field_name' => 'contanct_information',
          'show_in_graphql' => 1,
          'graphql_description' => '',
          'graphql_non_null' => 0,
          'type' => 'repeater',
          'instructions' => '',
          'required' => 1,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'collapsed' => '',
          'min' => 3,
          'max' => 4,
          'layout' => 'block',
          'button_label' => 'Add Contact person',
          'sub_fields' => array(
            array(
              'key' => 'field_1f932c3699c37e',
              'label' => 'Contact Type',
              'name' => 'contact_type',
              'graphql_field_name' => 'contact_type',
              'show_in_graphql' => 1,
              'graphql_description' => '',
              'graphql_non_null' => 0,
              'type' => 'text',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'default_value' => '',
              'placeholder' => '',
              'prepend' => '',
              'append' => '',
              'maxlength' => '',
            ),
            array(
              'key' => 'field_c2dea724715437',
              'label' => 'Contact Name',
              'name' => 'contact_name',
              'graphql_field_name' => 'contact_name',
              'show_in_graphql' => 1,
              'graphql_description' => '',
              'graphql_non_null' => 0,
              'type' => 'text',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'default_value' => '',
              'placeholder' => '',
              'prepend' => '',
              'append' => '',
              'maxlength' => '',
            ),
            array(
              'key' => 'field_928b3856814e0c',
              'label' => 'Contact Email',
              'name' => 'contact_email',
              'graphql_field_name' => 'contact_email',
              'show_in_graphql' => 1,
              'graphql_description' => '',
              'graphql_non_null' => 0,
              'type' => 'text',
              'instructions' => '',
              'required' => 0,
              'conditional_logic' => 0,
              'wrapper' => array(
                'width' => '',
                'class' => '',
                'id' => '',
              ),
              'default_value' => '',
              'placeholder' => '',
              'prepend' => '',
              'append' => '',
              'maxlength' => '',
            ),
          ),
        ),
      ),
      'location' => array(
        array(
          array(
            'param' => 'options_page',
            'operator' => '==',
            'value' => 'struck-settings',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => true,
      'description' => '',
      'show_in_rest' => 1,
    ));
  }

  public function acf_settings_css()
  {
    add_action('acf/input/admin_enqueue_scripts', 'settings_css');
    add_action('acf/input/admin_footer', 'settings_js');

    function settings_css()
    {
      $screen = get_current_screen();

      if ($screen && $screen->id === 'struck-logger_page_struck-settings') {
        wp_enqueue_style('struck-settings-acf-css', plugin_dir_url(__FILE__) . '/index.css', [], '1.0.0');
      }
    }

    function settings_js()
    {
?>
      <script>
        acf.addAction('ready', function($el) {
          // Check if we're on the specific options page
          if (acf.get('screen') !== 'options') return;

          // Optional: Check a specific options page ID (e.g., options-general, options-theme)
          if (acf.get('post_id') !== 'options') return; // or 'options_custom_name'

          const field = acf.getField('field_b6662c545d19c5');

          const currentColorVal = "#0F1010";

          const targetBG = document.querySelector(
            '.acf-field.acf-field-image.acf-field-b6662c545d19c5 > div > div > div > img');

          targetBG.style.padding = '20px';

          if (field) {
            targetBG.style.backgroundColor = currentColorVal;
            field.on('change', function(e) {
              var newColor = field.val();
              targetBG.style.backgroundColor = newColor;
            });
          }
        });
      </script>
<?php
    }
  }

  public function render_plugin_options_page()
  {
    echo '<div id="struck-options" class="struck"></div>';
  }

  public function add_type_attribute_admin($tag, $handle, $src)
  {
    // change the script tag by adding type="module" and return it.
    if ($handle === 'struck-plugin-options-dev' || $handle === 'struck-plugin-options-prod') {
      $tag = '<script type="module" src="' . esc_url($src) . '"></script>';
      return $tag;
    }
    // if not your script, do nothing and return original $tag
    return $tag;
  }

  public function enqueue_admin_scripts($hook)
  {

    // Are we on the plugin options page?
    if ($hook === $this->plugin_options_page) {

      // add react and react-dom from core
      $dep = ['wp-element'];

      $handle = 'struck-plugin-options-';

      add_filter('script_loader_tag', array($this, 'add_type_attribute_admin'), 10, 3);
      $mod_ver = is_dir(plugins_url("/dist/vue-wp.js", __FILE__)) ? date('YmdHi', filemtime(plugins_url("/dist/vue-wp.js", __FILE__))) : date("YmdHms");

      if (file_exists(dirname(__FILE__) . "/dist/vue-wp.js")) {
        $handle .= 'prod';
        wp_enqueue_script($handle, plugins_url("/dist/vue-wp.js", __FILE__), ['wp-element'], $mod_ver, true);
        wp_enqueue_style($handle, plugins_url("/dist/vue-wp.css", __FILE__), false, $mod_ver, 'all');
      } else {
        $handle .= 'dev';
        wp_enqueue_script($handle, 'http://localhost:5173/src/main.js', ['wp-element'], $mod_ver, true);
      }
    }
  }
}

$plugin_options = new struck_plugin_options();
$plugin_options->init();
