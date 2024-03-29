<?php

  return array(
  
    // ---------------------------------------------------
    //  Administration tools
    // ---------------------------------------------------
    
    'administration tool name test_mail_settings' => 'Test mail settings',
    'administration tool desc test_mail_settings' => 'Use this simple tool to send test emails to check if Feng Office mailer is well configured',
    'administration tool name mass_mailer' => 'Mass mailer',
    'administration tool desc mass_mailer' => 'Simple tool that let you send plain text messages to any group of users registered to the system',
  
    // ---------------------------------------------------
    //  Configuration categories and options
    // ---------------------------------------------------
  
    'configuration' => 'Configuration',
    
    'mail transport mail()' => 'Default PHP settings',
    'mail transport smtp' => 'SMTP server',
    
    'secure smtp connection no'  => 'No',
    'secure smtp connection ssl' => 'Yes, use SSL',
    'secure smtp connection tls' => 'Yes, use TLS',
    
    'file storage file system' => 'File system',
    'file storage mysql' => 'Database (MySQL)',
    
    // Categories
    'config category name general' => 'General',
    'config category desc general' => 'General Feng Office settings.',
    'config category name mailing' => 'Mailing',
    'config category desc mailing' => 'Use these settings to set up how Feng Office should handle email sending. You can use configuration options provided in your php.ini or set it so it uses any other SMTP server.',
  	'config category name modules' => 'Modules',
    'config category desc modules' => 'Use these settings to enable or disable Feng Office modules. Disabling a module only hides it from the graphical interface. It doesn\'t remove permissions from users to create or edit content objects.',
	'config category name passwords' => 'Passwords',
    'config category desc passwords' => 'Use these settings to manage password options.',
	
	//--------------------------------------------------
    // Validate Password
    // ---------------------------------------------------

  	'autentify password title'=> 'Authenticate Password',
  	'autentify password desc'=> 'You requested to enter to the administrator panel.<br/> Please re-enter your password',
  
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    // General
    'config option name site_name' => 'Site name',
    'config option desc site_name' => 'This value will be displayed as the site name on the Dashboard page',
    'config option name file_storage_adapter' => 'File storage',
    'config option desc file_storage_adapter' => 'Select where you want to store uploaded documents. Warning: Switching storage will make all previuosly uploaded files unavailable.',
    'config option name default_project_folders' => 'Default folders',
    'config option desc default_project_folders' => 'Folders that will be created when workspace is created. Every folder name should be in a new line. Duplicate or empty lines will be ignored',
    'config option name theme' => 'Theme',
    'config option desc theme' => 'Using themes you can change the default look and feel of Feng Office. Need to refresh for it to take effect.',
  	'config option name days_on_trash' => 'Days on trash',
    'config option desc days_on_trash' => 'How many days a content object is kept in the trash before being automatically deleted. If 0, objects won\'t be deleted from trash.',
	'config option name checkout_notification_dialog' => 'Checkout notification dialog for documents',
    'config option desc checkout_notification_dialog' => 'If enabled, the user will be prompted when downloading a file to select between editing or read only',
  	'config option name time_format_use_24' => 'Use 24 hour format',
  	'config option desc time_format_use_24' => 'If set, the 24 hour format will be used instead of the 12 hour format',
  	'config option name file_revision_comments_required' => 'File revision comments required',
    'config option desc file_revision_comments_required' => 'If set, adding new file revisions requires users to provide a new comment for each revision.',
 	'config option name show_feed_links' => 'Show feed links',
    'config option desc show_feed_links' => 'This allows you to show links to RSS or iCal feeds to the logged user throughout the system, so that he can subscribe to them. WARNING: These links contain information that can login a user to the system. If an unaware user shares one of this links he could be compromising all of his information.',
	
	'config option name ask_administration_autentification' => 'Authenticate administration',
    'config option desc ask_administration_autentification' => 'If "Yes" is chosen, a password authentication dialog will be displayed when accessing the administration panel',
  
  	'config option name external_users_see_other_users' => 'Allow external users see other users', 
  	'config option desc external_users_see_other_users' => "If 'Yes' is chosen, users that belong to other companies may see which users have permissions in the workspace they are standing at, as regular users do",
  
  	'config option name enable_notes_module' => 'Enable Notes Module',
  	'config option name enable_email_module' => 'Enable Email Module',
  	'config option name enable_contacts_module' => 'Enable Contacts Module',
  	'config option name enable_calendar_module' => 'Enable Calendar Module',
  	'config option name enable_documents_module' => 'Enable Documents Module',
  	'config option name enable_tasks_module' => 'Enable Tasks Module',
  	'config option name enable_weblinks_module' => 'Enable Web Links Module',
  	'config option name enable_time_module' => 'Enable Time Module',
  	'config option name enable_reporting_module' => 'Enable Reporting Module',
  
    'config option name upgrade_check_enabled' => 'Enable upgrade check',
    'config option desc upgrade_check_enabled' => 'If Yes system will once a day check if there are new versions of Feng Office available for download',
	'config option name work_day_start_time' => 'Work day start time',
  	'config option desc work_day_start_time' => 'Specifies the time when work day starts',
  
  	'config option name use_minified_resources' => 'Use minified resources',
  	'config option desc use_minified_resources' => 'Uses compressed Javascript and CSS to improve performance. You need to recompress JS and CSS if you modify them, by using the public/tools.',
    
  	'config option name currency_code' => 'Currency',
  	'config option desc currency_code' => 'Currency symbol',
	'config option name use_owner_company_logo_at_header' => 'Use Owner company\'s logo as application logo',
  	'config option desc use_owner_company_logo_at_header' => 'Put the Owner company\'s logo at the top right corner of the screen. Must refresh (F5) to apply the changes. Recommended size: 50x50 px.',
  
    // Mailing
    'config option name exchange_compatible' => 'Microsoft Exchange compatibility mode',
    'config option desc exchange_compatible' => 'If you are using Microsoft Exchange Server set this option to yes to avoid some known mailing problems.',
    'config option name mail_transport' => 'Mail transport',
    'config option desc mail_transport' => 'You can use default PHP settings for sending emails or specify SMTP server',
    'config option name smtp_server' => 'SMTP server',
  	'config option name smtp_address' => 'SMTP address',
  	'config option desc smtp_address' => 'Optional. Some servers require that you use an email address from that server to send email. Leave blank to use the user\'s email address.',
    'config option name smtp_port' => 'SMTP port',
    'config option name smtp_authenticate' => 'Use SMTP authentication',
    'config option name smtp_username' => 'SMTP username',
    'config option name smtp_password' => 'SMTP password',
    'config option name smtp_secure_connection' => 'Use secure SMTP connection',
  	'config option name user_email_fetch_count' => 'Email fetch limit',
  	'config option desc user_email_fetch_count' => 'How many emails to fetch when a user clicks on the "Check mail accounts" button. Using a large value may cause timeout errors to the user. Use 0 for no limit. Note, this doesn\'t affect email fetching through cron.',
	'config option name check_spam_in_subject' => 'Check email subjects for the text "**SPAM**"',
	'config option desc check_spam_in_subject' => 'If "**SPAM**" is found in subject then the email is sent to the junk folder.',
  
  	// Passwords
  	'config option name min_password_length' => 'Minimum password length',
  	'config option desc min_password_length' => 'Minimum number of characters required for the password',
  	'config option name password_numbers' => 'Password numbers',
  	'config option desc password_numbers' => 'Amount of numerical characters required for the password',
  	'config option name password_uppercase_characters' => 'Password uppercase characters',
  	'config option desc password_uppercase_characters' => 'Amount of uppercase characters required for the password',
  	'config option name password_metacharacters' => 'Password metacharacters',
  	'config option desc password_metacharacters' => 'Amount of metacharacters required for the password (e.g.: ., $, *)',
  	'config option name password_expiration' => 'Password expiration (days)',
  	'config option desc password_expiration' => 'Number of days in which a new password is valid (0 to disable this option)',
  	'config option name password_expiration_notification' => 'Password expiration notification (days before)',
  	'config option desc password_expiration_notification' => 'Number of days to notifify user before password expiration (0 to disable this option)',
  	'config option name account_block' => 'Block account on password expiration',
  	'config option desc account_block' => 'Block user account when password expires (requires admin to enable user account again)',
	'config option name new_password_char_difference' => 'Validate new password character difference against history',
  	'config option desc new_password_char_difference' => 'Validate that a new password differs in at least 3 characters against the last 10 passwords used by the user',
    'config option name validate_password_history' => 'Validate password history',
  	'config option desc validate_password_history' => 'Validate that a new password doesn\'t match any of the last 10 passwords used by the user',
  	'config option name checkout_for_editing_online' => 'Automatically checkout when editing online',
  	'config option desc checkout_for_editing_online' => 'When a user edits a document online it will be checkout so that no one else can edit it at the same time',
	'config option name block_login_after_x_tries' => 'Block login after 5 wrong tries',
	'config option desc block_login_after_x_tries' => 'If a user tries to login 5 times without success in the last 10 minutes, then the account will be blocked for 10 minutes.',
  
 	'can edit company data' => 'Can edit owner company data',
  	'can manage security' => 'Can manage security',
  	'can manage workspaces' => 'Can manage workspaces',
  	'can manage configuration' => 'Can manage configuration',
  	'can manage contacts' => 'Can manage all contacts',
  	'can manage reports' => 'Can manage reports',
  	'group users' => 'Group users',
    
  	
  	'user ws config category name dashboard' => 'Dashboard options',
  	'user ws config category name task panel' => 'Task options',
  	'user ws config category name general' => 'General',
	'user ws config category name calendar panel' => 'Calendar options',
	'user ws config category name mails panel' => 'Email options',
  	'user ws config option name show pending tasks widget' => 'Show pending tasks widget',
  	'user ws config option name pending tasks widget assigned to filter' => 'Show tasks assigned to',
  	'user ws config option name show late tasks and milestones widget' => 'Show late tasks and milestones widget',
  	'user ws config option name show messages widget' => 'Show notes widget',
  	'user ws config option name show comments widget' => 'Show comments widget',
  	'user ws config option name show documents widget' => 'Show documents widget',
  	'user ws config option name show calendar widget' => 'Show mini calendar widget',
  	'user ws config option name show charts widget' => 'Show charts widget',
  	'user ws config option name show emails widget' => 'Show emails widget',
  	'user ws config option name show dashboard info widget' => 'Show dashboard info widget',
  	'user ws config option name show getting started widget' => 'Show getting started widget',
  	'user ws config option name localization' => 'Localization',
  	'user ws config option desc localization' => 'Labels and dates will be displayed according to this locale. Need to refresh for it to take effect.',
  	'user ws config option name initialWorkspace' => 'Initial workspace',
  	'user ws config option desc initialWorkspace' => 'This setting lets you choose which workspace you will be selected when you login, or you can choose to remember the last workspace you were viewing.',
  	'user ws config option name rememberGUIState' => 'Remember the user interface state',
  	'user ws config option desc rememberGUIState' => 'This allows you to save the state of the graphical interface (size of panels, expanded/collapsed state, etc) for the next time that you log in. Warning: This feature is in BETA status.',
  	'user ws config option name time_format_use_24' => 'Use 24 hours format for time descriptions',
  	'user ws config option desc time_format_use_24' => 'If enabled time descriptions will be shown as \'hh:mm\' from 00:00 to 23:59, if not hours will go from 1 to 12 using AM or PM.',
  	'user ws config option name work_day_start_time' => 'Work day start time',
	'user ws config option desc work_day_start_time' => 'Specifies the time when work day starts',
  	'user ws config option name show dashboard info widget' => 'Show workspace information widget',
  	'user ws config option name show activity widget' => 'Show activity widget',
  	
  	'user ws config option name my tasks is default view' => 'Tasks assigned to me is the default view',
  	'user ws config option desc my tasks is default view' => 'If no is selected, the default view of the task panel will show all tasks',
  	'user ws config option name show tasks in progress widget' => 'Show \'Tasks in progress\' widget',
  	'user ws config option name can notify from quick add' => 'Task notification is checked by default',
  	'user ws config option desc can notify from quick add' => 'The notification checkbox enables the option to notify assigned users after a task is added or updated',
	'user ws config option name show_tasks_context_help' => 'Show context help for tasks',
  	'user ws config option desc show_tasks_context_help' => 'If enabled, a context help box will be displayed on the tasks panel',
 	'user ws config option name start_monday' => 'Start week on monday',
	'user ws config option desc start_monday' => 'Will show the calendar starting weeks on monday (must refresh to apply changes)',
  	'user ws config option name show_week_numbers' => 'Show week numbers',
	'user ws config option desc show_week_numbers' => 'Shows the week numbers on the monthly and weekly views.',
  
  	'user ws config option name date_format' => 'Date format',
  	'user ws config option desc date_format' => 'Template format to be applied to date values. d = day, m = month, y = year. You need to refresh in order to apply the changes.',
  	'user ws config option name descriptive_date_format' => 'Descriptive date format',
  	'user ws config option desc descriptive_date_format' => 'Template format to be applied to descriptive date values. Code explanations: d = Day number (2 digits with leading zeros), D = Day name (three letters), j = Day number, l = Complete day name, m = Month number (with leading zeros), M = Month name (three letters), n = Month number, F = Complete month name, Y = Year (4 digits), y = Year (2 digits). Refresh required.',

  	'user ws config option name show_context_help' => 'Show contextual help',
  	'user ws config option desc show_context_help' => 'Select if you want to always view help, never view it, or view it until each box is closed.',
  	'user ws config option name view deleted accounts emails' => 'View deleted accounts\' emails',
  	'user ws config option desc view deleted accounts emails' => 'Enables you to view the emails from your deleted email accounts (when you delete an account you must not delete emails to use this option)',
	'user ws config option name block_email_images' => 'Block email images',
	'user ws config option desc block_email_images' => 'Do not show images that are embedded in mail objects.',
	'user ws config option name draft_autosave_timeout' => 'Draft autosave interval',
	'user ws config option desc draft_autosave_timeout' => 'Seconds between each autosave operation for draft mails (0 to disable autosave)',
	'user ws config option name classify_mail_with_conversation' => 'Classify mails with its conversation',
	'user ws config option desc classify_mail_with_conversation' => 'If enabled received mails are classified into the conversation workspaces (if email belongs to a conversation).',
  
	'user ws config option name detect_mime_type_from_extension' => 'Detect mime type from extension',
  	'user ws config option desc detect_mime_type_from_extension' => 'Enable this to detect file\'s mime type by its extension',
  
  	'user ws config option name displayed events amount' => 'Number of events displayed',
  	'user ws config option desc displayed events amount' => 'The number of events per day that are shown in the month view',
  
  	'show context help always' => 'Always',
	'show context help never' => 'Never',
	'show context help until close' => 'Until close',
     	
   	'user ws config option name always show unread mail in dashboard' => 'Always show unread email in dashboard',
   	'user ws config option desc always show unread mail in dashboard' => 'When NO is chosen emails from the active workspace will be shown',
   	'workspace emails' => 'Workspace Mails',
  	'user ws config option name tasksShowWorkspaces' => 'Show workspaces',
  	'user ws config option name tasksShowTime' => 'Show time',
  	'user ws config option name tasksShowDates' => 'Show dates',
  	'user ws config option name tasksShowTags' => 'Show tags',
  	'user ws config option name tasksGroupBy' => 'Group by',
  	'user ws config option name tasksOrderBy' => 'Order by',
  	'user ws config option name task panel status' => 'Status',
  	'user ws config option name task panel filter' => 'Filter by',
  	'user ws config option name task panel filter value' => 'Filter value',
  
  	'templates' => 'Templates',
	'add template' => 'Add template',
	'confirm delete template' => 'Are you sure you want to delete this template?',
	'no templates' => 'There are no templates',
	'template name required' => 'The template\'s name is required',
	'can manage templates' => 'Can manage templates',
    'can manage time' => 'Can manage time',
  	'can add mail accounts' => 'Can add mail accounts',
	'new template' => 'New template',
	'edit template' => 'Edit template',
	'template dnx' => 'The template does not exist',
	'success edit template' => 'Template modified successfully',
	'log add cotemplates' => '{0} added',
	'log edit cotemplates' => '{0} modified',
	'success delete template' => 'Template deleted successfully',
	'error delete template' => 'Error deleting the template',
	'objects' => 'Objects',
	'objects in template' => 'Objects in template',
	'no objects in template' => 'There are no objects in this template',
	'add to a template' => 'Add to a template',
  	'add an object to template' => 'Add an object to this template',
  	'add a parameter to template' => 'Add a parameter to this template',
	'you are adding object to template' => 'You are adding {0} \'{1}\' to a template. Choose a template below or create a new one.',
	'success add object to template' => 'Object added to template successfully',
	'object type not supported' => 'This object type is not supported for templates',
  	'assign template to workspace' => 'Assign template to workspace',
  	'parameters' => 'Parameters',
  
  	'cron events' => 'Cron events',
  	'about cron events' => 'Learn about cron events...',
  	'cron events info' => 'Cron events let you execute tasks in Feng Office periodically, without having to login to the system. To enable cron events you need to configure a cron job to periodically execute the "cron.php" file, located at the root of Feng Office. The periodicity at which you run the cron job will determine the granularity at which you can run these cron events. For example, if you configure a cron job to run every five minutes, and you configure the cron event to check for upgrades every one minute, it will only be able to check for upgrades every five minutes. To learn about how to configure a cron job ask your system administrator or hosting provider.',
  	'cron event name check_mail' => 'Check mail',
  	'cron event desc check_mail' => 'This cron event will check for new email in all email accounts in the system.',
  	'cron event name purge_trash' => 'Purge trash',
  	'cron event desc purge_trash' => 'This cron event will delete objects older than the amount of days specified in the \'Days in trash\' configuration.',
  	'cron event name send_reminders' => 'Send reminders',
  	'cron event desc send_reminders' => 'This cron event will send email reminders.',
  	'cron event name check_upgrade' => 'Check upgrade',
  	'cron event desc check_upgrade' => 'This cron event will check for new versions of Feng Office.',
  	'cron event name send_notifications_through_cron' => 'Send notifications through cron',
  	'cron event desc send_notifications_through_cron' => 'If this event is enabled email notifications will be sent through cron and not when generated by Feng Office.',
  	'next execution' => 'Next execution',
  	'delay between executions' => 'Delay between executions',
  	'enabled' => 'Enabled',
  	'no cron events to display' => 'There are no cron events to display',
  	'success update cron events' => 'Cron events updated successfully',
  
  	'manual upgrade' => 'Manual upgrade',
  	'manual upgrade desc' => 'To manually upgrade Feng Office you have to download the new version of Feng Office, extract it to the root of your installation and then go to <a href="public/upgrade">\'public/upgrade\'</a> in your browser to run the upgrade process.',
  	'automatic upgrade' => 'Automatic upgrade',
  	'automatic upgrade desc' => 'The automatic upgrade will automatically download and extract the new version, and will run the upgrade process for you. The webserver needs write access to all folders.',
  	'start automatic upgrade' => 'Start automatic upgrade',
  
  	'select object type' => 'Select object type',
  	'select one' => 'Select one',
  	'email type' => 'Email',
  	'custom properties updated' => 'Custom properties updated',
  	'user ws config option name noOfTasks' => 'Set number of tasks shown as default',
  
  	'user ws config option name amount_objects_to_show' => 'Number of Linked Objects to show',
  	'user ws config option desc amount_objects_to_show' => 'Sets the number of Linked Objects to be displayed on object\'s views',
   	'user ws config option name show_two_weeks_calendar' => 'Show two weeks calendar widget',
  	'user ws config option desc show_two_weeks_calendar' => 'Sets the calendar widget to display two weeks',
	'user ws config option name attach_docs_content' => 'Attach files contents',
	'user ws config option desc attach_docs_content' => 'When this option is set to "Yes" file attachments will be added as regular email attachments. When "No" is chosen file attachments will be sent as a link to the file.',
	'user ws config option name max_spam_level' => 'Maximum spam level allowed',
	'user ws config option desc max_spam_level' => 'When fetching emails, messages with Spam evaluation greater than this value will be sent to "Junk" folder. Set to 0 for max filtering, 10 no filtering. This option works only if a spam filter tool is installed in your server.',
  
	'user ws config option name hide_quoted_text_in_emails' => 'Hide quoted text when viewing emails',
	'user ws config option desc hide_quoted_text_in_emails' => 'If enabled email messages will be displayed without the quoted text. There will be an option to view it while reading.',
  
	'edit default user preferences' => 'Edit default user preferences',
  	'default user preferences' => 'Default user preferences',
  	'default user preferences desc' => 'Choose the default values for user preferences. This values apply when the user hasn\'t chosen a value for an option yet.',
  
  	'mail accounts' => 'Email accounts',
  	'incoming server' => 'Incoming server',
  	'outgoing server' => 'Outgoing server',
  	'no email accounts' => 'No email accounts',
  	'user ws config option name create_contacts_from_email_recipients' => 'Create contacts from email recipients',
  	'user ws config option desc create_contacts_from_email_recipients' => 'When this option is set to "Yes" a contact will be automatically created for every email address you send an email to. You need the "Can manage all contacts" permission.',
  	'user ws config option name drag_drop_prompt' => 'Action to take on drag and drop to workspace',
  	'user ws config option desc drag_drop_prompt' => 'Choose which action should be taken when dragging an object to a workspace.',
  	'drag drop prompt option' => 'Prompt user for an action',
  	'drag drop move option' => 'Move to new workspace and lose previous workspaces',
  	'drag drop keep option' => 'Add to new workspace while keeping previous workspaces',
  	'user ws config option name mail_drag_drop_prompt' => 'Classify email attachments on drag and drop?',
  	'user ws config option desc mail_drag_drop_prompt' => 'Choose what should be done with email attachments when dragging an email to a workspace.',
  	'mail drag drop prompt option' => 'Prompt user for an action',
  	'mail drag drop classify option' => 'Classify attachments',
  	'mail drag drop dont option' => 'Don\'t classify attachments',
  	'user ws config option name show_emails_as_conversations' => 'Show emails as conversations',
  	'user ws config option desc show_emails_as_conversations' => 'If enabled email will be grouped into conversations in the Emails listing, showing all emails belonging to a same thread (replies, forwards, etc) as one entry in the listing.',
  	'user ws config option name autodetect_time_zone' => 'Autodetect timezone',
  	'user ws config option desc autodetect_time_zone' => 'When this option is enabled, the user\'s timezone will be autodetected from browser.',
  	'user ws config option name search_engine' => 'Search engine',
  	'user ws config option desc search_engine' => 'Choose which search engine to use. "Full" will do a more exhaustive search but will take much longer than "Quick". "Full" is not recommended for heavily loaded installations.',
	'user ws config option name activity widget elements' => 'Activity widget size',
	'user ws config option desc activity widget elements' => 'Number of items displayed in Activity widget.',
	'user ws config option name mail_account_err_check_interval' => 'Email accounts error checking interval',
	'user ws config option desc mail_account_err_check_interval' => 'Time between each verification at email accounts (0 to disable)',
  
  	'search engine mysql like' => 'Full',
  	'search engine mysql match' => 'Quick',
  
  	'user ws config option name task_display_limit' => 'Maximum number of tasks to display',
  	'user ws config option desc task_display_limit' => 'For performance reasons, this number should not be too big. Use 0 for no limit.',
  	
  	'user ws config option name show_file_revisions_search' => 'See File Revisions when performing searchs',	
  
  	'sync' => 'Sync',
  	'mails on imap acc already sync' => 'There are no emails left to be synchronized in this account',
  	'cant sync account' => 'This account cannot be synchronized. Check IMAP settings',
  
  	'config option name sent_mails_sync' => 'Enable IMAP synchronization', 
    'config option desc sent_mails_sync' => 'This option enables the synchronization when sending emails from Feng Office with the mail server through IMAP',
  
  	'succes save object subtypes' => 'Object subtypes saved sucessfully',
  
  ); // array

?>