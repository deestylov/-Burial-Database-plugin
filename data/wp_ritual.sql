-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Сен 19 2022 г., 15:20
-- Версия сервера: 8.0.27-0ubuntu0.20.04.1
-- Версия PHP: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `wordpress`
--

-- --------------------------------------------------------

--
-- Структура таблицы `{{prefix}}ritual`
--

CREATE TABLE `{{prefix}}ritual` (
  `id` int UNSIGNED NOT NULL,
  `registration_number` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `patronymic` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `date_birth` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `date_death` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `date_dburial` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `cemetery_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `site` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `row` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `{{prefix}}ritual`
--
ALTER TABLE `{{prefix}}ritual`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_surname` (`surname`),
  ADD KEY `index_name` (`name`),
  ADD KEY `index_patronymic` (`patronymic`),
  ADD KEY `index_cemetery_name` (`cemetery_name`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `{{prefix}}ritual`
--
ALTER TABLE `{{prefix}}ritual`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
