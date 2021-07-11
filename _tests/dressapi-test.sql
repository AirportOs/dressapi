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
CREATE DATABASE IF NOT EXISTS `dressapi-test` CHARSET=utf8;

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
  `email` varchar(128) DEFAULT NULL,
  `status` enum('Subscribed','Verified','Refused') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Subscribed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dump dei dati per la tabella `user`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `surname` varchar(80) NOT NULL,
  `address` varchar(160) CHARACTER SET utf8 NOT NULL,
  `zip_code` varchar(10) CHARACTER SET utf8 NOT NULL,
  `city` varchar(80) CHARACTER SET utf8 NOT NULL,
  `province` varchar(30) NOT NULL,
  `email` varchar(60) CHARACTER SET utf8 NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Struttura della tabella `user_role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `description` int(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
        (3, 101, 3);

-- --------------------------------------------------------


--
-- Struttura della tabella `controller_role_permission`
--

CREATE TABLE `controller_role_permission` ( `id` int(11) NOT NULL, `controller` varchar(80) NOT NULL COMMENT 'contains the module or table name managed by the base module', `permission` varchar(30) NOT NULL, `id_role` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=utf8
