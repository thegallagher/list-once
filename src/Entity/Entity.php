<?php

namespace ListOnce\Entity;

use Psr\Http\Message\RequestInterface;

class Entity
{
    /**
     * Data for this entity
     *
     * @var array|null
     */
    protected $data = null;

    /**
     * The data type of this entity
     *
     * @var string|null
     */
    protected $dataType = null;

    /**
     * The request used to retrieve this entity
     *
     * @var RequestInterface
     */
    protected $request = null;

    /**
     * Constructor
     *
     * @param array|object $data An array or array like object
     * @param RequestInterface $request
     * @param string|null $dataType The data type of this entity
     */
    protected function __construct($data, RequestInterface $request, $dataType = null)
    {
        $this->validate($data);
        $this->data = $data;
        $this->request = $request;
        $this->dataType = $dataType;
    }

    /**
     * Make an entity object
     *
     * @param array|object $data
     * @param RequestInterface $request
     * @param null $dataType
     *
     * @return static
     */
    public static function make($data, RequestInterface $request, $dataType = null)
    {
        $className = __NAMESPACE__ . '\\' . $dataType;
        if (class_exists($className)) {
            return new $className($data);
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
     * Get a value
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data->{$name};
    }

    /**
     * Check if a value is set
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data->{$name});
    }

    /**
     * Get the data type of this entity
     *
     * @return string|null
     */
    public function getDataType()
    {
        return $this->dataType;
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
}