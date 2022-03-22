-- Generation Time: Mar 22, 2022 at 01:46 AM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

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
  `can_delete` enum('YES','NO') DEFAULT 'NO',
  `only_owner` enum('NO','YES') NOT NULL DEFAULT 'NO'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `acl`
--

INSERT INTO `acl` (`id`, `id_role`, `id_module`, `can_read`, `can_insert`, `can_update`, `can_delete`, `only_owner`) VALUES
(1, 1, NULL, 'YES', 'YES', 'YES', 'YES', 'NO'),
(2, NULL, 1, 'YES', 'YES', 'YES', 'YES', 'YES'),
(4, NULL, 2, 'YES', 'NO', 'NO', 'NO', 'NO'),
(5, 101, 2, 'YES', 'YES', 'YES', 'YES', 'NO');

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `val` varchar(250) NOT NULL,
  `description` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `name`, `val`, `description`) VALUES
(1, 'HOMEPAGE_RELATIVE_URL', '/page/1', 'The homepage relative url'),
(2, 'WEBSITE_OWNER', 'DressApi', '');

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
-- Table structure for table `document`
--

CREATE TABLE `document` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` varchar(255) NOT NULL,
  `extension` varchar(20) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `document`
--

INSERT INTO `document` (`id`, `name`, `description`, `extension`, `filename`, `url`, `creation_date`, `id_user`) VALUES
(3, 'Electro', '', 'jpg', '1636638666_img_20181130_180133.jpg', 'https://dressapi.com', '2021-11-11 14:52:55', 0);

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int(11) NOT NULL,
  `title` varchar(60) NOT NULL,
  `abstract` varchar(250) NOT NULL,
  `body` text NOT NULL,
  `date` date NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `img` varchar(80) NOT NULL,
  `site` varchar(120) NOT NULL,
  `url` varchar(300) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` varchar(300) NOT NULL,
  `answer` text NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `priority` int(11) NOT NULL DEFAULT 1000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE `module` (
  `id` int(11) NOT NULL,
  `name` varchar(80) NOT NULL,
  `tablename` varchar(40) NOT NULL,
  `title` varchar(65) NOT NULL,
  `description` varchar(160) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `module`
--

INSERT INTO `module` (`id`, `name`, `tablename`, `title`, `description`) VALUES
(1, 'Sign', 'user', 'Sign', 'Login, Logout, Subscription, Unsubscription'),
(2, 'Pages', 'page', 'Page Module', ''),
(3, 'News', 'news', 'News', ''),
(4, 'Events', 'event', 'Events', ''),
(5, 'Faq', 'faq', 'Faq', ''),
(6, 'Documents', 'document', 'Documents', 'List of Documents');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(60) NOT NULL,
  `abstract` varchar(250) NOT NULL,
  `body` text NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `img` varchar(80) NOT NULL,
  `creation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `abstract`, `body`, `visible`, `status`, `img`, `creation_date`, `id_user`) VALUES
(3, 'New Titlefh 56', 'sfhj', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'yes', 'public', '1636638573_img_20181130_180150.jpg', '2021-11-11 14:56:13', 0);

-- --------------------------------------------------------

--
-- Table structure for table `node`
--

CREATE TABLE `node` (
  `id` int(11) NOT NULL,
  `id_nodetype` int(11) NOT NULL DEFAULT 11,
  `label` varchar(40) NOT NULL,
  `title` varchar(180) NOT NULL,
  `body` text NOT NULL,
  `description` varchar(160) NOT NULL,
  `visible` enum('no','yes') NOT NULL DEFAULT 'no',
  `status` enum('draft','reserved','public') NOT NULL DEFAULT 'draft',
  `creation_date` date NOT NULL DEFAULT current_timestamp(),
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `node`
--

INSERT INTO `node` (`id`, `id_nodetype`, `label`, `title`, `body`, `description`, `visible`, `status`, `creation_date`, `id_user`) VALUES
(1, 11, 'HOME', 'Welcome to DressApi: the new ORM REST API', 'The name \"Dress\" means it \"dress\" up your database, substantially it provides a quick REST API, to your db schema. \nORM means Object-relational mapping and DressApi maps your database dynamically. Although it is structured as an MVC (Model, View, Controller) it does not need to define a model for each table in the DB but if it automatically reads it from the DB. \nThe most obvious advantage is that if the data structure changes over time, even significantly, the model fits automatically without touching a line of your code.', 'Example of use DressApi', 'yes', 'draft', '2021-01-13', 2),
(2, 1, 'Experience', 'DressApi is new but contains long experience inside', 'I have a very long experience in programming with various languages, for the web I have always preferred PHP.\nIn about twenty years of developing web applications, I have always developed and used a personal framework that adopts the dynamic ORM logic and has evolved over time. Now a large part of the code has been rewritten from scratch in the most modern view of the REST API but the idea has remained the same and the experience has certainly allowed to create a solid and functional platform.', '', 'no', 'reserved', '2021-01-15', 101),
(3, 1, 'Test', 'title test', 'I have a very long experience in programming with various languages, for the web I have always preferred PHP.\nIn about twenty years of developing web applications, I have always developed and used a personal framework that adopts the dynamic ORM logic and has evolved over time. Now a large part of the code has been rewritten from scratch in the most modern view of the REST API but the idea has remained the same and the experience has certainly allowed to create a solid and functional platform.', 'Title Test OK!', 'yes', 'reserved', '2021-01-15', 2);

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
-- Table structure for table `route`
--

CREATE TABLE `route` (
  `id` int(11) NOT NULL,
  `origin_path` varchar(512) NOT NULL,
  `destination_path` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `route`
--

INSERT INTO `route` (`id`, `origin_path`, `destination_path`) VALUES
(1, 'login', 'sign/login-form'),
(2, 'logout', 'sign/logout');

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
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document`
--
ALTER TABLE `document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `node`
--
ALTER TABLE `node`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_nodetype` (`id_nodetype`);

--
-- Indexes for table `nodetype`
--
ALTER TABLE `nodetype`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `route`
--
ALTER TABLE `route`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `document`
--
ALTER TABLE `document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `module`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `node`
--
ALTER TABLE `node`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `route`
--
ALTER TABLE `route`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  ADD CONSTRAINT `node_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
