<?php


/*

# dev
cd C:\Users\sg\prj\minifyPHP
php -S 0.0.0.0:8080
echo "done" &&

# release
cd C:\Users\sg\prj\minifyPHP\release
php -S 0.0.0.0:8080
echo "done" &&


http://localhost:8080/
*/


// build用
$html = <<<'HTML'
@HTML
HTML;

// @PHP
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

function getTopFolders($path)
{
    $folders = [];
    foreach (scandir($path) as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        
        if (is_dir($path . DIRECTORY_SEPARATOR . $item)) {
            $folders[] = $item;
        }
    }
    return $folders;
}
function getFileLists($folder, $filterWord = '', $exceptPregs = [], $order = 0, &$maxCnt = -1, &$startCnt = 0, &$cnt = 0)
{
    if (!file_exists($folder)) {
        throw new \Exception("Not found folder: " . $folder);
    }
    if (is_file($folder)) { return [$folder]; }
    $files = scandir($folder, $order); // 1:降順
    $lists = [];
    foreach($files as $file) {
        if (
            $file === ".." ||
            $file === "." ||
            $file === ".svn" ||
            $file === ".git" ||
            $file === "vendor"  ||
            $file === "node_modules"
        ) {continue;}

        if (!str_starts_with($file, 'cache') && $filterWord !== '' && !preg_match_all("/.*?{$filterWord}.*?/s", $file, $match)) {
            continue;
        }

        $isExcept = false;
        foreach ($exceptPregs as $pregText) {
            if (trim($pregText) === '') { throw new \Exception('無効な正規表現'); }
            if (preg_match($pregText, $file)) {
                // echo '除外' . $file . "\n";
                $isExcept = true;
                break;
            }
        }
        if ($isExcept) { continue; }

        if (is_dir($folder.DIRECTORY_SEPARATOR.$file)) {
            $lists = array_merge($lists, getFileLists($folder. DIRECTORY_SEPARATOR .$file, $filterWord, $exceptPregs, $order, $maxCnt, $startCnt, $cnt));
        } else {
            $cnt++;
            if ($startCnt <= $cnt) {
                $lists[] = $folder.DIRECTORY_SEPARATOR.$file;
            }
        }
        if ($maxCnt != -1 && $maxCnt == $cnt) { break; }
    }
    return $lists;
}

// debug{

$css = file_get_contents('src/css.txt');
$js = file_get_contents('src/js.txt');

require_once('src/php/apiFunctions.php');
// $commandFiles = getFileLists('src/php/Commands');
// foreach ($commandFiles as $commandFile) {
//     require_once($commandFile);
// }

$html = file_get_contents('src/html.txt');
$html = str_replace(['@C'], [$css], $html);
$html = str_replace(['@J'], [$js], $html);

// }debug

$protocol   = isset($_SERVER["https"]) ? 'https' : 'http';
$domain     =  $protocol . '://' . $_SERVER['HTTP_HOST'];
$requestUrl = $domain . $_SERVER['REQUEST_URI'];
$route      = ltrim(str_replace($domain, '', $requestUrl), '/');
if ($route == '') { $route = 'index'; }
if ($route === 'favicon.ico') { echo 'test'; exit(); }
define("WEB_ROOT"      , $domain);
define("REQUEST_URL"   , $requestUrl);
define("REQUEST_ROUTE" , $route);
define("REQUEST_METHOD", $_SERVER["REQUEST_METHOD"]);
$path = explode('?', REQUEST_ROUTE)[0];
$paths = explode('/', $path);
define("REQUEST_ROUTE_TOP" , $paths[0]);

if (REQUEST_ROUTE_TOP === 'api') {
    array_shift($paths);
    define("API_ROUTE" , $paths[0]);
    array_shift($paths);
    define("CONTROLLER_METHOD" , $paths[0]);
} else {
    define("API_ROUTE" , '');
}

if (REQUEST_ROUTE_TOP !== 'api') {
    $html = str_replace(['@WEB_ROOT'], [WEB_ROOT], $html);
    echo $html;
    exit();
}

// api: /api/{class名}/method名
//      /api/command/run {command: 'php -m'}
$ret = '';
switch (API_ROUTE) {
    case 'api/test':
        break;
}

responseJson(['result' => $ret]);

