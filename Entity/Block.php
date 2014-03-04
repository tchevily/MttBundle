<?php

namespace CanalTP\MttBundle\Entity;

/**
 * Block
 */
class Block extends AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $typeId;

    /**
     * @var string
     */
    private $domId;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Object
     */
    private $timetable;

    /**
     * @var Object
     */
    private $stopPoint;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get typeId
     *
     * @return string
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set typeId
     *
     * @param  string $typeId
     * @return Block
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;

        return $this;
    }

    /**
     * Set domId
     *
     * @param  string $domId
     * @return Block
     */
    public function setDomId($domId)
    {
        $this->domId = $domId;

        return $this;
    }

    /**
     * Get domId
     *
     * @return string
     */
    public function getDomId()
    {
        return $this->domId;
    }

    /**
     * Set content
     *
     * @param  string $content
     * @return Block
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set title
     *
     * @param  string $title
     * @return Block
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set timetable
     *
     * @param integer $timetable
     *
     * @return Block
     */
    public function setTimetable($timetable)
    {
        $this->timetable = $timetable;

        return $this;
    }

    /**
     * Get timetable
     *
     * @return string
     */
    public function getTimetable()
    {
        return $this->timetable;
    }

    /**
     * Set stopPoint
     *
     * @param integer $stopPoint
     *
     * @return Block
     */
    public function setStopPoint($stopPoint)
    {
        $this->stopPoint = $stopPoint;

        return $this;
    }

    /**
     * Get stopPoint
     *
     * @return string
     */
    public function getStopPoint()
    {
        return $this->stopPoint;
    }
}
