<?php

namespace ListOnce\Entity;

use Psr\Http\Message\RequestInterface;

/**
 * Class EntityCollection
 *
 * Represents a collection of entities
 *
 * @package ListOnce\Entity
 */
class EntityCollection implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * Does this collection use pagination?
     *
     * @var bool
     */
    protected $hasPagination = false;

    /**
     * The property containing the current page
     *
     * @var string
     */
    protected $pageProperty = 'page';

    /**
     * The property containing the total pages
     *
     * @var string
     */
    protected $totalPagesProperty = 'total_pages';

    /**
     * The property containing the entities per page
     *
     * @var string
     */
    protected $perPageProperty = 'per_page';

    /**
     * The property containing entities
     *
     * @var string
     */
    protected $entityProperty = null;

    /**
     * The property containing total entities
     *
     * @var string
     */
    protected $totalEntitiesProperty = null;

    /**
     * The current index of the iterator
     *
     * @var int
     */
    protected $iteratorIndex = 0;

    /**
     * Entities
     *
     * @var array
     */
    protected $entities = [];

    /**
     * The data type of entities within this collection
     *
     * @var string
     */
    protected $dataType = null;

    /**
     * Array containing the pagination data
     *
     * @var array|null
     */
    protected $paginationData = null;

    /**
     * The request used to retrieve this collection
     *
     * @var RequestInterface
     */
    protected $request = null;

    /**
     * Constructor
     *
     * @param object $data
     * @param RequestInterface $request
     * @param string|null $dataType
     */
    protected function __construct($data, RequestInterface $request, $dataType = null)
    {
        $this->validate($data);
        $this->setEntities($data);
        $this->setPaginationData($data);

        $this->request = $request;

        if ($dataType !== null) {
            $this->dataType = $dataType;
        }
    }

    /**
     * Make an entity collection object
     *
     * @param array|object $data
     * @param RequestInterface $request
     * @param string $dataType
     *
     * @return static
     */
    public static function make($data, RequestInterface $request, $dataType)
    {
        $className = __NAMESPACE__ . '\\' . $dataType . 'Collection';
        if (class_exists($className)) {
            return new $className($data, $request);
        }
        return new static($data, $request, $dataType);
    }

    /**
     * Validate data
     *
     * @param object $data
     *
     * @return void
     */
    protected function validate($data)
    {
        if (!empty($data->error_message)) {
            throw new \RuntimeException('ListOnce API error: ' . $data->error_message);
        }

        if (!empty($data->ERROR)) {
            throw new \RuntimeException('ListOnce API error: ' . $data->ERROR);
        }
    }

    /**
     * Set the entities for this collection
     *
     * @param object $data
     *
     * @return void
     */
    protected function setEntities($data)
    {
        if ($this->entityProperty === null) {
            $this->entities = (array)$data;
        } elseif (isset($data->{$this->entityProperty})) {
            $this->entities = $data->{$this->entityProperty};
        }
    }

    /**
     * Set the pagination data
     *
     * @param object $data
     *
     * @return void
     */
    protected function setPaginationData($data)
    {
        if ($this->hasPagination) {
            $currentPage = isset($data->{$this->pageProperty}) ? $data->{$this->pageProperty} : 1;
            $totalPages = isset($data->{$this->totalPagesProperty}) ? $data->{$this->totalPagesProperty} : 1;
            $totalEntities = isset($data->{$this->totalEntitiesProperty}) ? $data->{$this->totalEntitiesProperty} : 0;
            $perPage = isset($data->{$this->perPageProperty}) ? $data->{$this->perPageProperty} : 0;
            $this->paginationData = compact('currentPage', 'totalPages', 'totalEntities', 'perPage');
        }
    }

    /**
     * Get pagination data
     *
     * @param string $key
     *
     * @return int|int[]
     */
    public function getPaginationData($key = null)
    {
        if (!$this->hasPagination) {
            throw new \UnexpectedValueException('This collection does not contain pagination data.');
        }

        if ($key === null) {
            return $this->paginationData;
        }

        if (isset($this->paginationData[$key])) {
            return $this->paginationData[$key];
        }

        throw new \UnexpectedValueException('Invalid pagination variable.');
    }

    /**
     * Get the request used to retrieve this collection
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the data type of entities within this collection
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Return the current element
     *
     * @return Entity Can return any type.
     */
    public function current()
    {
        return Entity::make($this->entities[$this->iteratorIndex], $this->request, $this->dataType);
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->iteratorIndex++;
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return $this->iteratorIndex;
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     */
    public function valid()
    {
        return isset($this->entities[$this->iteratorIndex]);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->iteratorIndex = 0;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset An offset to check for.
     *
     * @return boolean true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->entities[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset The offset to retrieve.
     *
     * @return Entity Can return all value types.
     */
    public function offsetGet($offset)
    {
        return Entity::make($this->entities[$offset], $this->request, $this->dataType);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value The value to set.
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('EntityCollection is immutable.');
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset The offset to unset.
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('EntityCollection is immutable.');
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return count($this->entities);
    }
}