<?php

class StruckObserver
{
  const WF_SCAN_STARTED = array('action' => 'antivirus_scan', 'description' => 'Ran anti-virus scan on the site.');
  const USER_CREATED = array('action' => 'user_created', 'description' => 'A new user account was created.');
  const USER_DELETED = array('action' => 'user_deleted', 'description' => 'A user account was deleted.');
  const USER_UPDATED = array('action' => 'user_updated', 'description' => 'User account details were updated for user: ');

  const USER_LOGGED_IN = array('action' => 'user_logged_in', 'description' => 'User successfully logged in.');
  const USER_LOGGED_OUT = array('action' => 'user_logged_out', 'description' => 'User logged out.');
  const USER_PASSWORD_RESET = array('action' => 'user_password_reset', 'description' => 'User password was reset.');

  const USER_ROLE_ADDED = array('action' => 'user_role_added', 'description' => 'A new role was assigned to a user.');
  const USER_ROLE_REMOVED = array('action' => 'user_role_removed', 'description' => 'A role was removed from a user.');

  const SITE_DATA_EXPORTED = array('action' => 'site_data_exported', 'description' => 'Site data was successfully exported.');
  const SITE_MAIL_SEND_FAILED = array('action' => 'site_mail_send_failed', 'description' => 'Failed to send site email.');
  const SITE_MAIL_SENT = array('action' => 'site_mail_sent', 'description' => 'Site email was sent successfully.');

  const SITE_OPTION_ADMIN_EMAIL = array('action' => 'site_option_admin_email', 'description' => 'Admin email setting was changed.');
  const SITE_OPTION_DEFAULT_ROLE = array('action' => 'site_option_default_role', 'description' => 'Default user role was modified.');
  const SITE_OPTION_HOME_URL = array('action' => 'site_option_home_url', 'description' => 'Home URL setting was updated.');
  const SITE_OPTION_SITE_URL = array('action' => 'site_option_site_url', 'description' => 'Site URL setting was updated.');
  const SITE_OPTION_USER_REGISTRATION = array('action' => 'site_option_user_registration', 'description' => 'User registration setting was modified.');

  const SITE_PLUGIN_INSTALLED = array('action' => 'site_plugin_installed', 'description' => 'A new plugin was installed.');
  const SITE_PLUGIN_DELETED = array('action' => 'site_plugin_deleted', 'description' => 'A plugin was removed.');
  const SITE_PLUGIN_ACTIVATED = array('action' => 'site_plugin_activated', 'description' => 'A plugin was activated.');
  const SITE_PLUGIN_DEACTIVATED = array('action' => 'site_plugin_deactivated', 'description' => 'A plugin was deactivated.');

  const SITE_THEME_INSTALLED = array('action' => 'site_theme_installed', 'description' => 'A new theme was installed.');
  const SITE_THEME_DELETED = array('action' => 'site_theme_deleted', 'description' => 'A theme was deleted.');
  const SITE_THEME_SWITCHED = array('action' => 'site_theme_switched', 'description' => 'The site theme was changed.');
  const SITE_THEME_CUSTOMIZED = array('action' => 'site_theme_customized', 'description' => 'The site theme was customized.');

  const SITE_UPDATE_AUTOMATIC_COMPLETED = array('action' => 'site_update_automatic_completed', 'description' => 'An automatic site update was completed.');
  const SITE_UPDATE_CORE = array('action' => 'site_update_core', 'description' => 'The site WordPress core was updated.');
  const SITE_UPDATE_PLUGIN = array('action' => 'site_update_plugin', 'description' => 'A site plugin was updated.');
  const SITE_UPDATE_THEME = array('action' => 'site_update_theme', 'description' => 'A site theme was updated.');

  const SITE_COMMENT_TRASHED = array('action' => 'site_comment_trashed', 'description' => 'A spam comment was trashed.');
  const SITE_COMMENT_DELETED = array('action' => 'site_comment_deleted', 'description' => 'A spam comment was deleted.');

  const SITE_CONTENT_CREATED = array('action' => 'site_content_created', 'description' => 'New content was created.');
  const SITE_CONTENT_UPDATED = array('action' => 'site_content_updated', 'description' => 'Content was updated.');
  const SITE_CONTENT_DELETED = array('action' => 'site_content_deleted', 'description' => 'Content was deleted.');

  public function __construct()
  {
    $this->_registerObservers($this);
  }

