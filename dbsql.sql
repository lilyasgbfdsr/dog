/*
 Navicat Premium Data Transfer

 Source Server Type    : MySQL
 Source Server Version : 50731
 Source Host           : 127.0.0.1:3306
 Source Schema         : dog

 Target Server Type    : MySQL
 Target Server Version : 50731
 File Encoding         : 65001

 Date: 01/08/2022 13:59:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

create database dog CHARSET utf8 COLLATE utf8_general_ci;

use dog;

-- ----------------------------
-- Table structure for config_price
-- ----------------------------
DROP TABLE IF EXISTS `config_price`;
CREATE TABLE `config_price` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `price` int(11) DEFAULT NULL COMMENT '配置金额',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of config_price
-- ----------------------------
BEGIN;
INSERT INTO `config_price` VALUES (1, 5, '2021-03-19 00:17:16', '2021-03-22 01:25:41');
COMMIT;

-- ----------------------------
-- Table structure for data_admin
-- ----------------------------
DROP TABLE IF EXISTS `data_admin`;
CREATE TABLE `data_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id主键',
  `guid` char(32) NOT NULL COMMENT '管理员guid',
  `username` varchar(255) NOT NULL COMMENT '用户ID',
  `password` char(32) NOT NULL COMMENT '密码',
  `status` tinyint(1) NOT NULL COMMENT '状态:1为启用 2为禁用',
  `token` char(32) NOT NULL COMMENT 'token值',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of data_admin
-- ----------------------------
BEGIN;
INSERT INTO `data_admin` VALUES (1, '80cb88dcfdf611e9b83e02424768c089', 'admin', 'e3ec826646d8731847eaef4417df18b2', 1, '0d977288ab9b11ea831200163e16b93e');
COMMIT;

-- ----------------------------
-- Table structure for data_config
-- ----------------------------
DROP TABLE IF EXISTS `data_config`;
CREATE TABLE `data_config` (
  `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id主键',
  `name` varchar(32) NOT NULL COMMENT '配置名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `value` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of data_config
-- ----------------------------
BEGIN;
INSERT INTO `data_config` VALUES (1, 'redis阀值', 1, 100);
COMMIT;

-- ----------------------------
-- Table structure for data_order
-- ----------------------------
DROP TABLE IF EXISTS `data_order`;
CREATE TABLE `data_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `reserve_id` char(32) NOT NULL COMMENT '预约uuid',
  `order_id` char(32) NOT NULL COMMENT '订单号',
  `order_price` decimal(10,2) NOT NULL COMMENT '订单金额',
  `status` tinyint(1) unsigned NOT NULL COMMENT '订单状态:1为已支付 2为未支付',
  `appid` varchar(32) DEFAULT NULL COMMENT '公众号appid',
  `bank_type` varchar(32) DEFAULT NULL COMMENT '付款银行:OTHERS其他（银行卡以外）',
  `cash_fee` int(10) DEFAULT NULL COMMENT '现金支付金额:分',
  `fee_type` varchar(8) DEFAULT NULL COMMENT '货币种类:默认人民币：CNY',
  `is_subscribe` char(1) DEFAULT NULL COMMENT '是否关注公众账号:Y-关注，N-未关注',
  `mch_id` varchar(32) DEFAULT NULL COMMENT '商户号',
  `nonce_str` varchar(32) DEFAULT NULL COMMENT '随机字符串',
  `openid` varchar(255) DEFAULT NULL COMMENT '用户标识',
  `out_trade_no` varchar(32) DEFAULT NULL COMMENT '商户订单号',
  `result_code` varchar(16) DEFAULT NULL COMMENT '业务结果',
  `sign` varchar(32) DEFAULT NULL COMMENT '签名',
  `time_end` varchar(16) DEFAULT NULL COMMENT '支付完成时间',
  `total_fee` int(10) unsigned DEFAULT NULL COMMENT '订单金额',
  `trade_type` varchar(16) DEFAULT NULL COMMENT '交易类型:JSAPI、NATIVE、APP',
  `transaction_id` varchar(64) DEFAULT NULL COMMENT '微信支付订单号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8535 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of data_order
-- ----------------------------
BEGIN;
INSERT INTO `data_order` VALUES (1, '95f2176c967b11eaac8600163e16b93e', '95f21938967b11ea8fcb00163e16b93e', 10.00, 1, 'wx644xxxxxxxx98dc', 'OTHERS', 1000, 'CNY', 'Y', '1572202221', '5ebe411cdd1aa', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', '95f21938967b11ea8fcb00163e16b93e', 'SUCCESS', '5F78FCC9E4B9F909BBC6F0D248594F0D', '20200515151337', 1000, 'JSAPI', '4200000530202005158951784087');
INSERT INTO `data_order` VALUES (2, '619dd5fa99d211eab38800163e16b93e', '619dd7d099d211eab19600163e16b93e', 5.00, 1, 'wx644xxxxxxxx98dc', 'OTHERS', 500, 'CNY', 'Y', '1572202221', '5ec3db38c72cd', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', '619dd7d099d211eab19600163e16b93e', 'SUCCESS', '179DEC46D460BC7DFDF8C7AFC9419F2B', '20200519211229', 500, 'JSAPI', '4200000527202005190933596362');
INSERT INTO `data_order` VALUES (3, 'e19ce3e4a30b11ea845500163e16b93e', 'e19ce5a6a30b11ea941c00163e16b93e', 10.00, 1, 'wx644xxxxxxxx98dc', 'CMB_CREDIT', 1000, 'CNY', 'Y', '1572202221', '5ed35529522a1', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', 'e19ce5a6a30b11ea941c00163e16b93e', 'SUCCESS', '14EDF9E2375E2DDA3D927C8CECC16206', '20200531145645', 1000, 'JSAPI', '4200000532202005313705476592');
INSERT INTO `data_order` VALUES (4, '13bc6006a99811eaac7c00163e16b93e', '13bc6254a99811ea852900163e16b93e', 5.00, 1, 'wx644xxxxxxxx98dc', 'ICBC_DEBIT', 500, 'CNY', 'Y', '1572202221', '5ede5159e7608', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', '13bc6254a99811ea852900163e16b93e', 'SUCCESS', 'D0567384861058A03E609ED8999C4983', '20200608225527', 500, 'JSAPI', '4200000531202006084661267711');
INSERT INTO `data_order` VALUES (6, 'e4690c52a9ef11ea9a6300163e16b93e', 'e4690e1ea9ef11ea93a400163e16b93e', 10.00, 1, 'wx644xxxxxxxx98dc', 'OTHERS', 1000, 'CNY', 'Y', '1572202221', '5edee4ae3fb44', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', 'e4690e1ea9ef11ea93a400163e16b93e', 'SUCCESS', '371AD74AF98055370A9BBF638751688E', '20200609092416', 1000, 'JSAPI', '4200000545202006097847135574');
INSERT INTO `data_order` VALUES (7, '7c68dd3aa9fe11eabb2c00163e16b93e', '7c68defca9fe11eabe0600163e16b93e', 10.00, 1, 'wx644xxxxxxxx98dc', 'OTHERS', 1000, 'CNY', 'Y', '1572202221', '5edefd2a378d8', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', '7c68defca9fe11eabe0600163e16b93e', 'SUCCESS', '020AF59F02DB81658FD22C480EAD36BE', '20200609110831', 1000, 'JSAPI', '4200000551202006097280920937');
INSERT INTO `data_order` VALUES (8, '3ab3ed7aaa0e11ea911500163e16b93e', '3ab3ef32aa0e11ea8c2300163e16b93e', 15.00, 1, 'wx644xxxxxxxx98dc', 'OTHERS', 1500, 'CNY', 'Y', '1572202221', '5edf1793e4e3f', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', '3ab3ef32aa0e11ea8c2300163e16b93e', 'SUCCESS', '07A36E447DAB967379308537C06DA971', '20200609130114', 1500, 'JSAPI', '4200000545202006092671130917');
INSERT INTO `data_order` VALUES (9, '37a6d33aaa0f11ea8acd00163e16b93e', '37a6d556aa0f11ea889000163e16b93e', 10.00, 1, 'wx644xxxxxxxx98dc', 'CMB_DEBIT', 1000, 'CNY', 'Y', '1572202221', '5edf193c4cf93', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', '37a6d556aa0f11ea889000163e16b93e', 'SUCCESS', '9AEAC387B15D695C53902D93E787B2AC', '20200609130816', 1000, 'JSAPI', '4200000523202006090420961561');
INSERT INTO `data_order` VALUES (10, '00ad6d9caa1b11eabaa600163e16b93e', '00ad6fe0aa1b11eaad9200163e16b93e', 5.00, 1, 'wx644xxxxxxxx98dc', 'OTHERS', 500, 'CNY', 'N', '1572202221', '5edf2d020ae8a', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', '00ad6fe0aa1b11eaad9200163e16b93e', 'SUCCESS', '5677470BECBC84904A23C40CF6F74ED7', '20200609143239', 500, 'JSAPI', '4200000530202006093219301006');
INSERT INTO `data_order` VALUES (11, '748579aeaa2411eab74600163e16b93e', '74857b5caa2411eaa96300163e16b93e', 10.00, 1, 'wx644xxxxxxxx98dc', 'OTHERS', 1000, 'CNY', 'Y', '1572202221', '5edf3cddd4283', 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', '74857b5caa2411eaa96300163e16b93e', 'SUCCESS', 'A20445D5C204090A14B0EDB26EBE47F9', '20200609154017', 1000, 'JSAPI', '4200000529202006093221654356');
COMMIT;

-- ----------------------------
-- Table structure for data_reserve
-- ----------------------------
DROP TABLE IF EXISTS `data_reserve`;
CREATE TABLE `data_reserve` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `reserve_id` char(32) NOT NULL COMMENT '预约uuid',
  `int_time` int(10) unsigned NOT NULL COMMENT '预约日期',
  `name` varchar(255) NOT NULL COMMENT '预约姓名',
  `telephone` char(11) NOT NULL COMMENT '电话',
  `number` tinyint(1) unsigned NOT NULL COMMENT '预约人数',
  `add_time` int(11) unsigned NOT NULL COMMENT '添加时间',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信唯一id',
  `hour` tinyint(1) unsigned NOT NULL COMMENT '预约时间段',
  `operate_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '操作：1为修改 2为取消',
  `check_status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '签到：1为签到',
  `date_time` varchar(32) DEFAULT NULL COMMENT '预约时间段',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8535 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of data_reserve
-- ----------------------------
BEGIN;
INSERT INTO `data_reserve` VALUES (1, '95f2176c967b11eaac8600163e16b93e', 1589644800, '陶xx', '13800000000', 0, 1589526812, 'oU-PAs7J-eN3DPY5mkbpxGZ2JBi8', 4, 2, 2, '15:25 - 16:15');
INSERT INTO `data_reserve` VALUES (2, '619dd5fa99d211eab38800163e16b93e', 1589904000, '程xx', '13800000000', 1, 1589893944, 'oU-PAs_4AIIoGUoNGfHP7ddWhikM', 5, 0, 2, '16:35 - 17:25');
INSERT INTO `data_reserve` VALUES (3, 'e19ce3e4a30b11ea845500163e16b93e', 1589904000, 'mia', '13800000000', 2, 1590908201, 'oU-PAsxQojefBs36Erszbi-D8_ZQ', 5, 0, 2, '16:35 - 17:25');
INSERT INTO `data_reserve` VALUES (4, '13bc6006a99811eaac7c00163e16b93e', 1591632000, '钟女士', '13800000000', 0, 1591628121, 'oU-PAs5kjgzjvPUFwcHYPtansCO8', 6, 2, 1, '17:45 - 18:35');
INSERT INTO `data_reserve` VALUES (5, 'ddfdcb50a9ef11eabf9900163e16b93e', 1591632000, '张xx', '13800000000', 2, 1591665827, 'oU-PAs1ppv5nDKnnFMylbVPeGjWg', 2, 0, 2, '13:05 - 13:55');
INSERT INTO `data_reserve` VALUES (6, 'e4690c52a9ef11ea9a6300163e16b93e', 1591632000, '张xx', '13800000000', 2, 1591665838, 'oU-PAs1ppv5nDKnnFMylbVPeGjWg', 2, 0, 2, '13:05 - 13:55');
INSERT INTO `data_reserve` VALUES (7, '7c68dd3aa9fe11eabb2c00163e16b93e', 1591632000, '邵xx', '13800000000', 2, 1591672106, 'oU-PAs3VtlTFc45okjs54wn1SB6s', 4, 0, 2, '15:25 - 16:15');
INSERT INTO `data_reserve` VALUES (8, '3ab3ed7aaa0e11ea911500163e16b93e', 1591632000, '佳x', '13800000000', 3, 1591678867, 'oU-PAszDPJdJQI8xxSjOK7kAh4eo', 3, 0, 2, '14:15 - 15:05');
INSERT INTO `data_reserve` VALUES (9, '37a6d33aaa0f11ea8acd00163e16b93e', 1591804800, '易x', '13800000000', 2, 1591679292, 'oU-PAswAlPf_NfMCCJFWSOUc-AFc', 6, 0, 2, '17:45 - 18:35');
INSERT INTO `data_reserve` VALUES (10, '00ad6d9caa1b11eabaa600163e16b93e', 1591632000, '你x', '13800000000', 1, 1591684354, 'oU-PAs_sE5kltk8z4ceJPejYs9VA', 4, 0, 2, '15:25 - 16:15');
INSERT INTO `data_reserve` VALUES (11, '748579aeaa2411eab74600163e16b93e', 1591718400, '邹x', '13800000000', 2, 1591688413, 'oU-PAs5ghygZPybzTC3wCOOoInHo', 3, 0, 2, '14:15 - 15:05');
COMMIT;

-- ----------------------------
-- Table structure for data_reserve_info
-- ----------------------------
DROP TABLE IF EXISTS `data_reserve_info`;
CREATE TABLE `data_reserve_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `int_time` int(10) unsigned NOT NULL COMMENT '预约日期',
  `add_time` int(11) unsigned NOT NULL COMMENT '添加时间',
  `hour` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '预约时间段',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '预约类型：1天 2时间段',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=415 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of data_reserve_info
-- ----------------------------
BEGIN;
INSERT INTO `data_reserve_info` VALUES (395, 1653926400, 1653277071, 1, 2);
INSERT INTO `data_reserve_info` VALUES (394, 1653840000, 1653136340, 1, 2);
INSERT INTO `data_reserve_info` VALUES (397, 1654099200, 1653410941, 1, 2);
INSERT INTO `data_reserve_info` VALUES (398, 1654185600, 1653528168, 1, 2);
INSERT INTO `data_reserve_info` VALUES (399, 1654272000, 1653621290, 1, 2);
INSERT INTO `data_reserve_info` VALUES (400, 1654358400, 1653667329, 1, 2);
INSERT INTO `data_reserve_info` VALUES (401, 1654444800, 1653750536, 1, 2);
INSERT INTO `data_reserve_info` VALUES (402, 1654531200, 1654013287, 1, 2);
INSERT INTO `data_reserve_info` VALUES (403, 1654617600, 1654013297, 1, 2);
INSERT INTO `data_reserve_info` VALUES (404, 1654704000, 1654013306, 1, 2);
INSERT INTO `data_reserve_info` VALUES (405, 1654790400, 1654110348, 1, 2);
INSERT INTO `data_reserve_info` VALUES (406, 1654876800, 1654187297, 1, 2);
INSERT INTO `data_reserve_info` VALUES (407, 1654963200, 1654272109, 1, 2);
INSERT INTO `data_reserve_info` VALUES (408, 1655049600, 1654340818, 1, 2);
INSERT INTO `data_reserve_info` VALUES (409, 1655136000, 1654446369, 1, 2);
INSERT INTO `data_reserve_info` VALUES (410, 1655222400, 1654528285, 1, 2);
INSERT INTO `data_reserve_info` VALUES (411, 1655308800, 1654613996, 1, 2);
INSERT INTO `data_reserve_info` VALUES (412, 1655395200, 1654708101, 1, 2);
INSERT INTO `data_reserve_info` VALUES (413, 1655481600, 1654788740, 1, 2);
INSERT INTO `data_reserve_info` VALUES (414, 1655568000, 1654877230, 1, 2);
COMMIT;

-- ----------------------------
-- Table structure for date_times
-- ----------------------------
DROP TABLE IF EXISTS `date_times`;
CREATE TABLE `date_times` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序字段',
  `start_time` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '开始时间段',
  `end_time` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '结束时间段',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of date_times
-- ----------------------------
BEGIN;
INSERT INTO `date_times` VALUES (1, 1, '11:00', '11:50', '2021-03-19 00:12:37', '2021-03-19 00:12:37');
INSERT INTO `date_times` VALUES (3, 2, '12:00', '12:50', '2021-03-19 00:24:45', '2021-03-19 00:24:45');
INSERT INTO `date_times` VALUES (4, 3, '13:05', '13:55', '2021-03-19 00:25:55', '2021-03-19 00:25:55');
INSERT INTO `date_times` VALUES (5, 4, '14:15', '15:05', '2021-03-19 00:26:58', '2021-03-19 00:26:58');
INSERT INTO `date_times` VALUES (6, 5, '15:25', '16:15', '2021-03-19 00:27:40', '2021-03-19 00:27:40');
INSERT INTO `date_times` VALUES (7, 6, '16:35', '17:25', '2021-03-19 00:28:26', '2021-03-19 00:28:26');
INSERT INTO `date_times` VALUES (8, 7, '17:45', '18:35', '2021-03-19 00:29:41', '2021-03-19 00:29:41');
INSERT INTO `date_times` VALUES (9, 8, '18:50', '19:40', '2021-03-19 00:30:28', '2021-03-19 00:30:28');
COMMIT;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of migrations
-- ----------------------------
BEGIN;
INSERT INTO `migrations` VALUES (1, '2014_10_12_000000_create_users_table', 1);
INSERT INTO `migrations` VALUES (2, '2014_10_12_100000_create_password_resets_table', 1);
INSERT INTO `migrations` VALUES (3, '2020_12_13_210937_create_data_admin_table', 2);
INSERT INTO `migrations` VALUES (4, '2020_12_13_210937_create_data_config_table', 2);
INSERT INTO `migrations` VALUES (5, '2020_12_13_210937_create_data_order_table', 2);
INSERT INTO `migrations` VALUES (6, '2020_12_13_210937_create_data_reserve_info_table', 2);
INSERT INTO `migrations` VALUES (7, '2020_12_13_210937_create_data_reserve_table', 2);
INSERT INTO `migrations` VALUES (8, '2021_02_07_172720_create_tips_table', 3);
INSERT INTO `migrations` VALUES (9, '2021_02_08_115202_create_date_times_table', 3);
INSERT INTO `migrations` VALUES (10, '2021_02_08_115235_create_date_config_price_table', 3);
INSERT INTO `migrations` VALUES (11, '2021_02_08_135001_add_date_time_to_data_reserve_table', 3);
COMMIT;

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of password_resets
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for tips
-- ----------------------------
DROP TABLE IF EXISTS `tips`;
CREATE TABLE `tips` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tip` text COLLATE utf8mb4_unicode_ci COMMENT '字段',
  `sort` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序字段',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of tips
-- ----------------------------
BEGIN;
INSERT INTO `tips` VALUES (1, '1.预约费：每人线上付10元，余款到店支付；', 2, '2021-03-22 00:45:07', '2021-03-22 00:45:07');
INSERT INTO `tips` VALUES (2, '2.场次说明：场次标注时段为本场开始和结束时间；', 3, '2021-03-22 00:45:56', '2021-03-22 00:45:56');
INSERT INTO `tips` VALUES (3, '3.关于迟到：建议您提前候场，如即将迟到请尽早致电客服沟通；', 4, '2021-03-22 00:47:09', '2021-03-22 00:47:09');
COMMIT;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
BEGIN;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;