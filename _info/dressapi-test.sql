
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
-- Table structure for table `acl`
--

CREATE TABLE `acl` (
  `id` int(11) NOT NULL,
  `id_role` int(11) DEFAULT NULL COMMENT 'Role of the user',
  `id_module` int(11) DEFAULT NULL COMMENT 'contains the index of module or table name managed by the base module',
  `can_read` enum('YES','NO') DEFAULT 'NO',
  `can_insert` enum('YES','NO') DEFAULT 'NO',
  `can_update` enum('YES','NO') DEFAULT 'NO',
  `can_delete` enum('YES','NO') DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl`
--

INSERT INTO `acl` (`id`, `id_role`, `id_module`, `can_read`, `can_insert`, `can_update`, `can_delete`) VALUES
(1, 1, NULL, 'YES', 'YES', 'YES', 'NO'),
(2, NULL, 1, 'YES', 'NO', 'NO', 'NO');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
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
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `surname`, `address`, `zip_code`, `city`, `state`, `email`) VALUES
(1, 'Joe', 'Sample', 'Via 1 Febbraio', '15005', 'Rome', 'Italy', 'jxsample@userdressapi.com'),
(2, 'Michael', 'Franks', 'The art of the tea street, 1046', '01975', 'Los Angeles', 'California', 'mfranks@userdressapi.com'),
(3, 'Pasquale', 'Tufano', 'Via Roccavione, 216', '10047', 'Turin', 'Italy', 'ptufano@userdressapi.com'),
(21, 'Joe', 'Sample', 'Via 1 Febbraio', '15005', 'Turin', 'Italy', 'jsample@userdressapi.com');

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `title` varchar(65) NOT NULL,
  `description` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id`, `name`, `title`, `description`) VALUES
(1, 'Node', 'Node Module', 'Description of Node Module');

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE `node` (
  `id` int(11) NOT NULL,
  `id_nodetype` int(11) NOT NULL,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `description` varchar(160) NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL,
  `id_parent_node` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `id_nodetype`, `title`, `body`, `description`, `visible`, `status`, `creation_date`, `id_user`, `id_parent_node`) VALUES
(1, 3, 'Welcome to DressApi: the new ORM REST API', 'The name \"Dress\" means it \"dress\" up your database, substantially it provides a quick REST API, to your db schema. \nORM means Object-relational mapping and DressApi maps your database dynamically. Although it is structured as an MVC (Model, View, Controller) it does not need to define a model for each table in the DB but if it automatically reads it from the DB. \nThe most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code.', 'Example of use DressApi', 'no', 'draft', '2021-01-13', 1, NULL),
(2, 3, 'DressApi is new but contains long experience inside', 'I have a very long experience in programming with various languages, for the web I have always preferred PHP.\r\nIn about twenty years of developing web applications, I have always developed and used a personal framework that adopts the dynamic ORM logic and has evolved over time. Now a large part of the code has been rewritten from scratch in the most modern view of the REST API but the idea has remained the same and the experience has certainly allowed to create a solid and functional platform.', '', 'no', 'draft', '2021-01-15', 103, NULL),
(3, 3, 'title test', 'I have a very long experience in programming with various languages, for the web I have always preferred PHP.\nIn about twenty years of developing web applications, I have always developed and used a personal framework that adopts the dynamic ORM logic and has evolved over time. Now a large part of the code has been rewritten from scratch in the most modern view of the REST API but the idea has remained the same and the experience has certainly allowed to create a solid and functional platform.', 'Title Test OK!', 'no', 'draft', '2021-01-15', 115, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nodetype`
--

CREATE TABLE `nodetype` (
  `id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `nodetype`
--

INSERT INTO `nodetype` (`id`, `name`, `description`) VALUES
(1, 'container', ''),
(2, 'menu', ''),
(3, 'link', ''),
(4, 'file', ''),
(11, 'page', ''),
(12, 'article', ''),
(13, 'news', ''),
(14, 'event', ''),
(15, 'comment', '');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`, `description`) VALUES
(1, 'Administrator', 'Can read - All permissions for anonymous is valid for all user'),
(2, 'Anonymous', 'Can read - All permissions for anonymous is valid for all user'),
(101, 'Editor', 'Full power: can Delete or publish a post'),
(102, 'Writer', 'Can write a post'),
(103, 'Commentator', 'Can write a comment');

-- --------------------------------------------------------

--
-- Table structure for table `user`
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
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `id_contact`, `domain`, `nickname`, `username`, `pwd`, `status`) VALUES
(1, 'Administrator', NULL, 'local', 'admin', 'admin', '51c3f5f5d8a8830bc5d8b7ebcb5717dfb4892d4766c2a77d', 'Verified'),
(2, 'Anonymous', NULL, 'local', 'Anonymous', '', '', 'Verified'),
(101, 'Joe Sample', 1, 'local', 'Big Joe', 'jsample', '1d628e0dd73490f28e5717bb2564f4760a6caf3922051f3a', 'Verified'),
(102, 'Michael Franks', 2, 'local', 'Mr Blue', 'mfranks', '292498b56f83154fc913b173e51ca43c898cc35944280aaa', 'Verified'),
(103, 'Pasquale Tufano', 3, 'local', 'Pask', 'ptufano', '9444701720f616c4a8985f7d4022c1507389a33208c9afb2', 'Verified'),
(115, 'J.Sample', 21, 'DressApi.com', 'Joe', 'Joe', '119dcb517fedfaba6f41824968610987702bf221b8e6afdd', 'Verified');

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `id_user`, `id_role`) VALUES
(1, 1, 1),
(11, 2, 2),
(101, 101, 101),
(102, 102, 102),
(103, 103, 103);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `acl`
--
ALTER TABLE `acl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_module` (`id_module`),
  ADD KEY `id_role` (`id_role`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `node`
--
ALTER TABLE `node`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_nodetype` (`id_nodetype`),
  ADD KEY `id_parent_node` (`id_parent_node`);

--
-- Indexes for table `nodetype`
--
ALTER TABLE `nodetype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_contact` (`id_contact`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `acl`
--
ALTER TABLE `acl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `node`
--
ALTER TABLE `node`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `nodetype`
--
ALTER TABLE `nodetype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `acl`
--
ALTER TABLE `acl`
  ADD CONSTRAINT `acl_ibfk_1` FOREIGN KEY (`id_module`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `acl_ibfk_2` FOREIGN KEY (`id_role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `node`
--
ALTER TABLE `node`
  ADD CONSTRAINT `node_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `node_ibfk_2` FOREIGN KEY (`id_parent_node`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
