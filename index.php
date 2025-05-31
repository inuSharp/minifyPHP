<?php


/*

# dev
cd C:\Users\sg\prj\minifyPHP
php -S 0.0.0.0:8080
echo "done" &&

http://localhost:8080/
*/

date_default_timezone_set('Asia/Tokyo');
define('PRJ_DIR', dirname(__FILE__));

// build用
$html = <<<'HTML'
@HTML
HTML;

// @PHP

// debug{

$css = file_get_contents('src/css.txt');

$js = file_get_contents('src/js/App.js');
$jsFiles = glob('src/js/*.js');
foreach ($jsFiles as $jsFile) {
    if ($jsFile === 'src/js/App.js') {
        continue;
    }
    $js .= file_get_contents($jsFile) . "\n";
}
$js .= 'window.setTimeout(() => { App.start(); }, 100);';


$requires = explode("\n", trim(file_get_contents('src/php/framework_requirements.txt')));
foreach ($requires as $r) {
    require_once(trim($r));
}
$featureFiles = getFileLists('src/php');
foreach ($featureFiles as $featureFile) {
    $featureFile = str_replace('\\', '/', $featureFile);
    if (in_array($featureFile, $requires)) {
        continue;
    }
    if ($featureFile === 'src/php/framework_requirements.txt') {
        continue;
    }
    require_once($featureFile);
}

$html = file_get_contents('src/html/layout.txt');

$bef = ['@C', '@J'];
$aft = [$css, $js];

while (true) {
    if (preg_match_all('/@html\(.*?\)/s', $html, $matches)) {
        Log::debug($matches);
        foreach ($matches[0] as $hit) {
            $fileName = str_replace(['@html(', ')'], '', $hit);
            $filePath = 'src/html/' . $fileName . '.txt';
            if (file_exists($filePath)) {
                $bef[] = $hit;
                $aft[] = file_get_contents($filePath);
            } else {
                Log::debug('not found:' . $filePath);
            }
        }
    }
    if (count($bef) !== 0) {
        $html = str_replace($bef, $aft, $html);
        $bef = [];
        $aft = [];
    } else {
        break;
    }
}

// }debug

setWebDefines();

if (REQUEST_ROUTE_TOP !== 'api') {
    $html = str_replace(['@WEB_ROOT'], [WEB_ROOT], $html);
    echo $html;
    // echo generateCalendarHtml(); // 今月
    exit();
}

// api: /api/{class名}/method名
//      /api/command/run {command: 'php -m'}
$ret = '';
switch (API_ROUTE) {
    case 'command':
        $ret = print_r(getConfig(), true);
        break;
    case 'memo':
        // /api/memo/index
        if (CONTROLLER_METHOD === 'index') {
            $ret = getMemoIndexes();
        }
        // /api/memo/search?w=
        if (CONTROLLER_METHOD === 'search') {
            $ret = searchMemo();
        }
        break;
    case 'db':
        // /api/db/index
        if (CONTROLLER_METHOD === 'index') {
            $ret = getDBList();
        }
        // /api/db/table_names
        if (CONTROLLER_METHOD === 'table_names') {
            $ret = getDBTableNames();
        }
        // /api/db/table_define
        if (CONTROLLER_METHOD === 'table_define') {
            $ret = getTableDefine();
        }
        break;
}

responseJson(['result' => $ret], 200);

