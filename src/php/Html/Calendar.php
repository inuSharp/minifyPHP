<?php

function generateCalendarHtml($year = null, $month = null)
{

    // 年月が指定されていなければ現在の年月を使う
    $year = $year ?? date('Y');
    $month = $month ?? date('n');

    $firstDayOfWeek = date('w', strtotime("$year-$month-01"));
    $daysInMonth = date('t', strtotime("$year-$month-01"));

    $html = "<table border='1' style='border-collapse: collapse; text-align: center;'>";
    $html .= "<caption>{$year}年{$month}月</caption>";
    $html .= "<tr><th>日</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th><th>土</th></tr><tr>";

    // 空白セル
    for ($i = 0; $i < $firstDayOfWeek; $i++) {
        $html .= "<td></td>";
    }

    // 日付セル
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $html .= "<td>$day</td>";

        if (($firstDayOfWeek + $day) % 7 == 0) {
            $html .= "</tr><tr>";
        }
    }

    // 残りの空白セル
    $remaining = (7 - ($firstDayOfWeek + $daysInMonth) % 7) % 7;
    for ($i = 0; $i < $remaining; $i++) {
        $html .= "<td></td>";
    }

    $html .= "</tr></table>";

    return $html;
}

