SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dressapi-test`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `comment` varchar(300) NOT NULL,
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) DEFAULT NULL,
  `id_page` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `comment`
--

INSERT INTO `comment` (`id`, `comment`, `creation_date`, `id_user`, `id_page`) VALUES
(1, 'oh yeah! It\'s a very good solution for a Rest API!', '2021-01-13', 101, 1),
(2, 'I love its simplicity!', '2021-01-15', 102, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `surname` varchar(80) NOT NULL,
  `address` varchar(160) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `city` varchar(80) NOT NULL,
  `state` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `contact`
--

INSERT INTO `contact` (`id`, `first_name`, `surname`, `address`, `zip_code`, `city`, `state`, `email`) VALUES
(1, 'Joe', 'Sample', 'Via 1 Febbraio', '15005', 'Rome', 'Italy', 'jsample@userdressapi.com'),
(2, 'Michael', 'Franks', 'The art of the tea street, 1046', '01975', 'Los Angeles', 'California', 'mfranks@userdressapi.com'),
(3, 'Pasquale', 'Tufano', 'Via Roccavione, 21', '10047', 'Turin', 'Italy', 'ptufano@userdressapi.com');

-- --------------------------------------------------------

--
-- Struttura della tabella `logger`
--

CREATE TABLE `logger` (
  `id` int(11) NOT NULL,
  `request` varchar(255) NOT NULL,
  `method` varchar(20) NOT NULL,
  `params` varchar(255) NOT NULL,
  `request_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status_code` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `logger`
--

INSERT INTO `logger` (`id`, `request`, `method`, `params`, `request_date`, `status_code`, `id_user`) VALUES
(1, 'comment', 'POST', 'Good!,2021-01-23,1', '2021-07-12 00:58:46', 201, 1),
(2, 'page/1', 'PATCH', '2021-01-13', '2021-07-12 01:03:50', 200, 1),
(3, 'comment/1,2/wr', 'GET', '', '2021-07-12 01:07:25', 200, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `moduletable`
--

CREATE TABLE `moduletable` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `moduletable`
--

INSERT INTO `moduletable` (`id`, `name`, `description`) VALUES
(1, 'page', ''),
(2, 'comment', '');

-- --------------------------------------------------------

--
-- Struttura della tabella `moduletable_role_permission`
--

CREATE TABLE `moduletable_role_permission` (
  `id` int(11) NOT NULL,
  `id_moduletable` int(11) DEFAULT NULL COMMENT 'contains the index of module or table name managed by the base module',
  `id_role` int(11) NOT NULL COMMENT 'Role of the user',
  `permission` enum('C','R','U','D','O') DEFAULT 'R' COMMENT 'C (create),R (read), U (update),D (delete) or O (options)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `moduletable_role_permission`
--

INSERT INTO `moduletable_role_permission` (`id`, `id_moduletable`, `id_role`, `permission`) VALUES
(1, NULL, 1, NULL),
(2, 1, 2, 'R'),
(3, 1, 3, 'C'),
(4, 1, 3, 'U'),
(5, 1, 3, 'D'),
(6, 1, 3, 'O'),
(7, 1, 4, 'C'),
(8, 1, 4, 'U'),
(9, 1, 4, 'O'),
(10, 2, 2, 'R'),
(11, 2, 3, 'C'),
(12, 2, 3, 'U'),
(13, 2, 3, 'D'),
(14, 2, 3, 'O'),
(15, 2, 4, 'C'),
(16, 2, 4, 'U'),
(17, 2, 4, 'O'),
(18, 2, 5, 'C');

-- --------------------------------------------------------

--
-- Struttura della tabella `page`
--

CREATE TABLE `page` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `page`
--

INSERT INTO `page` (`id`, `title`, `body`, `creation_date`, `id_user`) VALUES
(1, 'Welcome to DressApi: the new ORM REST API', 'The name \"Dress\" means it \"dress\" up your database, substantially it provides a quick REST API, to your db schema. \r\nORM means Object-relational mapping and DressApi maps your database dynamically. Although it is structured as an MVC (Model, View, Controller) it does not need to define a model for each table in the DB but if it automatically reads it from the DB. \r\nThe most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code.', '2021-01-13', 103),
(2, 'DressApi is new but contains long experience inside', 'I have a very long experience in programming with various languages, for the web I have always preferred PHP.\r\nIn about twenty years of developing web applications, I have always developed and used a personal framework that adopts the dynamic ORM logic and has evolved over time. Now a large part of the code has been rewritten from scratch in the most modern view of the REST API but the idea has remained the same and the experience has certainly allowed to create a solid and functional platform.', '2021-01-15', 103);

-- --------------------------------------------------------

--
-- Struttura della tabella `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `role`
--

INSERT INTO `role` (`id`, `name`, `description`) VALUES
(1, 'Administrator', 'Can read - All permissions for anonymous is valid for all user'),
(2, 'Anonymous', 'Can read - All permissions for anonymous is valid for all user'),
(3, 'Editor', 'Full power: can Delete or publish a post'),
(4, 'Writer', 'Can write a post'),
(5, 'Commentator', 'Can write a comment');

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `id_contact` int(11) DEFAULT NULL,
  `domain` varchar(60) DEFAULT 'local',
  `nickname` varchar(60) NOT NULL,
  `username` varchar(255) NOT NULL,
  `pwd` varchar(120) NOT NULL DEFAULT '-HsjK673Hf@fhs',
  `status` enum('Subscribed','Verified','Refused') NOT NULL DEFAULT 'Subscribed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`id`, `name`, `id_contact`, `domain`, `nickname`, `username`, `pwd`, `status`) VALUES
(1, 'Administrator', NULL, 'local', 'admin', 'admin', '51c3f5f5d8a8830bc5d8b7ebcb5717dfb4892d4766c2a77d', 'Verified'),
(2, 'Anonymous', NULL, 'local', 'Anonymous', '', '24cc78a7f6ff3546e7984e59695ca13d804e0b686e255194', 'Verified'),
(101, 'Joe Sample', 1, 'local', 'Big Joe', 'jsample', '1d628e0dd73490f28e5717bb2564f4760a6caf3922051f3a', 'Verified'),
(102, 'Michael Franks', 2, 'local', 'Mr Blue', 'mfranks', '292498b56f83154fc913b173e51ca43c898cc35944280aaa', 'Verified'),
(103, 'Pasquale Tufano', 3, 'local', 'Pask', 'ptufano', '9444701720f616c4a8985f7d4022c1507389a33208c9afb2', 'Verified');

-- --------------------------------------------------------

--
-- Struttura della tabella `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `user_role`
--

INSERT INTO `user_role` (`id`, `id_user`, `id_role`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 101, 3),
(4, 102, 4),
(5, 103, 5);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_ibfk_1` (`id_user`),
  ADD KEY `id_page` (`id_page`);

--
-- Indici per le tabelle `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `logger`
--
ALTER TABLE `logger`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indici per le tabelle `moduletable`
--
ALTER TABLE `moduletable`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `moduletable_role_permission`
--
ALTER TABLE `moduletable_role_permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_role` (`id_role`);

--
-- Indici per le tabelle `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indici per le tabelle `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `logger`
--
ALTER TABLE `logger`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT per la tabella `moduletable`
--
ALTER TABLE `moduletable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `moduletable_role_permission`
--
ALTER TABLE `moduletable_role_permission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT per la tabella `page`
--
ALTER TABLE `page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT per la tabella `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`id_page`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `logger`
--
ALTER TABLE `logger`
  ADD CONSTRAINT `logger_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `page`
--
ALTER TABLE `page`
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
