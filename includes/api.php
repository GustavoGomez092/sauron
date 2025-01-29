<?

class struck_API
{

  /**
   * Retrieves all logged actions from the database.
   *
   * @return array|null List of logged actions.
   */
  public function get_logs()
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';
    $logs = $wpdb->get_results("SELECT * FROM $table_name");
    return $logs;
  }

  /**
   * Retrieves the logs for an specific user.
   */
  public function get_user_logs($user_id)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';
    $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d", $user_id));
    return $logs;
  }

  /**
   * Retrieves the logs for an specific user and date.
   */
  public function get_user_logs_by_date($user_id, $date)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';
    $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND DATE(action_time) = %s", $user_id, $date));
    return $logs;
  }

  /**
   * Retrieves the logs for an specific user and date range.
   */
  public function get_user_logs_by_date_range($user_id, $start_date, $end_date)
  {
    global $wpdb;
    $table_name = $wpdb->prefix . 'struck_logs';
    $logs = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND DATE(action_time) BETWEEN %s AND %s", $user_id, $start_date, $end_date));
    return $logs;
  }

}