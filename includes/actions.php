<?
/**
 * Sauron_actions class handles logging of various user actions in WordPress.
 */
class Sauron_actions
{
  /**
   * Constructor initializes the wp_actions method.
   */
  function __construct()
  {
    $this->wp_actions();
  }

  /**
   * List of actions to be logged with corresponding descriptions.
   *
   * @var array
   */
  private $actions_list = array(
    'login' => 'User logged in',
    'logout' => 'User logged out',
    'wf_scan' => 'User Ran a Wordfence Scan',
    'comment_deleted' => 'User deleted a spam comment.',
    'comment_trashed' => 'User trashed a spam comment',
    'updated_plugin' => 'User updated a plugin: ',
    'installed_plugin' => 'User installed a plugin: ',
    'deleted_plugin' => 'User deleted a plugin: ',
    'updated_theme' => 'User updated a theme: ',
    'updated_core' => 'User updated WordPress core',
    'updated_page' => 'User updated a page: ',
    'updated_post' => 'User updated a post: ',
    'deleted_page' => 'User deleted a page: ',
    'deleted_post' => 'User deleted a post: ',
  );

  /**
   * Adds actions to be logged using WordPress hooks.
   */
  private function wp_actions()
  {

    /**
     * Logs a user action when they log in.
     */

    add_action(
      'wp_login',
      function ($user_login, $user) {
        return $this->log_action(array(
          'user_name' => $user_login,
          'user_id' => $user->ID,
          'action' => 'login',
          'action_description' => $this->actions_list['login']
        ));
      },
      10,
      2
    );

    /**
     * Logs a user action when they log out.
     */

    add_action('wp_logout', function ($user_id) {
      return $this->log_action(array(
        'user_name' => get_user_by('id', $user_id)->user_login,
        'user_id' => $user_id,
        'action' => 'logout',
        'action_description' => $this->actions_list['logout']
      ));
    });

    /**
     * Logs a user action when they trash a comment.
     */
    add_action('trash_comment', function ($comment_id) {
      $comment = get_comment($comment_id);
      $comment_author = $comment->comment_author;
      $author_email = $comment->comment_author_email;
      $message = $this->actions_list['comment_trashed'] . ' Comment author: ' . $comment_author . '. Author email: ' . $author_email . '. Comment ID: ' . $comment_id;
      return $this->log_action(array(
        'user_id' => $this->get_user_id(),
        'user_name' => $this->get_user_name(),
        'action' => 'comment_trashed',
        'action_description' => $message
      ));
    });

    /**
     * Logs a user action when they delete a comment.
     */
    add_action('delete_comment', function ($comment_id) {
      $comment = get_comment($comment_id);
      $comment_author = $comment->comment_author;
      $author_email = $comment->comment_author_email;
      $message = $this->actions_list['comment_deleted'] . ' Comment author: ' . $comment_author . '. Author email: ' . $author_email . '. Comment ID: ' . $comment_id;
      return $this->log_action(array(
        'user_id' => $this->get_user_id(),
        'user_name' => $this->get_user_name(),
        'action' => 'comment_deleted',
        'action_description' => $message
      ));
    });

    /**
     * Logs a user action when they update a plugin or a theme.
     */
    add_action('upgrader_process_complete', 'plugin_theme_upgrade', 10, 2);

    function plugin_theme_upgrade($upgrader_object, $options)
    {
      $current_plugin_path_name = plugin_basename(__FILE__);

      if ($options['action'] == 'update' && $options['type'] == 'plugin') {
        foreach ($options['plugins'] as $each_plugin) {
          if ($each_plugin == $current_plugin_path_name) {
            // .......................... YOUR CODES .............

          }
        }
      }

      if ($options['action'] == 'update' && $options['type'] == 'theme') {
        foreach ($options['themes'] as $each_theme) {
          if ($each_theme == $current_plugin_path_name) {
            // .......................... YOUR CODES .............

          }
        }
      }
    }
  }

  /**
   * Gets the current user ID.
   *
   * @return int Current user ID.
   */
  private function get_user_id()
  {
    $user_id = get_current_user_id();

    return $user_id;
  }

  /**
   * Gets the current user login name.
   *
   * @return string Current user login name.
   */
  private function get_user_name()
  {
    $current_user = wp_get_current_user();
    return $current_user->user_login;
  }

  /**
   * Logs a user action to the database.
   *
   * @param array|string $data Action data or action description.
   * @return void
   */
  public function log_action($data)
  {

    global $wpdb;
    $table_name = $wpdb->prefix . 'sauron_logs';

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

/**
 * Instantiates the Sauron_actions class to initialize logging.
 */
new Sauron_actions();