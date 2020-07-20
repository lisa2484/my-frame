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

-- 傾印  資料表 laravel.action_log 結構
CREATE TABLE IF NOT EXISTS `action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `user` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '管理者帳號',
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作時間',
  `action` mediumtext NOT NULL COMMENT '執行動作',
  `remark` varchar(50) NOT NULL COMMENT '操作功能',
  `fun` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '操作頁面',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `user` (`user`) USING BTREE,
  KEY `fun` (`fun`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='事件紀錄';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.authority 結構
CREATE TABLE IF NOT EXISTS `authority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `authority_name` varchar(50) NOT NULL,
  `authority` longtext NOT NULL,
  `updater` varchar(50) NOT NULL,
  `update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_ip` varchar(50) NOT NULL,
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='權限表';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.autorepmsg 結構
CREATE TABLE IF NOT EXISTS `autorepmsg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL COMMENT '標題',
  `keyword` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '關鍵字',
  `msg` varchar(400) NOT NULL COMMENT '回復訊息',
  `start_d` date NOT NULL DEFAULT '0000-00-00' COMMENT '開始日期',
  `end_d` date NOT NULL DEFAULT '0000-00-00' COMMENT '結束日期',
  `start_t` time NOT NULL DEFAULT '00:00:00' COMMENT '開始時間',
  `end_t` time NOT NULL DEFAULT '00:00:00' COMMENT '結束時間',
  `time_limit` int(1) NOT NULL DEFAULT '1' COMMENT '2:只限制時間  1:限制日期與時間 0:不限制時間',
  `onf` int(1) NOT NULL DEFAULT '1' COMMENT '1:開啟 0:關閉',
  `creator` varchar(50) NOT NULL COMMENT '建立者',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `create_ip` varchar(50) NOT NULL COMMENT '建立IP',
  `updater` varchar(50) NOT NULL COMMENT '更新者',
  `update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新時間',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='自動回覆訊息';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.autoservicerep 結構
CREATE TABLE IF NOT EXISTS `autoservicerep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父ID',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '標題',
  `msg` varchar(400) NOT NULL DEFAULT '' COMMENT '訊息內容',
  `onf` int(1) NOT NULL DEFAULT '1' COMMENT '1:有效 0:無效',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `creator` varchar(50) NOT NULL COMMENT '建立者',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `create_ip` varchar(50) NOT NULL COMMENT '建立者IP',
  `updater` varchar(50) NOT NULL COMMENT '更改者',
  `update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更改時間',
  `update_ip` varchar(50) NOT NULL COMMENT '更改者IP',
  `is_del` int(1) NOT NULL DEFAULT '0' COMMENT '1:刪除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='智能客服';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.chatroom_menu 結構
CREATE TABLE IF NOT EXISTS `chatroom_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '標題',
  `url` varchar(200) NOT NULL COMMENT '連結',
  `sort` int(11) NOT NULL COMMENT '排序',
  `filename` varchar(50) NOT NULL COMMENT '圖示檔案',
  `creator` varchar(50) NOT NULL COMMENT '建立者',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `create_ip` varchar(50) NOT NULL COMMENT '建立IP',
  `updater` varchar(50) NOT NULL COMMENT '更新者',
  `update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新時間',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  `is_del` int(1) NOT NULL DEFAULT '0' COMMENT '1:刪除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='聊天室MENU配置';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.ipwhitelist 結構
