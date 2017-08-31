-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Авг 31 2017 г., 21:54
-- Версия сервера: 5.7.19-0ubuntu0.16.04.1
-- Версия PHP: 7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vk-contest`
--
CREATE DATABASE IF NOT EXISTS `vk-contest` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `vk-contest`;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `ID` int(11) UNSIGNED NOT NULL,
  `Title` varchar(255) NOT NULL DEFAULT '',
  `Description` text NOT NULL,
  `Reward` int(11) NOT NULL,
  `CreatorID` int(11) NOT NULL,
  `StatusID` int(11) NOT NULL,
  `DateTime` datetime NOT NULL COMMENT 'Дата создания заказа'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`ID`, `Title`, `Description`, `Reward`, `CreatorID`, `StatusID`, `DateTime`) VALUES
  (1, 'Погулять с собакой', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 500, 1, 1, '2017-08-19 15:01:00'),
  (2, 'Погулять с попугайчиком', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod temp\n\nА это перенос', 500, 1, 1, '2017-08-19 15:02:00'),
  (3, 'Погулять с кошкой', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.\n', 500, 1, 1, '2017-08-19 15:03:00');

-- --------------------------------------------------------

--
-- Структура таблицы `order_status`
--

DROP TABLE IF EXISTS `order_status`;
CREATE TABLE `order_status` (
  `ID` int(11) UNSIGNED NOT NULL,
  `Title` varchar(50) NOT NULL DEFAULT '',
  `Hidden` tinyint(1) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `order_status`
--

INSERT INTO `order_status` (`ID`, `Title`, `Hidden`) VALUES
  (1, 'Новый заказ', 0),
  (2, 'В работе', 0),
  (3, 'Выполнен', 0),
  (4, 'Удалён', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `ID` int(11) UNSIGNED NOT NULL,
  `Balance` int(11) UNSIGNED NOT NULL,
  `Name` varchar(100) NOT NULL DEFAULT '',
  `Photo` varchar(500) NOT NULL DEFAULT '',
  `VkID` int(11) DEFAULT NULL,
  `Hash` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`ID`, `Balance`, `Name`, `Photo`, `VkID`, `Hash`) VALUES
  (1, 10001, 'Максим Лепеха', 'https://pp.userapi.com/c637426/v637426673/4a96e/qN0mVYONH9M.jpg', 6650673, 'test6650673'),
  (2, 4995, 'Павел Дуров', 'https://vk.com/images/camera_100.png', 1, 'test1');

-- --------------------------------------------------------

--
-- Структура таблицы `users_by_orders`
--

DROP TABLE IF EXISTS `users_by_orders`;
CREATE TABLE `users_by_orders` (
  `OrderID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- Индексы таблицы `users_by_orders`
--
ALTER TABLE `users_by_orders`
  ADD UNIQUE KEY `OrderID` (`OrderID`,`UserID`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT для таблицы `order_status`
--
ALTER TABLE `order_status`
  MODIFY `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
