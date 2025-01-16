<?

class Sauron_table_creation
{

  public function __construct()
  {
    add_action('init', array($this, 'create_sauron_table'));
  }

  public function create_sauron_table()
  {
    global $wpdb;

    $table_name = $wpdb->prefix . 'sauron_logs';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        user_name VARCHAR(60) NOT NULL,
        action_type VARCHAR(60) NOT NULL,
        action_taken TEXT NOT NULL,
        action_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY (id),
        KEY user_id (user_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }

}

new Sauron_table_creation();