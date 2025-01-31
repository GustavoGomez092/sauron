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
    $logs = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id LIMIT $limit OFFSET $offset");

    foreach ($logs as &$value) {

      $user = get_userdata($value->user_id);
      $user_email = $user->data->user_email;
      $role = $user->data->user_nicename;
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
  public function get_user_logs_by_date_range($user_id, $start_date, $end_date)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';
    $logs = '';
    if (!$user_id) {
      $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE DATE(action_time) BETWEEN %s AND %s", $start_date, $end_date));
    } else {
      $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND DATE(action_time) BETWEEN %s AND %s", $user_id, $start_date, $end_date));
    }

    foreach ($logs as &$value) {
      $user = get_userdata($value->user_id);
      $user_email = $user->data->user_email;
      $role = $user->data->user_nicename;
      $value->email = $user_email;
      $value->role = $role;
    }

    return $logs;
  }
}
