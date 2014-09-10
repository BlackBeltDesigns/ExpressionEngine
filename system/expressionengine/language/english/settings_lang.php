<?php

$lang = array(

/**
 * Menu
 */

'general_settings' =>
'General Settings',

'license_and_reg' =>
'License & Registration',

'url_path_settings' =>
'URL and Path Settings',

'outgoing_email' =>
'Outgoing e-mail',

'debugging_output' =>
'Debugging & Output',

'content_and_design' =>
'Content & Design',

'comment_settings' =>
'Comment Settings',

'template_settings' =>
'Template Settings',

'upload_directories' =>
'Upload Directories',

'word_censoring' =>
'Word Censoring',

'members' =>
'Members',

'messages' =>
'Messages',

'avatars' =>
'Avatars',

'security_privacy' =>
'Security & Privacy',

'access_throttling' =>
'Access Throttling',

'captcha' =>
'CAPTCHA',

/**
 * General Settings
 */

'site_name' =>
'Website name',

'site_name_desc' =>
'Used for <mark>{site_name}</mark>',

'site_online' =>
'Website online?',

'site_online_desc' =>
'When set to <b>offline</b>, only super admins and member groups with permissions will be able to browse your website.',

'version_autocheck' =>
'New version auto check',

'version_autocheck_desc' =>
'When set to <b>auto</b>, ExpressionEngine will automatically check for newer versions of the software.',

'online' =>
'Online',

'offline' =>
'Offline',

'auto' =>
'Auto',

'manual' =>
'Manual',

'check_now' =>
'Check now',

'defaults' =>
'Defaults',

'cp_theme' =>
'<abbr title="Control Panel">CP</abbr> theme',

'language' =>
'Language',

'language_desc' =>
'Default language.<br><i>Used in the control panel only.</i>',

'date_time_settings' =>
'Date &amp; Time Settings',

'timezone' =>
'Timezone',

'timezone_desc' =>
'Default local timezone.',

'date_time_fmt' =>
'Date &amp; time format',

'date_time_fmt_desc' =>
'Default date and time formats.<br><i>Used in the control panel only.</i>',

"24_hour" =>
"24-hour",

"12_hour" =>
"12-hour with AM/PM",

'include_seconds' =>
'Show seconds?',

'include_seconds_desc' =>
'When set to <b>yes</b>, date output will include seconds for display.',

'btn_save_settings' =>
'Save Settings',

'btn_save_settings_working' =>
'Saving...',

'preferences_updated' =>
'Preferences Updated',

'preferences_updated_desc' =>
'Your preferences have been saved successfully.',

'running_current' =>
'ExpressionEngine is up to date',

'running_current_desc' =>
'ExpressionEngine %s is the latest version.',

'error_getting_version'	=> 'You are using ExpressionEngine %s. Unable to determine if a newer version is available at this time.',

'version_update_available' =>
'A newer version of ExpressionEngine is available',

'version_update_inst' =>
'ExpressionEngine %s is available. <a href="%s">Download the latest version</a> and follow the <a href="%s">update instructions</a>.',

'settings_save_error' =>
'Attention: Settings not saved',

'settings_save_error_desc' =>
'We were unable to save your settings, please review and fix errors below.',

/**
 * License & Registration
 */

'license_and_reg_title' =>
'License &amp; Registration Settings',

'license_contact' =>
'Account holder e-mail',

'license_contact_desc' =>
'Contact e-mail for the account that owns this license.',

'license_number' =>
'License number',

'license_number_desc' =>
'Found on your <a href="%s">purchase management</a> page.',

'license_updated' =>
'License &amp; Registration Updated',

'license_updated_desc' =>
'Your license and registration information has been saved successfully.',

'invalid_license_number' =>
'The license number provided is not a valid license number.',

/**
 * URLs and Path Settings
 */

'url_path_settings_title' =>
'<abbr title="Uniform Resource Location">URL</abbr> and Path Settings',

'site_index' =>
'Website index page',

'site_index_desc' =>
'Most commonly <mark>index.php</mark>.',

'site_url' =>
'Website root directory',

'site_url_desc' =>
'<abbr title="Uniform Resource Location">URL</abbr> location of your <mark>index.php</mark>.',

'cp_url' =>
'Control panel directory',

'cp_url_desc' =>
'<abbr title="Uniform Resource Location">URL</abbr> location of your control panel.',

'themes_url' =>
'Themes directory',

'themes_url_desc' =>
'<abbr title="Uniform Resource Location">URL</abbr> location of your <mark>themes</mark> directory.',

'themes_path' =>
'Themes path',

'themes_path_desc' =>
'Full path location of your <mark>themes</mark> directory.',

'docs_url' =>
'Documentation directory',

'docs_url_desc' =>
'<abbr title="Uniform Resource Location">URL</abbr> location of your <mark>documentation</mark> directory.',

'member_segment_trigger' =>
'Profile <abbr title="Uniform Resource Location">URL</abbr> segment',

'member_segment_trigger_desc' =>
'Word that triggers member profile display. <b>Cannot</b> be the same as a template or template group.',

'category_segment_trigger' =>
'Category <abbr title="Uniform Resource Location">URL</abbr> segment',

'category_segment_trigger_desc' =>
'Word that triggers category display. <b>Cannot</b> be the same as a template or template group.',

'category_url' =>
'Category <abbr title="Uniform Resource Location">URL</abbr>',

'category_url_desc' =>
'When set to <b>titles</b>, category links will use category <abbr title="Uniform Resource Location">URL</abbr> titles instead of the category ids.',

'category_url_opt_titles' =>
'Titles',

'category_url_opt_ids' =>
'IDs',

'url_title_separator' =>
'<abbr title="Uniform Resource Location">URL</abbr> title separator',

'url_title_separator_desc' =>
'Character used to separate words in generated <abbr title="Uniform Resource Location">URL</abbr>s, <mark>hyphens (-)</mark> are recommended.',

'url_title_separator_opt_hyphen' =>
'Hyphen (different-words)',

'url_title_separator_opt_under' =>
'nderscore (different_words)',

/**
 * Outgoing Email
 */

'webmaster_email' =>
'Address',

'webmaster_email_desc' =>
'e-mail address you want automated e-mail to come from. Without this, automated e-mail will likely be marked as spam.',

'webmaster_name' =>
'From name',

'webmaster_name_desc' =>
'Name you want automated e-mails to use.',

'email_charset' =>
'Character encoding',

'email_charset_desc' =>
'e-mail require character encoding to be properly formatted. UTF-8 is recommended.',

'mail_protocol' =>
'Protocol',

'mail_protocol_desc' =>
'Preferred e-mail sending protocol. SMTP is recommended.',

'smtp_options' =>
'SMTP Options',

'smtp_server' =>
'Server address',

'smtp_server_desc' =>
'URL location of your <mark>SMTP server</mark>.',

'smtp_username' =>
'Username',

'smtp_username_desc' =>
'Username of your <mark>SMTP server</mark>.',

'smtp_password' =>
'Password',

'smtp_password_desc' =>
'Password of your <mark>SMTP server</mark>.',

'sending_options' =>
'Sending Options',

'mail_format' =>
'Mail format',

'mail_format_desc' =>
'Format that e-mails are sent in. Plain Text is recommended.',

'word_wrap' =>
'Enable word-wrapping?',

'word_wrap_desc' =>
'When set to <b>enable</b>, the system will wrap long lines of text to a more readable width.',

'php_mail' =>
'PHP Mail',

'sendmail' =>
'Sendmail',

'smtp' =>
'SMTP',

'plain_text' =>
'Plain Text',

'html' =>
'HTML',

'empty_stmp_fields' =>
'This field is required for SMTP.',

/**
 * Debugging & Output
 */

'enable_debugging' =>
'Enable debugging?',

'enable_debugging_desc' =>
'When set to <b>enable</b>, super admins and member groups with permissions will see PHP/MySQL errors when they occur.',

'show_profiler' =>
'Display profiler?',

'show_profiler_desc' =>
'When set to <b>yes</b>, super admins and member groups with permissions will see benchmark results, all SQL queries, and submitted form data displayed at the bottom of the browser window.',

'template_debugging' =>
'Display template debugging?',

'template_debugging_desc' =>
'When set to <b>yes</b>, super admins and member groups with permissions will see a list of details concerning the processing of the page.',

'output_options' =>
'Output Options',

'gzip_output' =>
'Enable <abbr title="GNU Zip Compression">GZIP</abbr> compression?',

'gzip_output_desc' =>
'When set to <b>yes</b>, your website will be compressed using GZIP compression, this will decrease page load times.',

'force_query_string' =>
'Force <abbr title="Uniform Resource Location">URL</abbr> query strings?',

'force_query_string_desc' =>
'When set to <b>yes</b>, servers that do not support <mark>PATH_INFO</mark> will use query string URLs instead.',

'send_headers' =>
'Use <abbr title="Hypertext Transfer Protocol">HTTP</abbr> page headers?',

'send_headers_desc' =>
'When set to <b>yes</b>, your website will generate <abbr title="Hypertext Transfer Protocol">HTTP</abbr> headers for all pages.',

'redirect_method' =>
'Redirection type',

'redirect_method_desc' =>
'Indicates type of page redirection the system will use for <mark>{redirect=\'\'}</mark> and other built in redirections.',

'redirect_method_opt_location' =>
'Location (fastest)',

'redirect_method_opt_refresh' =>
'Refresh (Windows only)',

'max_caches' =>
'Cachable <abbr title="Uniform Resource Identifier">URI</abbr>s',

'max_caches_desc' =>
'If you cache your pages or database, this limits the number of cache instances. We recommend 150 for small sites and 300 for large sites. The allowed maximum is 1000.',

/**
 * Content & Design
 */

'new_posts_clear_caches' =>
'Clear cache for new entries?',

'new_posts_clear_caches_desc' =>
'When set to <b>yes</b>, all caches will be cleared when authors publish new entries.',

'enable_sql_caching' =>
'Cache dynamic channel queries?',

'enable_sql_caching_desc' =>
'When set to <b>yes</b>, the speed of dynamic channel pages will be improved. do <b>not</b> use if you need the "future entries" or "expiring entries" features.',

'categories_section' =>
'Categories',

'auto_assign_cat_parents' =>
'Assign category parents?',

'auto_assign_cat_parents_desc' =>
'When set to <b>yes</b>, ExpressionEngine will automatically set the parent category when choosing a child category.',

'channel_manager' =>
'Channel Manager',

/**
 * Comment Settings
 */

'all_comments' =>
'All Comments',

'enable_comments' =>
'Enable comment module?',

'enable_comments_desc' =>
'When set to <b>enable</b>, channels will be able to use the comment module.',

'options' =>
'Options',

'comment_word_censoring' =>
'Enable word censoring?',

'comment_word_censoring_desc' =>
'When set to <b>enable</b>, commenting will use the <a href="%s">word censoring</a> filters.',

'comment_moderation_override' =>
'Moderate expired entires?',

'comment_moderation_override_desc' =>
'When set to <b>yes</b>, comments made on an expired entry will be submitted as closed and require review by a moderator.',

'comment_edit_time_limit' =>
'Comment edit time limit (in seconds)',

'comment_edit_time_limit_desc' =>
'Length of time that a user can edit their own comments, from submission. Use <b>0</b> for no limit.',

/**
 * Template Settings
 */

'template_manager' =>
'Template Manager',

'strict_urls' =>
'Enable strict <abbr title="Uniform Resource Location">URL</abbr>s?',

'strict_urls_desc' =>
'When set to <b>enable</b>, ExpressioneEngine will apply stricter rules to <abbr title="Uniform Resource Location">URL</abbr> handling.',

'site_404' =>
'404 page',

'site_404_desc' =>
'Template to be used as the 404 error page.',

'save_tmpl_revisions' =>
'Save template revisions?',

'save_tmpl_revisions_desc' =>
'When set to <b>yes</b>, ExpressionEngine will save up to <b>5</b> template revisions in the database.',

'max_tmpl_revisions' =>
'Maximum revisions?',

'max_tmpl_revisions_desc' =>
'Number of revisions stored in the database for each template. We recommend this be a low number, as this can cause you to have a larger than normal database.',

'save_tmpl_files' =>
'Save templates as files?',

'save_tmpl_files_desc' =>
'When set to yes, ExpressionEngine will store your templates as files on your server.',

'tmpl_file_basepath' =>
'Template directory',

'tmpl_file_basepath_desc' =>
'Full path location of your <mark>template</mark> directory.',

/**
 * Word Censoring
 */

'word_censorship' =>
'Word Censorship',

'enable_censoring' =>
'Enable censorship?',

'enable_censoring_desc' =>
'When set to <b>enable</b>, words listed will be replaced with the specified replacement characters.',

'censor_replacement' =>
'Replacement characters',

'censor_replacement_desc' =>
'Words that match any word in the words to censor list will be replaced with these characters.',

'censored_words' =>
'Words to censor',

'censored_words_desc' =>
'One word per line. All words listed will be replaced with the above specified characters.',

/**
 * Member Settings
 */

'member_settings' =>
'Member Settings',

'allow_member_registration' =>
'Allow registrations?',

'allow_member_registration_desc' =>
'When set to <b>yes</b>, users will be able to register member accounts.',

'req_mbr_activation' =>
'Account activation type',

'req_mbr_activation_desc' =>
'Choose how you want users to activate their registrations.',

'req_mbr_activation_opt_none' =>
'No activation required',

'req_mbr_activation_opt_email' =>
'Send activation e-mail',

'req_mbr_activation_opt_manual' =>
'Manually moderated by administrator',

'use_membership_captcha' =>
'Enable registration <abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr>?',

'use_membership_captcha_desc' =>
'When set to <b>enable</b>, users will be required to pass a <abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr> during registration.',

'require_terms_of_service' =>
'Require terms of service?',

'require_terms_of_service_desc' =>
'When set to <b>yes</b>, users must agree to terms of service during registration.',

'allow_member_localization' =>
'Allow members to set time preferences?',

'allow_member_localization_desc' =>
'When set to <b>yes</b>, members will be able to set a specific time and date localization for their account.',

'default_member_group' =>
'Default member group',

'default_member_group_desc' =>
'When a member meets the lock out requirement.',

'member_theme' =>
'Member profile theme',

'member_theme_desc' =>
'Default theme used for member profiles.',

'member_listing_settings' =>
'Member Listing Settings',

'memberlist_order_by' =>
'Sort by',

'memberlist_order_by_desc' =>
'Sorting type for the member listing.',

'memberlist_order_by_opt_posts' =>
'Total posts',

'memberlist_order_by_opt_screenname' =>
'Screen name',

'memberlist_order_by_opt_entries' =>
'Total entries',

'memberlist_order_by_reg_date' =>
'Registration date',

'memberlist_order_by_opt_comments' =>
'Total comments',

'memberlist_sort_order' =>
'Order by',

'memberlist_sort_order_desc' =>
'Sorting order for the member listing.',

'memberlist_sort_order_opt_asc' =>
'Ascending (A-Z)',

'memberlist_sort_order_opt_desc' =>
'Descending (Z-A)',

'memberlist_row_limit' =>
'Total results',

'memberlist_row_limit_desc' =>
'Total returned results per page for the member listing.',

'registration_notify_settings' =>
'Registration Notification Settings',

'new_member_notification' =>
'Enable new member notifications?',

'new_member_notification_desc' =>
'When set to <b>yes</b>, the following e-mail addresses will be notified anytime a new registration occurs.',

'mbr_notification_emails' =>
'Notification recipients',

'mbr_notification_emails_desc' =>
'Separate multiple e-mails with a comma.',

/**
 * Messages
 */

'messaging_settings' =>
'Messaging Settings',

'prv_msg_max_chars' =>
'Maximum characters',

'prv_msg_max_chars_desc' =>
'Maximum allowed characters in personal messages.',

'prv_msg_html_format' =>
'Formatting',

'prv_msg_html_format_desc' =>
'Select type of formatting to use for personal messages.',

"html_safe" =>
"Safe HTML only",

"html_all" =>
"All HTML (not recommended)",

"html_none" =>
"Convert HTML",

'prv_msg_auto_links' =>
'Convert <abbr title="Uniform Resource Location">URL</abbr>s and e-mails into links?',

'prv_msg_auto_links_desc' =>
'When set to <b>yes</b>, All <abbr title="Uniform Resource Location">URL</abbr>s and e-mails will be auto converted into hyper links.',

'attachment_settings' =>
'Attachment Settings',

'prv_msg_upload_path' =>
'Upload directory',

'prv_msg_upload_path_desc' =>
'Full path location for your <mark>attachement</mark> directory.',

'prv_msg_max_attachments' =>
'Maximum attachments',

'prv_msg_max_attachments_desc' =>
'Maximum allowed attachments per personal message.',

'prv_msg_attach_maxsize' =>
'Maximum file size (<abbr title="kilobyte">kb</abbr>)',

'prv_msg_attach_maxsize_desc' =>
'Maximum allowed file size per attachment in personal messages.',

'prv_msg_attach_total' =>
'Maximum total file size (<abbr title="megabyte">mb</abbr>)',

'prv_msg_attach_total_desc' =>
'Maximum allowed file size for all attachments for each member.',

/**
 * Avatars
 */

'avatar_settings' =>
'Avatar Settings',

'enable_avatars' =>
'Allow avatars?',

'enable_avatars_desc' =>
'When set to <b>yes</b>, members will be able to use avatars (representative images) in comments and forums.',

'allow_avatar_uploads' =>
'Allow avatar uploads?',

'allow_avatar_uploads_desc' =>
'When set to <b>yes</b>, members will be able to upload their own avatars (representative images).',

'avatar_url' =>
'Avatar directory',

'avatar_url_desc' =>
'<abbr title="Uniform Resource Location">URL</abbr> location of your <mark>avatar</mark> directory.',

'avatar_path' =>
'Avatar path',

'avatar_path_desc' =>
'Full path location of your <mark>avatar</mark> directory.',

'avatar_file_restrictions' =>
'Avatar File Restrictions',

'avatar_max_width' =>
'Maximum width',

'avatar_max_width_desc' =>
'Maximum allowed width of images uploaded for use as an avatar by members.',

'avatar_max_height' =>
'Maximum height',

'avatar_max_height_desc' =>
'Maximum allowed height of images uploaded for use as an avatar by members.',

'avatar_max_kb' =>
'Maximum file size (<abbr title="kilobytes">kb</abbr>)',

'avatar_max_kb_desc' =>
'Maximum allowed file size of images uploaded for use as an avatar by members.',

/**
 * CAPTCHA
 */

'captcha_settings' =>
'CAPTCHA Settings',

'captcha_settings_title' =>
'<abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr> Settings',

'captcha_font' =>
'Use TrueType font?',

'captcha_font_desc' =>
'When set to <b>yes</b>, <abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr> fields will use a TrueType font for display.',

'captcha_rand' =>
'Add random number?',

'captcha_rand_desc' =>
'When set to <b>yes</b>, <abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr> fields will randomly generate numbers as well as letters.',

'captcha_require_members' =>
'Require <abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr> while logged in?',

'captcha_require_members_desc' =>
'When set to <b>no</b>, logged in members will not be required to fill in <abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr> fields.',

'captcha_url' =>
'<abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr> directory',

'captcha_url_desc' =>
'<abbr title="Uniform Resource Location">URL</abbr> location of your <mark><abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr></mark> directory.',

'captcha_path' =>
'<abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr> path',

'captcha_path_desc' =>
'Full path location of your <mark><abbr title="Completely Automated Public Turing test to tell Computers and Humans Apart">CAPTCHA</abbr></mark> directory.',

/**
 * Security & Privacy
 */

'cp_session_type' =>
'<abbr title="Control Panel">CP</abbr> session type',

'website_session_type' =>
'Website Session type',

'cs_session' =>
'Cookies and session ID',

'c_session' =>
'Cookies only',

's_session' =>
'Session ID only',

'cookie_settings' =>
'Cookie Settings',

'cookie_domain' =>
'Domain',

'cookie_domain_desc' =>
'Use <mark>.yourdomain.com</mark> for system-wide cookies.',

'cookie_path' =>
'Path',

'cookie_path_desc' =>
'Path to apply cookies to the above domain. (<a href="%s">more info</a>)',

'cookie_prefix' =>
'Prefix',

'cookie_prefix_desc' =>
'Only required when running multiple installations of ExpressionEngine.',

'member_security_settings' =>
'Member Security Settings',

'allow_username_change' =>
'Allow members to change username?',

'allow_username_change_desc' =>
'When set to <b>yes</b>, members will be able to change their username.',

'un_min_len' =>
'Minimum username length',

'un_min_len_desc' =>
'Minimum number of characters required for new members\' usernames.',

'allow_multi_logins' =>
'Allow multiple logins?',

'allow_multi_logins_desc' =>
'When set to <b>yes</b>, members will be able to login simultaneously using one account. If session type is set to <mark>Cookies only</mark>, this will not work.',

'require_ip_for_login' =>
'Require user agent and <abbr title="Internet Protocol">IP</abbr> for login?',

'require_ip_for_login_desc' =>
'When set to <b>yes</b>, members will be unable to login without a valid user agent and <abbr title="Internet Protocol">IP</abbr> address.',

'password_lockout' =>
'Enable password lock out?',

'password_lockout_desc' =>
'When set to <b>enable</b>, members will be locked out of the system after failed log in attempts.',

'password_lockout_interval' =>
'Password lock out interval',

'password_lockout_interval_desc' =>
'Number of minutes a member should be locked out after four invalid login attempts.',

'require_secure_passwords' =>
'Require secure passwords?',

'require_secure_passwords_desc' =>
'When set to <b>yes</b>, members will be required to choose passwords containing at least one uppercase, one lowercase, and one numeric character.',

'pw_min_len' =>
'Minimum password length',

'pw_min_len_desc' =>
'Minimum number of characters required for new members\' passwords.',

'allow_dictionary_pw' =>
'Allow dictionary words in passwords?',

'allow_dictionary_pw_desc' =>
'When set to <b>yes</b>, members will be able to use common dictionary words in their password. <mark>requires dictionary file to be installed to enforce.</mark>',

'name_of_dictionary_file' =>
'Dictionary file',

'name_of_dictionary_file_desc' =>
'URL location of your <mark>dictionary</mark> file.',

'form_security_settings' =>
'Form Security Settings',

'deny_duplicate_data' =>
'Deny duplicate data?',

'deny_duplicate_data_desc' =>
'When set to <b>yes</b>, forms will disregard any submission that is an exact duplicate of existing data.',

'require_ip_for_posting' =>
'Require user agent and <abbr title="Internet Protocol">IP</abbr> for posting?',

'require_ip_for_posting_desc' =>
'When set to <b>yes</b>, members will be unable to post without a valid user agent and <abbr title="Internet Protocol">IP</abbr> address.',

'xss_clean_uploads' =>
'Apply <abbr title="Cross Site Scripting">XSS</abbr> filtering?',

'xss_clean_uploads_desc' =>
'When set to <b>yes</b>, forms will apply <abbr title="Cross Site Scripting">XSS</abbr> filtering to submissions.',

/**
 * Uploads
 */

'all_upload_dirs' =>
'All Upload Directories',

'file_manager' =>
'File Manager',

'upload_id' =>
'ID#',

'upload_name' =>
'Directory',

'upload_manage' =>
'Manage',

'upload_btn_view' =>
'view',

'upload_btn_edit' =>
'edit',

'upload_btn_sync' =>
'sync',

'upload_remove' =>
'Remove',

'upload_sync' =>
'Sync',

'upload_create' =>
'Create New',

''=>''
);

/* End of file settings_lang.php */
/* Location: ./system/expressionengine/language/english/settings_lang.php */
