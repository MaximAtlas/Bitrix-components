<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Tasks\Internals\TaskTable;

Bitrix\Main\Loader::includeModule('tasks');


require_once $_SERVER['DOCUMENT_ROOT'] . '/local/helpers/helpers.php';


class TaskPriorityController extends CBitrixComponent implements Controllerable
{

    public function configureActions(): array
    {
        return [
            'changePriority' => [
                'class' => self::class,
                'method' => 'changePriorityAction',
                'POST' => true,
            ],
            'getHistory' => [
                'class' => self::class,
                'method' => 'getHistoryAction',
                'POST' => false,
            ],
            ];




    }

    public function executeComponent(): void
    {

        \Bitrix\Main\UI\Extension::load("ui.vue");


        $this->arResult['TASKS'] = $this->getTasks();
        $this->processTasks();

        $this->arResult['HISTORY'] = $this->getHistoryAction(false);

        $this->includeComponentTemplate();
    }

    private function processTasks(): void
    {
        foreach ($this->arResult['TASKS'] as &$task) {
            $task['RESPONSIBLE_NAME'] = (getUserNameById($task['RESPONSIBLE_ID']));
            $task['CREATED_BY_NAME'] = (getUserNameById($task['CREATED_BY']));
            $task['CREATED_DATE'] = $task['CREATED_DATE']->format('Y-m-d H:i:s');

        }
    }


    private function getTasks(array $arOrder = [], array $arFilter = [], array $arSelect = [], bool $single = false)
    {
        $defaultOrder = ["UF_PRIORITY" => "ASC", "DEADLINE" => "ASC"];
        $defaultFilter = ["STATUS" => [1, 2, 3, 4, 5]];
        $defaultSelect = ["ID", "TITLE", "CREATED_DATE", "RESPONSIBLE_ID", "CREATED_BY", "UF_PRIORITY"];



        $dbTasks = TaskTable::getList([
            'order' => $arOrder ?: $defaultOrder,
            'filter' => $arFilter ?: $defaultFilter,
            'select' => $arSelect ?: $defaultSelect
        ]);


        return ($single ? $dbTasks->fetch() : $dbTasks->fetchAll());
    }

    private function getHistoryBlock():string
    {

        $entity = HighloadBlockTable::compileEntity(HighloadBlockTable::getById(2)->fetch());
        $dataClass = $entity->getDataClass();

        return $dataClass;

    }
    private function addInHistoryBlock($taskId, int $currentPriority, int $newPriority, string $action):void
    {
        $dataClass = $this->getHistoryBlock();

        $userId = $GLOBALS['USER']->GetID();

        $dataClass::add([
            'UF_TASK_ID' => $taskId,
            'UF_USER_ID' => $userId,
            'UF_CHANGE_DATE' => new \Bitrix\Main\Type\DateTime(),
            'UF_OLD_PRIORITY' => $currentPriority,
            'UF_NEW_PRIORITY' => $newPriority,
            'UF_ACTION' => $action
        ]);



    }

    private function checkHLBlock()
    {
        if (!Bitrix\Main\Loader::includeModule('highloadblock')) {
            throw new \Exception('Highloadblock module is not installed');
        }
    }

    public function getHistoryAction(bool $isResponse = true)
    {

        $this->checkHLBlock();

        $dataClass = $this->getHistoryBlock();

        $history = $dataClass::getList([
            'order' => ['UF_CHANGE_DATE' => 'DESC', 'UF_NEW_PRIORITY' => 'DESC'],
            'limit' => 20
        ])->fetchAll();

        $uHistory = $this->processHistory($history);

        return $isResponse ?  json_encode($uHistory) : $uHistory;
    }

    private function processHistory($history)
    {
        foreach ($history as &$record) {
            $record['UF_USER_NAME'] = (getUserNameById($record['UF_USER_ID']));
            $record['UF_CHANGE_DATE'] = ($record['UF_CHANGE_DATE']->format('Y-m-d H:i:s'));
        }
        return $history;
    }


    public function changePriorityAction(): false|string
    {
        $taskId = $this->request->getPost("task_id");
        $action = $this->request->getPost("action");


        $task =  $this->getTasks([],['ID' => $taskId],['UF_PRIORITY'], true);


        $currentPriority = $task['UF_PRIORITY'];
        $newPriority = $action === 'increase' ? $currentPriority + 1 : $currentPriority - 1;

        if ($newPriority < 1 || $newPriority > 20) {
            return json_encode(['success' => false, 'error' => 'incorrect property']);
        }

        TaskTable::update($taskId, ['UF_PRIORITY' => $newPriority]);



        $this->addInHistoryBlock($taskId, $currentPriority,  $newPriority,  $action);

        return json_encode(['success' => true]);


    }

}