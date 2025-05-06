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

// debug{

$css = file_get_contents('src/css.txt');
$js = file_get_contents('src/js.txt');

$requires = explode("\n", trim(file_get_contents('src/php/framework_requirements.txt')));
foreach ($requires as $r) {
    require_once(trim($r));
}

// $commandFiles = getFileLists('src/php/Commands');
// foreach ($commandFiles as $commandFile) {
//     require_once($commandFile);
// }

$html = file_get_contents('src/html.txt');
$html = str_replace(['@C'], [$css], $html);
$html = str_replace(['@J'], [$js], $html);

// }debug

setWebDefines();

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

