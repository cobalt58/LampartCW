-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 24, 2024 at 09:18 PM
-- Server version: 8.0.30
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `CentryKp`
--

-- --------------------------------------------------------

--
-- Table structure for table `Cart`
--

CREATE TABLE `Cart` (
  `cart_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Cart`
--

INSERT INTO `Cart` (`cart_id`, `user_id`) VALUES
(6, 21);

-- --------------------------------------------------------

--
-- Table structure for table `Cart_Item`
--

CREATE TABLE `Cart_Item` (
  `cart_item_id` int NOT NULL,
  `cart_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Cart_Item`
--

INSERT INTO `Cart_Item` (`cart_item_id`, `cart_id`, `product_id`, `quantity`) VALUES
(17, 6, 42, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE `Category` (
  `category_id` int NOT NULL,
  `parent_category_id` int DEFAULT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Category`
--

INSERT INTO `Category` (`category_id`, `parent_category_id`, `name`) VALUES
(1, NULL, 'Одяг'),
(2, NULL, 'Взуття'),
(3, NULL, 'Аксесуари'),
(4, 1, 'Чоловічий одяг'),
(5, 1, 'Жіночий одяг'),
(6, 2, 'Чоловіче взуття'),
(7, 2, 'Жіноче взуття'),
(8, 4, 'Сорочки'),
(9, 4, 'Штани'),
(10, 5, 'Сукні'),
(11, 5, 'Спідниці'),
(12, 5, 'Блузки'),
(13, 6, 'Кросівки'),
(14, 6, 'Туфлі'),
(15, 7, 'Босоніжки'),
(16, 7, 'Чоботи'),
(17, 3, 'Сумки'),
(18, 3, 'Ремені'),
(19, 3, 'Головні убори'),
(20, 19, 'Шапки'),
(21, 19, 'Балаклави'),
(22, 19, 'Бафи'),
(23, 19, 'Панами'),
(24, 19, 'Кепки');

-- --------------------------------------------------------

--
-- Table structure for table `Discount_Scheme`
--

CREATE TABLE `Discount_Scheme` (
  `discount_scheme_id` int NOT NULL,
  `min_spent_amount` decimal(10,2) DEFAULT NULL,
  `discount_percentage` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Discount_Scheme`
--

INSERT INTO `Discount_Scheme` (`discount_scheme_id`, `min_spent_amount`, `discount_percentage`) VALUES
(11, '5000.00', 5),
(12, '10000.00', 10),
(13, '15000.00', 15),
(14, '20000.00', 20),
(15, '25000.00', 25),
(16, '30000.00', 30);

-- --------------------------------------------------------

--
-- Table structure for table `Ordering`
--

CREATE TABLE `Ordering` (
  `order_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `discount_applied` decimal(10,2) DEFAULT NULL,
  `final_amount` decimal(10,2) DEFAULT NULL,
  `status` enum('Нове','Опрацьоване') DEFAULT NULL,
  `delivery_address` varchar(200) DEFAULT NULL,
  `total_order_price` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Ordering`
--

INSERT INTO `Ordering` (`order_id`, `user_id`, `order_date`, `total_amount`, `discount_applied`, `final_amount`, `status`, `delivery_address`, `total_order_price`) VALUES
(16, 21, '2024-12-24 21:02:16', '23399.82', '195.00', '19889.85', 'Опрацьоване', 'сюди', '13922.89');

--
-- Triggers `Ordering`
--
DELIMITER $$
CREATE TRIGGER `after_order_insert` AFTER INSERT ON `Ordering` FOR EACH ROW BEGIN

    UPDATE User

    SET total_spent_amount = COALESCE(total_spent_amount, 0) + NEW.final_amount

    WHERE user_id = NEW.user_id;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Order_Item`
--

CREATE TABLE `Order_Item` (
  `order_item_id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price_at_order_time` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Order_Item`
--

INSERT INTO `Order_Item` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_at_order_time`) VALUES
(13, 16, 3, 18, '1299.99');

--
-- Triggers `Order_Item`
--
DELIMITER $$
CREATE TRIGGER `update_product_quantity_after_insert` AFTER INSERT ON `Order_Item` FOR EACH ROW BEGIN

    -- Віднімання кількості з таблиці Product

    IF (SELECT `quantity` FROM `Product` WHERE `product_id` = NEW.`product_id`) >= NEW.`quantity` THEN

    UPDATE `Product`

    SET `quantity` = `quantity` - NEW.`quantity`

    WHERE `product_id` = NEW.`product_id`;

ELSE

    SIGNAL SQLSTATE '45000'

    SET MESSAGE_TEXT = 'Insufficient quantity in Product';

END IF;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `Product`
--

CREATE TABLE `Product` (
  `product_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Product`
--

INSERT INTO `Product` (`product_id`, `name`, `description`, `price`, `quantity`) VALUES
(1, 'Чоловіча сорочка Slim Fit', 'Стильна приталена сорочка з бавовни.', '499.99', 50),
(2, 'Жіноча сукня з квітковим принтом', 'Легка літня сукня з віскози.', '799.99', 30),
(3, 'Шкіряні чоловічі туфлі', 'Класичні шкіряні туфлі для офісу.', '1299.99', 0),
(4, 'Жіночі кросівки на платформі', 'Модні та зручні кросівки.', '999.99', 40),
(5, 'Сумка-шопер з екошкіри', 'Містка та екологічна сумка для покупок.', '399.99', 60),
(6, 'Чоловічий шкіряний ремінь', 'Якісний шкіряний ремінь з металевою пряжкою.', '249.99', 70),
(8, 'Спортивний рюкзак', 'Зручний рюкзак для тренувань та подорожей.', '599.50', 35),
(9, 'Зимова чоловіча шапка', 'Тепла шапка з вовни.', '299.99', 45),
(10, 'Жіночі босоніжки на підборах', 'Витончені босоніжки для особливих випадків.', '899.99', 25),
(11, 'Чоловіча сорочка Oxford Classic', 'Класична біла сорочка з бавовни для офісу', '579.99', 45),
(12, 'Чоловіча сорочка в клітинку Casual', 'Комфортна сорочка з натуральної тканини', '449.99', 55),
(13, 'Чоловіча сорочка з довгим рукавом Slim', 'Стильна приталена сорочка з мікрофібри', '629.99', 40),
(14, 'Чоловіча сорочка поло', 'Спортивна та зручна сорочка-поло', '399.99', 60),
(15, 'Чоловіча сорочка з принтом', 'Молодіжна сорочка з яскравим малюнком', '529.99', 35),
(16, 'Чоловіча джинсова сорочка', 'Стильна сорочка в техніці деніму', '699.99', 30),
(17, 'Чоловіча сорочка без коміра', 'Сучасна модель для розкутого стилю', '549.99', 42),
(18, 'Чоловіча сорочка з льону', 'Легка літня сорочка натурального кольору', '479.99', 50),
(19, 'Чоловіча сорочка з коротким рукавом', 'Зручна сорочка для літніх днів', '349.99', 65),
(20, 'Чоловіча сорочка з мережива', 'Унікальна сорочка з елементами мережива', '789.99', 25),
(21, 'Жіноча квіткова максі сукня', 'Романтична довга сукня з квітковим принтом', '1299.99', 35),
(22, 'Жіноча ділова сукня-футляр', 'Класична сукня для офісу', '899.99', 40),
(23, 'Жіноча коктейльна сукня', 'Елегантна сукня для особливих подій', '1599.99', 25),
(24, 'Жіноча трикотажна сукня', 'М\'яка та комфортна повсякденна сукня', '699.99', 50),
(25, 'Жіноча сукня з воланами', 'Жіночна сукня з романтичними деталями', '1099.99', 30),
(26, 'Жіноча сукня-комбінація', 'Мінімалістична шовкова сукня', '1249.99', 35),
(27, 'Жіноча сукня в стилі 60-х', 'Вінтажна модель з геометричним принтом', '1449.99', 20),
(28, 'Жіноча спортивна сукня', 'Зручна сукня для активного відпочинку', '549.99', 55),
(29, 'Жіноча сукня з асиметричним кроєм', 'Сучасна модель для сміливих', '1199.99', 28),
(30, 'Жіноча сукня-туніка', 'Вільного крою з етнічними мотивами', '799.99', 45),
(31, 'Жіноча сукня з шифону', 'Повітряна легка сукня', '1099.99', 33),
(32, 'Жіноча літня сукня-сарафан', 'Практична та зручна модель', '649.99', 52),
(34, 'Жіноча приталена сукня', 'Класична модель для будь-якої події', '1049.99', 38),
(35, 'Жіноча сукня з оборками', 'Романтична модель з елегантними деталями', '1399.99', 22),
(36, 'Чоловічі туфлі броги', 'Класичні шкіряні туфлі з перфорацією', '1599.99', 29),
(37, 'Чоловічі кеди шкіряні', 'Стильні повсякденні кеди', '899.99', 50),
(38, 'Чоловічі черевики зимові', 'Утеплені шкіряні черевики', '1799.99', 25),
(39, 'Чоловічі мокасини', 'Зручне легке взуття для дозвілля', '1099.99', 40),
(40, 'Чоловічі туфлі Оксфорд', 'Елегантні класичні туфлі', '1449.99', 35),
(41, 'Чоловічі кросівки для бігу', 'Професійні бігові кросівки', '1299.99', 45),
(42, 'Чоловічі черевики Челсі', 'Стильні шкіряні черевики', '1649.99', 25),
(43, 'Чоловічі туфлі з нубуку', 'М\'які та комфортні туфлі', '1199.99', 38),
(44, 'Чоловічі спортивні кросівки', 'Універсальне взуття для тренувань', '999.99', 55),
(45, 'Чоловічі туфлі для танців', 'Професійні туфлі для бальних танців', '1749.99', 15),
(46, 'Чоловічі сандалі шкіряні', 'Зручне літнє взуття', '799.99', 60),
(47, 'Чоловічі утеплені кросівки', 'Комфортне взуття для холодної пори', '1249.99', 40),
(48, 'Чоловічі човники', 'Легке літнє взуття', '699.99', 46),
(49, 'Чоловічі туфлі з тисненням', 'Унікальні туфлі з текстурованої шкіри', '1549.99', 25),
(50, 'Чоловічі трекінгові черевики', 'Надійне взуття для походів', '1899.99', 20);

--
-- Triggers `Product`
--
DELIMITER $$
CREATE TRIGGER `before_product_update` BEFORE UPDATE ON `Product` FOR EACH ROW BEGIN

    INSERT INTO Product_log (product_id, quantity, price, name, description, modified_time)

    VALUES (

        OLD.product_id,

        OLD.quantity,

        OLD.price,

        OLD.name,

        OLD.description,

        NOW()

    );

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `productwithdiscount`
-- (See below for the actual view)
--
CREATE TABLE `productwithdiscount` (
`product_id` int
,`name` varchar(100)
,`description` varchar(200)
,`quantity` int
,`original_price` decimal(10,2)
,`discounted_price` decimal(26,6)
,`discount_percentage` int
,`start_date` date
,`end_date` date
);

-- --------------------------------------------------------

--
-- Table structure for table `Product_Category`
--

CREATE TABLE `Product_Category` (
  `product_id` int NOT NULL,
  `category_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Product_Category`
--

INSERT INTO `Product_Category` (`product_id`, `category_id`) VALUES
(1, 8),
(11, 8),
(12, 8),
(13, 8),
(14, 8),
(15, 8),
(17, 8),
(18, 8),
(19, 8),
(20, 8),
(16, 9),
(2, 10),
(21, 10),
(22, 10),
(23, 10),
(24, 10),
(25, 10),
(26, 10),
(27, 10),
(28, 10),
(29, 10),
(30, 10),
(31, 10),
(32, 10),
(34, 10),
(35, 10),
(3, 13),
(37, 13),
(38, 13),
(39, 13),
(41, 13),
(44, 13),
(46, 13),
(47, 13),
(48, 13),
(50, 13),
(36, 14),
(40, 14),
(42, 14),
(43, 14),
(45, 14),
(49, 14),
(4, 15),
(10, 16),
(5, 17),
(8, 17),
(6, 18),
(9, 20);

-- --------------------------------------------------------

--
-- Table structure for table `Product_log`
--

CREATE TABLE `Product_log` (
  `product_id_log` int NOT NULL,
  `product_id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` varchar(200) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Product_log`
--

INSERT INTO `Product_log` (`product_id_log`, `product_id`, `name`, `description`, `quantity`, `price`, `modified_time`) VALUES
(1, 22, NULL, NULL, 40, '899.99', '2024-12-23 18:21:06'),
(2, 22, 'Жіноча ділова сукня-футляр Чорна', 'Класична сукня для офісу', 40, '899.99', '2024-12-23 18:24:28'),
(3, 3, 'Шкіряні чоловічі туфлі', 'Класичні шкіряні туфлі для офісу.', 20, '1299.99', '2024-12-23 23:55:05'),
(4, 48, 'Чоловічі човники', 'Легке літнє взуття', 50, '699.99', '2024-12-23 23:55:05'),
(5, 42, 'Чоловічі черевики Челсі', 'Стильні шкіряні черевики', 28, '1649.99', '2024-12-23 23:55:05'),
(6, 36, 'Чоловічі туфлі броги', 'Класичні шкіряні туфлі з перфорацією', 30, '1599.99', '2024-12-23 23:55:05'),
(7, 8, 'Спортивний рюкзак', 'Зручний рюкзак для тренувань та подорожей.', 35, '599.99', '2024-12-24 20:10:50'),
(8, 3, 'Шкіряні чоловічі туфлі', 'Класичні шкіряні туфлі для офісу.', 19, '1299.99', '2024-12-24 20:16:56'),
(9, 48, 'Чоловічі човники', 'Легке літнє взуття', 47, '699.99', '2024-12-24 20:16:56'),
(10, 42, 'Чоловічі черевики Челсі', 'Стильні шкіряні черевики', 27, '1649.99', '2024-12-24 20:16:56'),
(11, 3, 'Шкіряні чоловічі туфлі', 'Класичні шкіряні туфлі для офісу.', 18, '1299.99', '2024-12-24 21:02:16');

-- --------------------------------------------------------

--
-- Table structure for table `Product_Property_Value`
--

CREATE TABLE `Product_Property_Value` (
  `product_id` int NOT NULL,
  `property_id` int NOT NULL,
  `value` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Product_Property_Value`
--

INSERT INTO `Product_Property_Value` (`product_id`, `property_id`, `value`) VALUES
(1, 1, 'Синій'),
(1, 2, 'L'),
(1, 3, 'Бавовна'),
(1, 4, 'Весна/Літо'),
(1, 5, 'Casual'),
(1, 6, 'Довга'),
(1, 10, 'Україна'),
(1, 11, 'CentyKP'),
(1, 12, '100% бавовна'),
(1, 13, '2024'),
(1, 14, '1 рік'),
(1, 15, 'Приталена'),
(1, 16, '0.3 кг'),
(2, 1, 'Мультиколір'),
(2, 2, 'M'),
(2, 3, 'Віскоза'),
(2, 4, 'Весна/Літо'),
(2, 5, 'Романтичний'),
(2, 6, 'Міді'),
(2, 10, 'Україна'),
(2, 11, 'CentyKP'),
(2, 12, '100% віскоза'),
(2, 13, '2024'),
(2, 14, '1 рік'),
(2, 15, 'Вільна'),
(2, 16, '0.4 кг'),
(3, 1, 'Чорний'),
(3, 2, '42'),
(3, 3, 'Шкіра'),
(3, 4, 'Всесезонні'),
(3, 5, 'Класичний'),
(3, 7, 'Без підборів'),
(3, 10, 'Україна'),
(3, 11, 'CentyKP'),
(3, 13, '2024'),
(3, 14, '2 роки'),
(3, 16, '0.8 кг'),
(4, 1, 'Білий'),
(4, 2, '38'),
(4, 3, 'Текстиль'),
(4, 4, 'Весна/Літо'),
(4, 5, 'Спортивний'),
(4, 7, 'Платформа 5 см'),
(4, 10, 'Україна'),
(4, 11, 'CentyKP'),
(4, 13, '2024'),
(4, 14, '1 рік'),
(4, 16, '0.6 кг'),
(5, 1, 'Бежевий'),
(5, 3, 'Еко-шкіра'),
(5, 5, 'Мінімалістичний'),
(5, 8, '10 л'),
(5, 10, 'Україна'),
(5, 11, 'CentyKP'),
(5, 13, '2024'),
(5, 14, '1 рік'),
(5, 16, '0.5 кг'),
(6, 1, 'Коричневий'),
(6, 3, 'Шкіра'),
(6, 5, 'Класичний'),
(6, 10, 'Україна'),
(6, 11, 'CentyKP'),
(6, 13, '2024'),
(6, 14, '2 роки'),
(8, 1, 'Сірий'),
(8, 3, 'Поліестер'),
(8, 5, 'Спортивний'),
(8, 8, '25 л'),
(8, 10, 'Україна'),
(8, 11, 'CentyKP'),
(8, 13, '2024'),
(8, 14, '1 рік'),
(8, 16, '0.7 кг'),
(9, 1, 'Чорний'),
(9, 3, 'Вовна'),
(9, 4, 'Зима'),
(9, 5, 'Практичний'),
(9, 10, 'Україна'),
(9, 11, 'CentyKP'),
(9, 13, '2024'),
(9, 14, '1 рік'),
(9, 16, '0.2 кг'),
(10, 1, 'Бежевий'),
(10, 2, '39'),
(10, 3, 'Шкіра'),
(10, 4, 'Літо'),
(10, 5, 'Елегантний'),
(10, 7, 'Підбори 9 см'),
(10, 10, 'Україна'),
(10, 11, 'CentyKP'),
(10, 13, '2024'),
(10, 14, '1 рік'),
(10, 16, '0.5 кг'),
(11, 1, 'Білий'),
(11, 2, 'XL'),
(11, 3, 'Бавовна'),
(11, 4, 'Всесезонні'),
(11, 5, 'Класичний'),
(11, 10, 'Україна'),
(11, 11, 'CentyKP'),
(11, 12, '100% бавовна'),
(11, 13, '2024'),
(11, 14, '1 рік'),
(11, 16, '0.3 кг'),
(12, 1, 'Сірий'),
(12, 2, 'M'),
(12, 3, 'Бавовна'),
(12, 4, 'Весна/Літо'),
(12, 5, 'Casual'),
(12, 10, 'Україна'),
(12, 11, 'CentyKP'),
(12, 12, '100% бавовна'),
(12, 13, '2024'),
(12, 14, '1 рік'),
(12, 16, '0.28 кг'),
(13, 1, 'Чорний'),
(13, 2, 'L'),
(13, 3, 'Мікрофібра'),
(13, 4, 'Всесезонні'),
(13, 5, 'Діловий'),
(13, 10, 'Україна'),
(13, 11, 'CentyKP'),
(13, 12, '95% мікрофібра, 5% еластан'),
(13, 13, '2024'),
(13, 14, '1 рік'),
(13, 16, '0.32 кг'),
(14, 1, 'Синій'),
(14, 2, 'XL'),
(14, 3, 'Бавовна'),
(14, 4, 'Літо'),
(14, 5, 'Спортивний'),
(14, 10, 'Україна'),
(14, 11, 'CentyKP'),
(14, 12, '100% бавовна'),
(14, 13, '2024'),
(14, 14, '1 рік'),
(14, 16, '0.25 кг'),
(15, 1, 'Різнокольоровий'),
(15, 2, 'M'),
(15, 3, 'Бавовна'),
(15, 4, 'Весна/Літо'),
(15, 5, 'Сучасний'),
(15, 10, 'Україна'),
(15, 11, 'CentyKP'),
(15, 12, '100% бавовна'),
(15, 13, '2024'),
(15, 14, '1 рік'),
(15, 16, '0.3 кг'),
(16, 1, 'Темно-синій'),
(16, 2, 'L'),
(16, 3, 'Бавовна'),
(16, 4, 'Всесезонні'),
(16, 5, 'Класичний'),
(16, 10, 'Україна'),
(16, 11, 'CentyKP'),
(16, 12, '100% бавовна'),
(16, 13, '2024'),
(16, 14, '1 рік'),
(16, 16, '0.35 кг'),
(17, 1, 'Білий'),
(17, 2, 'S'),
(17, 3, 'Бавовна'),
(17, 4, 'Всесезонні'),
(17, 5, 'Мінімалістичний'),
(17, 10, 'Україна'),
(17, 11, 'CentyKP'),
(17, 12, '100% бавовна'),
(17, 13, '2024'),
(17, 14, '1 рік'),
(17, 16, '0.28 кг'),
(18, 1, 'Молочний'),
(18, 2, 'M'),
(18, 3, 'Льон'),
(18, 4, 'Літо'),
(18, 5, 'Casual'),
(18, 10, 'Україна'),
(18, 11, 'CentyKP'),
(18, 12, '100% льон'),
(18, 13, '2024'),
(18, 14, '1 рік'),
(18, 16, '0.32 кг'),
(19, 1, 'Блакитний'),
(19, 2, 'L'),
(19, 3, 'Бавовна'),
(19, 4, 'Літо'),
(19, 5, 'Спортивний'),
(19, 10, 'Україна'),
(19, 11, 'CentyKP'),
(19, 12, '100% бавовна'),
(19, 13, '2024'),
(19, 14, '1 рік'),
(19, 16, '0.25 кг'),
(20, 1, 'Білий з чорним'),
(20, 2, 'M'),
(20, 3, 'Мережево'),
(20, 4, 'Всесезонні'),
(20, 5, 'Елегантний'),
(20, 10, 'Україна'),
(20, 11, 'CentyKP'),
(20, 12, '50% бавовна, 50% мережево'),
(20, 13, '2024'),
(20, 14, '1 рік'),
(20, 16, '0.3 кг'),
(21, 1, 'Мультиколір'),
(21, 2, 'L'),
(21, 3, 'Шифон'),
(21, 4, 'Весна/Літо'),
(21, 5, 'Романтичний'),
(21, 6, 'Максі'),
(21, 10, 'Україна'),
(21, 11, 'CentyKP'),
(21, 12, '100% шифон'),
(21, 13, '2024'),
(21, 14, '1 рік'),
(21, 15, 'Вільна'),
(21, 16, '0.4 кг'),
(22, 1, 'Чорний'),
(22, 2, 'M'),
(22, 3, 'Поліестер'),
(22, 4, 'Всесезонні'),
(22, 5, 'Діловий'),
(22, 6, 'Міді'),
(22, 10, 'Україна'),
(22, 11, 'CentyKP'),
(22, 12, '95% поліестер, 5% еластан'),
(22, 13, '2024'),
(22, 14, '1 рік'),
(22, 15, 'Приталена'),
(22, 16, '0.35 кг'),
(23, 1, 'Темно-синій'),
(23, 2, 'S'),
(23, 3, 'Шовк'),
(23, 4, 'Всесезонні'),
(23, 5, 'Елегантний'),
(23, 6, 'Міні'),
(23, 10, 'Україна'),
(23, 11, 'CentyKP'),
(23, 12, '100% шовк'),
(23, 13, '2024'),
(23, 14, '1 рік'),
(23, 15, 'Приталена'),
(23, 16, '0.3 кг'),
(24, 1, 'Сірий'),
(24, 2, 'L'),
(24, 3, 'Трикотаж'),
(24, 4, 'Всесезонні'),
(24, 5, 'Casual'),
(24, 6, 'Міді'),
(24, 10, 'Україна'),
(24, 11, 'CentyKP'),
(24, 12, '95% віскоза, 5% еластан'),
(24, 13, '2024'),
(24, 14, '1 рік'),
(24, 15, 'Вільна'),
(24, 16, '0.4 кг'),
(25, 1, 'Рожевий'),
(25, 2, 'M'),
(25, 3, 'Шовк'),
(25, 4, 'Весна/Літо'),
(25, 5, 'Романтичний'),
(25, 6, 'Максі'),
(25, 10, 'Україна'),
(25, 11, 'CentyKP'),
(25, 12, '100% шовк'),
(25, 13, '2024'),
(25, 14, '1 рік'),
(25, 15, 'Вільна'),
(25, 16, '0.45 кг'),
(26, 1, 'Бежевий'),
(26, 2, 'S'),
(26, 3, 'Шовк'),
(26, 4, 'Всесезонні'),
(26, 5, 'Мінімалістичний'),
(26, 6, 'Міні'),
(26, 10, 'Україна'),
(26, 11, 'CentyKP'),
(26, 12, '100% шовк'),
(26, 13, '2024'),
(26, 14, '1 рік'),
(26, 15, 'Приталена'),
(26, 16, '0.25 кг'),
(27, 1, 'Чорно-білий'),
(27, 2, 'L'),
(27, 3, 'Поліестер'),
(27, 4, 'Весна/Літо'),
(27, 5, 'Вінтаж'),
(27, 6, 'Міді'),
(27, 10, 'Україна'),
(27, 11, 'CentyKP'),
(27, 12, '100% поліестер'),
(27, 13, '2024'),
(27, 14, '1 рік'),
(27, 15, 'Приталена'),
(27, 16, '0.35 кг'),
(28, 1, 'Синій'),
(28, 2, 'M'),
(28, 3, 'Спортивний трикотаж'),
(28, 4, 'Весна/Літо'),
(28, 5, 'Спортивний'),
(28, 6, 'Міні'),
(28, 10, 'Україна'),
(28, 11, 'CentyKP'),
(28, 12, '90% поліестер, 10% еластан'),
(28, 13, '2024'),
(28, 14, '1 рік'),
(28, 15, 'Приталена'),
(28, 16, '0.3 кг'),
(29, 1, 'Смарагдовий'),
(29, 2, 'S'),
(29, 3, 'Шовк'),
(29, 4, 'Весна/Літо'),
(29, 5, 'Сучасний'),
(29, 6, 'Максі'),
(29, 10, 'Україна'),
(29, 11, 'CentyKP'),
(29, 12, '100% шовк'),
(29, 13, '2024'),
(29, 14, '1 рік'),
(29, 15, 'Асиметрична'),
(29, 16, '0.4 кг'),
(30, 1, 'Теракотовий'),
(30, 2, 'L'),
(30, 3, 'Віскоза'),
(30, 4, 'Весна/Літо'),
(30, 5, 'Етнічний'),
(30, 6, 'Міді'),
(30, 10, 'Україна'),
(30, 11, 'CentyKP'),
(30, 12, '100% віскоза'),
(30, 13, '2024'),
(30, 14, '1 рік'),
(30, 15, 'Вільна'),
(30, 16, '0.38 кг'),
(31, 1, 'Ніжно-рожевий'),
(31, 2, 'M'),
(31, 3, 'Шифон'),
(31, 4, 'Літо'),
(31, 5, 'Романтичний'),
(31, 6, 'Максі'),
(31, 10, 'Україна'),
(31, 11, 'CentyKP'),
(31, 12, '100% шифон'),
(31, 13, '2024'),
(31, 14, '1 рік'),
(31, 15, 'Вільна'),
(31, 16, '0.35 кг'),
(32, 1, 'Жовтий'),
(32, 2, 'S'),
(32, 3, 'Бавовна'),
(32, 4, 'Літо'),
(32, 5, 'Casual'),
(32, 6, 'Міді'),
(32, 10, 'Україна'),
(32, 11, 'CentyKP'),
(32, 12, '100% бавовна'),
(32, 13, '2024'),
(32, 14, '1 рік'),
(32, 15, 'Вільна'),
(32, 16, '0.32 кг'),
(34, 1, 'Темно-зелений'),
(34, 2, 'M'),
(34, 3, 'Поліестер'),
(34, 4, 'Всесезонні'),
(34, 5, 'Класичний'),
(34, 6, 'Міді'),
(34, 10, 'Україна'),
(34, 11, 'CentyKP'),
(34, 12, '95% поліестер, 5% еластан'),
(34, 13, '2024'),
(34, 14, '1 рік'),
(34, 15, 'Приталена'),
(34, 16, '0.35 кг'),
(35, 1, 'Білий'),
(35, 2, 'S'),
(35, 3, 'Шовк'),
(35, 4, 'Весна/Літо'),
(35, 5, 'Романтичний'),
(35, 6, 'Максі'),
(35, 10, 'Україна'),
(35, 11, 'CentyKP'),
(35, 12, '100% шовк'),
(35, 13, '2024'),
(35, 14, '1 рік'),
(35, 15, 'Вільна'),
(35, 16, '0.4 кг');

-- --------------------------------------------------------

--
-- Table structure for table `Product_pt`
--

CREATE TABLE `Product_pt` (
  `product_id` int NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
PARTITION BY RANGE (floor(`price`))
(
PARTITION p_budget VALUES LESS THAN (600) ENGINE=InnoDB,
PARTITION p_regular VALUES LESS THAN (1500) ENGINE=InnoDB,
PARTITION p_premium VALUES LESS THAN MAXVALUE ENGINE=InnoDB
);

--
-- Dumping data for table `Product_pt`
--

INSERT INTO `Product_pt` (`product_id`, `product_name`, `category`, `price`, `stock_quantity`) VALUES
(1, 'Чоловіча сорочка Slim Fit', 'Стильна приталена сорочка з бавовни.', '499.99', 50),
(5, 'Сумка-шопер з екошкіри', 'Містка та екологічна сумка для покупок.', '399.99', 60),
(6, 'Чоловічий шкіряний ремінь', 'Якісний шкіряний ремінь з металевою пряжкою.', '249.99', 70),
(7, 'Жіночий капелюх з широкими крисами', 'Елегантний капелюх для захисту від сонця.', '349.99', 50),
(8, 'Спортивний рюкзак', 'Зручний рюкзак для тренувань та подорожей.', '599.99', 35),
(9, 'Зимова чоловіча шапка', 'Тепла шапка з вовни.', '299.99', 45),
(2, 'Жіноча сукня з квітковим принтом', 'Легка літня сукня з віскози.', '799.99', 30),
(3, 'Шкіряні чоловічі туфлі', 'Класичні шкіряні туфлі для офісу.', '1299.99', 20),
(4, 'Жіночі кросівки на платформі', 'Модні та зручні кросівки.', '999.99', 40),
(10, 'Жіночі босоніжки на підборах', 'Витончені босоніжки для особливих випадків.', '899.99', 25);

-- --------------------------------------------------------

--
-- Table structure for table `Promotion`
--

CREATE TABLE `Promotion` (
  `promotion_id` int NOT NULL,
  `product_id` int NOT NULL,
  `discount_percentage` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Promotion`
--

INSERT INTO `Promotion` (`promotion_id`, `product_id`, `discount_percentage`, `start_date`, `end_date`) VALUES
(1, 1, 20, '2024-01-01', '2024-02-28'),
(2, 3, 15, '2024-01-15', '2024-03-15'),
(3, 9, 25, '2024-12-01', '2025-01-31'),
(4, 2, 30, '2024-06-01', '2024-08-31'),
(5, 4, 25, '2024-07-01', '2024-08-15'),
(6, 5, 20, '2024-05-15', '2024-06-30'),
(7, 6, 15, '2024-04-01', '2024-05-15'),
(9, 8, 20, '2024-04-15', '2024-05-31'),
(10, 10, 35, '2024-02-14', '2024-02-15');

-- --------------------------------------------------------

--
-- Table structure for table `Property`
--

CREATE TABLE `Property` (
  `property_id` int NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Property`
--

INSERT INTO `Property` (`property_id`, `name`) VALUES
(1, 'Колір'),
(2, 'Розмір'),
(3, 'Матеріал'),
(4, 'Сезон'),
(5, 'Стиль'),
(6, 'Довжина'),
(7, 'Висота підборів'),
(8, 'Об`єм'),
(9, 'Вага'),
(10, 'Країна виробник'),
(11, 'Бренд'),
(12, 'Склад тканини'),
(13, 'Рік колекції'),
(14, 'Гарантія'),
(15, 'Посадка по фігурі'),
(16, 'Вага виробу');

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE `User` (
  `user_id` int NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `middlename` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `lastname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(60) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `role` enum('user','manager','admin','ban') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'user',
  `total_spent_amount` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `User`
--

INSERT INTO `User` (`user_id`, `name`, `middlename`, `lastname`, `email`, `password`, `phone`, `role`, `total_spent_amount`) VALUES
(1, 'John', 'A.', 'Doe', 'john.doe@example.com', 'pass123', '1234567890', 'user', '0.00'),
(2, 'Jane', 'B.', 'Smith', 'jane.smith@example.com', 'pass456', '0987654321', 'manager', '0.00'),
(3, 'Alice', 'C.', 'Brown', 'alice.brown@example.com', 'pass789', '1122334455', 'admin', '0.00'),
(4, 'Bob', 'D.', 'White', 'bob.white@example.com', 'pass321', '2233445566', 'user', '0.00'),
(5, 'Charlie', 'E.', 'Green', 'charlie.green@example.com', 'pass654', '3344556677', 'manager', '0.00'),
(6, 'David', 'F.', 'Black', 'david.black@example.com', 'pass987', '4455667788', 'admin', '0.00'),
(7, 'Ella', 'G.', 'Gray', 'ella.gray@example.com', 'pass135', '5566778899', 'user', '0.00'),
(8, 'Frank', 'H.', 'Silver', 'frank.silver@example.com', 'pass246', '6677889900', 'manager', '0.00'),
(9, 'Grace', 'I.', 'Gold', 'grace.gold@example.com', 'pass369', '7788990011', 'admin', '0.00'),
(10, 'Hank', 'J.', 'Stone', 'hank.stone@example.com', 'pass147', '8899001122', 'user', '0.00'),
(11, 'Іван', 'Петрович', 'Сидоренко', 'ivan.sydorenko@example.com', '482c811da5d5b4bc6d497ffa98491e38', '0951234567', 'user', '0.00'),
(12, 'Олена', 'Василівна', 'Коваленко', 'olena.kovalenko@example.com', '83cbbd381252d74d77a3ec135966d15e', '0679876543', 'user', '0.00'),
(13, 'Петро', 'Іванович', 'Мельник', 'petro.melnyk@example.com', 'a5beb6624e092adf7be31176c3079e64', '0931122334', 'user', '0.00'),
(14, 'Наталія', 'Сергіївна', 'Бондаренко', 'natalia.bondarenko@example.com', '1699d9ce00f1a3627850fd582352564c', '0664455667', 'user', '0.00'),
(15, 'Андрій', 'Миколайович', 'Ткаченко', 'andriy.tkachenko@example.com', 'b4af804009cb036a4ccdc33431ef9ac9', '0997788990', 'user', '0.00'),
(16, 'Світлана', 'Олександрівна', 'Кравченко', 'svitlana.kravchenko@example.com', '3fc0a7acf087f549ac2b266baf94b8b1', '0632233445', 'user', '0.00'),
(17, 'Юрій', 'Вікторович', 'Шевченко', 'yuriy.shevchenko@example.com', '513106c051f94528f1d386926aa65e1a', '0975566778', 'user', '0.00'),
(18, 'Тетяна', 'Іванівна', 'Коваль', 'tetiana.koval@example.com', '7ac60358d4f56501575fa9def6cc3bc3', '0508899001', 'user', '0.00'),
(19, 'Роман', 'Андрійович', 'Поліщук', 'roman.polishchuk@example.com', '3805248410673a8be6aa4807e61fb5ae', '0983344556', 'user', '0.00'),
(20, 'Вікторія', 'Дмитрівна', 'Савченко', 'victoria.savchenko@example.com', 'ef22cbee172258880fd316cb2b0cd2ed', '0916677889', 'user', '0.00'),
(21, 'Volodymyr', 'Mykolaiovych', 'Lampart', 'admin@gmail.com', '$2y$10$U3L77K1MV9sVl/kyVJc.4.v1SReblIdZ6TWdjauHdOp3PYEE7g55C', '0930235966', 'admin', '83537.34'),
(22, 'manager', 'manager', 'manager', 'manager@gmail.com', '$2y$10$bioU9A0/A1CT9q3joF0s5uFdE9HlCOIj7yyHh/lM9Wzfs4ddDBZnC', '0930235960', 'manager', '0.00');

-- --------------------------------------------------------

--
-- Structure for view `productwithdiscount`
--
DROP TABLE IF EXISTS `productwithdiscount`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `productwithdiscount`  AS SELECT `p`.`product_id` AS `product_id`, `p`.`name` AS `name`, `p`.`description` AS `description`, `p`.`quantity` AS `quantity`, `p`.`price` AS `original_price`, (case when ((`pr`.`discount_percentage` is not null) and (curdate() between `pr`.`start_date` and `pr`.`end_date`)) then (`p`.`price` * (1 - (`pr`.`discount_percentage` / 100.0))) else `p`.`price` end) AS `discounted_price`, `pr`.`discount_percentage` AS `discount_percentage`, `pr`.`start_date` AS `start_date`, `pr`.`end_date` AS `end_date` FROM (`product` `p` left join `promotion` `pr` on((`p`.`product_id` = `pr`.`product_id`)))  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Cart`
--
ALTER TABLE `Cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `cart_user_idx` (`user_id`);

--
-- Indexes for table `Cart_Item`
--
ALTER TABLE `Cart_Item`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `cartItem_cart_idx` (`cart_id`),
  ADD KEY `cartItem_product_idx` (`product_id`),
  ADD KEY `idx_cart_item_product` (`product_id`);

--
-- Indexes for table `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_idx` (`parent_category_id`);

--
-- Indexes for table `Discount_Scheme`
--
ALTER TABLE `Discount_Scheme`
  ADD PRIMARY KEY (`discount_scheme_id`);

--
-- Indexes for table `Ordering`
--
ALTER TABLE `Ordering`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `ordering_user_idx` (`user_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_status` (`status`);

--
-- Indexes for table `Order_Item`
--
ALTER TABLE `Order_Item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `orderItem_ordering_idx` (`order_id`),
  ADD KEY `orderItem_product_idx` (`product_id`);

--
-- Indexes for table `Product`
--
ALTER TABLE `Product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `Product_Category`
--
ALTER TABLE `Product_Category`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD UNIQUE KEY `idx_product_category` (`product_id`,`category_id`),
  ADD KEY `productCategory_category_idx` (`category_id`),
  ADD KEY `idx_category_id` (`category_id`);

--
-- Indexes for table `Product_log`
--
ALTER TABLE `Product_log`
  ADD PRIMARY KEY (`product_id_log`),
  ADD KEY `fk_product_log_product` (`product_id`);

--
-- Indexes for table `Product_Property_Value`
--
ALTER TABLE `Product_Property_Value`
  ADD PRIMARY KEY (`product_id`,`property_id`),
  ADD KEY `productPropertyValue_property_idx` (`property_id`);

--
-- Indexes for table `Product_pt`
--
ALTER TABLE `Product_pt`
  ADD PRIMARY KEY (`product_id`,`price`);

--
-- Indexes for table `Promotion`
--
ALTER TABLE `Promotion`
  ADD PRIMARY KEY (`promotion_id`),
  ADD UNIQUE KEY `product_id_UNIQUE` (`product_id`),
  ADD KEY `promotion_product_idx` (`product_id`);

--
-- Indexes for table `Property`
--
ALTER TABLE `Property`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `User`
--
ALTER TABLE `User`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email_unique` (`email`),
  ADD UNIQUE KEY `user_phone_unique` (`phone`),
  ADD UNIQUE KEY `idx_user_email` (`email`) USING BTREE,
  ADD UNIQUE KEY `idx_user_phone` (`phone`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Cart`
--
ALTER TABLE `Cart`
  MODIFY `cart_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Cart_Item`
--
ALTER TABLE `Cart_Item`
  MODIFY `cart_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `Category`
--
ALTER TABLE `Category`
  MODIFY `category_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `Discount_Scheme`
--
ALTER TABLE `Discount_Scheme`
  MODIFY `discount_scheme_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Ordering`
--
ALTER TABLE `Ordering`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Order_Item`
--
ALTER TABLE `Order_Item`
  MODIFY `order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `Product`
--
ALTER TABLE `Product`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `Product_log`
--
ALTER TABLE `Product_log`
  MODIFY `product_id_log` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `Promotion`
--
ALTER TABLE `Promotion`
  MODIFY `promotion_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `Property`
--
ALTER TABLE `Property`
  MODIFY `property_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `User`
--
ALTER TABLE `User`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Cart`
--
ALTER TABLE `Cart`
  ADD CONSTRAINT `cart_user` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

--
-- Constraints for table `Cart_Item`
--
ALTER TABLE `Cart_Item`
  ADD CONSTRAINT `cartItem_cart` FOREIGN KEY (`cart_id`) REFERENCES `Cart` (`cart_id`),
  ADD CONSTRAINT `cartItem_product` FOREIGN KEY (`product_id`) REFERENCES `Product` (`product_id`);

--
-- Constraints for table `Category`
--
ALTER TABLE `Category`
  ADD CONSTRAINT `parent` FOREIGN KEY (`parent_category_id`) REFERENCES `Category` (`category_id`);

--
-- Constraints for table `Ordering`
--
ALTER TABLE `Ordering`
  ADD CONSTRAINT `ordering_user` FOREIGN KEY (`user_id`) REFERENCES `User` (`user_id`);

--
-- Constraints for table `Order_Item`
--
ALTER TABLE `Order_Item`
  ADD CONSTRAINT `orderItem_ordering` FOREIGN KEY (`order_id`) REFERENCES `Ordering` (`order_id`),
  ADD CONSTRAINT `orderItem_product` FOREIGN KEY (`product_id`) REFERENCES `Product` (`product_id`);

--
-- Constraints for table `Product_Category`
--
ALTER TABLE `Product_Category`
  ADD CONSTRAINT `productCategory_category` FOREIGN KEY (`category_id`) REFERENCES `Category` (`category_id`),
  ADD CONSTRAINT `productCategory_product` FOREIGN KEY (`product_id`) REFERENCES `Product` (`product_id`);

--
-- Constraints for table `Product_log`
--
ALTER TABLE `Product_log`
  ADD CONSTRAINT `fk_product_log_product` FOREIGN KEY (`product_id`) REFERENCES `Product` (`product_id`);

--
-- Constraints for table `Product_Property_Value`
--
ALTER TABLE `Product_Property_Value`
  ADD CONSTRAINT `productPropertyValue_product` FOREIGN KEY (`product_id`) REFERENCES `Product` (`product_id`),
  ADD CONSTRAINT `productPropertyValue_property` FOREIGN KEY (`property_id`) REFERENCES `Property` (`property_id`);

--
-- Constraints for table `Promotion`
--
ALTER TABLE `Promotion`
  ADD CONSTRAINT `promotion_product` FOREIGN KEY (`product_id`) REFERENCES `Product` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
