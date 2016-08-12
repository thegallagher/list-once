<?php

namespace ListOnce\Entity;

class ExternalLinksCollection extends EntityCollection
{

    /**
     * The property containing entities
     *
     * @var string
     */
    protected $entityProperty = 'external_links';

    /**
     * The data type of entities within this collection
     *
     * @var string
     */
    protected $dataType = 'ExternalLink';
}