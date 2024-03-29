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
-- Структура таблицы `team_players`
--

CREATE TABLE `team_players` (
  `id` int(11) UNSIGNED NOT NULL,
  `team_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  `squad_number` varchar(30) NOT NULL DEFAULT '',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `display_order` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `default_status` varchar(25) NOT NULL DEFAULT 'not_available'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `team_players`
--

INSERT INTO `team_players` (`id`, `team_id`, `name`, `squad_number`, `is_deleted`, `display_order`, `default_status`) VALUES
(1, 1, 'Player 1-1', '1', 0, 1, 'first_eleven'),
(2, 1, 'Player 1-20', '20', 0, 2, 'first_eleven'),
(3, 1, 'Player 1-5', '5', 0, 3, 'first_eleven'),
(4, 1, 'Player 1-21', '21', 0, 4, 'first_eleven'),
(5, 1, 'Player 1-14', '14', 0, 5, 'first_eleven'),
(6, 1, 'Player 1-27', '27', 0, 6, 'first_eleven'),
(7, 1, 'Player 1-6', '6', 0, 7, 'first_eleven'),
(8, 1, 'Player 1-8', '8', 0, 8, 'first_eleven'),
(9, 1, 'Player 1-7', '7', 0, 9, 'first_eleven'),
(10, 1, 'Player 1-11', '11', 0, 10, 'first_eleven'),
(11, 1, 'Player 1-18', '18', 0, 11, 'first_eleven'),
(12, 1, 'Player 1-22', '22', 0, 1, 'substitute'),
(13, 1, 'Player 1-3', '3', 0, 2, 'substitute'),
(14, 1, 'Player 1-19', '19', 0, 3, 'substitute'),
(15, 1, 'Player 1-4', '4', 0, 4, 'substitute'),
(16, 1, 'Player 1-15', '15', 0, 5, 'substitute'),
(17, 1, 'Player 1-17', '17', 0, 6, 'substitute'),
(18, 1, 'Player 1-13', '13', 0, 7, 'substitute'),
(19, 2, 'Player 2-30', '30', 0, 1, 'first_eleven'),
(20, 2, 'Player 2-17', '17', 0, 2, 'first_eleven'),
(21, 2, 'Player 2-2', '2', 0, 3, 'first_eleven'),
(22, 2, 'Player 2-13', '13', 0, 4, 'first_eleven'),
(23, 2, 'Player 2-23', '23', 0, 5, 'first_eleven'),
(24, 2, 'Player 2-14', '14', 0, 6, 'first_eleven'),
(25, 2, 'Player 2-8', '8', 0, 7, 'first_eleven'),
(26, 2, 'Player 2-24', '24', 0, 8, 'first_eleven'),
(27, 2, 'Player 2-22', '22', 0, 9, 'first_eleven'),
(28, 2, 'Player 2-10', '10', 0, 10, 'first_eleven'),
(29, 2, 'Player 2-29', '29', 0, 11, 'first_eleven'),
(30, 2, 'Player 2-1', '1', 0, 1, 'substitute'),
(31, 2, 'Player 2-4', '4', 0, 2, 'substitute'),
(32, 2, 'Player 2-27', '27', 0, 3, 'substitute'),
(34, 2, 'Player 2-7', '7', 0, 4, 'substitute'),
(35, 2, 'Player 2-21', '21', 0, 5, 'substitute'),
(36, 2, 'Player 2-25', '25', 0, 6, 'substitute'),
(37, 2, 'Player 2-5', '5', 0, 7, 'substitute'),
(38, 2, 'Player 2-', '', 1, 0, 'not_available'),
(39, 2, 'Player 2-', '', 1, 0, 'not_available'),
(40, 2, 'Player 2-', '', 1, 0, 'not_available'),
(41, 3, 'Матюша Максим', '13', 0, 1, 'not_available'),
(42, 3, 'Мирошниченко Сергей', '24', 1, 4, 'not_available'),
(43, 3, 'Гаджибеков Альберт', '3', 0, 5, 'not_available'),
(44, 4, 'Артём Леонов', '1', 1, 1, 'not_available'),
(45, 4, 'Иван Хомуха', '5', 1, 2, 'not_available'),
(46, 5, 'Шебанов Денис', '1', 1, 0, 'not_available'),
(47, 5, 'Сухарев Сергей', '18', 1, 1, 'not_available'),
(48, 5, 'Ятченко Евгений', '33', 1, 2, 'not_available'),
(49, 5, 'Багаев Алан', '4', 0, 4, 'not_available'),
(50, 5, 'Климов Игорь', '24', 0, 5, 'not_available'),
(51, 5, 'Мухаметшин Рустем', '9', 0, 6, 'not_available'),
(52, 5, 'Киреев Игорь', '17', 0, 7, 'not_available'),
(53, 5, 'Поярков Денис', '25', 0, 8, 'not_available'),
(54, 5, 'Петров Илья', '95', 0, 9, 'not_available'),
(55, 5, 'Обухов Владимир', '20', 0, 10, 'not_available'),
(56, 5, 'Мухаметшин Руслан', '23', 0, 11, 'not_available'),
(57, 5, 'Кобзев Александр', '16', 0, 12, 'substitute'),
(58, 5, 'Лебедев Юрий', '87', 0, 13, 'substitute'),
(59, 5, 'Деревягин Павел', '5', 0, 14, 'substitute'),
(60, 5, 'Орлов Антон', '97', 0, 15, 'substitute'),
(61, 5, 'Дворецков Филипп', '21', 0, 16, 'substitute'),
(62, 5, 'Адаев Владислав', '96', 0, 17, 'substitute'),
(63, 5, 'Соболев Денис', '13', 0, 18, 'substitute'),
(64, 5, 'Маркин Михаил', '10', 0, 19, 'substitute'),
(65, 5, 'Ермаков Александр', '11', 0, 20, 'substitute'),
(66, 3, 'Руденок Константин', '35', 0, 3, 'not_available'),
(67, 6, 'Суворов Олег', '1', 0, 0, 'not_available'),
(68, 6, 'Терновский Дмитрий', '16', 0, 1, 'not_available'),
(69, 6, 'Бабенков Олег', '4', 0, 0, 'not_available'),
(70, 6, 'Кагермазов Сослан', '6', 0, 1, 'not_available'),
(71, 6, 'Бутурлакин Дмитрий', '20', 0, 1, 'not_available'),
(72, 6, 'Свиридов Даниил', '22', 0, 1, 'not_available'),
(73, 6, 'Владислав Турукин', '40', 0, 1, 'not_available'),
(74, 6, 'Божин Сергей', '47', 0, 1, 'not_available'),
(75, 6, 'Осипенко Максим', '55', 0, 1, 'not_available'),
(76, 6, 'Афанасьев Михаил', '77', 0, 2, 'not_available'),
(77, 6, 'Дутов Александр', '66', 0, 2, 'not_available'),
(78, 6, 'Колесников Кирилл', '36', 0, 2, 'not_available'),
(79, 6, 'Красов Николай', '25', 0, 2, 'not_available'),
(80, 6, 'Кузьмин Максим', '96', 0, 2, 'not_available'),
(81, 6, 'Лебеденко Игорь', '10', 0, 2, 'not_available'),
(82, 6, 'Ломакин Александр', '90', 0, 2, 'not_available'),
(83, 6, 'Макарчук Артем', '19', 0, 2, 'not_available'),
(84, 6, 'Маликов Дмитрий', '37', 0, 2, 'not_available'),
(85, 6, 'Мануковский Александр', '33', 0, 2, 'not_available'),
(86, 6, 'Молодцов Артем', '27', 0, 2, 'not_available'),
(87, 6, 'Неплюев Дмитрий', '26', 0, 2, 'not_available'),
(88, 6, 'Садов Дмитрий', '45', 0, 2, 'not_available'),
(89, 6, 'Саенко Давид', '21', 0, 2, 'not_available'),
(90, 6, 'Шогенов Марат', '8', 0, 0, 'not_available'),
(91, 6, 'Арустамян Артур', '97', 0, 3, 'not_available'),
(92, 6, 'Бирюков Михаил', '17', 0, 3, 'not_available'),
(93, 6, 'Денисов Ярослав', '18', 0, 3, 'not_available'),
(94, 6, 'Дорожкин Денис', '13', 0, 3, 'not_available'),
(95, 6, 'Орлов Сергей', '7', 0, 3, 'not_available'),
(96, 6, 'Перцев Иван', '31', 0, 3, 'not_available'),
(97, 3, 'Безлихотнов Никита', '99', 0, 20, 'not_available'),
(98, 3, 'Кутин Денис', '64', 0, 8, 'not_available'),
(99, 3, 'Концедалов Алексей', '45', 0, 10, 'not_available'),
(100, 3, 'Лусикян Эдуард', '11', 0, 11, 'not_available'),
(101, 3, 'Падерин Игорь', '10', 0, 19, 'not_available'),
(102, 3, 'Гурфов Азамат', '7', 0, 17, 'not_available'),
(103, 3, 'Клопков Денис', '4', 0, 12, 'not_available'),
(104, 3, 'Романенко Владимир', '87', 0, 18, 'not_available'),
(105, 3, 'Синявский Семен', '93', 0, 21, 'not_available'),
(106, 3, 'Герасимов Артем', '31', 0, 2, 'not_available'),
(107, 3, 'Соболь Артём', '6', 0, 7, 'not_available'),
(108, 3, 'Соловьев Алексей', '2', 0, 6, 'not_available'),
(109, 3, 'Поляков Олег', '90', 0, 13, 'not_available'),
(110, 3, 'Каюмов Дмитрий', '8', 0, 16, 'not_available'),
(111, 3, 'Веркашанский Сергей', '9', 0, 22, 'not_available'),
(112, 3, 'Мичуренков Дмитрий', '19', 0, 23, 'not_available'),
(113, 7, 'Белоус Илья', '88', 0, 3, 'not_available'),
(114, 7, 'Рязанцев Александр', '99', 0, 2, 'not_available'),
(115, 7, 'Сергей Иванов', '33', 0, 0, 'not_available'),
(116, 7, 'Иван Сергеев', '4', 0, 0, 'not_available'),
(117, 7, 'Исупов Вячеслав', '18', 0, 0, 'not_available'),
(118, 7, 'Данилкин Егор', '15', 0, 1, 'not_available'),
(119, 7, 'Филин Александр', '25', 0, 1, 'not_available'),
(120, 7, 'Смирнов Александр', '86', 0, 1, 'not_available'),
(121, 7, 'Димидко Александр', '2', 0, 2, 'not_available'),
(122, 7, 'Корян Аршак', '22', 0, 2, 'not_available'),
(123, 7, 'Мостовой Андрей', '9', 0, 2, 'not_available'),
(124, 7, 'Нефтуллин Равиль', '52', 0, 2, 'not_available'),
(125, 7, 'Алиев Камран', '17', 0, 3, 'not_available'),
(126, 7, 'Кавлинов Денис (в)', '77', 0, 0, 'not_available'),
(127, 7, 'Иванов Дмитрий', '5', 0, 1, 'not_available'),
(128, 7, 'Мильдзихов Давид', '32', 0, 1, 'not_available'),
(129, 7, 'Козлов Александр', '33', 0, 2, 'not_available'),
(130, 7, 'Петрусев Михаил', '7', 0, 2, 'not_available'),
(131, 7, 'Талалай Денис', '19', 0, 2, 'not_available'),
(132, 7, 'Моргунов Александр', '4', 0, 4, 'not_available'),
(133, 7, 'Яковлев Максим', '8', 0, 3, 'not_available'),
(134, 8, 'Лобамцев Мирослав', '41', 0, 0, 'not_available'),
(135, 8, 'Мязин Андрей', '87', 0, 1, 'not_available'),
(136, 10, 'Лаук Максим', '10', 0, 7, 'not_available'),
(137, 11, 'Кашчелан Младен', '18', 0, 8, 'first_eleven'),
(138, 11, 'Шевчук Сергей', '11', 1, 0, 'first_eleven'),
(139, 11, 'Себай Сенин', '10', 0, 10, 'first_eleven'),
(140, 12, 'Молчанов Дмитрий', '20', 1, 0, 'first_eleven'),
(141, 3, 'Дышеков Аслан', '33', 0, 15, 'not_available'),
(142, 13, 'Павленко Андрей', '77', 0, 1, 'not_available'),
(143, 13, 'Суслов Кирилл', '15', 0, 1, 'not_available'),
(144, 13, 'Визнович Илья', '98', 0, 1, 'not_available'),
(145, 14, 'Подбельцев Александр', '9', 0, 1, 'not_available'),
(146, 14, 'Магомедов Бутта', '97', 0, 0, 'not_available'),
(147, 16, 'Пантелеев Владислав', '83', 0, 0, 'not_available'),
(148, 17, 'Клещенко Александр', '24', 0, 0, 'not_available'),
(149, 17, 'Тлупов Ислам', '17', 0, 0, 'not_available'),
(150, 17, 'Кухарчук Илья', '44', 0, 0, 'not_available'),
(151, 18, 'Горбунов Игорь', '11', 1, 0, 'not_available'),
(152, 18, 'Барсов Максим', '10', 0, 0, 'not_available'),
(153, 18, 'Соловьёв Иван', '9', 0, 0, 'not_available'),
(154, 19, 'Гоцук Кирилл', '24', 0, 1, 'not_available'),
(155, 8, 'Фильцов Александр', '1', 0, 0, 'not_available'),
(156, 8, 'Байрыев Азат', '28', 0, 1, 'not_available'),
(157, 8, 'Бугаев Роман', '43', 0, 1, 'not_available'),
(158, 8, 'Васильев Максим', '23', 0, 1, 'not_available'),
(159, 8, 'Карпов Максим', '50', 0, 1, 'not_available'),
(160, 8, 'Маляров Кирилл', '11', 0, 0, 'not_available'),
(161, 8, 'Пискунов Антон', '5', 0, 1, 'not_available'),
(162, 8, 'Щербаков Константин', '4', 0, 1, 'not_available'),
(163, 8, 'Эдиев Исмаил', '3', 0, 1, 'not_available'),
(164, 8, 'Воробьёв Роман', '7', 0, 1, 'not_available'),
(165, 8, 'Друзин Алексей', '30', 0, 2, 'not_available'),
(166, 8, 'Завезен Юрий', '31', 0, 2, 'not_available'),
(167, 8, 'Лобкарев Владимир', '77', 0, 2, 'not_available'),
(168, 8, 'Попов Артём', '25', 0, 2, 'not_available'),
(169, 8, 'Самсонов Артём', '88', 0, 2, 'not_available'),
(170, 8, 'Султанов Мухаммад', '22', 0, 2, 'not_available'),
(171, 8, 'Шарипов Альберт', '10', 0, 2, 'not_available'),
(172, 8, 'Якуба Денис', '8', 0, 2, 'not_available'),
(173, 8, 'Абдулавов Исламнур', '9', 0, 3, 'not_available'),
(174, 8, 'Коротаев Александр', '13', 0, 3, 'not_available'),
(175, 8, 'Муллин Камиль', '72', 0, 3, 'not_available'),
(176, 8, 'Николаев Олег', '18', 0, 3, 'not_available'),
(177, 8, 'Чабанов Евгений', '97', 0, 3, 'not_available'),
(178, 8, 'Янушковский Роман', '17', 0, 3, 'not_available'),
(179, 9, 'Прудников Николай', '32', 0, 0, 'not_available'),
(180, 20, 'Григорьев Максим', '9', 0, 2, 'not_available'),
(181, 20, 'Каленкович Виталий', '93', 1, 4, 'not_available'),
(182, 4, 'Генералов Егор', '31', 1, 0, 'not_available'),
(183, 4, 'Ненахов Максим', '77', 1, 1, 'not_available'),
(184, 4, 'Генералов Егор', '31', 0, 1, 'not_available'),
(185, 4, 'Хомуха Иван', '5', 0, 2, 'not_available'),
(186, 4, 'Ненахов Максим', '77', 0, 3, 'not_available'),
(187, 4, 'Алейник Олег', '88', 0, 4, 'not_available'),
(188, 4, 'Кабутов Дмитрий', '17', 0, 5, 'not_available'),
(189, 4, 'Самсонов Артём', '8', 0, 6, 'not_available'),
(190, 4, 'Квеквескири Ираклий', '33', 0, 7, 'not_available'),
(191, 4, 'Дедечко Денис', '69', 0, 8, 'not_available'),
(192, 4, 'Черевко Александр', '26', 0, 9, 'not_available'),
(193, 4, 'Казанков Максим', '7', 0, 10, 'not_available'),
(194, 4, 'Базелюк Константин', '11', 0, 11, 'not_available'),
(195, 4, 'Леонов Артём', '1', 0, 12, 'not_available'),
(196, 4, 'Труфанов Григорий', '97', 0, 13, 'not_available'),
(197, 4, 'Фадеев Николай', '44', 0, 14, 'not_available'),
(198, 4, 'Максименко Александр', '96', 0, 15, 'not_available'),
(199, 4, 'Бирюков Кирилл', '4', 0, 16, 'not_available'),
(200, 4, 'Карасев Павел', '6', 0, 17, 'not_available'),
(201, 4, 'Секульски Лукаш', '22', 0, 18, 'not_available'),
(202, 4, 'Радченко Александр', '14', 0, 19, 'not_available'),
(203, 5, 'Шебанов Денис', '1', 0, 1, 'not_available'),
(204, 5, 'Сухарев Сергей', '18', 0, 2, 'not_available'),
(205, 5, 'Ятченко Евгений', '33', 0, 3, 'not_available'),
(206, 3, 'Макоев Залим', '88', 0, 9, 'not_available'),
(207, 3, 'Суханов Эдуард', '14', 0, 14, 'not_available'),
(208, 3, 'Соблиров Астемир', '5', 0, 24, 'not_available'),
(209, 10, 'Бучнев Станислав', '22', 0, 1, 'not_available'),
(210, 10, 'Васиев Фарход', '26', 0, 2, 'not_available'),
(211, 10, 'Козлов Савелий', '66', 0, 3, 'not_available'),
(212, 10, 'Столяренко Александр', '20', 0, 4, 'not_available'),
(213, 10, 'Кононов Игорь', '14', 0, 5, 'not_available'),
(214, 10, 'Теленков Никита', '24', 0, 6, 'not_available'),
(215, 10, 'Чудин Иван', '8', 0, 8, 'not_available'),
(216, 10, 'Парняков Владимир', '9', 0, 9, 'not_available'),
(217, 10, 'Вотинов Максим', '45', 0, 10, 'not_available'),
(218, 10, 'Малоян Артур', '38', 0, 11, 'not_available'),
(219, 10, 'Обухов Игорь', '1', 0, 12, 'not_available'),
(220, 10, 'Шакуро Павел', '90', 0, 13, 'not_available'),
(221, 10, 'Хайманов Игорь', '15', 0, 14, 'not_available'),
(222, 10, 'Рябокобыленко Артур', '5', 0, 15, 'not_available'),
(223, 10, 'Глухов Егор', '17', 0, 16, 'not_available'),
(224, 10, 'Лешонок Владимир', '18', 0, 17, 'not_available'),
(225, 10, 'Баев Алексей', '21', 0, 18, 'not_available'),
(226, 11, 'Вавилин Денис', '21', 0, 1, 'not_available'),
(227, 11, 'Шляков Евгений', '5', 0, 2, 'not_available'),
(228, 11, 'Горбатюк Александр', '29', 0, 3, 'not_available'),
(229, 11, 'Рыбин Алексей', '2', 0, 4, 'not_available'),
(230, 11, 'Овсиенко Евгений', '4', 0, 5, 'not_available'),
(231, 11, 'Чернышов Олег', '7', 0, 6, 'not_available'),
(232, 11, 'Килин Антон', '8', 0, 7, 'not_available'),
(233, 11, 'Чуперка Валерий', '92', 0, 9, 'not_available'),
(234, 11, 'Аппаев Хызыр', '14', 0, 11, 'not_available'),
(235, 11, 'Смирнов Олег', '1', 0, 12, 'not_available'),
(236, 11, 'Гарбуз Константин', '19', 0, 13, 'not_available'),
(237, 11, 'Мищенко Михаил', '25', 0, 14, 'not_available'),
(238, 11, 'Шевчук Сергей', '11', 0, 15, 'not_available'),
(239, 11, 'Мурнин Андрей', '70', 0, 16, 'not_available'),
(240, 11, 'Часовских Андрей', '15', 0, 17, 'not_available'),
(241, 11, 'Мамтов Хасан', '9', 0, 18, 'not_available'),
(242, 12, 'Ломаев Иван', '1', 0, 1, 'not_available'),
(243, 12, 'Бартасевич Владимир', '13', 0, 2, 'not_available'),
(244, 12, 'Солдатенков Александр', '6', 0, 3, 'not_available'),
(245, 12, 'Паршиков Владислав', '2', 0, 4, 'not_available'),
(246, 12, 'Колесниченко Кирилл', '24', 0, 5, 'not_available'),
(247, 12, 'Ежов Роман', '11', 0, 6, 'not_available'),
(248, 12, 'Умяров Наиль', '26', 0, 7, 'not_available'),
(249, 12, 'Витюгов Максим', '8', 0, 8, 'not_available'),
(250, 12, 'Горшков Максим', '23', 0, 9, 'not_available'),
(251, 12, 'Майрович Максим', '18', 0, 10, 'not_available'),
(252, 12, 'Сарвели Владислав', '9', 0, 11, 'not_available'),
(253, 12, 'Радионов Александр', '55', 0, 12, 'not_available'),
(254, 12, 'Каракоз Станислав', '5', 0, 13, 'not_available'),
(255, 12, 'Михайлов Александр', '44', 0, 14, 'not_available'),
(256, 12, 'Редькович Дмитрий', '4', 0, 15, 'not_available'),
(257, 12, 'Заварухин Дмитрий', '31', 0, 16, 'not_available'),
(258, 12, 'Герчиков Леонид', '68', 0, 17, 'not_available'),
(259, 12, 'Радостев Семен', '77', 0, 18, 'not_available'),
(260, 12, 'Тюменцев Даниил', '7', 0, 19, 'not_available'),
(261, 12, 'Великородный Дмитрий', '21', 0, 20, 'not_available'),
(262, 12, 'Молчанов Дмитрий', '20', 1, 21, 'not_available'),
(263, 21, 'Палиенко Максим', '10', 0, 0, 'not_available'),
(264, 22, 'Цыган Николай', '22', 0, 0, 'not_available'),
(265, 22, 'Макаренко Александр', '8', 0, 0, 'not_available'),
(266, 22, 'Кушнирук Антон', '5', 0, 0, 'not_available'),
(267, 22, 'Аравин Алексей', '2', 0, 0, 'not_available'),
(268, 22, 'Смирнов Михаил', '25', 0, 0, 'not_available'),
(269, 22, 'Чебатару Евгений', '37', 0, 0, 'not_available'),
(270, 22, 'Галыш Виталий', '10', 0, 0, 'not_available'),
(271, 22, 'Васильев Максим', '97', 0, 0, 'not_available'),
(272, 22, 'Киреенко Павел', '23', 0, 0, 'not_available'),
(273, 22, 'Кисилев Максим', '16', 0, 0, 'not_available'),
(274, 22, 'Палюткин Антон', '20', 0, 0, 'not_available'),
(275, 22, 'Курбанов Шамиль', '33', 0, 0, 'not_available'),
(276, 22, 'Парфинович Роман', '17', 0, 0, 'not_available'),
(277, 22, 'Дудолев Артем', '18', 0, 0, 'not_available'),
(278, 22, 'Свежов Виктор', '91', 0, 0, 'not_available'),
(279, 22, 'Житнев Максим', '9', 0, 0, 'not_available'),
(280, 22, 'Азаров Владимир', '19', 0, 0, 'not_available'),
(281, 22, 'Гладышев Алексей', '55', 0, 0, 'not_available'),
(282, 21, 'Анисимов Артур', '1', 0, 0, 'not_available'),
(283, 21, 'Морозов Юрий', '55', 0, 0, 'not_available'),
(284, 21, 'Абазов Руслан', '57', 0, 0, 'not_available'),
(285, 21, 'Хрипков Андрей', '90', 0, 0, 'not_available'),
(286, 21, 'Федорив Виталий', '24', 0, 0, 'not_available'),
(287, 21, 'Игнатович Павел', '13', 0, 0, 'not_available'),
(288, 21, 'Абрамов Артём', '4', 0, 0, 'not_available'),
(289, 21, 'Симанов Аркадий', '27', 0, 0, 'not_available'),
(290, 21, 'Аюпов Тимур', '5', 0, 0, 'not_available'),
(291, 21, 'Делькин Артём', '11', 0, 0, 'not_available'),
(292, 21, 'Сысуев Николай', '99', 0, 0, 'not_available'),
(293, 21, 'Хайруллов Радик', '92', 0, 0, 'not_available'),
(294, 21, 'Бочаров Антон', '95', 0, 0, 'not_available'),
(295, 21, 'Семейкин Артём', '28', 0, 0, 'not_available'),
(296, 21, 'Гогличидзе Лео', '91', 0, 0, 'not_available'),
(297, 21, 'Нежелев Анантолий', '7', 0, 0, 'not_available'),
(298, 21, 'Скворцов Алексей', '19', 0, 0, 'not_available'),
(299, 21, 'Фомин Даниил', '74', 0, 0, 'not_available'),
(300, 21, 'Чирьяк Ловре', '33', 0, 0, 'not_available'),
(301, 21, 'Сергеев Виктор', '14', 0, 0, 'not_available'),
(302, 20, 'Лантратов Илья', '1', 0, 0, 'not_available'),
(303, 20, 'Тишкин Максим', '44', 0, 0, 'not_available'),
(304, 20, 'Магаль Руслан', '28', 0, 0, 'not_available'),
(305, 20, 'Плиев Константин', '33', 0, 0, 'not_available'),
(306, 20, 'Буйволов Андрей', '25', 0, 0, 'not_available'),
(307, 20, 'Каленкович Виталий', '93', 0, 0, 'not_available'),
(308, 20, 'Таказов Сослан', '30', 0, 0, 'not_available'),
(309, 20, 'Касаев Алан', '11', 0, 0, 'not_available'),
(310, 20, 'Шаваев Алихан', '19', 0, 0, 'not_available'),
(311, 20, 'Погребняк Кирилл', '14', 0, 0, 'not_available'),
(312, 20, 'Шешуков Александр', '2', 0, 1, 'not_available'),
(313, 20, 'Радунович Павле', '7', 0, 1, 'not_available'),
(314, 20, 'Волков Алексей', '98', 0, 1, 'not_available'),
(315, 20, 'Дзаламидзе Ника', '10', 0, 1, 'not_available'),
(316, 20, 'Шабалин Даниил', '13', 0, 1, 'not_available'),
(317, 20, 'Крючков Владислав', '23', 0, 1, 'not_available'),
(318, 20, 'Помазан Евгений', '32', 0, 1, 'not_available'),
(319, 9, 'Кизеев Михаил', '93', 0, 0, 'not_available'),
(320, 9, 'Рукас Томас', '96', 0, 0, 'not_available'),
(321, 9, 'Скроботов Илья', '80', 0, 0, 'not_available'),
(322, 9, 'Терентьев Денис', '3', 0, 0, 'not_available'),
(323, 9, 'Кириллов Дмитрий', '61', 0, 0, 'not_available'),
(324, 9, 'Плетнев Дмитрий', '49', 0, 0, 'not_available'),
(325, 9, 'Мусаев Леон', '38', 0, 0, 'not_available'),
(326, 9, 'Камышев Илья', '97', 0, 0, 'not_available'),
(327, 9, 'Богаев Дмитрий', '88', 0, 0, 'not_available'),
(328, 9, 'Макеев Кирилл', '76', 0, 0, 'not_available'),
(329, 9, 'Гойло Никита', '70', 0, 1, 'not_available'),
(330, 9, 'Пенчиков Даниил', '79', 0, 1, 'not_available'),
(331, 9, 'Синяк Антон', '72', 0, 1, 'not_available'),
(332, 9, 'Бугриев Сергей', '59', 0, 1, 'not_available'),
(333, 9, 'Иванов Сергей', '74', 0, 1, 'not_available'),
(334, 9, 'Казаков Руслан', '47', 0, 1, 'not_available'),
(335, 9, 'Капленко Кирилл', '55', 0, 1, 'not_available'),
(336, 9, 'Воробьев Илья', '84', 0, 1, 'not_available'),
(337, 13, 'Котляров Александр', '1', 0, 0, 'not_available'),
(338, 13, 'Царикаев Тарас', '71', 0, 1, 'not_available'),
(339, 13, 'Степанец Павел', '4', 0, 1, 'not_available'),
(340, 13, 'Замалиев Наиль', '33', 0, 1, 'not_available'),
(341, 13, 'Батов Максим', '5', 0, 1, 'not_available'),
(342, 13, 'Дзахов Давид', '17', 0, 1, 'not_available'),
(343, 13, 'Гордиенко Руслан', '9', 0, 1, 'not_available'),
(344, 13, 'Прокофьев Станислав', '11', 0, 1, 'not_available'),
(345, 13, 'Вамбольт Денис', '95', 0, 2, 'not_available'),
(346, 13, 'Патрашко Виктор', '22', 0, 2, 'not_available'),
(347, 13, 'Больевич Деян', '29', 0, 3, 'not_available'),
(348, 13, 'Насадюк Максим', '3', 0, 3, 'not_available'),
(349, 13, 'Машнев Максим', '19', 0, 3, 'not_available'),
(350, 13, 'Калугин Дмитрий', '14', 0, 3, 'not_available'),
(351, 13, 'Пономаренко Сергей', '12', 0, 3, 'not_available'),
(352, 13, 'Визнович Илья', '98', 0, 3, 'not_available'),
(353, 13, 'Хлебородов Иван', '7', 0, 3, 'not_available'),
(354, 13, 'Марущак Кирилл', '18', 0, 1, 'not_available'),
(355, 18, 'Заболотный Николай', '12', 0, 0, 'not_available'),
(356, 18, 'Заика Кирилл', '27', 0, 0, 'not_available'),
(357, 18, 'Миладинович Иван', '45', 0, 0, 'not_available'),
(358, 18, 'Почивалин Валерий', '92', 0, 0, 'not_available'),
(359, 18, 'Юрганов Игорь', '20', 0, 0, 'not_available'),
(360, 18, 'Померко Алексей', '5', 0, 0, 'not_available'),
(361, 18, 'Песегов Евгений', '7', 0, 0, 'not_available'),
(362, 18, 'Горбунов Игорь', '11', 0, 0, 'not_available'),
(363, 18, 'Саная Анзор', '23', 0, 0, 'not_available'),
(364, 18, 'Солдатенко Ростислав', '13', 0, 1, 'not_available'),
(365, 18, 'Мустафин Темур', '63', 0, 1, 'not_available'),
(366, 18, 'Калугин Никита', '26', 0, 1, 'not_available'),
(367, 18, 'Скляров Максим', '17', 0, 1, 'not_available'),
(368, 18, 'Лагатор Душан', '94', 0, 1, 'not_available'),
(369, 18, 'Косянчук Роман', '14', 0, 1, 'not_available'),
(370, 18, 'Обольский Николай', '99', 0, 1, 'not_available'),
(371, 17, 'Мелихов Александр', '42', 0, 0, 'not_available'),
(372, 17, 'Зуйков Сергей', '23', 0, 0, 'not_available'),
(373, 17, 'Тихий Дмитрий', '6', 0, 1, 'not_available'),
(374, 17, 'Шумских Алексей', '5', 0, 1, 'not_available'),
(375, 17, 'Сасин Дмитрий', '7', 0, 1, 'not_available'),
(376, 17, 'Калинский Николай', '78', 0, 1, 'not_available'),
(377, 17, 'Шалаев Олег', '18', 0, 1, 'not_available'),
(378, 17, 'Казаев Ян', '10', 0, 1, 'not_available'),
(379, 17, 'Кузьмичев Илья', '11', 0, 1, 'not_available'),
(380, 17, 'Шафинский Юрий', '1', 0, 0, 'not_available'),
(381, 17, 'Мануйлов Роман', '27', 0, 0, 'not_available'),
(382, 17, 'Карымов Марк', '69', 0, 1, 'not_available'),
(383, 17, 'Тен Петр', '22', 0, 1, 'not_available'),
(384, 17, 'Лисинков Айдар', '2', 0, 1, 'not_available'),
(385, 17, 'Гвинейский Никита', '98', 0, 1, 'not_available'),
(386, 17, 'Ставпец Александр', '61', 0, 1, 'not_available'),
(387, 17, 'Макурин Антон', '14', 0, 1, 'not_available'),
(388, 17, 'Фомин Семен', '4', 0, 1, 'not_available'),
(389, 17, 'Крапухин Станислав', '90', 0, 1, 'not_available'),
(390, 14, 'Арапов Дмитрий', '35', 0, 1, 'not_available'),
(391, 14, 'Дубовой Владислав', '2', 0, 1, 'not_available'),
(392, 14, 'Демченко Андрей', '77', 0, 1, 'not_available'),
(393, 14, 'Карташов Дмитрий', '55', 0, 1, 'not_available'),
(394, 14, 'Гараев Виктор', '93', 0, 1, 'not_available'),
(395, 14, 'Шахтиев Халид', '95', 0, 1, 'not_available'),
(396, 14, 'Гречкин Алексей', '79', 0, 1, 'not_available'),
(397, 14, 'Шаров Сергей', '1', 0, 1, 'not_available'),
(398, 14, 'Крутов Игорь', '13', 0, 1, 'not_available'),
(399, 14, 'Хохлачев Александр', '7', 0, 1, 'not_available'),
(400, 14, 'Чалый Никита', '27', 0, 1, 'not_available'),
(401, 14, 'Гиоргобиани Николай', '19', 0, 1, 'not_available'),
(402, 14, 'Обозный Максим', '11', 0, 1, 'not_available'),
(403, 14, 'Граб Вячеслав', '1', 0, 2, 'not_available'),
(404, 14, 'Бутырин Сергей', '31', 0, 2, 'not_available'),
(405, 14, 'Геворкян Артем', '9', 0, 2, 'not_available'),
(406, 19, 'Чагров Никита', '13', 0, 0, 'not_available'),
(407, 19, 'Багаев Михаил', '71', 0, 1, 'not_available'),
(408, 19, 'Никитин Денис', '97', 0, 1, 'not_available'),
(409, 19, 'Гоцук Кирилл', '24', 0, 1, 'not_available'),
(410, 19, 'Дашаев Аслан', '4', 0, 1, 'not_available'),
(411, 19, 'Акбашев Роман', '7', 0, 1, 'not_available'),
(412, 19, 'Земсков Михаил', '8', 0, 1, 'not_available'),
(413, 19, 'Кубышкин Илья', '23', 0, 1, 'not_available'),
(414, 19, 'Альшин Ильнур', '70', 0, 1, 'not_available'),
(415, 19, 'Стеклов Вадим', '20', 0, 1, 'not_available'),
(416, 19, 'Федчук Артем', '10', 0, 1, 'not_available'),
(417, 19, 'Саутин Александр', '1', 0, 2, 'not_available'),
(418, 19, 'Войнов Александр', '22', 0, 2, 'not_available'),
(419, 19, 'Ковалев Константин', '14', 0, 2, 'not_available'),
(420, 19, 'Синяев Денис', '17', 0, 2, 'not_available'),
(421, 19, 'Минаев Роман', '77', 0, 2, 'not_available'),
(422, 19, 'Руденко Владислав', '27', 0, 2, 'not_available'),
(423, 19, 'Касьян Александр', '19', 0, 2, 'not_available'),
(424, 16, 'Терешкин Владислав', '95', 0, 0, 'not_available'),
(425, 16, 'Гапонов Илья', '56', 0, 0, 'not_available'),
(426, 16, 'Маслов Павел', '39', 0, 0, 'not_available'),
(427, 16, 'Лихачёв Александр', '44', 0, 0, 'not_available'),
(428, 16, 'Воропаев Артем', '36', 0, 0, 'not_available'),
(429, 16, 'Бакаев Солтмурад', '87', 0, 0, 'not_available'),
(430, 16, 'Пантелеев Владислав', '83', 1, 0, 'not_available'),
(431, 16, 'Рудковский Егор', '13', 0, 0, 'not_available'),
(432, 16, 'Еременко Сергей', '45', 0, 0, 'not_available'),
(433, 16, 'Руденко Александр', '79', 0, 0, 'not_available'),
(434, 16, 'Мелкадзе Георгий', '37', 0, 0, 'not_available'),
(435, 16, 'Ярусов Даниил', '48', 0, 1, 'not_available'),
(436, 16, 'Ермаков Данила', '30', 0, 1, 'not_available'),
(437, 16, 'Актисов Максим', '96', 0, 1, 'not_available'),
(438, 16, 'Миронов Леонид', '35', 0, 1, 'not_available'),
(439, 15, 'Яшин Дмитрий', '16', 0, 0, 'not_available'),
(440, 16, 'Полубояринов Данил', '97', 0, 1, 'not_available'),
(441, 15, 'Цховребов Валерий', '3', 0, 1, 'not_available'),
(442, 15, 'Самошников Илья', '77', 0, 1, 'not_available'),
(443, 16, 'Митрога Дмитрий', '89', 0, 1, 'not_available'),
(444, 15, 'Магадиев Денис', '2', 0, 1, 'not_available'),
(445, 15, 'Первушин Федор', '94', 0, 1, 'not_available'),
(446, 15, 'Кожемякин Олег', '8', 0, 1, 'not_available'),
(447, 15, 'Камилов Владислав', '21', 0, 1, 'not_available'),
(448, 15, 'Самойлов Дмитрий', '15', 0, 1, 'not_available'),
(449, 15, 'Низамутдинов Эльдар', '9', 0, 1, 'not_available'),
(450, 16, 'Тиерно Тиуб', '28', 0, 1, 'not_available'),
(451, 15, 'Гелоян Ишхан', '10', 0, 1, 'not_available'),
(452, 15, 'Самодин Сергей', '22', 0, 1, 'not_available'),
(453, 15, 'Гошев Евгений', '97', 0, 2, 'not_available'),
(454, 15, 'Иванов Андрей', '20', 0, 2, 'not_available'),
(455, 15, 'Покидышев Николай', '17', 0, 2, 'not_available'),
(456, 15, 'Мацхарашвили Никита', '99', 0, 2, 'not_available'),
(457, 15, 'Деобальд Павел', '57', 0, 2, 'not_available'),
(458, 15, 'Алейников Василий', '27', 0, 2, 'not_available'),
(459, 15, 'Нарылков Сергей', '11', 0, 2, 'not_available'),
(460, 15, 'Булия Эдуард', '7', 0, 2, 'not_available'),
(461, 3, 'Мирошниченко Сергей', '24', 0, 2, 'not_available'),
(462, 16, 'Тюнин Николай', '4', 0, 1, 'not_available'),
(463, 23, 'Григорян Арутюн', '87', 0, 1, 'not_available'),
(464, 23, 'Уткин Даниил', '47', 0, 1, 'not_available'),
(465, 23, 'Сулейманов Магомед-Шапи', '93', 0, 2, 'not_available'),
(466, 23, 'Сафонов Матвей', '39', 0, 0, 'not_available'),
(467, 23, 'Бочко Николай', '65', 1, 1, 'not_available'),
(468, 23, 'Бочко Николай', '65', 0, 0, 'not_available'),
(469, 23, 'Татаев Алексей', '41', 0, 0, 'not_available'),
(470, 23, 'Голубев Артём', '50', 0, 0, 'not_available'),
(471, 23, 'Мартынов Илья', '61', 0, 0, 'not_available'),
(472, 23, 'Ивашин Андрей', '40', 0, 0, 'not_available'),
(473, 23, 'Мацукатов Алекс', '62', 0, 0, 'not_available'),
(474, 23, 'Онугха Герман', '60', 0, 0, 'not_available'),
(475, 23, 'Латышонок Евгений', '52', 0, 1, 'not_available'),
(476, 23, 'Назаров Евгений', '94', 0, 1, 'not_available'),
(477, 23, 'Парадин Игорь', '45', 0, 1, 'not_available'),
(478, 23, 'Текучев Александр', '36', 0, 1, 'not_available'),
(479, 23, 'Картич Анатолий', '23', 0, 1, 'not_available'),
(480, 23, 'Сергеев Никита', '63', 0, 1, 'not_available'),
(481, 23, 'Халназаров Рустам', '72', 0, 1, 'not_available'),
(482, 5, 'Юсупов Радик', '19', 0, 21, 'not_available'),
(483, 12, 'Глушенков Иван', '15', 0, 0, 'not_available'),
(484, 12, 'Цыпченко Дмитрий', '91', 0, 0, 'not_available'),
(485, 12, 'Зиньченко Антон', '17', 0, 0, 'not_available'),
(486, 12, 'Канищев Александр', '19', 0, 0, 'not_available'),
(487, 11, 'Кленкин Данил', '23', 0, 0, 'not_available'),
(488, 11, 'Косицин Сергей', '55', 0, 1, 'not_available'),
(489, 11, 'Чистяков Дмитрий', '78', 0, 1, 'not_available'),
(490, 11, 'Лазуткин Максим', '91', 0, 1, 'not_available'),
(491, 11, 'Олабиран Блессин', '20', 0, 1, 'not_available'),
(492, 11, 'Рагулькин Евгений', '94', 0, 1, 'not_available'),
(493, 10, 'Леонтьев Игорь', '52', 0, 19, 'not_available'),
(494, 10, 'Коронов Игорь', '14', 0, 20, 'not_available'),
(495, 10, 'Карпов Данил', '11', 0, 21, 'not_available'),
(496, 13, 'Носов Александр', '10', 0, 0, 'not_available'),
(497, 13, 'Голышев Павел', '8', 0, 0, 'not_available'),
(498, 17, 'Зубчихин Никита', '63', 0, 0, 'not_available'),
(499, 17, 'Смирнов Олег', '1', 0, 0, 'not_available'),
(500, 17, 'Сальников Иван', '51', 0, 0, 'not_available'),
(501, 17, 'Бугаев Роман', '8', 0, 0, 'not_available'),
(502, 17, 'Запрягаев Василий', '39', 0, 0, 'not_available'),
(503, 17, 'Каккоев Никита', '24', 0, 0, 'not_available'),
(504, 17, 'Пенчиков Даниил', '23', 0, 0, 'not_available'),
(505, 17, 'Эдиев Исмаил', '3', 0, 0, 'not_available'),
(506, 17, 'Андреев Иван', '75', 0, 0, 'not_available'),
(507, 17, 'Андреев Максим', '10', 0, 0, 'not_available'),
(508, 17, 'Абдулавов Исламнур', '99', 0, 0, 'not_available'),
(509, 17, 'Гасилин Алексей', '11', 0, 0, 'not_available'),
(510, 17, 'Кудряшов Павел', '9', 0, 0, 'not_available'),
(511, 3, 'Лаук Максим', '95', 0, 0, 'not_available'),
(512, 3, 'Тен Пётр', '22', 0, 0, 'not_available'),
(513, 3, 'Мустафин Темур', '15', 0, 0, 'not_available'),
(514, 3, 'Таказов Сослан', '30', 0, 0, 'not_available'),
(515, 17, 'Макурин Антон', '14', 0, 1, 'not_available'),
(516, 17, 'Карымов Марк', '69', 0, 1, 'not_available'),
(517, 18, 'Зайцев Андрей', '91', 0, 1, 'not_available'),
(518, 18, 'Бычков Андрей', '25', 0, 0, 'not_available'),
(519, 18, 'Иващенко Станислав', '66', 0, 1, 'not_available'),
(520, 18, 'Калайджян Аркадий', '22', 0, 1, 'not_available'),
(521, 18, 'Маргасов Тимофей', '34', 0, 1, 'not_available'),
(522, 18, 'Бахтияров Акмаль', '21', 0, 1, 'not_available'),
(523, 18, 'Бурмистров Никита', '18', 0, 1, 'not_available'),
(524, 18, 'Вартанян Ефрем', '37', 0, 1, 'not_available'),
(525, 18, 'Жевтяк Родион', '96', 0, 1, 'not_available'),
(526, 18, 'Казаев Ян', '55', 0, 1, 'not_available'),
(527, 18, 'Касаев Алан', '3', 0, 1, 'not_available'),
(528, 18, 'Ремизов Егор', '36', 0, 1, 'not_available'),
(529, 18, 'Рыбачук Андрей', '38', 0, 1, 'not_available'),
(530, 18, 'Саламатов Никита', '8', 0, 1, 'not_available'),
(531, 18, 'Альфред Стефен', '29', 0, 1, 'not_available');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `team_players`
--
ALTER TABLE `team_players`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_deleted` (`is_deleted`),
  ADD KEY `display_order` (`display_order`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `team_players`
--
ALTER TABLE `team_players`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=532;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
