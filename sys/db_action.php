<?php

namespace app\sys;

use mysqli;
use Throwable;

class DB extends db_connect
{
    private static $dbcon;
    /** @var int */
    private static $num_rows = 0;
    /** @var string */
    private static $lastSqlCode = '';
    /** @var bool */
    private static $begin_transaction = false;

    private const error_log_url = __DIR__ . '/log';
    private const error_log_file_name = 'mysqlerr';

    static function select(string $SQLCode)
    {
        self::$lastSqlCode = $SQLCode;
        $quest = self::$dbcon->query($SQLCode);
        if ($quest == false || self::$dbcon->errno) {
            self::errorLog(self::$dbcon->error);
            self::setRollback();
            return [];
        }
        self::$num_rows = $quest->num_rows;
        return mysqli_fetch_all($quest, MYSQLI_ASSOC);
    }

    static function DBCode(string $SQLCode)
    {
        self::$lastSqlCode = $SQLCode;
        $request = true;
        if (!self::$dbcon->query($SQLCode) || self::$dbcon->errno) {
            $request = false;
            self::errorLog(self::$dbcon->error);
            self::setRollback();
        }
        return $request;
    }

    static function setBeginTransaction()
    {
        self::$dbcon->begin_transaction();
        self::$begin_transaction = true;
    }

    static function setRollback()
    {
        if (self::$begin_transaction) {
            self::$dbcon->rollback();
            self::$begin_transaction = false;
            die('资料库更新失败');
        }
    }

    static function setCommit()
    {
        if (self::$begin_transaction) {
            self::$dbcon->commit();
            self::$begin_transaction = false;
        }
    }

    static function getNumRows()
    {
        return self::$num_rows;
    }

    static function getAffectedRows(): int
    {
        return self::$dbcon->affected_rows;
    }

    static function getInsertId(): int
    {
        return self::$dbcon->insert_id;
    }

    static function dbConnect()
    {
        return self::$dbcon;
    }

    static function getLastAction()
    {
        return self::$lastSqlCode;
    }

    static function dbClose()
    {
        self::$dbcon->close();
    }

    static function getRealEscapeString(string $str)
    {
        return mysqli_real_escape_string(self::$dbcon, $str);
    }

    static function dbCon(int $set)
    {
        if (!isset(self::$dbcon)) {
            $dbset = self::getConnectData($set);
            try {
                self::$dbcon = new mysqli($dbset[0], $dbset[1], $dbset[2], $dbset[3]);
            } catch (Throwable $e) {
                die('资料库连线失败');
            }
            mysqli_query(self::$dbcon, "SET NAMES utf8");
            mysqli_query(self::$dbcon, "SET CHARACTER_SET_CLIENT=utf8");
            mysqli_query(self::$dbcon, "SET CHARACTER_SET_RESULTS=utf8");
        }
    }

    static private function errorLog(string $e)
    {
        if (!is_dir(self::error_log_url)) mkdir(self::error_log_url);
        $f = fopen(self::error_log_url . '/' . self::error_log_file_name . date('YmdO'), 'a');
        fwrite($f, date('His') . ' mysqlerror:' . $e . ',sqlcode:' . self::$lastSqlCode . PHP_EOL);
        fclose($f);
    }
}
