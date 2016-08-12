<?php

namespace ListOnce\Entity;

class AgentCollection extends EntityCollection
{

    /**
     * The property containing entities
     *
     * @var string
     */
    protected $entityProperty = 'agents';

    /**
     * The data type of entities within this collection
     *
     * @var string
     */
    protected $dataType = 'Agent';
}