<?php

namespace CanalTP\MttBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * StopTimetableRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StopTimetableRepository extends EntityRepository
{
    /*
     * Finding a StopTimetable in database using an externalRouteId.
     * If the object doesn't exist, create it
     *
     * @param string $externalRouteId
     * @param integer $lineConfig
     */
    public function findOrCreateStopTimetable($externalRouteId, $lineConfig)
    {
        $stopTimetable = null;

        if ($lineConfig != null) {
            $stopTimetable = $this->findOneBy(
                array(
                    'externalRouteId' => $externalRouteId,
                    'lineConfig' => $lineConfig->getId(),
                )
            );
        }

        // not found then insert it
        if (empty($stopTimetable)) {
            $stopTimetable = new StopTimetable();
            $stopTimetable->setExternalRouteId($externalRouteId);
            $stopTimetable->setLineConfig($lineConfig);

            $this->getEntityManager()->persist($stopTimetable);
            $this->getEntityManager()->flush();
            $this->getEntityManager()->refresh($stopTimetable);
        }

        return $stopTimetable;
    }

    /**
     * Does this stopTimetable have a task under progress?
     * @return boolean
     */
    public function hasAmqpTasksRunning($stopTimetableId, $taskTypeId = AmqpTask::AREA_PDF_GENERATION_TYPE)
    {
        $result = $this->getEntityManager()->getRepository('CanalTPMttBundle:AmqpTask')->findBy(
            array(
                'objectId' => $stopTimetableId,
                'status' => AmqpTask::LAUNCHED_STATUS,
                'typeId' => $taskTypeId
            )
        );

        return count($result) > 0;
    }
}