  private function _registerObservers($auditLog)
  {
    $auditLog->_addObserver('wp_ajax_nopriv_wordfence_testAjax', function () { //Wordfence scan started
      $this->_recordLocalEvent(self::WF_SCAN_STARTED['action'], self::WF_SCAN_STARTED['description']);
    });

    $auditLog->_addObserver('user_register', function ($data) { //User created
      $user = get_user_by('id', $data);
      $message = self::USER_CREATED['description'] . ' User name: ' . $user->user_login . '.User email: ' . $user->user_email . ' User ID: ' . $user->ID;
      $this->_recordLocalEvent(self::USER_CREATED['action'], $message);
    });

    $auditLog->_addObserver('deleted_user', function ($data, $data1) { //User deleted
      $this->_recordLocalEvent(self::USER_DELETED['action'], self::USER_DELETED['description']);
    });

    $auditLog->_addObserver('profile_update', function ($data, $data1) { //User updated
      $message = self::USER_UPDATED['description'] . $data1->user_login . ', user email: ' . $data1->user_email . ' User ID: ' . $data1->ID . ' (This log only refers to the updated user, the updated data for that user cannot be logged for security reasons)';
      $this->_recordLocalEvent(self::USER_UPDATED['action'], $message);
    });

    $auditLog->_addObserver('wp_login', function () { //User logged in
      $this->_recordLocalEvent(self::USER_LOGGED_IN['action'], self::USER_LOGGED_IN['description']);
    });

    $auditLog->_addObserver('wp_logout', function ($data) { //User logged out
      $this->_recordLocalEvent(self::USER_LOGGED_OUT['action'], self::USER_LOGGED_OUT['description'], array('user_id' => $data));
    });

    $auditLog->_addObserver('after_password_reset', function ($data) { //User password reset
      $message = self::USER_PASSWORD_RESET['description'] . ' User name: ' . $data->user_login . '. User email: ' . $data->user_email;
      $this->_recordLocalEvent(self::USER_PASSWORD_RESET['action'], $message);
    });

    $auditLog->_addObserver('add_user_role', function ($data, $data1) { //User role assigned
      $user = get_user_by('id', $data);
      $message = self::USER_ROLE_ADDED['description'] . ' User name: ' . $user->user_login . '. User email: ' . $user->user_email . '. Role added: ' . $data1;
      $this->_recordLocalEvent(self::USER_ROLE_ADDED['action'], $message);
    });

    $auditLog->_addObserver('remove_user_role', function ($data, $data1) { //User role removed
      $user = get_user_by('id', $data);
      $message = self::USER_ROLE_REMOVED['description'] . ' User name: ' . $user->user_login . '. User email: ' . $user->user_email . '. Role removed: ' . $data1;
      $this->_recordLocalEvent(self::USER_ROLE_REMOVED['action'], $message);
    });

    $auditLog->_addObserver('export_wp', function () { //Exported WP data
      $this->_recordLocalEvent(self::SITE_DATA_EXPORTED['action'], self::SITE_DATA_EXPORTED['description']);
    });

    $auditLog->_addObserver('wp_mail_failed', function () { //Mail send failed
      $this->_recordLocalEvent(self::SITE_MAIL_SEND_FAILED['action'], self::SITE_MAIL_SEND_FAILED['description']);
    });

    $auditLog->_addObserver('wp_mail', function () { //Mail sent
      $this->_recordLocalEvent(self::SITE_MAIL_SENT['action'], self::SITE_MAIL_SENT['description']);
    });

    $auditLog->_addObserver('update_option_admin_email', function () { // admin email
      $this->_recordLocalEvent(self::SITE_OPTION_ADMIN_EMAIL['action'], self::SITE_OPTION_ADMIN_EMAIL['description']);
    });

    $auditLog->_addObserver('update_option_default_role', function () { //Default role on user registration
      $this->_recordLocalEvent(self::SITE_OPTION_DEFAULT_ROLE['action'], self::SITE_OPTION_DEFAULT_ROLE['description']);
    });

    $auditLog->_addObserver('update_option_home', function () { //Home URL
      $this->_recordLocalEvent(self::SITE_OPTION_HOME_URL['action'], self::SITE_OPTION_HOME_URL['description']);
    });

    $auditLog->_addObserver('update_option_siteurl', function () { //Site URL
      $this->_recordLocalEvent(self::SITE_OPTION_SITE_URL['action'], self::SITE_OPTION_SITE_URL['description']);
    });

    $auditLog->_addObserver('update_option_users_can_register', function () { //User registration allowed
      $this->_recordLocalEvent(self::SITE_OPTION_USER_REGISTRATION['action'], self::SITE_OPTION_USER_REGISTRATION['description']);
    });

    $auditLog->_addObserver('upgrader_process_complete', function ($hook_extra, $response) { //Plugin/theme installed/updated

      if ($response) {
        if (isset($response['action']) && isset($response['type'])) { //Install or update
          if ($response['action'] == 'install') {
            if ($response['type'] == 'plugin') {
              $message = self::SITE_PLUGIN_INSTALLED['description'] . ' Plugin name: ' . $hook_extra->new_plugin_data['Name'] . '. Version: ' . $hook_extra->new_plugin_data['Version'];
              $this->_recordLocalEvent(self::SITE_PLUGIN_INSTALLED['action'], $message);
            } else if ($response['type'] == 'theme') {
              $message = self::SITE_THEME_INSTALLED['description'] . ' Theme name: ' . $hook_extra->new_theme_data['Name'] . '. Version: ' . $hook_extra->new_theme_data['Version'];
              $this->_recordLocalEvent(self::SITE_THEME_INSTALLED['action'], $message);
            }
          } else if ($response['action'] == 'update') {
            if ($response['type'] == 'plugin') {
              $message = self::SITE_UPDATE_PLUGIN['description'] . ' Plugin name: ' . $hook_extra->new_plugin_data['Name'] . '. from version: ' . $hook_extra->skin->plugin_info['Version'] . ' to version: ' . $hook_extra->new_plugin_data['Version'];
              $this->_recordLocalEvent(self::SITE_UPDATE_PLUGIN['action'], $message);
            } else if ($response['type'] == 'theme') {
              $message = self::SITE_UPDATE_THEME['description'] . ' Theme name: ' . $hook_extra->skin->theme_info['Name'] . '. from version: ' . $hook_extra->skin->theme_info['Version'] . ' to version: ' . $hook_extra->new_theme_data['Version'];
              $this->_recordLocalEvent(self::SITE_UPDATE_THEME['action'], $message);
            }
          }
        }
      }

      // return $response;
    });

    $auditLog->_addObserver('activated_plugin', function ($data) { //Plugin activated
      $plugin_dir = WP_PLUGIN_DIR . '/' . $data;
      $plugin_data = get_plugin_data($plugin_dir);
      $message = self::SITE_PLUGIN_ACTIVATED['description'] . ' Plugin name: ' . $plugin_data['Name'] . '. Version: ' . $plugin_data['Version'];
      $this->_recordLocalEvent(self::SITE_PLUGIN_ACTIVATED['action'], $message);
    });

    $auditLog->_addObserver('deactivated_plugin', function ($data) { //Plugin deactivated
      $plugin_dir = WP_PLUGIN_DIR . '/' . $data;
      $plugin_data = get_plugin_data($plugin_dir);
      $message = self::SITE_PLUGIN_DEACTIVATED['description'] . ' Plugin name: ' . $plugin_data['Name'] . '. Version: ' . $plugin_data['Version'];
      $this->_recordLocalEvent(self::SITE_PLUGIN_DEACTIVATED['action'], $message);
    });

    $auditLog->_addObserver('deleted_plugin', function ($data) { //Plugin deleted
      $message = self::SITE_PLUGIN_DELETED['description'] . ' Deleted folder: ' . $data;
      $this->_recordLocalEvent(self::SITE_PLUGIN_DELETED['action'], $message);
    });

    $auditLog->_addObserver('switch_theme', function ($data, $data1, $data2) { //Theme switched
      $message = self::SITE_THEME_SWITCHED['description'] . ' From theme: ' . $data2->Name . ', to theme: ' . $data1->Name;
      $this->_recordLocalEvent(self::SITE_THEME_SWITCHED['action'], $message);
    });

    $auditLog->_addObserver('deleted_theme', function ($data) { //Theme deleted
      $message = self::SITE_THEME_DELETED['description'] . ' Deleted theme: ' . $data;
      $this->_recordLocalEvent(self::SITE_THEME_DELETED['action'], $message);
    });

    $auditLog->_addObserver('customize_save_after', function () { //Theme customized
      $this->_recordLocalEvent(self::SITE_THEME_CUSTOMIZED['action'], self::SITE_THEME_CUSTOMIZED['description']);
    });

    $auditLog->_addObserver('upgrader_process_complete', function ($upgrader, $hook_extra) { //Core updated
      if (is_array($hook_extra) && isset($hook_extra['type']) && $hook_extra['type'] == 'core' && isset($hook_extra['action']) && $hook_extra['action'] == 'update') {

        global $wp_version;
        require(ABSPATH . 'wp-includes/version.php');
        /** @var string $wp_version */
        $message = self::SITE_UPDATE_CORE['description'] . ' To version ' . $wp_version . '.';
        $this->_recordLocalEvent(self::SITE_UPDATE_CORE['action'], $message);
      }
    });

    $auditLog->_addObserver('automatic_updates_complete', function () { //Automatic updates complete
      $this->_recordLocalEvent(self::SITE_UPDATE_AUTOMATIC_COMPLETED['action'], self::SITE_UPDATE_AUTOMATIC_COMPLETED['description']);
    });

    $auditLog->_addObserver('trashed_comment', function ($comment_id) { //Comment trashed
      $comment = get_comment($comment_id);
      $comment_author = $comment->comment_author;
      $author_email = $comment->comment_author_email;
      $message = self::SITE_COMMENT_TRASHED['description'] . ' Comment author: ' . $comment_author . '. Author email: ' . $author_email . '. Comment ID: ' . $comment_id;
      $this->_recordLocalEvent(self::SITE_COMMENT_TRASHED['action'], $message);
    });

    $auditLog->_addObserver('deleted_comment', function ($comment_id) { //Comment deleted
      $comment = get_comment($comment_id);
      $comment_author = $comment->comment_author;
      $author_email = $comment->comment_author_email;
      $message = self::SITE_COMMENT_DELETED['description'] . ' Comment author: ' . $comment_author . '. Author email: ' . $author_email . '. Comment ID: ' . $comment_id;
      $this->_recordLocalEvent(self::SITE_COMMENT_DELETED['action'], $message);
    });

    $auditLog->_addObserver('save_post', function ($post_id, $post, $update) {
      // make sure status is not draft
      if (!$update && $post->post_status != 'draft') {
        $message = self::SITE_CONTENT_CREATED['description'] . ' Post type: ' . $post->post_type . '. Page ID: ' . $post_id;
        $this->_recordLocalEvent(self::SITE_CONTENT_CREATED['action'], $message);
      }
    });

    $auditLog->_addObserver('save_post', function ($post_id, $post, $update) {
      // make sure status is not trash
      if ($update && $post->post_status != 'trash') {
        $message = self::SITE_CONTENT_UPDATED['description'] . ' Post type: ' . $post->post_type . '. Page title: ' . $post->post_title . '. Page ID: ' . $post_id;
        $this->_recordLocalEvent(self::SITE_CONTENT_UPDATED['action'], $message);
      }
    });

    $auditLog->_addObserver('delete_post', function ($post_id) {
      $post = get_post($post_id);
      $message = self::SITE_CONTENT_DELETED['description'] . ' Post type: ' . $post->post_type . '. Page title: ' . $post->post_title . '. Page ID: ' . $post_id;
      $this->_recordLocalEvent(self::SITE_CONTENT_DELETED['action'], $message);
    });
  }

