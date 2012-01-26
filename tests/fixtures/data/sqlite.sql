DROP TABLE IF EXISTS `attachments`;
CREATE TABLE `attachments` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `object_id` INTEGER,
  `object_type` VARCHAR(250),
  `file` varchar(250),
  `file_origin` varchar(250)
);

-- ----------------------------
-- Table structure for categories
-- ----------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` varchar(200)
);

-- ----------------------------
-- Table structure for certificates
-- ----------------------------
DROP TABLE IF EXISTS `certificates`;
CREATE TABLE `certificates` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `product_id` int(11),
  `name` varchar(11)
);

-- ----------------------------
-- Table structure for product_descriptions
-- ----------------------------
DROP TABLE IF EXISTS `product_descriptions`;
CREATE TABLE `product_descriptions` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `product_id` int(11) NOT NULL,
  `color` varchar(200),
  `size` varchar(200)
);

-- ----------------------------
-- Table structure for products
-- ----------------------------
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `category_id` int(11),
  `name` varchar(200),
  `price` double
);

-- ----------------------------
-- Table structure for products_2_tags
-- ----------------------------
DROP TABLE IF EXISTS `products_2_tags`;
CREATE TABLE `products_2_tags` (
  `product_id` INTEGER NOT NULL,
  `tag_id` INTEGER NOT NULL,
  PRIMARY KEY  (`product_id`,`tag_id`)
);

-- ----------------------------
-- Table structure for tags
-- ----------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
  `name` varchar(200)
);

-- ----------------------------
-- Records
-- ----------------------------
INSERT INTO `categories` VALUES ('1', 'Auto');
INSERT INTO `categories` VALUES ('2', 'Mobile');
INSERT INTO `categories` VALUES ('3', 'Used');

INSERT INTO `tags` VALUES ('1', 'bad');
INSERT INTO `tags` VALUES ('2', 'good');
INSERT INTO `tags` VALUES ('3', 'awesome');

INSERT INTO `certificates` VALUES ('1', '1', '9045');
INSERT INTO `product_descriptions` VALUES ('1', '1', 'Red', '100x100');
INSERT INTO `products` VALUES ('1', '1', 'Test Product', '99');
INSERT INTO `attachments` VALUES ('1', '1', 'product_image', 'file.txt', '/path/to/file.txt');


INSERT INTO `products_2_tags` VALUES ('1', '2');
INSERT INTO `products_2_tags` VALUES ('1', '3');