<?php

namespace CanalTP\MethBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * StopPointRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StopPointRepository extends EntityRepository
{
    public function updatePdfGenerationDate($externalStopPointId)
    {
        $stopPoint = $this->getStopPoint($externalStopPointId);
        
        $stopPoint->setPdfGenerationDate(new \DateTime());
        $this->getEntityManager()->persist($stopPoint);
        $this->getEntityManager()->flush();
    }
    
    public function getStopPoint($externalStopPointId)
    {
        $stopPoint = $this->findOneByExternalId($externalStopPointId);
        // do this stop_point exists?
        if (empty($stopPoint)) {
            $stopPoint = $this->insertStopPoint($externalStopPointId);
        }
        return $stopPoint;
    }
    
    private function insertStopPoint($externalStopPointId)
    {
        $stopPoint = new StopPoint();
        $stopPoint->setExternalId($externalStopPointId);
        $this->getEntityManager()->persist($stopPoint);
        
        return $stopPoint;
    }
    
    private function getLastUpdate($timetable)
    {
        $lastUpdate = null;
        foreach ($timetable->getBlocks() as $block){
            if ($block->getUpdated() != NULL && $block->getUpdated() > $lastUpdate){
                $lastUpdate = $block->getUpdated();
            }
        }
        
        return $lastUpdate;
    }
    
    public function hasPdfUpToDate($stopPoint, $timetable)
    {
        if (
        // no stop point
        empty($stopPoint) ||
        // no pdf generated yet => return FALSE
        $stopPoint->getPdfGenerationDate() == NULL || 
        // line was modified after pdf generation
        $this->getLastUpdate($timetable) > $stopPoint->getPdfGenerationDate()
        ){
            return FALSE;
        }
        else{
            return TRUE;
        }
    }
}