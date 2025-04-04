export default function useActions() {
  const translateAction = (action) => {
    switch (action) {
      case "comment_trashed":
        return "Comment Trashed";
      case "comment_deleted":
        return "Comment Deleted";
      case "post_publish":
        return "Post Published";
      case "login":
        return "Logged In";
      case "login_failed":
        return "Login Failed";
      case "draft_save":
        return "Draft Saved";
      case "media_upload":
        return "Image Uploaded";
      case "user_create":
        return "User Created";
      case "category_update":
        return "Category Updated";
      case "post_schedule":
        return "Post Scheduled";
      case "theme_activate":
        return "Theme Activated";
      case "installed_plugin":
        return "Plugin Installed";
      case "updated_plugin":
        return "Plugin Updated";
      case "comment_reply":
        return "Comment Replied";
      case "profile_update":
        return "Profile Updated";
      case "settings_update":
        return "Settings Changed";
      case "menu_edit":
        return "Menu Edited";
      case "password_reset":
        return "Password Reset";
      case "database_optimized":
        return "Database Optimized";
      case "antivirus_scan":
        return "Antivirus Scan";
      case "user_created":
        return "User Created";
      case "user_deleted":
        return "User Deleted";
      case "user_updated":
        return "User Updated";
      case "user_logged_in":
        return "User Logged In";
      case "user_logged_out":
        return "User Logged Out";
      case "user_password_reset":
        return "User Password Reset";
      case "user_role_added":
        return "User Role Added";
      case "user_role_removed":
        return "User Role Removed";
      case "site_data_exported":
        return "Site Data Exported";
      case "site_mail_send_failed":
        return "Sending Site Mail Failed";
      case "site_mail_sent":
        return "Site Mail Sent";

      default:
        return action.toUpperCase();
    }
  };

  const renderColor = (action) => {
    if (!action) {
      return "yellow";
    }
    if (
      action.includes("created") ||
      action.includes("registration") ||
      action.includes("added") ||
      action.includes("login") ||
      action.includes("logged_in") ||
      action.includes("installed") ||
      action.includes("activated") ||
      action.includes("SITE_REVIEW") ||
      action.includes("UPDATED_CONFIG")
    ) {
      return "green";
    } else if (
      action.includes("deleted") ||
      action.includes("trashed") ||
      action.includes("failed") ||
      action.includes("removed") ||
      action.includes("deactivated")
    ) {
      return "red";
    } else {
      return "yellow";
    }
  };
  return { translateAction, renderColor };
}
