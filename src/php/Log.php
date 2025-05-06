<?php

class Log
{
    public static $logDir = '';
    public static $rotated  = false;
    public static function getFilePath()
    {
        if (self::$logDir == '') {
            self::$logDir = './log';
        }
        if (!file_exists(self::$logDir)) {
            echo self::$logDir . "\n";
            if(mkdir(self::$logDir, 0777)){
                //作成したディレクトリのパーミッションを確実に変更
                chmod(self::$logDir, 0777);
            }
        }
        // if (config('DEBUG')) {
            return self::$logDir . '/web.log';
        // } else {
        //     return self::$logDir . '/' . date('Y-m-d') . '.log';
        // }
    }
    public static function access($s)
    {
        self::write('ACCESS', $s, self::getFilePath());
    }
    public static function info($s)
    {
        self::write('INFO  ', $s, self::getFilePath());
    }
    public static function error($s)
    {
        self::write('ERROR ', $s, self::getFilePath());
    }
    public static function debug($s)
    {
        // if (config('DEVELOP_MODE')) {
            self::write('DEBUG ', $s, self::getFilePath());
        //}
    }
    public static function write($tag, $s, $path)
    {
        self::rotate();
        if (is_array($s) || is_object($s)) {
            $s = json_encode($s);
        }
        $s = '[' . date('Y-m-d_H:i:s') . '] ' . $s;
        file_put_contents($path, $tag . ' : ' . $s . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
    private static function rotate()
    {
        //if (config('DEBUG')) {
            return;
        //}
        // 1リクエストで1回
        if (self::$rotated) {
            return;
        }
        self::$rotated = true;

        // 日をまたぐ間はろーてーとしない
        $nowTime = date('His');
        $time = intval(date('His'));
        // 0:05:00以下もしくは 23:55:00以上
        if ($time < 500 || 235500 < $time) {
            return;
        }

        foreach(glob(self::$logDir.'/*.log') as $file){
            $logdate = str_replace(['.log','-'], '', basename($file));
            // 今日のログならしない
            if ($logdate == date('Ymd')) {
                continue;
            }
            try {
                $ym      = substr($logdate,0,6);
                $backUpDir = self::$logDir.'/'.$ym;
                if (!file_exists($backUpDir)) {
                    if(mkdir($backUpDir, 0777)){
                        chmod($backUpDir, 0777);
                    }
                }
                rename($file, $backUpDir . '/' . basename($file));
            } catch(\Exception $e) {
                file_put_contents(dataDir(). 'log_error.txt', 'rotate error');
            }
        }
    }
}

