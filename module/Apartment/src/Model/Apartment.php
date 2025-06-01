<?php
namespace Apartment\Model;

class Apartment
{
    private $id;
    private $name;
    private $city;

    public function __construct($name, $city, $id=null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->city = $city;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function exchangeArray(array $array)
    {
        $this->id = !empty($array['id']) ? $array['id'] : null;
        $this->name = !empty($array['name']) ? $array['name'] : null;
        $this->city = !empty($array['city']) ? $array['city']: null;
    }

    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city,
        ];
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'city' => $this->city
        ];
    }
}