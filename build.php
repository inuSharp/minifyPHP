<?php

/*

 cd C:\Users\sg\prj\minifyPHP && php build.php

# release
cd C:\Users\sg\prj\minifyPHP\release
php -S 0.0.0.0:8081
echo "done" &&

http://localhost:8081/

*/

// https://github.com/searchturbine/phpwee-php-minifier
require_once("libs/JsMin/JsMin.php");
require_once("src/php/File.php");

function minifyCss(string $css): string
{
    // 改行とタブ、複数スペースを削除
    $css = preg_replace('/\s+/', ' ', $css);
    // 不要なスペースを削除（例えば { の前後など）
    $css = preg_replace('/\s*([{};:,])\s*/', '$1', $css);
    // 最後に残った不要なスペースを削除
    $css = trim($css);

    return $css;
}

// echo minifyCss($css);

function simpleHtmlMinify(string $html): string
{
    // 改行とタブを削除
    $html = str_replace(["\n", "\r", "\t"], '', $html);

    // 複数スペースを1つにまとめる
    $html = preg_replace('/ {2,}/', ' ', $html);

    return $html;
}



// 先頭に<?phpは必須。ないとminifyされない
$index = file_get_contents('index.php');
$css = minifyCss(file_get_contents('src/css.txt'));

$js = file_get_contents('src/js/App.js');
$jsFiles = glob('src/js/*.js');
foreach ($jsFiles as $jsFile) {
    if ($jsFile === 'src/js/App.js') {
        continue;
    }
    $js .= file_get_contents($jsFile) . "\n";
}
$js .= 'window.setTimeout(() => { App.start(); }, 100);';

// php
$php = '';
$requires = explode("\n", trim(file_get_contents('src/php/framework_requirements.txt')));
foreach ($requires as $r) {
    $php .= ltrim(file_get_contents(trim($r)), '<?php');
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
    $php .= ltrim(file_get_contents($featureFile), '<?php');
}

// HtmlMin::minifyすると@Cが消えるので先にstyleを置き換え
$html = file_get_contents('src/html/layout.txt');
$bef = ['@C', '@J'];
$aft = [$css, $js];
while (true) {
    if (preg_match_all('/@html\(.*?\)/s', $html, $matches)) {
        foreach ($matches[0] as $hit) {
            $fileName = str_replace(['@html(', ')'], '', $hit);
            $filePath = 'src/html/' . $fileName . '.txt';
            if (file_exists($filePath)) {
                $bef[] = $hit;
                $aft[] = file_get_contents($filePath);
            } else {
                echo 'not found:' . $filePath . "\n";
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
$html = str_replace(['@C'], [$css], $html);
$html = simpleHtmlMinify($html);
$html = str_replace(['@J'], [$js], $html);
$index = str_replace(['@PHP', '@HTML'], [$php, $html], $index);
$index = preg_replace('/\/\/ debug\{.*?\/\/ \}debug/s', '', $index);


// minifyなしでデバッグする場合
// file_put_contents('./release/index.php', $index);


// minifyする
file_put_contents('minify.txt', $index);
exec('php -w ' . 'minify.txt', $output);
$outputText = implode("\n", $output);
unlink('minify.txt');
file_put_contents('./release/index.php', $outputText);