  protected function _addObserver($hooks, $closure, $type = 'action')
  {
    if (!is_array($hooks)) {
      $hooks = array($hooks);
    }

    try {
      $introspection = new ReflectionFunction($closure);
      if ($type == 'action') {
        foreach ($hooks as $hook) {
          add_action($hook, $closure, 1, $introspection->getNumberOfParameters());
        }
      } else if ($type == 'filter') {
        foreach ($hooks as $hook) {
          add_filter($hook, $closure, 1, $introspection->getNumberOfParameters());
        }
      }
    } catch (Exception $e) {
      //Ignore
    }
  }

  private function _recordLocalEvent($type, $message, $data = null)
  {

    $user = null;

    if ($data) {
      // if key user_id exists in data, get user by id
      if (array_key_exists('user_id', $data)) {
        $user = get_user_by('id', $data['user_id']);
      }
    } else {
      $user = wp_get_current_user();
    }

    $entry = array(
      'action' => $type,
      'action_description' => $message,
      'user_id' => $user->exists() ? $user->ID : 0,
      'user_name' => $user->exists() ? $user->user_login : 'WordPress',
    );

    $this->log_action($entry);
  }

  private function log_action($data)
  {

    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';

    if (is_array($data)) {
      $user_id = $data['user_id'];
      $user_name = $data['user_name'];
      $action_type = $data['action'];
      $action_taken = $data['action_description'];

      $wpdb->insert(
        $table_name,
        array(
          'user_id' => $user_id,
          'user_name' => $user_name,
          'action_type' => $action_type,
          'action_taken' => $action_taken
        )
      );
    }
  }
}

new StruckObserver();
