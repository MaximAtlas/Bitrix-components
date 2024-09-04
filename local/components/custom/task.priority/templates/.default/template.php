<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\Extension;




$APPLICATION->SetTitle('Приоритеты задач');
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php // Подключение CSS-файлов Bootstrap из папки bitrix/css
    Asset::getInstance()->addCss('/bitrix/css/main/bootstrap_v4/bootstrap.min.css');
    ?>

    <title>Приоритеты задач</title>
</head>
<body>
<div id="task">
    <p> Приоритет выставляется от 1 (максимальный) до 20 (минимальный), сортировка происходит динамически с задержкой 3 секунды</p>

    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Название задачи</th>
            <th>Дата постановки</th>
            <th>Ответственный</th>
            <th>Постановщик</th>
            <th>Приоритет (от 1 до 20)</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="task in tasks" :key="task.ID">
            <td>{{ task.TITLE }}</td>
            <td>{{ (task.CREATED_DATE) }}</td>
            <td>{{ task.RESPONSIBLE_NAME }}</td>
            <td>{{ task.CREATED_BY_NAME }}</td>
            <td>{{ task.UF_PRIORITY }}</td>
            <td>
                <div class="button-container">
                    <button @click="changePriority(task.ID, 'increase')" class="btn btn-info">+</button>
                    <button @click="changePriority(task.ID, 'decrease')" class="btn btn-info">-</button>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</div>

<div id="history">
    <h3>История изменений приоритетов</h3>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>Задача</th>
            <th>Пользователь</th>
            <th>Дата и время</th>
            <th>Старый приоритет</th>
            <th>Новый приоритет</th>
            <th>Действие</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="record in history" :key="record.ID">
            <td>{{ record.UF_TASK_ID }}</td>
            <td>{{ record.UF_USER_NAME }}</td>
            <td>{{ record.UF_CHANGE_DATE }}</td>
            <td>{{ record.UF_OLD_PRIORITY }}</td>
            <td>{{ record.UF_NEW_PRIORITY }}</td>
            <td>{{ record.UF_ACTION }}</td>
        </tr>
        </tbody>
    </table>
</div>
</body>

<script type="text/javascript">
    const tasksData = <?= json_encode($arResult['TASKS']) ?>;
    const historyData = <?= json_encode($arResult['HISTORY']) ?>;
</script>
</html>


