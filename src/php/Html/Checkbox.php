<?php

function makeIndexCheckbox($items)
{

    $format = '<label><input type="checkbox"> @LABEL</label>';
    $html = '';

    foreach ($items as $item) {
        $html .= '<div>' . str_replace(['@LABEL'], [$item], $format) . '</div>';
    }


    return $html;
}

