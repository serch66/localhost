<?php

const DIRECTION_BLACKLIST = [
    3,
    4,
    5,
    6,
];

foreach ($arResult['ROWS'] as $index => $row) {
    $direction = (int)$row['data']['AT_DIRECTION'];
    if (in_array($direction, DIRECTION_BLACKLIST)) {
        unset($arResult['ROWS'][$index]);
    }
}
