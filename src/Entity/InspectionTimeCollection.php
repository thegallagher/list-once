<?php

namespace ListOnce\Entity;

class InspectionTimeCollection extends EntityCollection
{
    /**
     * Does this collection use pagination?
     *
     * @var bool
     */
    protected $hasPagination = true;

    /**
     * The property containing entities
     *
     * @var string
     */
    protected $entityProperty = 'inspection_times';

    /**
     * The property containing total entities
     *
     * @var string
     */
    protected $totalEntitiesProperty = 'total_inspection_times';

    /**
     * The data type of entities within this collection
     *
     * @var string
     */
    protected $dataType = 'InspectionTime';
}