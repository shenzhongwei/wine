/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50547
Source Host           : localhost:3306
Source Database       : yii2plus

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2016-07-08 10:23:12
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for auth_assignment
-- ----------------------------
DROP TABLE IF EXISTS `mm_admin_type`;
CREATE TABLE `mm_admin_type` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `mm_admin_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `mm_admin_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_assignment
-- ----------------------------
INSERT INTO `mm_admin_type` VALUES ('超级管理员', '1', '1467629090');

-- ----------------------------
-- Table structure for auth_item
-- ----------------------------
DROP TABLE IF EXISTS `mm_admin_item`;
CREATE TABLE `mm_admin_item` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `type` (`type`),
  CONSTRAINT `mm_admin_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `mm_admin_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item
-- ----------------------------
INSERT INTO `mm_admin_item` VALUES ('普通管理员', '1', '普通管理员', null, null, '1467626553', '1467626553');
INSERT INTO `mm_admin_item` VALUES ('用户管理', '2', '用户管理', null, null, '1467626475', '1467626475');
INSERT INTO `mm_admin_item` VALUES ('超级权限', '2', '超级权限拥有最高级系统权限', null, null, '1467628984', '1467629027');
INSERT INTO `mm_admin_item` VALUES ('超级管理员', '1', '超级管理员拥有最高级别系统权限', null, null, '1467629059', '1467629059');

-- ----------------------------
-- Table structure for auth_item_child
-- ----------------------------
DROP TABLE IF EXISTS `mm_admin_item_child`;
CREATE TABLE `mm_admin_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `mm_admin_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `mm_admin_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mm_admin_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `mm_admin_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_item_child
-- ----------------------------
INSERT INTO `mm_admin_item_child` VALUES ('普通管理员', '用户管理');
INSERT INTO `mm_admin_item_child` VALUES ('超级管理员', '超级权限');

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `mm_admin_rule`;
CREATE TABLE `mm_admin_rule` (
  `name` varchar(64) NOT NULL,
  `data` text,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of auth_rule
-- ----------------------------