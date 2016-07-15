<?php

namespace ListOnce;

class Response extends \ArrayObject
{

    /**
     * Total amount of objects on all pages
     *
     * @var int
     */
    public $totalObjects = 1;

    /**
     * Current page
     *
     * @var int
     */
    public $currentPage = 1;

    /**
     * Total pages
     *
     * @var int
     */
    public $totalPages = 1;

    /**
     * Total pages
     *
     * @var int|null
     */
    public $pageSize = null;

    /**
     * Data type
     *
     * @var string|null
     */
    public $dataType = null;

    /**
     * Construct ListOnce response
     *
     * @param object|array $response
     * @param string|null $dataField
     * @param string|null $dataType
     */
    public function __construct($response, $dataField = null, $dataType = null)
    {
        $data = $response;
        if ($dataField) {
            if (empty($response->{$dataField})) {
                $data = [];
            } else {
                $data = $response->{$dataField};
            }
        }

        if (!is_array($data)) {
            $data = [$data];
        }

        parent::__construct($data);

        if ($dataField && isset($response->total_listings)) {
            $this->totalObjects = $response->total_listings;
        } else {
            $this->totalObjects = count($data);
        }

        if ($dataField && isset($response->page)) {
            $this->currentPage = $response->page;
        }

        if ($dataField && isset($response->total_pages)) {
            $this->totalPages = $response->total_pages;
        }

        if ($dataField && isset($response->per_page)) {
            $this->pageSize = $response->per_page;
        }

        $this->dataType = $dataType;
    }

    /**
     * Merge a response into this response.
     *
     * Responses must have the same data type. Pagination cannot be supported.
     *
     * @param Response $response
     */
    public function merge(self $response)
    {
        if ($response->dataType !== $this->dataType) {
            throw new \RuntimeException('Data type mismatch. Cannot merge mismatched responses.');
        }

        foreach ($response as $value) {
            $this->append($value);
        }

        $this->totalObjects += $response->totalObjects;
        $this->currentPage = 1;
        $this->totalPages = 1;
        $this->pageSize = null;
    }
}