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