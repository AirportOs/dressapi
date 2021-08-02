
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
-- Structure table `acl`
--

CREATE TABLE `acl` (
  `id` int(11) NOT NULL,
  `id_role` int(11) DEFAULT NULL COMMENT 'Role of the user',
  `id_moduletable` int(11) DEFAULT NULL COMMENT 'contains the index of module or table name managed by the base module',
  `can_read` enum('YES','NO') DEFAULT 'NO',
  `can_insert` enum('YES','NO') DEFAULT 'NO',
  `can_update` enum('YES','NO') DEFAULT 'NO',
  `can_delete` enum('YES','NO') DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for the table `acl`
--

INSERT INTO `acl` (`id`, `id_role`, `id_moduletable`, `can_read`, `can_insert`, `can_update`, `can_delete`) VALUES
(1, 1, NULL, 'YES', 'YES', 'YES', 'YES'),
(2, NULL, 1, 'YES', 'NO', 'NO', 'NO'),
(3, 2, 2, 'YES', 'NO', 'NO', 'NO');

-- --------------------------------------------------------

--
-- Structure table `comment`
--

CREATE TABLE `comment` (
  `id` int(11) NOT NULL,
  `comment` varchar(300) NOT NULL,
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) DEFAULT NULL,
  `id_page` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data dump for the table `comment`
--

INSERT INTO `comment` (`id`, `comment`, `creation_date`, `id_user`, `id_page`) VALUES
(1, 'oh yeah! It\'s a very good solution for a Rest API!', '2021-01-13', 101, 1),
(1000, 'Yii is better than DressApi!', '2021-07-18', 2, 1);

-- --------------------------------------------------------

--
-- Structure table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `surname` varchar(80) NOT NULL,
  `address` varchar(160) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `city` varchar(80) NOT NULL,
  `state` varchar(30) NOT NULL,
  `email` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for the table `contact`
--

INSERT INTO `contact` (`id`, `name`, `surname`, `address`, `zip_code`, `city`, `state`, `email`) VALUES
(1, 'Joe', 'Sample', 'Via 1 Febbraio', '15005', 'Rome', 'Italy', 'jsample@userdressapi.com'),
(2, 'Michael', 'Franks', 'The art of the tea street, 1046', '01975', 'Los Angeles', 'California', 'mfranks@userdressapi.com'),
(3, 'Pasquale', 'Tufano', 'Via Roccavione, 216', '10047', 'Turin', 'Italy', 'ptufano@userdressapi.com');

-- --------------------------------------------------------

--
-- Structure table `moduletable`
--

CREATE TABLE `moduletable` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data dump for the table `moduletable`
--

INSERT INTO `moduletable` (`id`, `name`, `description`) VALUES
(1, 'page', ''),
(2, 'comment', '');

-- --------------------------------------------------------

--
-- Structure table `page`
--

CREATE TABLE `page` (
  `id` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data dump for the table `page`
--

INSERT INTO `page` (`id`, `title`, `body`, `creation_date`, `id_user`) VALUES
(1, 'Welcome to DressApi: the new ORM REST API', 'The name \"Dress\" means it \"dress\" up your database, substantially it provides a quick REST API, to your db schema. \r\nORM means Object-relational mapping and DressApi maps your database dynamically. Although it is structured as an MVC (Model, View, Controller) it does not need to define a model for each table in the DB but if it automatically reads it from the DB. \r\nThe most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code.', '2021-01-13', 103),
(2, 'DressApi is new but contains long experience inside', 'I have a very long experience in programming with various languages, for the web I have always preferred PHP.\r\nIn about twenty years of developing web applications, I have always developed and used a personal framework that adopts the dynamic ORM logic and has evolved over time. Now a large part of the code has been rewritten from scratch in the most modern view of the REST API but the idea has remained the same and the experience has certainly allowed to create a solid and functional platform.', '2021-01-15', 103);

-- --------------------------------------------------------

--
-- Structure table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for the table `role`
--

INSERT INTO `role` (`id`, `name`, `description`) VALUES
(1, 'Administrator', 'Can read - All permissions for anonymous is valid for all user'),
(2, 'Anonymous', 'Can read - All permissions for anonymous is valid for all user'),
(3, 'Editor', 'Full power: can Delete or publish a post'),
(4, 'Writer', 'Can write a post'),
(5, 'Commentator', 'Can write a comment');

-- --------------------------------------------------------

--
-- Structure table `user`
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
-- Data dump for the table `user`
--

INSERT INTO `user` (`id`, `name`, `id_contact`, `domain`, `nickname`, `username`, `pwd`, `status`) VALUES
(1, 'Administrator', NULL, 'local', 'admin', 'admin', '51c3f5f5d8a8830bc5d8b7ebcb5717dfb4892d4766c2a77d', 'Verified'),
(2, 'Anonymous', NULL, 'local', 'Anonymous', '', '24cc78a7f6ff3546e7984e59695ca13d804e0b686e255194', 'Verified'),
(101, 'Joe Sample', 1, 'local', 'Big Joe', 'jsample', '1d628e0dd73490f28e5717bb2564f4760a6caf3922051f3a', 'Verified'),
(102, 'Michael Franks', 2, 'local', 'Mr Blue', 'mfranks', '292498b56f83154fc913b173e51ca43c898cc35944280aaa', 'Verified'),
(103, 'Pasquale Tufano', 3, 'local', 'Pask', 'ptufano', '9444701720f616c4a8985f7d4022c1507389a33208c9afb2', 'Verified');

-- --------------------------------------------------------

--
-- Structure table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for the table `user_role`
--

INSERT INTO `user_role` (`id`, `id_user`, `id_role`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 101, 3),
(4, 102, 4),
(5, 103, 5);

--
-- Indici for the tables scaricate
--

--
-- Indici for the tables `acl`
--
ALTER TABLE `acl`
  ADD PRIMARY KEY (`id`);

--
-- Indici for the tables `comment`
--
ALTER TABLE `comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_ibfk_1` (`id_user`),
  ADD KEY `id_page` (`id_page`);

--
-- Indici for the tables `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indici for the tables `moduletable`
--
ALTER TABLE `moduletable`
  ADD PRIMARY KEY (`id`);

--
-- Indici for the tables `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indici for the tables `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indici for the tables `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indici for the tables `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT for the tables
--

--
-- AUTO_INCREMENT for the table `acl`
--
ALTER TABLE `acl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for the table `comment`
--
ALTER TABLE `comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1066;

--
-- AUTO_INCREMENT for the table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for the table `moduletable`
--
ALTER TABLE `moduletable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for the table `page`
--
ALTER TABLE `page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for the table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for the table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for the table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Limiti for the tables scaricate
--

--
-- Limiti for the table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`id_page`) REFERENCES `page` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti for the table `page`
--
ALTER TABLE `page`
  ADD CONSTRAINT `id_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti for the table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
