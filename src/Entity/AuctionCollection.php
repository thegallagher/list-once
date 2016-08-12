<?php

namespace ListOnce\Entity;

class ListingCollection extends EntityCollection
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
    protected $entityProperty = 'auctions';

    /**
     * The property containing total entities
     *
     * @var string
     */
    protected $totalEntitiesProperty = 'total_auctions';

    /**
     * The data type of entities within this collection
     *
     * @var string
     */
    protected $dataType = 'Auction';
}