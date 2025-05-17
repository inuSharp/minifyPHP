<?php

$requiredParsedown = false;
$markdown = null;
function parseMarkdown($text)
{
    global $requiredParsedown, $markdown;

    if (!$requiredParsedown) {
        $requiredParsedown = true;
    }

    if (!$markdown) {
        $markdown = new Parsedown();
    }
    return $markdown->text($text);
}

