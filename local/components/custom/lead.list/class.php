<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use Bitrix\Main\Loader;
use Bitrix\Crm\LeadTable;
use Bitrix\Main\Engine\Contract\Controllerable;

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/helpers/helpers.php';


class LeadListController extends CBitrixComponent implements Controllerable
{
    const URL_LID = '/lead_list/';

    public function configureActions(): array
    {
    return [
        'updateCheckboxStatus' => [
            'class' => self::class,
            'method' => 'updateCheckboxStatus',
            'POST' => true,
        ],];

}

protected function checkCRM(): void
{
    if (!Loader::includeModule('crm')) {
        ShowError("CRM module is not installed");
        return;
    }
}

    public function executeComponent()
    {
        $this->checkCRM();

        $this->arResult['LEADS'] = $this->getLeads();

        $this->userByLeadID();

        $this->includeComponentTemplate();
    }

    protected function getLeads(array $arSelect = [], array $filter = [])
    {

        $defaultSelect = array('ID', 'TITLE', 'DATE_CREATE', 'SOURCE_ID', 'ASSIGNED_BY_ID', 'STATUS_ID', 'UF_CHECKED');



        $DBLeadsList = LeadTable::getList(array(
            'select' => $arSelect ?: $defaultSelect,
            'filter' => $filter,
            'order' => array('DATE_CREATE' => 'DESC'),
            'limit' => 20
        ));

        return $DBLeadsList->fetchAll();
    }
    protected function userByLeadID():void
    {
        foreach ($this->arResult['LEADS'] as &$lead) {

            $lead['ASSIGNED_BY_ID'] = (getUserNameById($lead['ASSIGNED_BY_ID']));
        }}
    public function updateLeadStatus()
    {
        $this->checkCRM();

        $checkedLeads = $this->request->getPost("checked_leads") ?? [];
        $checkedLeadIds = array_map('intval', $checkedLeads);


        $latestLeadsId = array_column($this->getLeads(['ID', 'UF_CHECKED'], ['=UF_CHECKED' => 1]), 'ID');

        $leadsToCheck = array_diff($checkedLeadIds, $latestLeadsId);

        $leadsToUncheck = array_diff($latestLeadsId, $checkedLeadIds);


        $this->updateLeadStatusDB($leadsToCheck, 1);
        $this->updateLeadStatusDB($leadsToUncheck, 0);


        LocalRedirect(self::URL_LID);
        exit;

    }
        private function updateLeadStatusDB(array $leadIds, int $status)
        {
              foreach ($leadIds as $leadId) {
                LeadTable::update($leadId, ['UF_CHECKED' => $status]);
            }
        }

}
