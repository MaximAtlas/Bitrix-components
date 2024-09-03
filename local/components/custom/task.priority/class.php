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
/*            'getHistory' => [
                'class' => self::class,
                'method' => 'getHistoryAction',
                'POST' => false,
            ],*/
            ];




    }

    public function executeComponent(): void
    {

        $this->arResult['TASKS'] = $this->getTasks();

        $this->userByTask();
        $this->timeTaskToFormat();


        $this->arResult['HISTORY'] = $this->getHistoryAction();

        $this->includeComponentTemplate();
    }
    protected function userByTask():void
    {
        foreach ($this->arResult['TASKS'] as &$task) {
            $task['RESPONSIBLE_NAME'] = (getUserNameById($task['RESPONSIBLE_ID']));
            $task['CREATED_BY_NAME'] = (getUserNameById($task['CREATED_BY']));
        }
    }
    protected function timeTaskToFormat(): void
    {
        foreach ($this->arResult['TASKS'] as &$task) {
            $task['CREATED_DATE'] = ($task['CREATED_DATE']->format('Y-m-d H:i:s'));
        }
    }


    protected function getTasks(array $arOrder = [], array $arFilter = [], array $arSelect = [], bool $single = false)
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

    protected function GetHistoryBlock():string
    {

        $entity = HighloadBlockTable::compileEntity(HighloadBlockTable::getById(2)->fetch());
        $dataClass = $entity->getDataClass();

        return $dataClass;

    }
    protected function addInHistoryBlock($taskId, int $currentPriority, int $newPriority, string $action):void
    {
        $dataClass = $this->GetHistoryBlock();

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

    public function getHistoryAction()
    {
        $dataClass = $this->GetHistoryBlock();

        $history = $dataClass::getList([
            'order' => ['UF_CHANGE_DATE' => 'DESC'],
            'limit' => 20
        ])->fetchAll();

        foreach ($history as &$record) {
            $record['UF_USER_NAME'] = (getUserNameById($record['UF_USER_ID']));
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