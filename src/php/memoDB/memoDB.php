<?php

// function getMemoPath() {
//     $settingFile = PRJ_DIR . '/' . 'memo_path.txt';
//     if (!file_exists($settingFile)) {
//         exit('not found memo_path.txt');
//     }
//     return  trim(file_get_contents($settingFile));
// }

function getMemoTag($tag) {
    // $text = file_get_contents(getMemoPath());
    $text = file_get_contents(PRJ_DIR . '/memo.txt');
    $ret = '';
    $reg = '/<'.$tag.'>.*?<\/'.$tag.'>/s';
    if (preg_match($reg, $text, $match)) {
        $ret = trim(str_replace(["<{$tag}>", "</{$tag}>"], '', $match[0]));
    }
    return $ret;
}

function getConfig() {
    static $config;
    if (is_null($config)) {
        $config = parse_ini_string(getMemoTag('CONFIG'), true);
    }
    return $config;
}



function getMemoPrjInfo() {
}

function getCurrentPrjName() {
    static $cp;
    if (is_null($cp)) {
        $settingFile = GR_DIR . '/' . 'current_prj.txt';
        $cp = trim(file_get_contents($settingFile));
        if ($cp === '') {
            exit('please select project.');
        }
    }
    return $cp;
}

function getPrjConfig()
{
    $prjName = getCurrentPrjName();
    $config = getConfig();
    return $config['prj.' . $prjName];
}

function memoDBTest() {
    var_dump(getConfig());
}

// phpの配列定義フォーマットのテキストを配列に変換する
function convertArrayDefineText(string $text): array
{
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $result = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '') continue;

        // コメント（//）の前まで取得
        $line = preg_split('/\s*\/\/.*/', $line)[0];
        $line = trim($line);

        // 先頭と末尾のクォートを除去（' または "）
        $line = preg_replace('/^["\'](.*)["\']$/', '$1', $line);

        if ($line !== '') {
            $result[] = $line;
        }
    }

    return $result;
}

function getMemoIndexes() {
    $text = file_get_contents('memo.txt');
    $sections = explode('======================================================================', $text);

    $searchWord = 'work done_commit_pr';
    if (trim($searchWord) === '') {
        exit('検索文字を指定してください');
    }

    $searchWords = array_filter(explode(' ', $searchWord), function($w) {
        return trim($w) !== '';
    });

    $allTags = [];
    foreach ($sections as $section) {
        if (preg_match('/.*?Tags:.*?\n/s', $section, $match)) {
            $tagStr = str_replace('Tags:', '', trim($match[0]));
            $tags = array_filter(explode(' ', $tagStr), function($w) {
                return trim($w) !== '';
            });
            $allTags = array_merge($allTags, $tags);
        }
    }

    $allTags = array_unique($allTags);
    $html = '';
    foreach ($allTags as $one) {
        $html .= '<label><input name="checkIndex" type="checkbox" value="' . $one . '" onchange="Memo.changeIndex()">' . $one . '</label><br>';
    }
    return $html;
}

function searchMemo() {
    $text = file_get_contents('memo.txt');
    $sections = explode('======================================================================', $text);

    $searchWord = 'work done_commit_pr';
    if (trim($searchWord) === '') {
        exit('検索文字を指定してください');
    }

    $searchWords = array_filter(explode(' ', $searchWord), function($w) {
        return trim($w) !== '';
    });

    $ret = '';
    foreach ($sections as $section) {
        if (preg_match('/.*?Tags:.*?\n/s', $section, $match)) {
            $tagStr = str_replace('Tags:', '', trim($match[0]));
            $tags = array_filter(explode(' ', $tagStr), function($w) {
                return trim($w) !== '';
            });
            // var_dump($tags);
            if (count(array_diff($searchWords, $tags)) === 0) {
                $lineNum = getMemoLineNumber(trim($match[0]));
                $body = trim(str_replace(trim($match[0]), '', $section));
                $ret .= '<h2>' . $match[0]. ' (' . $lineNum . ")</h2>\n";
                $ret .= '<div class="section">' . parseMarkdown($body). "</div>\n";
            }
        }
    }
    return $ret;
}

function getMemoLineNumber(string $search): int
{
    $filename = PRJ_DIR . '/memo.txt';
    if (!file_exists($filename)) {
        return 0;
    }

    $file = fopen($filename, 'r');
    if (!$file) {
        return 0;
    }

    $lineNumber = 0;
    while (($line = fgets($file)) !== false) {
        $lineNumber++;
        if (strpos($line, $search) !== false) {
            fclose($file);
            return $lineNumber;
        }
    }

    fclose($file);
    return 0;
}





