<?php

use Bitrix\Main\Routing\RoutingConfigurator;

return function (RoutingConfigurator $routes)
{
  routingProd($routes);
};
function routingProd(RoutingConfigurator $routes)
{


    $routes->post('/lead_list/update/', function () {


        include $_SERVER['DOCUMENT_ROOT'] . '/local/components/custom/lead.list/class.php';


        $controller = new LeadListController();

        return $controller->updateLeadStatus();
    });

    $routes->post('/task_priority/update', function () {


        include $_SERVER['DOCUMENT_ROOT'] . '/local/components/custom/task.priority/class.php';

        $controller = new TaskPriorityController();

        return $controller->changePriorityAction();

    });
    $routes->get('/task_priority/history', function () {

        include $_SERVER['DOCUMENT_ROOT'] . '/local/components/custom/task.priority/class.php';

        $controller = new TaskPriorityController();

        return $controller->getHistoryAction();

    });
}
