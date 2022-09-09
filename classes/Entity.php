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
//        $factory = new \REDCapEntity\EntityFactory();
//        $entities = $factory->query('project_external_modules_usage')->condition('project_id', $projectId)
//            ->execute();;
//
//        return $entities;
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
        $query = "select * from redcap_entity_projects_overdue_payments where processed = 0 AND project_id = " . intval($projectId);
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

    public function updateProcessedOverduePayments($projectId): void
    {
        $query = "UPDATE redcap_entity_projects_overdue_payments set processed = 1 where project_id = " . intval($projectId);
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
                $ems = $this->generateProjectEMUsageArray($projectId);
                $external_modules = htmlspecialchars(json_encode($ems), ENT_QUOTES, 'UTF-8');
                $sql = "insert into redcap_entity_projects_overdue_payments (`project_id`, `external_modules`, monthly_payments, created, updated, instance, year, month) values ('$projectId', '$external_modules' , '{$monthlyPayment}','" . time() . "','" . time() . "','1', '" . date("Y") . "', '" . date("m") . "')";
                db_query($sql);
            }
        }
    }

    /**
     * @param string $emJSON
     * @return string;
     */
    public function makeExternalModuleHtmlTable($emJSON)
    {
        $external_modules = json_decode(html_entity_decode($emJSON), true);
        $table = '';
        if (!empty($external_modules)) {
            $table = "<table><thead><tr><th>Name</th><th>Maintenance Fees</th></tr></thead><tbody>";

            foreach ($external_modules as $external_module) {
                if ($external_module['maintenance_fees'] == 0) {
                    continue;
                }
                $table .= "<tr><td>" . $external_module['prefix'] . "</td><td>$" . $external_module['maintenance_fees'] . "</td></tr>";
            }
            $table .= "</tbody></table>";
        }
        return $table;
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
            if ($data['is_em_enabled']) {
                $total += $data['maintenance_fees'];
            }
        }

        return $total;
    }

    public function updateEMChargesWithR2P2Id($updatedIds)
    {
        $result = [];
        foreach ($updatedIds as $id) {
            $sql = sprintf("UPDATE %s SET r2p2_em_charge_id = %s WHERE id = %s", db_escape('redcap_entity_external_modules_charges'), db_escape($id['r2p2_charge_id']), db_escape($id['redcap_charge_id']));
            $q = db_query($sql);
            $result[] = 'REDCap Entity Charge Record ' . $id['r2p2_em_charge_id'] . ' Updated Successfully!';
        }
        return $result;
    }

    public function getEMMonthlyCharges($year, $month, $project_id = '')
    {
        if ($project_id) {
            $sql = sprintf("SELECT * from %s WHERE  charge_month = %s AND charge_year = %s AND project_id = %s AND r2p2_em_charge_id IS NULL", db_escape('redcap_entity_external_modules_charges'), db_escape($month), db_escape($year), db_escape($project_id));
        } else {
            $sql = sprintf("SELECT * from %s WHERE  charge_month = %s AND charge_year = %s AND r2p2_em_charge_id IS NULL", db_escape('redcap_entity_external_modules_charges'), db_escape($month), db_escape($year));
        }

        $result = array();
        $q = db_query($sql);
        if (db_num_rows($q) > 0) {
            while ($row = db_fetch_assoc($q)) {
                $result[] = $row;
            }
        } else {
            if (db_error()) {
                $this->emError(db_error());
            }
        }
        return $result;
    }

    public function generateProjectEMUsageArray($projectID): array
    {
        $result = array();
        $pointer = 0;
        foreach ($this->getProjectEmUsageRecords($projectID) as $data) {
            //$data = $entity->getData();


            if ($data['has_maintenance_fees'] == '0') {
                $maintenance_monthly_cost = 'Fee waived';
            } elseif ($data['maintenance_fees'] != '' && $data['is_em_enabled'] && $data['maintenance_fees']) {
                $maintenance_monthly_cost = '$' . $data['maintenance_fees'];
            } elseif (is_null($data['maintenance_fees']) && $data['is_em_enabled']) {
                $maintenance_monthly_cost = '<a target=\"_blank\" href=\"https://medwiki.stanford.edu/x/dZeWCg\">To be determined</a>';
            } elseif (!$data['is_em_enabled'] && $data['maintenance_fees']) {
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