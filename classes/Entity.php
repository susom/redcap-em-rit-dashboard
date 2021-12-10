<?php


namespace Stanford\ProjectPortal;

/**
 * Class Entity
 * @package Stanford\ProjectPortal
 * @property array $record
 */
class Entity
{
    use emLoggerTrait;

    private $record;

    public $PREFIX;

    /**
     * @param $PREFIX
     */
    public function __construct($PREFIX)
    {
        $this->PREFIX = $PREFIX;
    }


    public function getREDCapRecordViaID($projectId, $eventId, $recordId)
    {

        $param = array(
            'project_id' => $projectId,
            'return_format' => 'array',
            'records' => [$recordId],
            'events' => $eventId
        );
        $data = \REDCap::getData($param);
        return $data;
    }

    public function getProjectEmUsageRecords($projectId)
    {
        $this->emError("Before Entity");
        $factory = new \REDCapEntity\EntityFactory();
        $entities = $factory->query('project_external_modules_usage')->condition('project_id', $projectId)
            ->execute();
        $this->emError("After Entity");

        return $entities;
        $query = "select * from redcap_entity_project_external_modules_usage where project_id = " . intval($projectId);
        $q = db_query($query);
        $result = [];
        while ($row = db_fetch_assoc($q)) {
            $result[] = $row;
        }
        return $result;
    }

    /**
     * @param int $projectId
     * @return array
     */
    public function getOverduePayments($projectId): array
    {
        $query = "select * from redcap_entity_projects_overdue_payments where project_id = " . intval($projectId);
        $q = db_query($query);
        $result = [];
        while ($row = db_fetch_assoc($q)) {
            $result[] = $row;
        }
        return $result;
    }

    public function deleteOverduePayments($projectId)
    {
        $query = "delete from redcap_entity_projects_overdue_payments where project_id = " . intval($projectId);
        $q = db_query($query);
    }

    public function checkOverduePayments($projectId, $monthlyPayment)
    {
        if ($monthlyPayment > 0) {
            $month = date('m', time());
            $year = date('Y', time());
            // check if overdue payment record exists for current month
            $query = "select * from redcap_entity_projects_overdue_payments where project_id = " . intval($projectId) . " and `year` = " . intval($year) . " and `month` = " . intval($month);
            $q = db_query($query);
            $row = db_fetch_assoc($q);
            if (empty($row)) {
                $sql = "insert into redcap_entity_projects_overdue_payments (`project_id`, monthly_payments, created, updated, instance) values ('$projectId' , '{$monthlyPayment}','" . time() . "','" . time() . "','1')";
                db_query($sql);
            }
        }
    }

    public function getProjectTotalMaintenanceFees($projectId)
    {
        $total = 0;
        foreach ($this->getProjectEmUsageRecords($projectId) as $entity) {
            $data = $entity->getData();
            $total += $data['maintenance_fees'];
        }
        return $total;
    }

    public function getTotalMonthlyPayment($projectId)
    {
        $total = 0;
        foreach ($this->getProjectEmUsageRecords($projectId) as $data) {
            $total += $data['maintenance_fees'];
        }

        return $total;
    }

    public function generateProjectEMUsageArray($projectID): array
    {
        $result = array();
        $pointer = 0;
        foreach ($this->getProjectEmUsageRecords($projectID) as $entity) {
            $data = $entity->getData();


            if ($data['maintenance_fees'] != '' && $data['is_em_enabled'] && $data['maintenance_fees']) {
                $maintenance_monthly_cost = '$' . $data['maintenance_fees'];
            } elseif (is_null($data['maintenance_fees'])) {
                $maintenance_monthly_cost = '<a target="_blank" href="https://medwiki.stanford.edu/pages/viewpage.action?pageId=177641333#R2P2REDCapMaintenanceAgreement(RMA)-WhatdoesitmeanifanExternalModule\'smonthlymaintenancecostis%22ToBeDetermined%22">To be determined</a>';
            } elseif (!$data['is_em_enabled']) {
                $maintenance_monthly_cost = 'Module Disabled';
            } else {
                $maintenance_monthly_cost = 'No Monthly Charge';
            }
            $row = array(
                'prefix' => $data['module_prefix'],
//            'is_enabled' => $data['is_em_enabled'] == true ? 'Yes' : 'No',
                'maintenance_fees' => $data['maintenance_fees'] != '' && $data['is_em_enabled'] ? $data['maintenance_fees'] : 0,
                'maintenance_monthly_cost' => $maintenance_monthly_cost,
            );
            $pointer++;
            $result[] = $row;
        }
        return $result;
    }

    /**
     * @return array
     */
    public function getRecord(): array
    {
        return $this->record;
    }

    /**
     * @param array $record
     */
    public function setRecord(array $record): void
    {
        $this->record = $record;
    }
}