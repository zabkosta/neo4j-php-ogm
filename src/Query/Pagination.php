<?php

namespace GraphAware\Neo4j\OGM\Query;

class Pagination
{
    const KEY_FIRST = "first";
    const KEY_MAX = "max";
    const KEY_ORDER_BY = "order";

    protected $first;

    protected $max;

    protected $orderBy;

    private function __construct($first, $max, $orderBy = null)
    {
        $this->first = $first;
        $this->max = $max;
        $this->orderBy = $orderBy;
    }

    public static function create(array $filters)
    {
        if (array_key_exists(self::KEY_FIRST, $filters) && array_key_exists(self::KEY_MAX, $filters)) {
            $order = array_key_exists(self::KEY_ORDER_BY, $filters) && !empty($filters[self::KEY_ORDER_BY]) && is_array($filters[self::KEY_ORDER_BY]) ? $filters[self::KEY_ORDER_BY] : null;

            return new self($filters[self::KEY_FIRST], $filters[self::KEY_MAX], $order);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @return mixed
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return null
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

}