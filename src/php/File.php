<?php

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

