-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 09:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mr.freeze`
--
CREATE DATABASE IF NOT EXISTS `mr.freeze` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mr.freeze`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `a_id` int(11) NOT NULL,
  `a_name` varchar(100) NOT NULL,
  `a_username` varchar(100) NOT NULL,
  `a_password` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`a_id`, `a_name`, `a_username`, `a_password`) VALUES
(1, 'เมธาวาลัย ', 'mind', '4081'),
(2, 'นัธวุฒิ ', 'mark', '4106'),
(3, 'ตะวันฉาย ', 'sun', '4142'),
(4, 'พาฤดี', 'ja', '4151');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `c_id` varchar(255) NOT NULL,
  `c_name_eng` varchar(255) NOT NULL,
  `c_name_th` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`c_id`, `c_name_eng`, `c_name_th`) VALUES
('dc06', 'dessert', 'ของหวานแช่แข็ง'),
('fc03', 'fastfood', 'อาหารสำเร็จรูปแช่เเข็ง'),
('ic05', 'ingredients', 'แป้งและวัตถุดิบแช่แข็ง'),
('mc01', 'meat', 'เนื้อสัตว์เเช่เเข็ง'),
('rc04', 'readyfood', 'อาหารปรุงสุกพร้อมทานแช่แข็ง'),
('sc02', 'seafood', 'อาหารทะเลเเช่เเข็ง');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `u_id` int(255) NOT NULL,
  `u_name` varchar(255) NOT NULL,
  `u_email` varchar(255) NOT NULL,
  `u_password` varchar(255) NOT NULL,
  `u_phone` varchar(255) NOT NULL,
  `u_add` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`u_id`, `u_name`, `u_email`, `u_password`, `u_phone`, `u_add`) VALUES
(1, 'Tawan', '66@test.com', '$2y$10$vueWUZBi4Czncmj66xRyWuqXR9WXnwZdxiMGrBdW8EyqxAfL7cJ2q', '1234567890', ''),
(2, 'ดีๆๆ', 'dee@1', '$2y$10$OMKZTEXNZM7eN.8i4l5VdODnv9GvZaqaLx6La5OfZ98eySHZYYLPO', '1234567890', ''),
(3, 'เมธาวาลัย พรมน้อย', '66010914081@msu.ac.th', '$2y$10$lfJ/oxA/G2M6tkoN8XCqw.sZ8pdMq8VAnA6UX1fuwH3lKqYBw2QL6', '0610314179', ''),
(4, 'นัธวุฒิ ', 'MMM@gmail.com', '$2y$10$iwbKG2Pybs5L8zhPXgkoM.Ep9ItVoxlWQkctr5gVI0k4YbmFfxga2', '096325874', '');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `or_id` varchar(20) NOT NULL,
  `u_id` int(20) NOT NULL,
  `or_total_amount` decimal(10,2) NOT NULL,
  `or_status` enum('รอชำระเงิน','ชำระเงินแล้ว','จัดส่งแล้ว','ยกเลิก') NOT NULL DEFAULT 'รอชำระเงิน',
  `or_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`or_id`, `u_id`, `or_total_amount`, `or_status`, `or_date`) VALUES
('', 1, 2.00, 'ชำระเงินแล้ว', '2026-02-04 02:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `detail_id` int(11) NOT NULL,
  `or_id` varchar(20) NOT NULL,
  `P_id` varchar(10) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `P_id` varchar(255) NOT NULL,
  `P_name` varchar(255) NOT NULL,
  `p_description` varchar(255) DEFAULT NULL,
  `P_amonut` int(11) NOT NULL,
  `P_price` int(11) NOT NULL,
  `P_img` varchar(50) DEFAULT NULL,
  `C_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`P_id`, `P_name`, `p_description`, `P_amonut`, `P_price`, `P_img`, `C_id`) VALUES