CREATE TABLE IF NOT EXISTS `ipwhitelist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(50) NOT NULL,
  `onf` int(1) NOT NULL DEFAULT '1' COMMENT '0:關閉 1:開啟',
  `creator` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '建立人',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `create_ip` varchar(50) NOT NULL COMMENT '建立IP',
  `updater` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '最後修改人',
  `update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '最後修改時間',
  `update_ip` varchar(50) NOT NULL COMMENT '修改IP',
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='白名單';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.login_log 結構
CREATE TABLE IF NOT EXISTS `login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '帳號',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '暱稱(帳號暱稱)',
  `authority_name` varchar(50) NOT NULL DEFAULT '' COMMENT '權限名稱',
  `login_date` datetime NOT NULL COMMENT '登入時間',
  `session_id` varchar(50) NOT NULL COMMENT 'session_id',
  `headers` mediumtext NOT NULL COMMENT 'header',
  `success` int(1) NOT NULL DEFAULT '0' COMMENT '1:登入成功 0:登入失敗',
  `ip` varchar(50) NOT NULL COMMENT '登入IP',
  PRIMARY KEY (`id`),
  KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='登入紀錄\r\n非後台可移除資料';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.menu 結構
CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `belong` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `url` varchar(50) DEFAULT '',
  `seq` int(10) unsigned NOT NULL DEFAULT '0',
  `icon` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.messages_dtl 結構
CREATE TABLE IF NOT EXISTS `messages_dtl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `main_id` int(11) NOT NULL COMMENT '主檔ID',
  `msg_from` int(1) NOT NULL COMMENT '1:會員 2:客服 3:bot 4:system',
  `content` mediumtext NOT NULL COMMENT '內容',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT '0:string 1:json',
  `filename` varchar(100) NOT NULL COMMENT '上傳檔案名稱',
  `time` int(15) NOT NULL COMMENT '時戳',
  `service_act` varchar(50) NOT NULL DEFAULT '' COMMENT '客服帳號',
  `service_name` varchar(50) NOT NULL DEFAULT '' COMMENT '客服暱稱',
  `service_img` varchar(50) DEFAULT '' COMMENT '客服頭像',
  PRIMARY KEY (`id`),
  KEY `main_id` (`main_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='對話訊息明細\r\n非後台可修改可移除資料';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.messages_main 結構
CREATE TABLE IF NOT EXISTS `messages_main` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '狀態 0:等待對話 1:處理中 2:處理完畢 3:垃圾訊息',
  `member_id` varchar(50) NOT NULL DEFAULT '' COMMENT '會員帳號',
  `member_name` varchar(50) NOT NULL DEFAULT '' COMMENT '會員姓名',
  `member_env` varchar(10) NOT NULL COMMENT '會員使用環境',
  `member_ip` varchar(50) NOT NULL COMMENT '會員IP',
  `member_loc` varchar(50) NOT NULL DEFAULT '' COMMENT '會員地區',
  `member_from` varchar(50) NOT NULL COMMENT '來源網站',
  `user_id` varchar(50) NOT NULL COMMENT '客服ID',
  `start_time` int(15) NOT NULL COMMENT '開始時戳',
  `end_time` int(15) NOT NULL COMMENT '結束時戳',
  `rep_len` int(15) NOT NULL DEFAULT '0' COMMENT '首次回應時戳',
  `circle_count` int(15) NOT NULL DEFAULT '0' COMMENT '訊息數量',
  `evaluation` int(11) NOT NULL DEFAULT '0' COMMENT '評價',
  `unread` int(11) NOT NULL DEFAULT '0' COMMENT '未讀訊息量',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='對話訊息主檔\r\n非後台可刪除資料';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.searchautorep 結構
CREATE TABLE IF NOT EXISTS `searchautorep` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` varchar(400) NOT NULL,
  `onf` int(1) NOT NULL DEFAULT '1' COMMENT '1:開啟 0:關閉',
  `creator` varchar(50) NOT NULL COMMENT '建立者',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `create_ip` varchar(50) NOT NULL COMMENT '建立IP',
  `updater` varchar(50) NOT NULL COMMENT '更新者',
  `update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新時間',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='查無關鍵字回覆';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.user 結構
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '帳號',
  `password` varchar(50) NOT NULL COMMENT '密碼',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '暱稱',
  `img_name` varchar(50) NOT NULL DEFAULT '' COMMENT '圖片名稱',
  `authority` int(11) NOT NULL COMMENT '權限',
  `chg_pw_time` int(15) NOT NULL DEFAULT '0' COMMENT '密碼重設時戳',
  `creator` varchar(50) NOT NULL COMMENT '建立者',
  `create_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `create_ip` varchar(50) NOT NULL COMMENT '建立IP',
  `updater` varchar(50) NOT NULL COMMENT '更新者',
  `update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新時間',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.usermsg 結構
CREATE TABLE IF NOT EXISTS `usermsg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'user_id',
  `tag` varchar(50) NOT NULL COMMENT '標籤',
  `msg` varchar(400) NOT NULL COMMENT '訊息內容',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `creator` varchar(50) NOT NULL COMMENT '建立者',
  `create_dt` datetime NOT NULL COMMENT '建立時間',
  `create_ip` varchar(50) NOT NULL COMMENT '建立IP',
  `updater` varchar(50) NOT NULL COMMENT '更新者',
  `update_dt` datetime NOT NULL COMMENT '更新時間',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  `is_del` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客服常用語';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.user_online_status 結構
CREATE TABLE IF NOT EXISTS `user_online_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT 'userID',
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '上線狀態 0:off 1:on',
  `last_online_time` int(15) unsigned NOT NULL DEFAULT '0' COMMENT '最後上線時戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客服上線狀態';

-- 取消選取資料匯出。

-- 傾印  資料表 laravel.web_set 結構
CREATE TABLE IF NOT EXISTS `web_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `set_key` varchar(50) NOT NULL COMMENT '設定key',
  `value` text NOT NULL,
  `updater` varchar(50) NOT NULL COMMENT '更新人',
  `update_dt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新日期',
  `update_ip` varchar(50) NOT NULL COMMENT '更新IP',
  PRIMARY KEY (`id`),
  UNIQUE KEY `set_key` (`set_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='網站設定';

-- 取消選取資料匯出。

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
