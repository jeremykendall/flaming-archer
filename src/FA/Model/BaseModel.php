<?php

namespace FA\Model;

class BaseModel implements \Serializable
{
    /**
     * @var int id
     */
    protected $id;

    /**
     * Public constructor
     *
     * @param array $data OPTIONAL data
     */
    public function __construct(array $data = array())
    {
        if (!empty($data)) {
            $this->fromArray($data);
        }
    }

    /**
     * Get id
     *
     * @return int id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Serializesa class
     *
     * @return string Serialized user
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Unserializes data
     *
     * @param string $data Serialized data
     */
    public function unserialize($data)
    {
        $this->fromArray(unserialize($data));
    }

    /**
     * Sets properties from array
     *
     * @param array $data
     */
    public function fromArray(array $data)
    {
        foreach ($data as $property => $value) {
            if (($property == 'lastLogin' || $property == 'posted') && $value != null) {
                if (!$value instanceof \DateTime) {
                    $value = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                }
            }

            $setter = 'set' . ucfirst($property);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            }
        }
    }
}
