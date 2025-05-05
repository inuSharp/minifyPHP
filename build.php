<?php

/*

 cd C:\Users\sg\prj\minifyPHP && php build.php


*/

// https://github.com/searchturbine/phpwee-php-minifier
require_once("libs/JsMin/JsMin.php");

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
$js = JsMin::minify(file_get_contents('src/js.txt'));

// php
$php = '';
$php .= ltrim(file_get_contents('src/php/apiFunctions.php'), '<?php');

// HtmlMin::minifyすると@Cが消えるので先にstyleを置き換え
$html = file_get_contents('src/html.txt');
$html = str_replace(['@C'], [$css], $html);
$html = simpleHtmlMinify($html);
$html = str_replace(['@J'], [$js], $html);
$index = str_replace(['@PHP', '@HTML'], [$php, $html], $index);
// TODO // debug{ から // }debug までを削除
$index = preg_replace('/\/\/ debug\{.*?\/\/ \}debug/s', '', $index);


// minifyなしでデバッグする場合
// file_put_contents('./release/index.php', $index);


// minifyする
file_put_contents('minify.txt', $index);
exec('php -w ' . 'minify.txt', $output);
$outputText = implode("\n", $output);
unlink('minify.txt');
file_put_contents('./release/index.php', $outputText);

