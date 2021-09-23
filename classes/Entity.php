<?php


namespace Stanford\ProjectPortal;

/**
 * Class Entity
 * @package Stanford\ProjectPortal
 * @property array $record
 */
class Entity
{
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
        $factory = new \REDCapEntity\EntityFactory();
        $entities = $factory->query('project_external_modules_usage')->condition('project_id', $projectId)
            ->execute();;

        return $entities;
    }

    public function generateProjectEMUsageArray($projectID): array
    {
        $result = array();
        $pointer = 0;
        foreach ($this->getProjectEmUsageRecords($projectID) as $entity) {
            $data = $entity->getData();
            $row = array(
                'prefix' => $data['module_prefix'],
//            'is_enabled' => $data['is_em_enabled'] == true ? 'Yes' : 'No',
                'maintenance_fees' => $data['maintenance_fees'] != '' && $data['is_em_enabled'] ? $data['maintenance_fees'] : 0,
                'maintenance_monthly_cost' => $data['maintenance_fees'] != '' && $data['is_em_enabled'] ? '$' . $data['maintenance_fees'] : 'No Monthly Charge',
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