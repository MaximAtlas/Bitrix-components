<?php

use Bitrix\Main\UserTable;
function getUserNameById($userId) {
$user = UserTable::getById($userId)->fetch();
return $user ? $user['NAME'] . ' ' . $user['LAST_NAME'] : 'Неизвестно';
}
