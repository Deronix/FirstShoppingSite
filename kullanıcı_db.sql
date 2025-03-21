-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 21 Mar 2025, 17:12:10
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `kullanıcı_db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cart_data`
--

CREATE TABLE `cart_data` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `address` text NOT NULL,
  `card_number` varchar(16) NOT NULL,
  `cvc` varchar(3) NOT NULL,
  `delivery_code` varchar(8) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanıcı_verileri`
--

CREATE TABLE `kullanıcı_verileri` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `token` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanıcı_verileri`
--

INSERT INTO `kullanıcı_verileri` (`id`, `username`, `email`, `password`, `phone`, `token`) VALUES
(30, 'LastChristmas', 'IGaveYouMyHeart@The.VeryNextDay', '$argon2id$v=19$m=65536,t=4,p=1$dEpqSm4wNVpEZGZFZWRLcA$sGo56V3i0YxFr0xUpZ0GArNmHgJsCXXe9vfSRGRF7i4', '+90 555 555 55 55 ', '6a1ddd239c8c0c4b48f00929f27b8e30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler`
--

CREATE TABLE `urunler` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `information` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `question` text DEFAULT NULL,
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`id`, `name`, `description`, `price`, `image`, `image2`, `image3`, `date_added`, `information`, `comments`, `question`, `answer`) VALUES
(1, 'Wooden Coffee Table', 'A sturdy and stylish wooden coffee table.', 89.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wooden_coffee_table.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wooden_coffee_table2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wooden_coffee_table3.jpg', '2025-01-10 21:32:48', 'Made from solid oak with a natural finish.', 'Great product!', 'Does it require assembly?', 'Yes, minimal.'),
(2, 'Modern Floor Lamp', 'Sleek floor lamp with adjustable head .', 45.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/modern_floor_lamp.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/modern_floor_lamp2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/modern_floor_lamp3.jpg', '2025-01-10 21:32:48', 'Features an energy-saving LED bulb.', 'Beautiful lighting!', 'What colors are available?', 'Black and white.'),
(3, 'Wall Art Set', 'Three-piece abstract wall art for modern spaces.', 75.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wall_art_set.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wall_art_set2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wall_art_set3.jpg', '2025-01-10 21:32:48', 'Printed on premium canvas with a matte finish.', 'Eye-catching design!', 'Can it be hung outdoors?', 'Indoor use only.'),
(4, 'Leather Office Chair', 'Ergonomic office chair with premium leather.', 150.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/leather_office_chair.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/leather_office_chair2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/leather_office_chair3.jpg', '2025-01-10 21:32:48', 'Adjustable height and lumbar support.', 'Very comfortable!', 'How much weight does it hold?', 'Up to 250 lbs.'),
(5, 'Cotton T-Shirt', '100% cotton T-shirt available in various colors.\n', 12.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cotton_tshirt.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cotton_tshirt2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cotton_tshirt3.jpg', '2025-01-10 21:32:48', 'Soft, breathable fabric.', 'Fits perfectly!', 'Is it pre-shrunk?', 'Yes.'),
(6, 'Multi-Tool Set', '15-piece multi-tool set with a carrying case.\n', 34.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/multi_tool_set.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/multi_tool_set2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/multi_tool_set3.jpg', '2025-01-10 21:32:48', 'Ideal for home and outdoor repairs.', 'Good value!', 'Are the tools stainless steel?', 'Yes.'),
(7, 'Ceramic Vase', 'Elegant ceramic vase with a smooth finish.\n', 22.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/ceramic_vase.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/ceramic_vase2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/ceramic_vase3.jpg', '2025-01-10 21:32:48', 'Height: 12 inches, perfect for medium-sized bouquets.', 'Lovely craftsmanship!', 'What colors are available?', 'White and teal.'),
(8, 'Electric Kettle', 'Fast-boiling electric kettle with automatic shut-off.', 29.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_kettle.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_kettle2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_kettle3.jpg', '2025-01-10 21:32:48', '1.7-liter capacity, BPA-free.', 'Heats water quickly!', 'Is it cordless?', 'Yes, with a base.'),
(9, 'Bookshelf Unit', 'Tall five-tier wooden bookshelf.\n', 65.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bookshelf_unit.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bookshelf_unit2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bookshelf_unit3.jpg', '2025-01-10 21:32:48', 'Comes with wall-mounting hardware.', 'Sturdy and spacious!', 'Can it hold heavy books?', 'Yes, up to 20 lbs per shelf.'),
(10, 'Wool Throw Blanket', 'Cozy wool throw blanket for chilly evenings.\n', 39.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wool_throw_blanket.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wool_throw_blanket2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wool_throw_blanket3.jpg', '2025-01-10 21:32:48', 'Made from 100% natural wool.', 'Very warm!', 'Is it machine washable?', 'Dry clean only.'),
(11, 'Garden Tool Set', '5-piece garden tool set including a trowel.', 19.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/garden_tool_set.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/garden_tool_set2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/garden_tool_set3.jpg', '2025-01-10 21:32:48', 'Ergonomic handles for comfort.', 'Handy for gardening!', 'Are the handles rubberized?', 'Yes.'),
(12, 'Bamboo Cutting Board', 'Eco-friendly bamboo cutting board with groove.', 15.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bamboo_cutting_board.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bamboo_cutting_board2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bamboo_cutting_board3.jpg', '2025-01-10 21:32:48', 'Naturally antimicrobial.', 'Great quality!', 'Is it dishwasher safe?', 'Hand wash only.'),
(13, 'Stainless Steel Pan', 'Non-stick stainless steel frying pan with lid.', 49.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/stainless_steel_pan.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/stainless_steel_pan2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/stainless_steel_pan3.jpg', '2025-01-10 21:32:48', 'Diameter: 12 inches, oven-safe up to 400°F.', 'Heats evenly!', 'Does it have a warranty?', 'Yes, 1 year.'),
(14, 'Wall Clock', 'Minimalist wall clock with a silent mechanism.', 24.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wall_clock.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wall_clock2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wall_clock3.jpg', '2025-01-10 21:32:48', 'Requires 1 AA battery (not included).', 'Very quiet!', 'Is it battery-operated?', 'Yes.'),
(15, 'Steel Water Bottle', 'Insulated water bottle keeps drinks cold for hours.', 18.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/steel_water_bottle.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/steel_water_bottle2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/steel_water_bottle3.jpg', '2025-01-10 21:32:48', 'Leak-proof, BPA-free.', 'Great insulation!', 'Is it dishwasher safe?', 'Yes.'),
(16, 'Denim Jacket', 'Classic denim jacket with button closures.\n', 55.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/denim_jacket.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/denim_jacket2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/denim_jacket3.jpg', '2025-01-10 21:32:48', 'Made of durable denim with two chest pockets.', 'Stylish!', 'Does it have an inner lining?', 'No.'),
(17, 'Decorative Mirror', 'Round decorative mirror with a golden frame.\n', 45.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/decorative_mirror.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/decorative_mirror2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/decorative_mirror3.jpg', '2025-01-10 21:32:48', 'Diameter: 24 inches.', 'Beautiful addition!', 'Can it be wall-mounted?', 'Yes, hardware included.'),
(18, 'Camping Tent', 'Two-person waterproof lightweight camping tent.', 89.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/camping_tent.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/camping_tent2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/camping_tent3.jpg', '2025-01-10 21:32:48', 'Easy setup with aluminum poles.', 'Great for hiking!', 'Is it suitable for windy conditions?', 'Yes.'),
(21, 'Wireless Earbuds', 'High-quality sound & noise-canceling feature.', 99.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wireless_earbuds.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wireless_earbuds2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wireless_earbuds3.jpg', '2025-01-10 18:35:37', 'Includes charging case and Bluetooth 5.0.', 'Amazing sound!', 'Does it have a mic?', 'Yes, built-in.'),
(22, 'Air Fryer', 'Compact air fryer for healthy frying.\n', 89.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/air_fryer.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/air_fryer2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/air_fryer3.jpg', '2025-01-10 18:35:37', '2-liter capacity with timer control.', 'Makes crispy fries!', 'Is it dishwasher safe?', 'Yes, basket only.'),
(23, 'Smartphone Holder', 'Adjustable car mount for smartphones. ', 19.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/smartphone_holder.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/smartphone_holder2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/smartphone_holder3.jpg', '2025-01-10 18:35:37', 'Compatible with devices up to 6.5 inches.', 'Holds firmly!', 'Does it rotate?', 'Yes, 360 degrees.'),
(24, 'Robot Vacuum Cleaner', 'Automated vacuum cleaner with smart navigation.', 249.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/robot_vacuum_cleaner.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/robot_vacuum_cleaner2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/robot_vacuum_cleaner3.jpg', '2025-01-10 18:35:37', 'Includes app control and scheduling.', 'Cleans well!', 'Does it return to base?', 'Yes, auto-docking.'),
(25, 'Insulated Lunch Bag', 'Durable lunch bag with multiple compartments. ', 25.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/insulated_lunch_bag.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/insulated_lunch_bag2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/insulated_lunch_bag3.jpg', '2025-01-10 18:35:37', 'Keeps food cold or warm for hours.', 'Good size!', 'Is it leakproof?', 'Yes.'),
(26, 'Yoga Mat', 'Non-slip yoga mat with cushioning.', 29.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/yoga_mat.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/yoga_mat2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/yoga_mat3.jpg', '2025-01-10 18:35:37', '6mm thick, lightweight.', 'Perfect grip!', 'Is it washable?', 'Yes, hand wash.'),
(27, 'Portable Blender', 'USB rechargeable portable blender.\nVery fast.', 39.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/portable_blender.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/portable_blender2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/portable_blender3.jpg', '2025-01-10 18:35:37', '400ml capacity with stainless steel blades.', 'Very convenient!', 'Can it crush ice?', 'Yes, small cubes.'),
(28, 'Electric Toothbrush', 'Rechargeable toothbrush with multiple modes.', 45.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_toothbrush.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_toothbrush2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_toothbrush3.jpg', '2025-01-10 18:35:37', 'Includes 2 brush heads.', 'Teeth feel clean!', 'Does it have a timer?', 'Yes, 2 minutes.'),
(29, 'Laptop Stand', 'Adjustable aluminum laptop stand. very sturdy.', 27.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/laptop_stand.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/laptop_stand2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/laptop_stand3.jpg', '2025-01-10 18:35:37', 'Supports up to 17-inch laptops.', 'Sturdy and lightweight!', 'Is it foldable?', 'Yes.'),
(30, 'Rechargeable Flashlight', 'Bright LED flashlight with rechargeable battery.\n', 18.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/rechargeable_flashlight.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/rechargeable_flashlight2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/rechargeable_flashlight3.jpg', '2025-01-10 18:35:37', 'Includes USB charging cable.', 'Very bright!', 'How long does the battery last?', 'Up to 8 hours.'),
(31, 'Bluetooth Speaker', 'Portable Bluetooth speaker with deep bass.\n', 59.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bluetooth_speaker.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bluetooth_speaker2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bluetooth_speaker3.jpg', '2025-01-10 18:35:37', '10-hour battery life.', 'Great sound!', 'Is it waterproof?', 'Yes, IPX7.'),
(32, 'Digital Pressure Cooker', 'Multi-function pressure cooker with digital control.', 110.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/digital_pressure_cooker.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/digital_pressure_cooker2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/digital_pressure_cooker3.jpg', '2025-01-10 18:35:37', 'Includes 7 cooking modes.', 'Very versatile!', 'Is it non-stick?', 'Yes, inner pot.'),
(33, 'Memory Foam Pillow', 'Ergonomic memory foam pillow for better sleep. ', 40.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/memory_foam_pillow.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/memory_foam_pillow2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/memory_foam_pillow3.jpg', '2025-01-10 18:35:37', 'Cooling gel layer.', 'Super comfortable!', 'Is it washable?', 'Cover only.'),
(34, 'Travel Backpack', 'Water-resistant travel backpack with USB port. ', 65.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/travel_backpack.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/travel_backpack2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/travel_backpack3.jpg', '2025-01-10 18:35:37', 'Includes hidden anti-theft pocket.', 'Very spacious!', 'Is it TSA-approved?', 'Yes.'),
(35, 'Electric Griddle', 'Large non-stick electric griddle.\n', 55.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_griddle.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_griddle2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_griddle3.jpg', '2025-01-10 18:35:37', 'Ideal for pancakes and eggs.', 'Cooks evenly!', 'Is it smokeless?', 'Yes.'),
(36, 'Popcorn Maker', 'Hot air popcorn maker for healthy snacks.', 24.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/popcorn_maker.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/popcorn_maker2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/popcorn_maker3.jpg', '2025-01-10 18:35:37', 'No oil required.', 'Works great!', 'How much does it pop?', 'Up to 16 cups.'),
(37, 'Waterproof Picnic Blanket', 'Large picnic blanket with waterproof backing.', 29.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/picnic_blanket.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/picnic_blanket2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/picnic_blanket3.jpg', '2025-01-10 18:35:37', '60x80 inches, foldable.', 'Easy to carry!', 'Is it machine washable?', 'Yes.'),
(38, 'Electric Hand Mixer', 'Hand mixer with multiple speed settings.\nVery fast.', 35.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/hand_mixer.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/hand_mixer2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/hand_mixer3.jpg', '2025-01-10 18:35:37', 'Includes 2 beaters and a whisk.', 'Powerful motor!', 'Does it have a turbo mode?', 'Yes.'),
(39, 'Cordless Drill', 'Rechargeable cordless drill with 20V battery.', 79.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cordless_drill.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cordless_drill2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cordless_drill3.jpg', '2025-01-10 18:35:37', 'Includes 30-piece accessory kit.', 'Very durable!', 'Is it lightweight?', 'Yes.'),
(40, 'Weighted Blanket', 'Calming weighted blanket with breathable cover.', 59.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/weighted_blanket.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/weighted_blanket2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/weighted_blanket3.jpg', '2025-01-10 18:35:37', '15 lbs for relaxation.', 'Really helps sleep!', 'Is it machine washable?', 'Yes, cover only.');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler_hesap_cart`
--

CREATE TABLE `urunler_hesap_cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urunler_hesap_cart`
--

INSERT INTO `urunler_hesap_cart` (`id`, `user_id`, `product_id`, `quantity`, `price`, `added_at`) VALUES
(77, 66, 23, 1, 0.00, '2025-01-12 14:41:04'),
(78, 66, 2, 2, 0.00, '2025-01-12 14:41:14'),
(79, 66, 22, 2, 0.00, '2025-01-12 14:41:59'),
(84, 70, 1, 1, 0.00, '2025-01-12 20:07:36'),
(85, 70, 2, 1, 0.00, '2025-01-12 20:07:37'),
(86, 70, 3, 1, 0.00, '2025-01-12 20:07:39'),
(87, 70, 4, 1, 0.00, '2025-01-12 20:07:43'),
(88, 110, 2, 1, 0.00, '2025-01-12 23:25:10'),
(89, 30, 2, 1, 0.00, '2025-03-21 16:01:19');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler_hesap_favorites`
--

CREATE TABLE `urunler_hesap_favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urunler_hesap_favorites`
--

INSERT INTO `urunler_hesap_favorites` (`id`, `user_id`, `product_id`, `added_at`) VALUES
(56, 62, 9, '2025-01-11 03:14:16'),
(57, 62, 7, '2025-01-11 03:14:19'),
(58, 62, 29, '2025-01-11 03:14:25'),
(59, 62, 8, '2025-01-11 03:24:58'),
(60, 63, 2, '2025-01-11 18:49:39'),
(61, 63, 8, '2025-01-11 18:49:46'),
(62, 63, 6, '2025-01-11 18:50:16'),
(63, 63, 4, '2025-01-11 18:50:19'),
(102, 66, 2, '2025-01-12 12:02:35'),
(103, 66, 22, '2025-01-12 14:41:05'),
(106, 70, 1, '2025-01-12 20:07:35'),
(107, 70, 2, '2025-01-12 20:07:38'),
(108, 70, 3, '2025-01-12 20:07:40'),
(109, 70, 4, '2025-01-12 20:07:42'),
(110, 110, 2, '2025-01-12 23:24:43'),
(111, 110, 3, '2025-01-12 23:24:49'),
(112, 110, 1, '2025-01-12 23:24:50'),
(113, 110, 6, '2025-01-12 23:24:57'),
(114, 30, 2, '2025-03-21 16:01:36');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urun_popular`
--

CREATE TABLE `urun_popular` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `information` text DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `question` text DEFAULT NULL,
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `urun_popular`
--

INSERT INTO `urun_popular` (`id`, `name`, `description`, `price`, `image`, `image2`, `image3`, `date_added`, `information`, `comments`, `question`, `answer`) VALUES
(21, 'Wireless Earbuds', 'High-quality sound & noise-canceling feature.', 99.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wireless_earbuds.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wireless_earbuds2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/wireless_earbuds3.jpg', '2025-01-10 21:35:37', 'Includes charging case and Bluetooth 5.0.', 'Amazing sound!', 'Does it have a mic?', 'Yes, built-in.'),
(22, 'Air Fryer', 'Compact air fryer for healthy frying.\n', 89.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/air_fryer.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/air_fryer2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/air_fryer3.jpg', '2025-01-10 21:35:37', '2-liter capacity with timer control.', 'Makes crispy fries!', 'Is it dishwasher safe?', 'Yes, basket only.'),
(23, 'Smartphone Holder', 'Adjustable car mount for smartphones. ', 19.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/smartphone_holder.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/smartphone_holder2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/smartphone_holder3.jpg', '2025-01-10 21:35:37', 'Compatible with devices up to 6.5 inches.', 'Holds firmly!', 'Does it rotate?', 'Yes, 360 degrees.'),
(24, 'Robot Vacuum Cleaner', 'Automated vacuum cleaner with smart navigation.', 249.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/robot_vacuum_cleaner.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/robot_vacuum_cleaner2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/robot_vacuum_cleaner3.jpg', '2025-01-10 21:35:37', 'Includes app control and scheduling.', 'Cleans well!', 'Does it return to base?', 'Yes, auto-docking.'),
(25, 'Insulated Lunch Bag', 'Durable lunch bag with multiple compartments. ', 25.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/insulated_lunch_bag.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/insulated_lunch_bag2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/insulated_lunch_bag3.jpg', '2025-01-10 21:35:37', 'Keeps food cold or warm for hours.', 'Good size!', 'Is it leakproof?', 'Yes.'),
(26, 'Yoga Mat', 'Non-slip yoga mat with cushioning. ', 29.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/yoga_mat.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/yoga_mat2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/yoga_mat3.jpg', '2025-01-10 21:35:37', '6mm thick, lightweight.', 'Perfect grip!', 'Is it washable?', 'Yes, hand wash.'),
(27, 'Portable Blender', 'USB rechargeable portable blender.\n', 39.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/portable_blender.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/portable_blender2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/portable_blender3.jpg', '2025-01-10 21:35:37', '400ml capacity with stainless steel blades.', 'Very convenient!', 'Can it crush ice?', 'Yes, small cubes.'),
(28, 'Electric Toothbrush', 'Rechargeable toothbrush with multiple modes.', 45.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_toothbrush.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_toothbrush2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_toothbrush3.jpg', '2025-01-10 21:35:37', 'Includes 2 brush heads.', 'Teeth feel clean!', 'Does it have a timer?', 'Yes, 2 minutes.'),
(29, 'Laptop Stand', 'Adjustable aluminum laptop stand. ', 27.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/laptop_stand.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/laptop_stand2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/laptop_stand3.jpg', '2025-01-10 21:35:37', 'Supports up to 17-inch laptops.', 'Sturdy and lightweight!', 'Is it foldable?', 'Yes.'),
(30, 'Rechargeable Flashlight', 'Bright LED flashlight with rechargeable battery.\n', 18.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/rechargeable_flashlight.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/rechargeable_flashlight2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/rechargeable_flashlight3.jpg', '2025-01-10 21:35:37', 'Includes USB charging cable.', 'Very bright!', 'How long does the battery last?', 'Up to 8 hours.'),
(31, 'Bluetooth Speaker', 'Portable Bluetooth speaker with deep bass.\n', 59.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bluetooth_speaker.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bluetooth_speaker2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/bluetooth_speaker3.jpg', '2025-01-10 21:35:37', '10-hour battery life.', 'Great sound!', 'Is it waterproof?', 'Yes, IPX7.'),
(32, 'Digital Pressure Cooker', 'Multi-function pressure cooker with digital control.', 110.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/digital_pressure_cooker.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/digital_pressure_cooker2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/digital_pressure_cooker3.jpg', '2025-01-10 21:35:37', 'Includes 7 cooking modes.', 'Very versatile!', 'Is it non-stick?', 'Yes, inner pot.'),
(33, 'Memory Foam Pillow', 'Ergonomic memory foam pillow for better sleep.', 40.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/memory_foam_pillow.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/memory_foam_pillow2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/memory_foam_pillow3.jpg', '2025-01-10 21:35:37', 'Cooling gel layer.', 'Super comfortable!', 'Is it washable?', 'Cover only.'),
(34, 'Travel Backpack', 'Water-resistant travel backpack with USB port.', 65.00, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/travel_backpack.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/travel_backpack2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/travel_backpack3.jpg', '2025-01-10 21:35:37', 'Includes hidden anti-theft pocket.', 'Very spacious!', 'Is it TSA-approved?', 'Yes.'),
(35, 'Electric Griddle', 'Large non-stick electric griddle.\n', 55.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_griddle.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_griddle2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/electric_griddle3.jpg', '2025-01-10 21:35:37', 'Ideal for pancakes and eggs.', 'Cooks evenly!', 'Is it smokeless?', 'Yes.'),
(36, 'Popcorn Maker', 'Hot air popcorn maker for healthy snacks.\n', 24.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/popcorn_maker.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/popcorn_maker2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/popcorn_maker3.jpg', '2025-01-10 21:35:37', 'No oil required.', 'Works great!', 'How much does it pop?', 'Up to 16 cups.'),
(37, 'Waterproof Picnic Blanket', 'Large picnic blanket with waterproof backing.\n', 29.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/picnic_blanket.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/picnic_blanket2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/picnic_blanket3.jpg', '2025-01-10 21:35:37', '60x80 inches, foldable.', 'Easy to carry!', 'Is it machine washable?', 'Yes.'),
(38, 'Electric Hand Mixer', 'Hand mixer with multiple speed settings.\n', 35.50, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/hand_mixer.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/hand_mixer2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/hand_mixer3.jpg', '2025-01-10 21:35:37', 'Includes 2 beaters and a whisk.', 'Powerful motor!', 'Does it have a turbo mode?', 'Yes.'),
(39, 'Cordless Drill', 'Rechargeable cordless drill with 20V battery.', 79.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cordless_drill.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cordless_drill2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/cordless_drill3.jpg', '2025-01-10 21:35:37', 'Includes 30-piece accessory kit.', 'Very durable!', 'Is it lightweight?', 'Yes.'),
(40, 'Weighted Blanket', 'Calming weighted blanket with breathable cover.', 59.99, '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/weighted_blanket.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/weighted_blanket2.jpg', '/WEB-VSCODE/GLOBALFILE/Dosyalar/images/weighted_blanket3.jpg', '2025-01-10 21:35:37', '15 lbs for relaxation.', 'Really helps sleep!', 'Is it machine washable?', 'Yes, cover only.');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `cart_data`
--
ALTER TABLE `cart_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Tablo için indeksler `kullanıcı_verileri`
--
ALTER TABLE `kullanıcı_verileri`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `unique_username` (`username`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `unique_phone` (`phone`);

--
-- Tablo için indeksler `urunler`
--
ALTER TABLE `urunler`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `urunler_hesap_cart`
--
ALTER TABLE `urunler_hesap_cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_id`);

--
-- Tablo için indeksler `urunler_hesap_favorites`
--
ALTER TABLE `urunler_hesap_favorites`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `urun_popular`
--
ALTER TABLE `urun_popular`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `cart_data`
--
ALTER TABLE `cart_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Tablo için AUTO_INCREMENT değeri `kullanıcı_verileri`
--
ALTER TABLE `kullanıcı_verileri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=112;

--
-- Tablo için AUTO_INCREMENT değeri `urunler`
--
ALTER TABLE `urunler`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Tablo için AUTO_INCREMENT değeri `urunler_hesap_cart`
--
ALTER TABLE `urunler_hesap_cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- Tablo için AUTO_INCREMENT değeri `urunler_hesap_favorites`
--
ALTER TABLE `urunler_hesap_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- Tablo için AUTO_INCREMENT değeri `urun_popular`
--
ALTER TABLE `urun_popular`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `cart_data`
--
ALTER TABLE `cart_data`
  ADD CONSTRAINT `cart_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `kullanıcı_verileri` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_data_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `urunler` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
