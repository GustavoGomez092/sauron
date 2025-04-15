<?

class Struck_API
{

  /**
   * Retrieves all logged actions from the database.
   *
   * @return array|null List of logged actions.
   */
  public function get_logs($offset, $limit)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';
    // $total_entries = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT $limit OFFSET $offset");


    foreach ($logs as &$value) {
      if ($value->user_id == 0) {
        $value->email = '-';
        $value->role = 'System';
        continue;
      }
      $user = get_userdata($value->user_id);
      $user_email = $user->data->user_email;
      $role = $user->roles[0];
      $value->email = $user_email;
      $value->role = $role;
    }
    return $logs;
  }

  public function get_total_entries()
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';
    $total_entries = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

    return $total_entries;
  }


  /**
   * Retrieves the logs for an specific user and date range.
   */
  public function get_user_logs_by_date_range($user_ids, $start_date, $end_date)
  {

    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';
    $logs = '';
    // extract the ids from the $user_ids array, the id is in the key id
    $user_ids = array_map(function ($user) {
      return $user['id'];
    }, $user_ids);
    // convert the array to a comma separated string
    $user_ids = implode(',', $user_ids);
    // check if there is a * in the array, if so, set the $user_ids to null
    if (str_contains('*', $user_ids)) {
      $user_ids = null;
    }
    // check if the $user_ids is null, if so, get all logs
    if ($user_ids === null) {
      $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE DATE(action_time) BETWEEN %s AND %s", $start_date, $end_date));
    } else {
      $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id IN ($user_ids) AND DATE(action_time) BETWEEN %s AND %s", $start_date, $end_date));
    }

    foreach ($logs as &$value) {
      $user = get_userdata($value->user_id);
      $user_email = $user ? $user->data->user_email : '';
      $role = $user ? $user->roles[0] : 'System';
      $value->email = $user_email;
      $value->role = $role;
    }

    return $logs;
  }

  public function validate_action($action)
  {
    $valid_actions = ['SITE_REVIEW', 'UPDATED_CONFIG'];
    return in_array($action, $valid_actions);
  }


  public function insert_manual_entry($action_type, $action_taken)
  {
    if (!$this->validate_action($action_type)) {
      return false; // Invalid action type
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;


    $data = [
      'user_id' => $user_id,
      'user_name' => $current_user->display_name,
      'action_type' => $action_type,
      'action_taken' => $action_taken,
    ];

    $format = ['%d', '%s', '%s', '%s', '%s', '%s'];

    return $wpdb->insert($table_name, $data, $format);
  }
}