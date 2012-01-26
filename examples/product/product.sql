-- ----------------------------
-- Table structure for attachments
-- ----------------------------
CREATE TABLE `attachments` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `object_id` int(11) default NULL,
  `object_type` enum('product_image','certificate') default NULL,
  `file` varchar(250) default NULL,
  `file_origin` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for categories
-- ----------------------------
CREATE TABLE `categories` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for certificates
-- ----------------------------
CREATE TABLE `certificates` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) default NULL,
  `name` varchar(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_descriptions
-- ----------------------------
CREATE TABLE `product_descriptions` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) NOT NULL,
  `color` varchar(200) default NULL,
  `size` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for products
-- ----------------------------
CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `category_id` int(11) default NULL,
  `name` varchar(200) default NULL,
  `price` double default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for products_2_tags
-- ----------------------------
CREATE TABLE `products_2_tags` (
  `product_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`product_id`,`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tags
-- ----------------------------
CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records 
-- ----------------------------
INSERT INTO `categories` VALUES ('1', 'Auto');
INSERT INTO `categories` VALUES ('2', 'Mobile');
INSERT INTO `categories` VALUES ('3', 'Used');
INSERT INTO `certificates` VALUES ('1', '1', '9045');
INSERT INTO `product_descriptions` VALUES ('1', '1', 'Red', '100x100');
INSERT INTO `products` VALUES ('1', '1', 'Test Product', '99');
INSERT INTO `attachments` VALUES ('1', '1', 'product_image', 'file.text', 'path/to/file.txt');
INSERT INTO `products_2_tags` VALUES ('1', '2');
INSERT INTO `products_2_tags` VALUES ('1', '3');
INSERT INTO `tags` VALUES ('1', 'bad');
INSERT INTO `tags` VALUES ('2', 'good');
INSERT INTO `tags` VALUES ('3', 'awesome');