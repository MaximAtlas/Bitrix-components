<?php

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$APPLICATION->IncludeComponent(    "custom:task.priority",
    ".default",
    []);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");

?>
