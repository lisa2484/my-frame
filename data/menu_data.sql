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

-- 正在傾印表格  laravel.menu 的資料：~22 rows (近似值)
/*!40000 ALTER TABLE `menu` DISABLE KEYS */;
INSERT INTO `menu` (`id`, `belong`, `name`, `url`, `seq`, `icon`) VALUES
	(1, 0, '仪表板', 'dashboard', 0, ''),
	(2, 0, '网站配置', 'webset', 1, ''),
	(3, 0, '版面配置', '', 2, ''),
	(4, 3, '客户画面配置', 'chatroom_set', 0, ''),
	(5, 3, 'menu配置', 'chatroom_menu', 1, ''),
	(6, 0, '客户对话查询', 'customermsg', 3, ''),
	(7, 0, '开始聊天', '', 4, ''),
	(8, 0, '智能客服', '', 5, ''),
	(9, 8, '基本设置', 'botset', 0, ''),
	(10, 8, '欢迎讯息配置', 'botwelcome', 1, ''),
	(11, 8, '自动回应讯息配置', 'autorepmsg', 2, ''),
	(12, 8, '智能客服讯息配置', 'autoservicerep', 3, ''),
	(13, 0, '系统设置', '', 6, ''),
	(14, 13, '帐号管理', 'userset', 0, ''),
	(15, 13, '权限管理', 'authority', 1, ''),
	(16, 13, 'IP白名单', 'whitelist', 2, ''),
	(17, 13, '操作纪录', 'actionlog', 3, ''),
	(18, 13, '登入纪录', 'loginlog', 4, ''),
	(19, 8, '查无关键字讯息配置', 'searchautorep', 4, ''),
	(20, 0, '个人设置', 'usermsg', 0, ''),
	(21, 0, '修改密码', 'userpwd', 0, ''),
	(22, 7, '客服聊天室功能', 'chat_service', 0, '');
/*!40000 ALTER TABLE `menu` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
