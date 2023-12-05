DROP DATABASE IF EXISTS clothshop;
CREATE DATABASE clothshop;
USE clothshop;
DROP TABLE IF EXISTS `order_items`;


DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL UNIQUE ,
  `price` int(11) NOT NULL,
  `image` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL ,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (product_id) REFERENCES product(id),
  FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


INSERT INTO product (id, name, price, image) VALUES (1, 'Vintage Sport Jacket - AK02', 200000, 'assets/black.jpg');
INSERT INTO product (id, name, price, image) VALUES (2, 'Design Studio Jacket - AK01', 200000, 'assets/design-studio-jacket---ak01.jpg');
INSERT INTO product (id, name, price, image) VALUES (3, 'Jacket New Collection Blue - AK05', 122001, 'assets/jacket-new-collection-blue---ak05.png');
INSERT INTO product (id, name, price, image) VALUES (4, 'Premium Cotton MyCAT', 283000, 'assets/catt.webp');
INSERT INTO product (id, name, price, image) VALUES (5, 'Zip Karants Local Brand', 167000, 'assets/combo.webp');
INSERT INTO product (id, name, price, image) VALUES (6, 'Premium Tee Rainbow', 190000, 'assets/paint.jpg');
INSERT INTO product (id, name, price, image) VALUES (7, 'Oversize Local Brand Karants', 165000, 'assets/premium.jpg');
INSERT INTO product (id, name, price, image) VALUES (8, 'Unisex Karants Make Yourself Color', 227000, 'assets/punkdoll.webp');
INSERT INTO product (id, name, price, image) VALUES (9, 'Cotton Made You Smile', 188000, 'assets/punkglasses.jpg');
INSERT INTO product (id, name, price, image) VALUES (10, 'Premium Cotton RabbitPink', 298000, 'assets/rabbit.webp');
INSERT INTO product (id, name, price, image) VALUES (11, 'Karants Premium Cotton', 187000, 'assets/thun-bf.jpg');
INSERT INTO product (id, name, price, image) VALUES (12, 'KARANTS Chrysanthemum Flower', 234000, 'assets/thun-flow.webp');
INSERT INTO product (id, name, price, image) VALUES (13, 'Karants IN NỔI GAFFITY', 255000, 'assets/thun-rainbow.webp');
INSERT INTO product (id, name, price, image) VALUES (14, 'Karants Make Yourself Color', 156000, 'assets/tiny-rainbow.webp');
INSERT INTO product (id, name, price, image) VALUES (15, 'Vintage Sport Jacket - AK03', 185000, 'assets/white-black.webp');
INSERT INTO product (id, name, price, image) VALUES (16, 'Monisac Blue', 240000, 'assets/monisac-blue.jpg');

INSERT INTO orders (id, name, address, phone, note) VALUES (1, 'Nguyễn Văn Chi', '123 Đinh Huyền Trinh', '0957465746', '');

INSERT INTO order_items (id, product_id, order_id, quantity) VALUES (1, 1, 1, 1);
INSERT INTO order_items (id, product_id, order_id, quantity) VALUES (2, 10, 1, 1);

