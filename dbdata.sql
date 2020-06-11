-- --------------------------------------------------------
-- 主機:                           127.0.0.1
-- 伺服器版本:                        10.1.34-MariaDB - mariadb.org binary distribution
-- 伺服器作業系統:                      Win32
-- HeidiSQL 版本:                  10.3.0.5771
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 傾印  資料表 laravel.authority 結構
CREATE TABLE IF NOT EXISTS `authority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `authority_name` varchar(50) NOT NULL DEFAULT '0',
  `authority` longtext,
  `is_del` int(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='權限表';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.bg_user 結構
CREATE TABLE IF NOT EXISTS `bg_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) NOT NULL DEFAULT '',
  `authority` int(11) NOT NULL DEFAULT '0',
  `account` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '0',
  `password` varchar(50) NOT NULL DEFAULT '0',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `final_login_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.login_log 結構
CREATE TABLE IF NOT EXISTS `login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `session_id` varchar(50) NOT NULL,
  `headers` mediumtext NOT NULL,
  `success` int(1) NOT NULL DEFAULT '0',
  `ip` varchar(50) NOT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `authority_name` varchar(50) DEFAULT NULL,
  `login_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='登入紀錄';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.menu 結構
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `belong` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '0',
  `url` varchar(50) DEFAULT '0',
  `seq` int(10) unsigned DEFAULT '0',
  `icon` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.web_set 結構
CREATE TABLE IF NOT EXISTS `web_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_key` varchar(50) NOT NULL,
  `value` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `set_key` (`set_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='網站設定';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.whitelist 結構
CREATE TABLE IF NOT EXISTS `whitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `creator` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '建立人',
  `creator_name` varchar(50) NOT NULL DEFAULT '' COMMENT '建立人暱稱',
  `creation_date` datetime NOT NULL COMMENT '建立時間',
  `updater` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '最後修改人',
  `updater_name` varchar(50) NOT NULL DEFAULT '' COMMENT '修改人暱稱',
  `update_date` datetime NOT NULL COMMENT '最後修改時間',
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='白名單';

-- 取消選取資料匯出。

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
