<?php return array(
	'administration tool name test_mail_settings' => '测试邮件设置',
	'administration tool desc test_mail_settings' => '使用简单工具发送测试邮件，检查邮件系统是否配置正确',
	'administration tool name mass_mailer' => '批量邮件',
	'administration tool desc mass_mailer' => '可以让你发送文本消息到组中所有注册用户的简单工具',
	'configuration' => '配置',
	'mail transport mail()' => '默认的PHP设置',
	'mail transport smtp' => 'SMTP服务器',
	'secure smtp connection no' => '否',
	'secure smtp connection ssl' => '是，使用SSL',
	'secure smtp connection tls' => '是, 使用TLS',
	'file storage file system' => '文件系统',
	'file storage mysql' => '数据库(MySQL)',
	'config category name general' => '通用',
	'config category desc general' => '通用设置.',
	'config category name mailing' => '发送邮件',
	'config category desc mailing' => '使用这些配置可以设置Feng Office如何处理电子邮件的发送。你可以使用你的php.ini提供的配置选项或者设置为使用其他的SNMP服务器。',
	'config category name modules' => '模块',
	'config category desc modules' => '使用这些配置可以设置Feng Office模块是否启用。禁用一个模块仅会图形界面中隐藏，它并不移除用户创建或者编辑的内容对象的权限。',
	'config option name site_name' => '站点名称',
	'config option desc site_name' => '这个信息将显示在仪表板页面作为站点名称',
	'config option name file_storage_adapter' => '文件存储空间',
	'config option desc file_storage_adapter' => '选择你想要保存上传文档的位置，警告：切换存储空间将使以前上传的文件失效。',
	'config option name default_project_folders' => '缺省工作区文件夹',
	'config option desc default_project_folders' => '这些文件夹将在工作区创建时同时被创建，每一行是一个文件夹名称，相同的或者空行将被忽略。',
	'config option name theme' => '主题',
	'config option desc theme' => '使用主题你可以改变Feng Office的缺省观感，需要刷新以生效。',
	'config option name days_on_trash' => '垃圾保留天数',
	'config option desc days_on_trash' => '一个内容对象将在保留在回收站中多少天后被自动删除，如果为0，对象将不会自动删除。',
	'config option name enable_notes_module' => '启用笔记模块',
	'config option name enable_email_module' => '启用电子邮件模块',
	'config option name enable_contacts_module' => '启用联系人模块',
	'config option name enable_calendar_module' => '启用日历模块',
	'config option name enable_documents_module' => '启用文档模块',
	'config option name enable_tasks_module' => '启用任务模块',
	'config option name enable_weblinks_module' => '启用网站链接模块',
	'config option name enable_time_module' => '启用工时统计模块',
	'config option name enable_reporting_module' => '启用报告模块',
	'config option name upgrade_check_enabled' => '启用升级检测',
	'config option desc upgrade_check_enabled' => '如果启用系统即将每天一次检查是否有一个新版本的Feng Office可以下载。',
	'config option name work_day_start_time' => '工作日开始时间',
	'config option desc work_day_start_time' => '指定工作日的时间',
	'config option name use_minified_resources' => '使用压缩的资源',
	'config option desc use_minified_resources' => '使用压缩的Javascript 和 CSS 来改进性能。如果你编辑了它们，那么你需要重新压缩JS和CSS，请使用Feng Office目录下的public/tools。',
	'config option name exchange_compatible' => 'Microsoft Exchange 兼容模式',
	'config option desc exchange_compatible' => '如果你使用Microsoft Exchange 服务器，设置这个选项为 yes 来避免已知的邮件问题。',
	'config option name mail_transport' => '邮件传递',
	'config option desc mail_transport' => '你可以使用缺省的PHP配置或者指定SMTP服务器来发送邮件。',
	'config option name smtp_server' => 'SMTP 服务器',
	'config option name smtp_port' => 'SMTP 端口',
	'config option name smtp_authenticate' => '使用 SMTP 认证',
	'config option name smtp_username' => 'SMTP 用户名',
	'config option name smtp_password' => 'SMTP 密码',
	'config option name smtp_secure_connection' => '使用安全 SMTP 连接',
	'can edit company data' => '可以编辑公司数据',
	'can manage security' => '可以管理安全',
	'can manage workspaces' => '可以管理工作区',
	'can manage configuration' => '可以管理系统配置',
	'can manage contacts' => '可以管理联系人',
	'group users' => '组用户',
	'user ws config category name dashboard' => '仪表盘选项',
	'user ws config category name task panel' => '任务选项',
	'user ws config category name general' => '通用',
	'user ws config option name show pending tasks widget' => '显示挂起任务的小工具',
	'user ws config option name pending tasks widget assigned to filter' => '显示任务分配给',
	'user ws config option name show late tasks and milestones widget' => '显示超期的任务和里程碑的小工具',
	'user ws config option name show messages widget' => '显示笔记的小工具',
	'user ws config option name show comments widget' => '显示评论的小工具',
	'user ws config option name show documents widget' => '显示文档的小工具',
	'user ws config option name show calendar widget' => '显示小日历的小工具',
	'user ws config option name show charts widget' => '显示图表的小工具',
	'user ws config option name show emails widget' => '显示电子邮件的小工具',
	'user ws config option name localization' => '本地化',
	'user ws config option desc localization' => '标签和日期将依照这个本地设置被显示，需要刷新以生效。',
	'user ws config option name initialWorkspace' => '初始工作区',
	'user ws config option desc initialWorkspace' => '这个设置可以配置在你登录时自动选择的工作区，或者你可以选择记住你查看的最后的工作区。',
	'user ws config option name rememberGUIState' => '记住用户界面状态',
	'user ws config option desc rememberGUIState' => '这允许你保存图形界面的状态（面板大小，收缩/展开状态等等），在你下次登录时自动启用。警告：这是一个BETA测试版本的特性。',
	'user ws config option name time_format_use_24' => '使用24小时制显示时间',
	'user ws config option desc time_format_use_24' => '如果启用，时间信息将显示为：“hh:mm”的格式，从00:00 到 23:59, 否则，小时数将从 1 到 12 并使用 AM 或 PM.',
	'user ws config option name work_day_start_time' => '工作日开始时间',
	'user ws config option desc work_day_start_time' => '指定当工作日开始的时间',
	'user ws config option name my tasks is default view' => '分配给我的任务是缺省的视图',
	'user ws config option desc my tasks is default view' => '如果选择否，任务面板的缺省视图将显示所有的任务。',
	'user ws config option name show tasks in progress widget' => '显示“处理中任务”小工具',
	'user ws config option name can notify from quick add' => '缺省检查任务通告',
	'user ws config option desc can notify from quick add' => '通告可选框允许当任务被添加或者更新时通知分派的用户。',
	'backup process desc' => '备份保存整个应用程序的当前状态到一个压缩的文件夹， 它可以被用来简单备份一个完整的Feng Office安装。<br> 生成数据库和文件系统的备份可能会超过几秒，制作一个备份的处理过程有以下三步： <br>1、运行备份程序。 <br>2、下载这个备份包。 <br> 3、可选的，在未来不需要时，一个备份可以被手工删除。<br> ',
	'start backup' => '运行备份程序',
	'start backup desc' => '运行备份程序将删除上一次的备份数据，并生成一个新的。',
	'download backup' => '下载备份',
	'download backup desc' => '可以允许下载一个备份，你必须首先生成一份备份数据。',
	'delete backup' => '删除备份',
	'delete backup desc' => '删除最后的备份，它将不能被下载。强烈建议在下载后删除备份。',
	'backup' => '备份',
	'backup menu' => '备份菜单',
	'last backup' => '最后一次备份被创建于',
	'no backups' => '没有备份数据可以下载',
	'user ws config option name always show unread mail in dashboard' => '总是显示未读的电子邮件在仪表盘中',
	'user ws config option desc always show unread mail in dashboard' => '当选择NO时，活动工作区中的电子邮件将被显示在仪表盘。',
	'workspace emails' => '工作区邮件',
	'user ws config option name tasksShowWorkspaces' => '显示工作区',
	'user ws config option name tasksShowTime' => '显示时间',
	'user ws config option name tasksShowDates' => '显示日期',
	'user ws config option name tasksShowTags' => '显示标签',
	'user ws config option name tasksGroupBy' => '分组条件',
	'user ws config option name tasksOrderBy' => '排序条件',
	'user ws config option name task panel status' => '状态',
	'user ws config option name task panel filter' => '筛选条件',
	'user ws config option name task panel filter value' => '筛选信息',
	'templates' => '模板',
	'add template' => '添加模板',
	'confirm delete template' => '你是否确认删除这个模板?',
	'no templates' => '没有模板',
	'template name required' => '需要模板名称',
	'can manage templates' => '可以管理模板',
	'new template' => '新模板',
	'edit template' => '编辑模板',
	'template dnx' => '模板不存在',
	'success edit template' => '模板成功修改',
	'log add cotemplates' => '{0} 已添加',
	'log edit cotemplates' => '{0} 已修改',
	'success delete template' => '模板删除成功',
	'error delete template' => '删除模板错误',
	'objects' => '对象',
	'objects in template' => '模板中的对象',
	'no objects in template' => '这个模板中没有对象',
	'add to a template' => '添加模板',
	'add an object to template' => '添加一个对象到这个模板',
	'you are adding object to template' => '你正在添加{0}“{1}”到模板。在下面选择模板或者为{0}创建新模板 .',
	'success add object to template' => '对象添加到模板成功',
	'object type not supported' => '这个对象类型不被模板支持',
	'assign template to workspace' => '指定模板到工作区',
	'cron events' => '定时事件',
	'about cron events' => '关于定时事件...',
	'cron events info' => '定时事件可以让你在Feng Office中定期执行任务，而不需要登录到系统中。为了启用定时事件你需要配置一个定时作业来周期性的执行文件："cron.php"，这个文件在Feng Office的根目录下面。这个你运行定时作业的周期决定了你可以运行定时事件的最小时间间隔。 例如：如果你配置定时作业每5分钟一次，并且你配置检查更新的定时事件为每分钟一次， 那么实际上将每5分钟检查一次更新。如何配置定时作业请询问你的系统管理员或者主机提供商。',
	'cron event name check_mail' => '检查邮件',
	'cron event desc check_mail' => '这个定时事件将检查所有系统中电子邮件帐户中的新邮件。',
	'cron event name purge_trash' => '清理回收站',
	'cron event desc purge_trash' => '这个定时事件将删除超期的回收站中的对象。',
	'cron event name send_reminders' => '发送提醒',
	'cron event desc send_reminders' => '这个定时事件将自动发送电子邮件提醒。',
	'cron event name check_upgrade' => '检查更新',
	'cron event desc check_upgrade' => '这个定时事件将检测Feng Office的新版本。',
	'cron event name create_backup' => '创建备份',
	'cron event desc create_backup' => '创建一个备份让你可以从超级管理员的备份区下载。',
	'next execution' => '下一次执行',
	'delay between executions' => '在两次执行中的间隔',
	'enabled' => '启用',
	'no cron events to display' => '没有定时事件显示',
	'success update cron events' => '定时时间更新成功',
	'manual upgrade' => '手动升级',
	'manual upgrade desc' => '为了手动升级Feng Office你需要下载最新版本的Feng Office,解压缩到你安装的根目录然后在浏览器中定位到<a href="public/upgrade">\'public/upgrade\'</a> 运行升级程序。',
	'automatic upgrade' => '自动升级',
	'automatic upgrade desc' => '自动更新将自动下载并且解压缩新版本，并且运行升级程序，web服务器需要对所有的文件夹拥有写权限。',
	'start automatic upgrade' => '开始自动升级',
	'config category name passwords' => '密码',
	'config category desc passwords' => '用此设置管理密码选项',
	'autentify password title' => '验证密码',
	'autentify password desc' => '需要您进入管理面板。<br/>请重新输入密码。',
	'config option name time_format_use_24' => '使用24小时制',
	'config option name checkout_notification_dialog' => '文档签出提醒对话',
	'config option name file_revision_comments_required' => '文档修订意见为必填',
	'config option name show_feed_links' => '显示Feed链接',
	'config option name currency_code' => '货币',
	'config option desc currency_code' => '货币符号',
	'config option name smtp_address' => 'SMTP地址',
	'config option name user_email_fetch_count' => '邮件获取限额',
	'config option name min_password_length' => '最小密码长度',
	'config option desc min_password_length' => '密码所需字符最小数量',
	'config option name password_numbers' => '密码数字',
	'config option desc password_numbers' => '密码所需数字型字符数',
	'config option name password_uppercase_characters' => '密码大写字符',
	'config option desc password_uppercase_characters' => '密码所需大写字符数',
	'config option name password_metacharacters' => '密码元字符',
	'config option desc password_metacharacters' => '密码所需元字符数',
	'config option name password_expiration' => '密码有效期（天数）',
	'config option desc password_expiration' => '新密码有效天数（填“0”则禁用此项）',
	'can manage reports' => '可以管理报告',
	'user ws config category name calendar panel' => '日历选项',
	'user ws config category name mails panel' => '邮件选项',
	'user ws config option name show dashboard info widget' => '显示“工作区信息”小工具',
	'user ws config option name show getting started widget' => '显示“开始”小工具',
	'user ws config option name show_tasks_context_help' => '显示任务的上下文帮助',
	'user ws config option desc show_tasks_context_help' => '若启用，上下文帮助框将显示在任务面板。',
	'user ws config option name start_monday' => '每周始于周一',
	'user ws config option desc start_monday' => '将日历显示为始于周一（须刷新以应用更改）',
	'user ws config option name show_week_numbers' => '显示周数',
	'config option name validate_password_history' => '认证密码历史',
	'user ws config option name date_format' => '日期格式',
	'user ws config option name show_context_help' => '显示上下文帮助',
	'user ws config option name view deleted accounts emails' => '查看已删除账户的邮件',
	'user ws config option name block_email_images' => '不显示邮件中的图片',
	'user ws config option desc block_email_images' => '不显示邮件对象中嵌入的图片。',
	'user ws config option name draft_autosave_timeout' => '草稿自动保存间隔',
	'user ws config option desc draft_autosave_timeout' => '每次自动保存邮件草稿操作的间隔秒数',
	'show context help always' => '总是',
	'show context help never' => '从不',
	'show context help until close' => '直到关闭',
	'can manage time' => '可以管理工时统计',
	'can add mail accounts' => '可以添加邮件账户',
	'add a parameter to template' => '为此模板添加参数',
	'parameters' => '参数',
	'cron event name send_notifications_through_cron' => '通过计划发送提醒通知',
	'select object type' => '选择对象类型',
	'select one' => '选择一个',
	'email type' => '电子邮件类型',
	'custom properties updated' => '客户属性已更新',
	'user ws config option name noOfTasks' => '设置默认显示的任务数量',
	'user ws config option name amount_objects_to_show' => '要显示的链接对象数',
	'user ws config option desc amount_objects_to_show' => '设置在对象视图中显示的链接对象数',
	'user ws config option name show_two_weeks_calendar' => '显示“两周日历”小工具',
	'user ws config option desc show_two_weeks_calendar' => '设置日历工具显示两周日程',
	'user ws config option name attach_docs_content' => '附加文件内容',
	'default user preferences' => '默认用户参数',
	'mail accounts' => '邮件账户',
	'incoming server' => '收件服务器',
	'outgoing server' => '发件服务器',
	'no email accounts' => '无邮件账户',
	'user ws config option name create_contacts_from_email_recipients' => '从邮件收件人创建联系人',
	'user ws config option name drag_drop_prompt' => '拖放到工作区时要执行的操作',
	'drag drop prompt option' => '提醒用户作出一个操作',
	'mail drag drop classify option' => '分类附件',
	'mail drag drop dont option' => '不分类附件',
	'user ws config option name show_emails_as_conversations' => '以会话形式显示邮件',
	'user ws config option name search_engine' => '搜索引擎',
	'search engine mysql like' => '全面',
	'search engine mysql match' => '快速',
	'config option name account_block' => '密码过期时冻结账户',
	'user ws config option name descriptive_date_format' => '描述性日期格式',
	'user ws config option name hide_quoted_text_in_emails' => '查看邮件时隐藏引用文本',
	'edit default user preferences' => '编辑默认用户参数',
	'config option desc checkout_notification_dialog' => '若启用，系统将提醒用户在下载文件时是选择“编辑”或者“只读”。',
	'config option desc time_format_use_24' => '设置时间格式为24小时制。',
	'config option desc file_revision_comments_required' => '若选中，添加新文件版本时需要用户为每个版本填写评论。',
	'config option desc show_feed_links' => '此项允许向登录用户显示RSS或iCal链接，用户可以通过链接订阅相关信息。<strong>警告：</strong>这些链接包含的信息可以使用户登录到系统。如果不知情的用户共享了这个链接，他的所有信息权益可能会受到侵犯。',
	'config option name ask_administration_autentification' => '验证管理权限',
	'config option desc ask_administration_autentification' => '若选是，在用户访问“系统管理”面板时，系统将弹出密码验证对话框。',
	'config option name detect_mime_type_from_extension' => 'Detect mime type from extension',
	'config option desc detect_mime_type_from_extension' => 'Enable this to detect file\'s mime type by its extension',
	'config option desc smtp_address' => '可选。某些服务器需要你使用Email地址来从该服务器发送邮件。留空则使用用户账号中的Email地址。',
	'config option desc user_email_fetch_count' => '当用户点击“检查邮件账户”时，获取的邮件数。数值过大可能导致超时错误。“0”表示无限制。注：此选项不影响通过定时器（Cron）获取邮件的数量。',
	'config option name password_expiration_notification' => '密码过期提醒（提前天数）',
	'config option desc password_expiration_notification' => '密码过期提醒的提前天数（“0”禁用此选项）',
	'config option desc account_block' => '密码过期时冻结账户（需管理员开通冻结账户）',
	'config option name new_password_char_difference' => '验证新密码与历史密码的区别',
	'config option desc new_password_char_difference' => '验证新密码与前10个历史密码至少3个字符的区别',
	'config option desc validate_password_history' => '验证确保新密码与用户所用的前10个密码不同。',
	'config option name checkout_for_editing_online' => '在线编辑时自动签出',
	'config option desc checkout_for_editing_online' => '当用户在线编辑文档时，自动签出该文档，以免别人同时编辑。',
	'user ws config option desc show_week_numbers' => '在月视图和周视图中显示周数。',
	'user ws config option desc date_format' => '时间格式。代码说明：d = Day number (两位数，一位数则开头补“0”)，D = Day name (三个字母)，j = Day number，l = Complete day name, m = Month number (一位数则开头补“0”), M = Month name (三个字母), n = Month number, F = Complete month name, Y = Year (4位数), y = Year (2位数). 刷新查看变更结果。',
	'user ws config option desc descriptive_date_format' => '应用到描述性日期值的模板格式。代码注释： 同“日期格式”。刷新查看变更结果。',
	'user ws config option desc show_context_help' => '选中此项，则一直显示帮助信息，从不显示，或者直到区块关闭。',
	'user ws config option desc view deleted accounts emails' => '选中此项，您就可以查看已删除的邮件账户中的邮件（要使用本功能，您在删除邮件账户时，不能删除账户中的邮件）。',
	'cron event desc send_notifications_through_cron' => '若此事件开启，邮件提醒将通过cron发送，而不是当系统生成事件时发送。',
	'user ws config option desc attach_docs_content' => '选“是”，则文件附件将被添加为正常的邮件附件；选“否”，则将以文件链接的形式添加到邮件附件。',
	'user ws config option name max_spam_level' => '所允许的最高垃圾邮件等级',
	'user ws config option desc max_spam_level' => '在获取邮件时，高于垃圾邮件等级的邮件将被转到“垃圾”文件夹。“0”最大过滤，“10”则无过滤。此选项仅在垃圾邮件过滤工具已安装在服务器上的情况下起作用。',
	'user ws config option desc hide_quoted_text_in_emails' => '若启用，Email信息将不显示引用文本。当查阅邮件时，将会有查看引用文本的选项。',
	'default user preferences desc' => '为用户偏好选择默认值。用户未选择选项值时，将应用这个值。',
	'user ws config option desc create_contacts_from_email_recipients' => '当选“是”时，将根据您所发邮件的每个收件人Email地址自动创建一个联系人。您需要“可以管理所有联系人”的权限。',
	'user ws config option desc drag_drop_prompt' => '选择当拖拽一个对象到工作区时将采取何种操作。',
	'drag drop move option' => '从旧工作区中移动到新工作区',
	'drag drop keep option' => '添加到新工作区，并在旧工作区中保留',
	'user ws config option name mail_drag_drop_prompt' => '当拖拽时分类邮件附件？',
	'user ws config option desc mail_drag_drop_prompt' => '选择当拖动邮件到某工作区时对邮件附件所采取的处理方式。',
	'mail drag drop prompt option' => '弹出提醒用户执行一个操作',
	'user ws config option desc show_emails_as_conversations' => '若启用，邮件将被归组到邮件列表所在会话中。将以列表中的一条信息的形式显示所有属于同一请求（回复、转发等）的邮件。',
	'user ws config option name autodetect_time_zone' => '自动检测时区',
	'user ws config option desc autodetect_time_zone' => '启用此项，则用户的时区将通过自动探测浏览器来设置。',
	'user ws config option desc search_engine' => '选择使用哪个搜索引擎。“全面”将执行更全面的搜索，但是会比“快速”需要更长时间。',
	'user ws config option name show activity widget' => '显示“动态”工具',
	'user ws config option name detect_mime_type_from_extension' => '从扩展名检测MIME类型',
	'user ws config option desc detect_mime_type_from_extension' => '启用此项，从扩展名检测文件的MIME类型',
	'user ws config option name activity widget elements' => '“动态”工具大小',
	'user ws config option desc activity widget elements' => '动态”工具中显示的项的数量。',
	'user ws config option name task_display_limit' => '任务显示的最大条数',
	'user ws config option desc task_display_limit' => '出于性能考虑，此数值不可太大。填“0”则不限制。',
	'config option name use_owner_company_logo_at_header' => '使用公司Logo作为系统图标',
	'config option desc use_owner_company_logo_at_header' => '公司Logo显示于屏幕右上角。刷新（按F5）页面应用变更。Logo建议大小50 x 50像素。',
	'user ws config option name mail_account_err_check_interval' => '邮件账户检查间隔设置错误',
	'sync' => '同步',
	'mails on imap acc already sync' => '此账户没有要同步的邮件了。',
	'cant sync account' => '此账户无法同步，请检查IMAP设置。',
	'succes save object subtypes' => '对象子类型保存成功！',
	'user ws config option name classify_mail_with_conversation' => '使用会话来分类邮件',
	'user ws config option desc classify_mail_with_conversation' => '若启用，已收邮件将被归类到会话所在工作区（如果邮件属于某个会话的话）。',
	'user ws config option desc mail_account_err_check_interval' => '检查邮件账户的时间间隔（“0”为禁用）',
	'config option name sent_mails_sync' => '当从OA系统发送邮件时，启用IMAP同步。',
); ?>
