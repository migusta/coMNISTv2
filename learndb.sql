-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июл 25 2017 г., 13:25
-- Версия сервера: 5.7.17
-- Версия PHP: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `id787690_learndb`
--

-- --------------------------------------------------------

--
-- Структура таблицы `actions`
--

CREATE TABLE `actions` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `lessonid` int(11) NOT NULL,
  `finished` tinyint(1) DEFAULT NULL,
  `score` int(11) DEFAULT '0',
  `started` tinyint(1) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `actions`
--

INSERT INTO `actions` (`id`, `userid`, `lessonid`, `finished`, `score`, `started`) VALUES
(26, 1, 3, 0, 0, 0),
(25, 1, 2, 1, 3, 1),
(23, 1, 1, 1, 3, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `lessons`
--

CREATE TABLE `lessons` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `next_id` int(11) DEFAULT NULL,
  `img` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `lessons`
--

INSERT INTO `lessons` (`id`, `title`, `next_id`, `img`) VALUES
(1, 'First lesson', 2, 'lesson1.png'),
(2, 'Second lesson', 3, NULL),
(3, 'Third lesson', NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `user_pass` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_pass`) VALUES
(1, 'anna', 'anuta04@yandex.ru', '$2y$10$3z59cmDYamlsxXr8rfVgbOSuTr4DhMmDF7o5XP7XVtWhpOFWTiZUm'),
(2, 'beta', 's@ya.ru', '$2y$10$qYdjru94i8o6rH4xVVH7O.9SlJgfmaE.16CD/OQtDljOjblqYLfKi');

-- --------------------------------------------------------

--
-- Структура таблицы `words`
--

CREATE TABLE `words` (
  `id` int(10) UNSIGNED NOT NULL,
  `word_en` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `word_ru` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `img` text COLLATE utf8_unicode_ci,
  `lessonid` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `words`
--

INSERT INTO `words` (`id`, `word_en`, `word_ru`, `img`, `lessonid`) VALUES
(1, 'COW', 'КОРОВА', 'cow.jpg', 1),
(2, 'ABBA', 'АББА', 'abba.png', 1),
(3, 'LEG', 'НОГА', 'leg.jpg', 2),
(4, 'CAT', 'КОТ', 'cat.jpg', 2),
(5, 'ATOM', 'АТОМ', 'atom.jpg', NULL),
(6, 'MAKET', 'МАКЕТ', 'maket.jpg', NULL),
(7, 'BAB', 'БАБ', 'bab.jpg', NULL),
(8, 'BAABA', 'БААБА', 'baaba.png', NULL),
(9, 'A', 'A', NULL, NULL),
(10, 'A', 'A', NULL, NULL),
(11, 'A', 'A', NULL, NULL),
(12, 'ABC', 'ABC', NULL, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `lessonid` (`lessonid`);

--
-- Индексы таблицы `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lesson` (`next_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name` (`user_name`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- Индексы таблицы `words`
--
ALTER TABLE `words`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_lesson` (`lessonid`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `actions`
--
ALTER TABLE `actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT для таблицы `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT для таблицы `words`
--
ALTER TABLE `words`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `words`
--
ALTER TABLE `words`
  ADD CONSTRAINT `fk_lesson` FOREIGN KEY (`lessonid`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
