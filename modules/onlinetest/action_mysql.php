<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2016 Nuke.vn. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 11 Jul 2016 09:00:15 GMT
 */

if (!defined('NV_IS_FILE_MODULES'))
    die('Stop!!!');

$sql_drop_module = array();

$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_bank";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_category";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_comment";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_contribute_permission";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_user";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_exam";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_log";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_level";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_limit";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_money";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_point";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_question";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_question_import";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_ranking";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_recharge";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rechargebank";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_report";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_type_exam";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_essay";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_essay_exam";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_essay";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_essay_row";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_limit_essay";

$sql_create_module = $sql_drop_module;
 

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_essay (
	essay_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	category_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	user_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	user_name varchar(250)  COLLATE utf8mb4_unicode_ci NOT NULL,
	question mediumtext  COLLATE utf8mb4_unicode_ci NOT NULL,
	status tinyint(1) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	date_modified int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (essay_id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_essay_exam (
  essay_exam_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  group_exam_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  group_exam_list varchar(255)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  title varchar(250)  COLLATE utf8mb4_unicode_ci NOT NULL,
  code varchar(20)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  images varchar(250)  COLLATE utf8mb4_unicode_ci NOT NULL,
  thumb tinyint(1) unsigned NOT NULL DEFAULT '0',
  introtext mediumtext  COLLATE utf8mb4_unicode_ci NOT NULL,
  description mediumtext  COLLATE utf8mb4_unicode_ci NOT NULL,
  keywords mediumtext  COLLATE utf8mb4_unicode_ci NOT NULL,
  num_question smallint(4) unsigned NOT NULL DEFAULT '0',
  point mediumint(8) unsigned NOT NULL DEFAULT '0',
  time int(11) unsigned NOT NULL DEFAULT '0',
  config text  COLLATE utf8mb4_unicode_ci NOT NULL,
  rules text  COLLATE utf8mb4_unicode_ci NOT NULL,
  group_user varchar(255)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  user_create_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  viewed mediumint(8) unsigned NOT NULL DEFAULT '0',
  status tinyint(1) unsigned NOT NULL DEFAULT '0',
  date_added int(11) unsigned NOT NULL DEFAULT '0',
  date_modified int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (essay_exam_id),
  UNIQUE KEY title (title),
  UNIQUE KEY code (code)
) ENGINE=MyISAM;";
 

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_essay (
  history_essay_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  history_alias varchar(15)  COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  userid mediumint(8) unsigned NOT NULL DEFAULT '0',
  test_time int(1) unsigned NOT NULL DEFAULT '0' COMMENT 'timestamp',
  time_do_test int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Giây',
  score smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'Điểm thi',
  max_score smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Max điểm cho bài thi',
  point mediumint(8) unsigned NOT NULL DEFAULT '0',
  essay_exam_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  num_question smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Số câu hỏi mặc định',
  time smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT 'Phút',
  is_sended tinyint(1) unsigned NOT NULL DEFAULT '0',
  is_deleted tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (history_essay_id),
  UNIQUE KEY history_alias (history_alias)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_essay_row (
  row_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  essay_id int(11) unsigned NOT NULL DEFAULT '0',
  question mediumtext  COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'câu hỏi',
  answer mediumtext  COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'đáp án',
  history_essay_id mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT 'id history',
  PRIMARY KEY (row_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_limit_essay (
  userid mediumint(8) unsigned NOT NULL DEFAULT '0',
  essay_exam_id mediumint(8) unsigned NOT NULL DEFAULT '0',
  test_limit smallint(3) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY userid_essay_exam_id (userid,essay_exam_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_bank (
	bank_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(250) NOT NULL DEFAULT '',
	code varchar(50) NOT NULL DEFAULT '',
	weight mediumint(8) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (bank_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_user (
	group_user_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	parent_id smallint(5) unsigned NOT NULL DEFAULT 0,
	title varchar(250) NOT NULL,
	alias varchar(250) NOT NULL DEFAULT '',
	description text NOT NULL,
	weight smallint(5) unsigned NOT NULL DEFAULT 0,
	sort smallint(5) NOT NULL DEFAULT 0,
	lev smallint(5) NOT NULL DEFAULT 0,
	numsubcat smallint(5) NOT NULL DEFAULT 0,
	subcatid varchar(250) DEFAULT '',
	number mediumint(8) unsigned NOT NULL DEFAULT 0,
	user_manager_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	user_create_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	status tinyint(1) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (group_user_id),
	UNIQUE KEY alias (alias),
	KEY parent_id (parent_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_user_list (
	group_user_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	userid mediumint(8) unsigned NOT NULL DEFAULT 0,
	UNIQUE KEY group_user_id_userid (group_user_id,userid),
	KEY group_user_id (group_user_id),
	KEY userid (userid)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_category (
	category_id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
	parent_id smallint(5) unsigned NOT NULL DEFAULT '0',
	title varchar(250) NOT NULL,
	alias varchar(250) NOT NULL DEFAULT '',
	description text NOT NULL,
	meta_title varchar(250) NOT NULL,
	meta_description varchar(250) NOT NULL,
	meta_keyword varchar(250) NOT NULL,
	weight smallint(5) unsigned NOT NULL DEFAULT '0',
	sort smallint(5) NOT NULL DEFAULT '0',
	lev smallint(5) NOT NULL DEFAULT '0',
	numsubcat smallint(5) NOT NULL DEFAULT '0',
	subcatid varchar(250) DEFAULT '',
	status tinyint(1) unsigned NOT NULL DEFAULT '0',
	num_rows mediumint(8) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	date_modified int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (category_id),
	UNIQUE KEY alias (alias),
	KEY parent_id (parent_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_comment (
	comment_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	userid mediumint(8) unsigned NOT NULL DEFAULT '0',
	question_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	comment text NOT NULL,
	status tinyint(1) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (comment_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config (
	config_name varchar(30) NOT NULL,
	config_value mediumtext NOT NULL,
	UNIQUE KEY config_name (config_name)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_contribute_permission (
	group_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	permission mediumblob NOT NULL,
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	date_modified int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (group_id),
	UNIQUE KEY group_id (group_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_group_exam (
	group_exam_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	parent_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	title varchar(250) NOT NULL,
	alias varchar(250) NOT NULL DEFAULT '',
	description text NOT NULL,
	meta_title varchar(250) NOT NULL,
	meta_description varchar(250) NOT NULL,
	meta_keyword varchar(250) NOT NULL,
	groups_view varchar(250) NOT NULL,
	numlinks smallint(5) unsigned NOT NULL DEFAULT 0,
	weight smallint(5) unsigned NOT NULL DEFAULT 0,
	sort smallint(5) NOT NULL DEFAULT 0,
	lev smallint(5) NOT NULL DEFAULT 0,
	numsubcat smallint(5) NOT NULL DEFAULT 0,
	subcatid varchar(250) DEFAULT '',
	inhome tinyint(1) unsigned NOT NULL DEFAULT 0,
	status tinyint(1) unsigned NOT NULL DEFAULT 0,
	num_rows mediumint(8) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (group_exam_id),
	UNIQUE KEY alias (alias),
	KEY parent_id (parent_id)

) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history (
	history_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	history_alias varchar(15) NOT NULL DEFAULT '',
	userid mediumint(8) unsigned NOT NULL DEFAULT 0,
	test_time int(1) unsigned NOT NULL DEFAULT 0 COMMENT 'timestamp',
	time_do_test int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'Giây',
	list_answer text NOT NULL COMMENT 'danh sách đáp án',
	question text NOT NULL,
	score double unsigned NOT NULL DEFAULT 0 COMMENT 'Điểm thi',
	max_score smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'Max điểm cho bài thi',
	point mediumint(8) unsigned NOT NULL DEFAULT 0,
	type_exam_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	type_id tinyint(1) unsigned NOT NULL DEFAULT 0,
	num_question smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'Số câu hỏi mặc định',
	time smallint(4) unsigned NOT NULL DEFAULT 0 COMMENT 'Phút',
	is_sended tinyint(1) unsigned NOT NULL DEFAULT 0,
	is_deleted tinyint(1) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (history_id),
	UNIQUE KEY history_alias (history_alias)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_history_log (
	history_log_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	history_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	title varchar(255) NOT NULL DEFAULT '',
	infringe tinyint(1) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (history_log_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_level (
	level_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(250) NOT NULL DEFAULT '',
	description mediumtext NOT NULL,
	weight smallint(4) unsigned NOT NULL DEFAULT '0',
	status tinyint(1) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	date_modified int(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (level_id),
	UNIQUE KEY title (title)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_limit (
	userid mediumint(8) unsigned NOT NULL DEFAULT '0',
	type_exam_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	test_limit smallint(3) unsigned NOT NULL DEFAULT '0',
	UNIQUE KEY userid_type_exam_id (userid,type_exam_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_money (
	money_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	userid mediumint(8) unsigned NOT NULL DEFAULT '0',
	logip varchar(250) NOT NULL DEFAULT '',
	supplier varchar(250) NOT NULL DEFAULT '',
	money int(11) unsigned NOT NULL DEFAULT '0',
	point int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (money_id),
	KEY userid (userid)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_point (
	userid mediumint(8) unsigned NOT NULL DEFAULT '0',
	point int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (userid),
	UNIQUE KEY userid (userid)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_question (
	question_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	category_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	level_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	contribute tinyint(1) unsigned NOT NULL DEFAULT 0,
	user_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	user_name varchar(250) NOT NULL,
	question mediumtext NOT NULL,
	analyzes mediumtext NOT NULL,
	answers mediumblob NOT NULL,
	trueanswer varchar(255) NOT NULL,
	comment mediumint(8) unsigned DEFAULT 0,
	status tinyint(1) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (question_id)
) ENGINE=MyISAM";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_question_import (
	question_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	category_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	level_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	contribute tinyint(1) unsigned NOT NULL DEFAULT 0,
	user_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	user_name varchar(250) NOT NULL,
	question mediumtext NOT NULL,
	analyzes mediumtext NOT NULL,
	answers mediumblob NOT NULL,
	trueanswer varchar(255) NOT NULL,
	comment mediumint(8) unsigned DEFAULT 0,
	duplicate tinyint(1) unsigned NOT NULL DEFAULT 0,
	status tinyint(1) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (question_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_ranking (
	ranking_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	title varchar(250) NOT NULL,
	description mediumtext NOT NULL,
	min_score float unsigned NOT NULL DEFAULT '0',
	max_score float unsigned NOT NULL DEFAULT '0',
	weight smallint(5) unsigned NOT NULL DEFAULT '0',
	status tinyint(1) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	date_modified int(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (ranking_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_recharge (
	recharge_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	userid mediumint(8) unsigned NOT NULL DEFAULT '0',
	logip varchar(50) NOT NULL DEFAULT '',
	supplier varchar(10) NOT NULL DEFAULT '',
	seri_number varchar(50) NOT NULL DEFAULT '',
	pin_number varchar(50) NOT NULL DEFAULT '',
	money int(11) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (recharge_id),
	KEY userid (userid)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_rechargebank (
	rechargebank_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	userid mediumint(8) unsigned NOT NULL DEFAULT '0',
	bank_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	transaction varchar(50) NOT NULL DEFAULT '',
	money int(11) unsigned NOT NULL DEFAULT '0',
	note varchar(250) NOT NULL DEFAULT '',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (rechargebank_id),
	KEY userid (userid)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_report (
	report_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	question_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	userid mediumint(8) unsigned NOT NULL DEFAULT '0',
	title varchar(250) NOT NULL,
	note text NOT NULL,
	reply mediumtext NOT NULL,
	status tinyint(1) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (report_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_type_exam (
	type_exam_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	group_exam_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	group_exam_list varchar(255) NOT NULL DEFAULT '',
	title varchar(250) NOT NULL,
	code varchar(20) DEFAULT NULL,
	images varchar(250) NOT NULL,
	thumb tinyint(1) unsigned NOT NULL DEFAULT 0,
	introtext mediumtext NOT NULL,
	description mediumtext NOT NULL,
	keywords mediumtext NOT NULL,
	num_question smallint(4) unsigned NOT NULL DEFAULT 0,
	point mediumint(8) unsigned NOT NULL DEFAULT 0,
	time int(11) unsigned NOT NULL DEFAULT 0,
	config text NOT NULL,
	rules text NOT NULL,
	group_user varchar(255) NOT NULL DEFAULT '',
	type_id tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0: tự động, 1: chỉ định câu hỏi',
	random tinyint(1) NOT NULL DEFAULT 0 COMMENT 'sử dụng khi type_id=1',
	pdf varchar(255) NOT NULL DEFAULT '',
	analyzed varchar(255) NOT NULL DEFAULT '',
	video varchar(255) NOT NULL DEFAULT '',
	allow_show_answer tinyint(1) unsigned NOT NULL DEFAULT 0,
	allow_download tinyint(1) unsigned NOT NULL DEFAULT 0,
	allow_video tinyint(1) unsigned NOT NULL DEFAULT 0,
	user_create_id mediumint(8) unsigned NOT NULL DEFAULT 0,
	viewed mediumint(8) unsigned NOT NULL DEFAULT 0,
	status tinyint(1) unsigned NOT NULL DEFAULT 0,
	date_added int(11) unsigned NOT NULL DEFAULT 0,
	date_modified int(11) unsigned NOT NULL DEFAULT 0,
	tested int(11) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (type_exam_id),
	UNIQUE KEY title (title),
	UNIQUE KEY code (code)
) ENGINE=MyISAM;";


$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config (config_name, config_value) VALUES
('open', '1'),
('max_score', '10'),
('facebook_appid', ''),
('allow_show_answer', '1'),
('test_limit', '0'),
('perpage', '30'),
('format_code_id', 'TO%09s'),
('test_timeout', '0'),
('core_api_http_usr', 'xmerchant_25466'),
('api_username', 'toanhocbactrungnamvn'),
('api_password', 'jv3vrD6TD6zDUoUZ2enJ'),
('core_api_http_pwd', 'fbc4acd9884c6dbb'),
('secure_code', 'fbc4acd9884c6dbb'),
('merchant_id', '25466'),
('show_comment', '1'),
('number_comment', '5'),
('time_modify_comment', '1'),
('time_delete_comment', '1'),
('bonus_score', '1'),
('convert_to_vcoin', '100'),
('default_form_import', '/uploads/onlinetest/de-thi-mau.docx'),
('default_form_import1', ''),
('intro', '<p>Tất cả các đề thi Online trên <a class=\"color-blue\" href=\"#/\" style=\"text-decoration: underline\">demo.com</a> đều có phương pháp làm bài, đáp án và lời giải chi tiết.</p>\r\n\r\n<p>Đáp án và lời giải chi tiết sẽ được công bố ngay sau khi thành viên nộp bài thi.</p>\r\n\r\n<p>Bài tập tự luận sẽ được giáo viên chấm điểm và gửi nhận xét bài làm sau 2 ngày học sinh nộp bài thi.</p>'),
('allow_download', '1'),
('allow_video', '1'),
('allow_analyzed', '1'),
('default_group_teacher', '2'),
('default_group_student', '1');";

 