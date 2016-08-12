<?php

namespace ListOnce\Entity;

class AlertCollection extends EntityCollection
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
    protected $entityProperty = 'alerts';

    /**
     * The property containing total entities
     *
     * @var string
     */
    protected $totalEntitiesProperty = 'total_alerts';

    /**
     * The data type of entities within this collection
     *
     * @var string
     */
    protected $dataType = 'Alert';
}