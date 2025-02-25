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
        'menu_slug' => 'settings',
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
        array(
          'key' => 'field_67928ef91b68e',
          'label' => 'Address Information',
          'name' => 'address_information',
          'aria-label' => '',
          'type' => 'wysiwyg',
          'instructions' => '',
          'required' => 1,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'allow_in_bindings' => 0,
          'tabs' => 'all',
          'toolbar' => 'full',
          'media_upload' => 1,
          'delay' => 0,
        ),
        array(
          'key' => 'field_67928f271b68f',
          'label' => 'Technical Producer',
          'name' => 'assigned_manager',
          'aria-label' => '',
          'type' => 'text',
          'instructions' => '',
          'required' => 1,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '',
          'maxlength' => '',
          'allow_in_bindings' => 0,
          'placeholder' => '',
          'prepend' => '',
          'append' => '',
        ),
        array(
          'key' => 'field_c57c1926644da1',
          'label' => 'Technical Producer Email',
          'name' => 'assigned_manager_email',
          'graphql_field_name' => 'assigned_manager_email',
          'show_in_graphql' => 1,
          'graphql_description' => '',
          'graphql_non_null' => 0,
          'type' => 'text',
          'instructions' => '',
          'required' => 1,
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
          'key' => 'field_2a818401e932ce',
          'label' => 'Development Director',
          'name' => 'development_director',
          'graphql_field_name' => 'development_director',
          'show_in_graphql' => 1,
          'graphql_description' => '',
          'graphql_non_null' => 0,
          'type' => 'text',
          'instructions' => '',
          'required' => 1,
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
          'key' => 'field_9774be569bb544',
          'label' => 'Contact Email',
          'name' => 'contact_email',
          'graphql_field_name' => 'contact_email',
          'show_in_graphql' => 1,
          'graphql_description' => '',
          'graphql_non_null' => 0,
          'type' => 'text',
          'instructions' => '',
          'required' => 1,
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
          'key' => 'field_679290751b690',
          'label' => 'Color Skin',
          'name' => 'color_skin',
          'aria-label' => '',
          'type' => 'color_picker',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'default_value' => '#EF4136',
          'enable_opacity' => 0,
          'return_format' => 'string',
          'allow_in_bindings' => 0,
        ),
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
            'width' => '',
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
      ),
      'location' => array(
        array(
          array(
            'param' => 'options_page',
            'operator' => '==',
            'value' => 'settings',
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

      if (file_exists(dirname(__FILE__) . "/dist/vue-wp.js")) {
        $handle .= 'prod';
        wp_enqueue_script($handle, plugins_url("/dist/vue-wp.js", __FILE__), ['wp-element'], '0.1', true);
        wp_enqueue_style($handle, plugins_url("/dist/vue-wp.css", __FILE__), false, '0.1', 'all');
      } else {
        $handle .= 'dev';
        wp_enqueue_script($handle, 'http://localhost:5173/src/main.js', ['wp-element'], '0.1', true);
      }
    }
  }
}

$plugin_options = new struck_plugin_options();
$plugin_options->init();
