<?php

namespace ListOnce\Entity;

class FloorplansCollection extends EntityCollection
{

    /**
     * The property containing entities
     *
     * @var string
     */
    protected $entityProperty = 'floorplans';

    /**
     * The data type of entities within this collection
     *
     * @var string
     */
    protected $dataType = 'Floorplan';
}