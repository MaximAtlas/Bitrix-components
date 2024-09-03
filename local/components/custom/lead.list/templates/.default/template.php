<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use Bitrix\Main\Page\Asset;

$APPLICATION->SetTitle('Список лидов');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>


    <?php
    // Подключение CSS-файлов Bootstrap из папки bitrix/css
    Asset::getInstance()->addCss('/bitrix/css/main/bootstrap_v4/bootstrap.min.css');
    ?>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Таблица лидов</h2>
    <form action="/lead_list/update/" method="post">
        <?php
        // CSRF token для защиты
        echo bitrix_sessid_post();
        ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Название</th>
            <th>Дата создания</th>
            <th>Источник</th>
            <th>Ответственный</th>
            <th>Статус</th>
            <th class="text-center">Проверено</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $leads = $arResult['LEADS'];
        foreach ($leads as $lead) {
        echo '<tr>';
            echo '<td>' . htmlspecialchars($lead['TITLE']) . '</td>';
            echo '<td>' . htmlspecialchars($lead['DATE_CREATE']->format('Y-m-d H:i:s')) . '</td>';
            echo '<td>' . htmlspecialchars($lead['SOURCE_ID']) . '</td>';
            echo '<td>' . htmlspecialchars($lead['ASSIGNED_BY_ID']) . '</td>';
            echo '<td>' . htmlspecialchars($lead['STATUS_ID']) . '</td>';

            $checked = $lead['UF_CHECKED'] == 1 ? 'checked' : '';

            echo '<td class="text-center"><input type="checkbox" name="checked_leads[]" value="' . htmlspecialchars($lead['ID']) . '" ' . $checked . '></td>';
            echo '</tr>';
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5"></td>
            <td class="text-center">
                <button type="submit" class="btn btn-primary">Обновить</button>
            </td>
        </tr>
        </tfoot>
    </table>

    </form>
</div>