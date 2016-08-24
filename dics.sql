/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50540
Source Host           : 127.0.0.1:3306
Source Database       : wine

Target Server Type    : MYSQL
Target Server Version : 50540
File Encoding         : 65001

Date: 2016-08-24 17:00:10
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dics
-- ----------------------------
DROP TABLE IF EXISTS `dics`;
CREATE TABLE `dics` (
  `type` varchar(128) NOT NULL DEFAULT '' COMMENT '类型',
  `id` tinyint(2) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
  PRIMARY KEY (`type`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='字典表';

-- ----------------------------
-- Records of dics
-- ----------------------------
INSERT INTO `dics` VALUES ('消息类型', '1', '系统通知');
INSERT INTO `dics` VALUES ('消息类型', '2', '用户通知');
INSERT INTO `dics` VALUES ('消息类型', '3', '订单通知');
INSERT INTO `dics` VALUES ('消息跳转页面', '1', '首页');
INSERT INTO `dics` VALUES ('消息跳转页面', '2', '开通会员页面');
INSERT INTO `dics` VALUES ('消息跳转页面', '3', '订单物流页');
INSERT INTO `dics` VALUES ('消息跳转页面', '4', '订单详情页');
INSERT INTO `dics` VALUES ('消息跳转页面', '5', '商品分类列表页');
INSERT INTO `dics` VALUES ('消息跳转页面', '6', '商品抢购列表页');
INSERT INTO `dics` VALUES ('消息跳转页面', '7', '商品会员列表页');
INSERT INTO `dics` VALUES ('消息跳转页面', '8', '购物车列表页');
INSERT INTO `dics` VALUES ('消息类型', '4', '商品通知');
INSERT INTO `dics` VALUES ('消息跳转页面', '9', '我的收藏列表页');
INSERT INTO `dics` VALUES ('消息跳转页面', '10', '商品详情页');
INSERT INTO `dics` VALUES ('图片类型', '1', '外部网页');
INSERT INTO `dics` VALUES ('图片类型', '2', '产品广告');
INSERT INTO `dics` VALUES ('图片类型', '3', '品牌广告');
INSERT INTO `dics` VALUES ('图片类型', '4', '商家广告');
INSERT INTO `dics` VALUES ('图片类型', '5', '香型广告');
INSERT INTO `dics` VALUES ('图片类型', '6', '类型广告');
INSERT INTO `dics` VALUES ('订单状态', '0', '已取消');
INSERT INTO `dics` VALUES ('订单状态', '1', '新订单');
INSERT INTO `dics` VALUES ('订单状态', '2', '已付款');
INSERT INTO `dics` VALUES ('订单状态', '3', '已装箱');
INSERT INTO `dics` VALUES ('订单状态', '4', '配送中');
INSERT INTO `dics` VALUES ('订单状态', '5', '已送达');
INSERT INTO `dics` VALUES ('订单状态', '6', '已评价');
INSERT INTO `dics` VALUES ('付款方式', '1', '余额支付');
INSERT INTO `dics` VALUES ('付款方式', '2', '支付宝支付');
INSERT INTO `dics` VALUES ('付款方式', '3', '微信支付');
INSERT INTO `dics` VALUES ('图片类型', '7', '启动页限一张');
INSERT INTO `dics` VALUES ('钱包类型', '1', '余额');
INSERT INTO `dics` VALUES ('钱包类型', '2', '支付宝');
INSERT INTO `dics` VALUES ('钱包类型', '3', '微信');
INSERT INTO `dics` VALUES ('钱包明细类型', '1', '订单支出');
INSERT INTO `dics` VALUES ('钱包明细类型', '2', '订单收入');
INSERT INTO `dics` VALUES ('钱包明细类型', '3', '活动奖励');
INSERT INTO `dics` VALUES ('优惠适用对象', '2', '商家通用');
INSERT INTO `dics` VALUES ('优惠适用对象', '1', '平台通用');
INSERT INTO `dics` VALUES ('钱包明细类型', '4', ' 充值余额增加');
INSERT INTO `dics` VALUES ('优惠适用对象', '3', '店铺通用');
INSERT INTO `dics` VALUES ('优惠适用对象', '4', '某产品可用');
INSERT INTO `dics` VALUES ('图片类型', '8', '充值广告');
