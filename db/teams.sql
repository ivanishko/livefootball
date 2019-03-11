-- phpMyAdmin SQL Dump
-- version 4.8.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Мар 10 2019 г., 18:58
-- Версия сервера: 5.6.40
-- Версия PHP: 5.6.35

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `fcarmavi_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `teams`
--

CREATE TABLE `teams` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT '',
  `logo` varchar(250) NOT NULL DEFAULT '',
  `manager` varchar(60) NOT NULL DEFAULT '',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `teams`
--

INSERT INTO `teams` (`id`, `name`, `logo`, `manager`, `is_deleted`) VALUES
(1, 'Team 1', 'img/logo/demo-t1.png', 'Manager 1', 1),
(2, 'Team 2', 'img/logo/demo-t2.png', 'Manager 2', 1),
(3, 'Армавир', 'img/logo/FC-Armavir_Logo_color.png', 'Арсен Папикян', 0),
(4, 'СКА-Хабаровск', 'img/logo/bigLogo.png', 'Сергей Передня', 0),
(5, 'Мордовия', 'img/logo/Mordoviya.png', 'Мустафин Марат', 0),
(6, 'Факел', 'img/logo/fakel_2.png', 'Волгин С.В.', 0),
(7, 'Химки', 'img/logo/khimki.png', 'Шалимов Игорь', 0),
(8, 'Ротор', 'img/logo/rotor.png', 'Роберт Евдокимов', 0),
(9, 'Зенит-2', 'img/logo/aa35864408420135851e883e14d7928e.png', 'Владислав Родимов', 0),
(10, 'Тюмень', 'img/logo/Тюмень.png', 'Горан Алексич', 0),
(11, 'Тамбов', 'img/logo/tambov.png', 'Шипшев Тимур', 0),
(12, 'Чертаново', 'img/logo/chertanovo.png', 'Игорь Осинькин', 0),
(13, 'Луч', 'img/logo/luch.png', 'Рустем Хузин', 0),
(14, 'Чайка', 'img/logo/chayka.png', 'Виталий Семакин', 0),
(15, 'Шинник', 'img/logo/shinnik.png', 'Александр Побегалов', 0),
(16, 'Спартак-2', 'img/logo/spartak.png', 'Виктор Булатов', 0),
(17, 'Томь', 'img/logo/tom.png', 'Василий Баскаков', 0),
(18, 'Сочи', 'img/logo/сочи.png', 'Александр Точилин', 0),
(19, 'Авангард', 'img/logo/avangard.png', 'Игорь Беляев', 0),
(20, 'Балтика', 'img/logo/Baltika.png', 'Игорь Ледяхов', 0),
(21, 'Нижний Новгород', 'img/logo/hizhniy.png', 'Дмитрий Черышев', 0),
(22, 'Сибирь', 'img/logo/sibir.png', 'Евгений Обгольц', 0),
(23, 'Краснодар-2', 'img/logo/krasnodar.png', 'Александр Нагорный', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