('DS001', 'ซี สมายล์ โมจิชีส', 'โมจิชีส 500 กรัม ', 100, 125, 'jpg', 'dc06'),
('DS002', 'เอโร่ โกลด์ ขนมปังบันเบอร์เกอร์', 'ขนมปังบันเบอร์เกอร์ 60 กรัม x 6', 100, 33, 'jpg', 'dc06'),
('DS003', 'เอโร่ โกลด์ มินิครัวซองต์เนยสด', 'มินิครัวซองต์เนยสด 500 กรัม', 100, 225, 'jpg', 'dc06'),
('DS004', 'เค้กกล้วยหอมครีมชีสนูเทลล่า', 'เค้กกล้วยหอมครีมชีสนูเทลล่า 360 กรัม', 100, 139, 'jpg', 'dc06'),
('DS005', 'แดรี่โด ไดฟูกุช็อกโกแลตดูไบ', 'ไดฟูกุช็อกโกแลตดูไบ รสพิสตาชิโอ 210 กรัม', 100, 140, 'jpg', 'dc06'),
('DS006', 'เอโร่ โกลด์ ฮันนี่โทสลูกเกด', 'ฮันนี่โทสลูกเกด 520 กรัม', 100, 90, 'jpg', 'dc06'),
('DS007', 'เอโร่ โกลด์ สตอเบอรี่ชีสเค้ก', 'สตอเบอรี่ชีสเค้ก 450 กรัม ', 100, 275, 'jpg', 'dc06'),
('DS008', 'เอโร่ ขนมปังเนยสด', 'ขนมปังเนยสด 35 กรัม x 6', 100, 48, 'jpg', 'dc06'),
('DS009', 'มูฟเวนพิก ไอศกรีม สวิสช็อกโกแลต', 'ไอศกรีม สวิสช็อกโกแลต 500 มิลลิกรัม', 100, 319, 'jpg', 'dc06'),
('DS010', 'มูฟเวนพิก ไอศกรีม มิ้นท์ช็อกโกแลต', 'ไอศกรีม มิ้นท์ช็อกโกแลต 500 มิลลิกรัม', 100, 319, 'jpg', 'dc06'),
('DS011', 'มูฟเวนพิก ไอศกรีม สตรอเบอร์รี่', 'ไอศกรีม สตรอเบอร์รี่ 500 มิลลิกรัม', 100, 319, 'jpg', 'dc06'),
('DS012', 'โดรายากิแช่แข็ง ไส้ถั่วแดง', 'โดรายากิแช่แข็ง ไส้ถั่วแดง 50 กรัม x 3 ชิ้น', 100, 55, 'jpg', 'dc06'),
('DS013', 'เอเล่แอนเวียร์ พุดดิ้งวานิลลา', 'พุดดิ้งวานิลลา 100 กรัม ', 100, 51, 'jpg', 'dc06'),
('DS014', 'เอโร่ โกลด์ วาฟเฟิลแช่แข็ง', 'วาฟเฟิลแช่แข็ง 225 กรัม', 100, 85, 'jpg', 'dc06'),
('DS015', 'เอโร่ เค้กช็อคโกแลตลาวา', 'เค้กช็อคโกแลตลาวา 468 กรัม', 100, 185, 'jpg', 'dc06'),
('FF001', 'เซพแพค เฟรนซ์ฟรายส์แช่เเข็ง', 'เฟรนซ์ฟรายส์แช่เเข็ง ขนาด 12 มม. เส้นหยัก 2 กิโลกรัม', 109, 100, 'jpg', 'fc03'),
('FF002', 'แหลมทอง ไส้กรอกไก่', 'ใส้กรอกไก่ 1 กิโลกรัม', 100, 85, 'jpg', 'fc03'),
('FF003', 'พี.เอฟ.พี ฟิชริง', 'ฟิชริง 1 กิโลกรัม', 100, 175, 'jpg', 'fc03'),
('FF004', 'เอเอฟเอ็ม ชีสบอล', 'ชีสบอล แช่แข็ง 500 กรัม', 100, 99, 'jpg', 'fc03'),
('FF005', 'เอเอฟเอ็ม ไส้กรอกไก่รมควัน', 'ไส้กรอกรมควันหนังกรอบสอดใส้ชีส 500 กรัม ', 100, 85, 'jpg', 'fc03'),
('FF006', 'สุรพลฟู้ดส์ ทอดมันกุ้ง', 'ทอดมันกุ้ง แช่แข็ง 30 ชิ้น 900 กรัม', 100, 399, 'jpg', 'fc03'),
('FF007', 'ห้าดาว ไก่จ๊อ', 'ไก่จ๊อ 1 กิโลกรัม', 100, 169, 'jpg', 'fc03'),
('FF008', 'บุชเชอร์ มาเบิ้ลเบคอน', 'มาเบิ้ลเบคอน 500 กรัม ', 100, 149, 'jpg', 'fc03'),
('FF009', 'บุชเชอร์ พอร์คแฮมรมควัน', 'พอร์คแฮมรมควัน 1 กิโลกรัม', 100, 299, 'jpg', 'fc03'),
('FF010', 'เอโร่ อิบิโรลไส้กุ้งแช่แข็ง', 'อิบิโรลไส้กุ้งแช่แข็ง 750 กรัม', 100, 335, 'jpg', 'fc03'),
('FF011', 'เอโร่ ไส้กรอกแฟรงค์ไก่หนังกรอบ', 'ไส้กรอกแฟรงค์ไก่หนังกรอบ 1 กิโลกรัม', 100, 145, 'jpg', 'fc03'),
('FF012', 'เอโร่ นักเก็ตไก่', 'นักเก็ตไก่แช่แข็ง 1 กิโลกรัม', 100, 149, 'jpg', 'fc03'),
('FF013', 'เอโร่ มันเทศชุบแป้งทอด', 'มันเทศชุบแป้งทอดแช่แข็ง 1 กิโลกรัม', 100, 109, 'jpg', 'fc03'),
('FF014', 'เอโร่ ไก่กรอบ', 'ไก่กรอบ 800 กรัม ', 100, 185, 'jpg', 'fc03'),
('FF015', 'เอโร่ เฟรนซ์ฟรายส์แช่แข็ง ', 'เฟรนซ์ฟรายส์ เส้นเล็กตรง ขนาด 7 มิลลิเมตร 1 กิโลกรัม', 100, 89, 'jpg', 'fc03'),
('IGN001', 'เดลีซัน แผ่นแป้งพิซซ่า ', 'แผ่นแป้งพิซซ่า 9 นิ้ว 320 กรัม', 100, 115, 'jpg', 'ic05'),
('IGN002', 'เอโร่ ไข่ไก่ เบอร์ 2', 'ไข่ไก่เบอร์2 มีฝา 30 ฟอง', 100, 118, 'jpg', 'ic05'),
('IGN003', 'ตรานกอินทรี แป้งขนมปัง', 'แป้งขนมปังคุณภาพสูง 1 กิโลกรัม', 100, 39, 'jpg', 'ic05'),
('IGN004', 'คาร์เนชัน เอ็กซ์ตร้าครีมเทียม', 'ครีมเทียมพร่องไขมัน 1 โล', 100, 62, 'jpg', 'ic05'),
('IGN005', 'ลิตเติลเชฟ แผ่นแป้งปอเปี๊ยะ', 'แผ่นแป้งปอเปี๊ยะแช่แข็ง 8.5 นิ้ว 40 แผ่น 660 กรัม', 100, 88, 'jpg', 'ic05'),
('IGN006', 'ครัววังทิพย์ แ้ปงทอดกรอบ', 'แป้งทอดกรอบ 1 กิโลกรัม', 100, 51, 'jpg', 'ic05'),
('IGN007', 'อลาวรี่ เชดด้าชีส', 'เชดด้าชีส 12 สไลซ์ 250 กรัม', 100, 151, 'jpg', 'ic05'),
('IGN008', 'ลูกศรฟ้า แป้งอเนกประสงค์', 'แป้งอเนกประสงค์ 1 กิโลกรัม', 100, 34, 'jpg', 'ic05'),
('IGN009', 'เอโร่ สตรอว์เบอร์รี่', 'สตรอว์เบอร์รี่แช่แข็ง 1 กิโลกรัม', 100, 75, 'jpg', 'ic05'),
('IGN010', 'เอโร่ มะม่วงน้ำดอกไม้', 'มะม่วงน้ำดอกไม้หั่นเต๋าแช่แข็ง 1 กิโลกรัม', 100, 199, 'jpg', 'ic05'),
('IGN011', 'เอโร่ บลูเบอร์รี่', 'บลูเบอร์รี่แช่แข็ง 1 กิโลกรัม ', 100, 159, 'jpg', 'ic05'),
('IGN012', 'วัตตีส์ ถั่นลันเตา', 'ถั่นลันเตาแช่แข็ง 1 กิโลกรัม', 100, 89, 'jpg', 'ic05'),
('IGN013', 'เอโร่ ผักรวม', 'ผักรวมแช่แข็ง 1 กิโลกรัม ', 100, 49, 'jpg', 'ic05'),
('IGN014', 'เอโร่ บล็อคโคลี่', 'บล็อคโคลี่แช่แข็ง 1 กิโลกรัม', 100, 75, 'jpg', 'ic05'),
('IGN015', 'เอโร่ ข้าวโพดหวาน', 'ข้าวโพดหวานแช่แข็ง 1 กิโลกรัม ', 100, 59, 'jpg', 'ic05'),
('M001', 'สันนอกหมู', 'สันนอกหมู 1 กิโลกรัม', 100, 127, 'jpg', 'mc01'),
('M002', 'สะโพกหมู', 'สะโพกหมู 1 กิโลกรัม ', 100, 112, 'jpg ', 'mc01'),
('M003', 'สามชั้นหมู ', 'สามชั้นหมู 1 กิโลกรัม', 100, 176, 'jpg', 'mc01'),
('M004', 'เนื้อหมูบด', 'เนื้อหมูบด 1 กิโลกรัม', 100, 123, 'jpg', 'mc01'),
('M005', 'อกไก่', 'อกไก่เนื้อล้วนติดหนัง 1 กิโลกรัม', 100, 70, 'jpg', 'mc01'),
('M006', 'สะโพกไก่', 'สะโพกไก่ติดกระดูก 1 กิโลกรัม', 100, 85, 'jpg', 'mc01'),
('M007', 'ปีกบนไก่', 'ปีกบนไก่ 1 กิโลกรัม', 100, 83, 'jpg', 'mc01'),
('M008', 'ปีกกลางไก่', 'ปีกกลางไก่ 1 กิโลกรัม', 100, 155, 'jpg', 'mc01'),
('M009', 'สันคอวัว', 'สันคอวัวออสเตรเลีย 1 กิโลกรัม', 100, 376, 'jpg', 'mc01'),
('M010', 'สันคอไทย', 'สันคอไทยวากิวสไลซ์แช่แข็ง 1 กิโลกรัม ', 100, 350, 'jpg', 'mc01'),
('M011', 'สันคอวัวสไลซ์', 'สันคอวัวสไลซ์แช่แข็ง 500 กรัม', 100, 293, 'jpg', 'mc01'),
('M012', 'เนื้อพิคานย่าวัว', 'เนื้อพิคานย่าวัวออสเตรเลีย 1 กิโลกรัม', 100, 549, 'jpg', 'mc01'),
('M013', 'ซี่โครงแกะออสเตรเลีย', 'ซี่โครงแกะออสเตรเลียแช่แข็ง 1 โลกรัม', 100, 720, 'jpg', 'mc01'),
('M014', 'ขาแกะนิวซีแลนด์', 'ขาแกะนิวซีแลนด์ติดกระดูกแช่แข็ง 3 กิโลกรัม', 100, 400, 'jpg', 'mc01'),
('M015', 'น่องขาหลังแกะนิวซีแลนด์', 'น่องขาหลังแกะนิวซีแลนด์แช่แข็ง 1 กิโลกรัม', 100, 550, 'jpg', 'mc01'),
('RF001', 'เทสตี้มีล ข้าวพะแนงหมู', 'ข้าวพะแนงหมู 240 กรัม x 3', 100, 109, 'jpg', 'rc04'),
('RF002', 'เทสตี้มีล ข้าวกะเพราหมู', 'ข้าวกะเพราหมู 210 กรัม  x 3', 100, 90, 'jpg', 'rc04'),
('RF003', 'เทสตี้มีล ข้าวหมูกระเทียม', 'ข้าวหมูกระเทียม 200 กรัม x 3', 100, 90, 'jpg', 'rc04'),
('RF004', 'เดลี่ไทย ข้าวผัดปูแช่แข็ง', 'ข้าวผัดปูแช่แข็ง 200 กรัม x 3', 100, 115, 'jpg', 'rc04'),
('RF005', 'วีจีฟอร์เลิฟ ข้าวกะเพราหมูกรอบวีแกน', 'ข้าวกะเพราหมูกรอบวีแกน 300 กรัม', 100, 69, 'jpg', 'rc04'),
('RF006', 'เอสแอนด์พี สปาเก็ตตี้ซอสไก่', 'สปาเก็ตตี้ซอสไก่ 230 กรัม 4 ถุง', 100, 179, 'jpg', 'rc04'),
('RF007', 'เอสแอนด์พี สปาเก็ตตี้คาโบนาร่าแช่แข็ง', 'สปาเก็ตตี้คาโบนาร่าแช่แข็ง 230 กรัม 4 ถุง', 100, 189, 'jpg', 'rc04'),
('RF008', 'เจด ดราก้อน ขนมจีบไส้กุ้งแช่แข็ง', 'ขนมจีบไส้กุ้งแช่แข็ง 480 กรัม 30 ชิ้น', 100, 189, 'jpg', 'rc04'),
('RF009', 'เจด ดราก้อน ฮะเก๋าไส้กุ้งแช่แข็ง', 'ฮะเก๋าไส้กุ้งแช่แข็ง 18 กรัม 24 ชิ้น', 100, 175, 'jpg', 'rc04'),
('RF010', 'เจด ดราก้อน กุ้งพันสาหร่ายแช่แข็ง', 'กุ้งพันสาหร่ายแช่แข็ง 450 กรัม 30 ชิ้น', 100, 189, 'jpg', 'rc04'),
('RF011', 'เซพแพ็ค ซาลาเปาหมูสับ (หมูผสมไก่)', 'ซาลาเปาหมูสับ (หมูผสมไก่) 20 ชิ้น แพ็ค 740 กรัม', 100, 135, 'jpg', 'rc04'),
('RF012', 'เซพแพ็ค ซาลาเปาไส้ครีม', 'ซาลาเปาไส้ครีม 20 ชิ้น 740 กรัม ', 100, 129, 'jpg', 'rc04'),
('RF013', 'ซีพี ข้าวหน้าเป็ดย่างโฟร์ซีซั่นส์', 'ข้าวหน้าเป็ดย่างโฟร์ซีซั่นส์ แช่แข็ง 320 กรัม', 100, 80, 'jpg', 'rc04'),
('RF014', 'ซีพี บะหมี่เกี๊ยวกุ้งจักรพรรดิ', 'บะหมี่เกี๊ยวกุ้งจักรพรรดิ 226 กรัม x 3', 100, 175, 'jpg', 'rc04'),
('RF015', 'ซีพี เกี๊ยวลุยสวน (หมูผสมไก่) พร้อมน้ำจิ้ม', 'เกี๊ยวลุยสวน (หมูผสมไก่) พร้อมน้ำจิ้ม แช่แข็ง 660 กรัม', 100, 169, 'jpg', 'rc04'),
('SF001', 'กุ้งขาวจัมโบ้', 'กุ้งขาวจัมโบ้ 1 กิโลกรัม', 100, 369, 'jpg', 'sc02'),
('SF002', 'กุ้งขาวไว้หางแช่แข็ง', 'กุ้งขาวไว้หางแช่แข็ง 1 กิโลกรัม', 100, 340, 'jpg', 'sc02'),
('SF003', 'หมึกกล้วย', 'หมึกกล้วย ไซส์ M 1กิโลกรัม', 100, 222, 'jpg', 'sc02'),
('SF004', 'หมึกกระดองเจาะกลาง', 'หมึกกระดองเจาะกลาง 1 กิโลกรัม', 100, 244, 'jpg', 'sc02'),
('SF005', 'หมึกกรอบเล็ก', 'หมึกกรอบเล็ก 1 กรัม', 100, 145, 'jpg', 'sc02'),
('SF006', 'หนวดหมึกยักษ์', 'หนวดหมึกยักษ์ 1 กิโลกรัม', 100, 151, 'jpg', 'sc02'),
('SF007', 'หมึกบั้งแช่แข็ง', 'หมึกบั้งแช่แข็ง 1 กิโลกรัม', 100, 159, 'jpg', 'sc02'),
('SF008', 'หมึกหั่นวงแช่แข็ง', 'หมึกหั่นวงแช่แข็ง 1 กิโลกรัม', 100, 114, 'jpg', 'sc02'),
('SF009', 'ปูนิ่มแช่แข็ง', 'ปูนิ่มแช่แข็ง 1 กิโลกรัม', 100, 419, 'jpg', 'sc02'),
('SF010', 'เนื้อปูก้อนแช่แข็ง', 'เนื้อปูก้อนแช่แข็ง 500 กรัม', 100, 345, 'jpg', 'sc02'),
('SF011', 'เนื้อหอยนางรม', 'เนื้อหอยนางรม 100 กรัม', 100, 33, 'jpg', 'sc02'),
('SF012', 'เนื้อหอยแมลงภู่แช่แข็ง', 'เนื้อหอยแมลงภู่แช่แข็ง 1 กิโลกรัม', 100, 93, 'jpg', 'sc02'),
('SF013', 'ปลาแซลมอนนอร์เวย์', 'ปลาแซลมอนนอร์เวย์ 6 กิโลกรัม', 100, 348, 'jpg', 'sc02'),
('SF014', 'ปลาซาบะนอรเวย์แช่แข็ง', 'ปลาซาบะนอรเวย์แช่แข็ง ไซส์ L 1 กิโลกรัม', 100, 195, 'jpg', 'sc02'),
('SF015', 'เนื้อปลาแพนกาเซียสส่วนลำตัวแช่แข็ง', 'เนื้อปลาแพนกาเซียสส่วนลำตัวแช่แข็ง 1 กิโลกรัม', 100, 89, 'jpg', 'sc02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`a_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`u_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`or_id`),
  ADD KEY `fk_orders_user` (`u_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`detail_id`),
  ADD KEY `or_id` (`or_id`),
  ADD KEY `P_id` (`P_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`P_id`),
  ADD KEY `C_id` (`C_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `a_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `u_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `detail_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`u_id`) REFERENCES `customers` (`u_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `Order_Details_ibfk_1` FOREIGN KEY (`or_id`) REFERENCES `orders` (`or_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `Products_ibfk_1` FOREIGN KEY (`C_id`) REFERENCES `categories` (`C_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
