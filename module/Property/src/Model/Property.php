<?php
namespace Property\Model;

class Property
{
    private $id;
    private $name;
    private $location_id;
    private $emission;
    private $rate;
    private $imageurl;
    private $city;
    private $area;

    public function __construct($name, $location_id, $emission, $imageurl, $city, $area, $rate=null, $id=null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->location_id = $location_id;
        $this->emission = $emission;
        $this->rate = $rate;
        $this->imageurl = $imageurl;
        $this->city = $city;
        $this->area = $area;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getLocationId()
    {
        return $this->location_id;
    }

    public function getEmission()
    {
        return $this->emission;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function getImageUrl()
    {
        return $this->imageurl;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getArea()
    {
        return $this->area;
    }

    public function exchangeArray(array $array)
    {
        $this->id = !empty($array['id']) ? $array['id'] : null;
        $this->name = !empty($array['name']) ? $array['name'] : null;
        $this->location_id = !empty($array['location_id']) ? $array['location_id']: null;
        $this->emission = !empty($array['emission']) ? $array['emission']: null;
        $this->rate = !empty($array['rate']) ? $array['rate']: null;
        $this->imageurl = !empty($array['imageurl']) ? $array['imageurl']: null;
        $this->city = !empty($array['city']) ? $array['city']: null;
        $this->area = !empty($array['area']) ? $array['area']: null;

    }

    public function getArrayCopy()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'location_id' => $this->location_id,
            'emission' => $this->emission,
            'rate' => $this->rate,
            'imageurl' => $this->imageurl,
            'city' => $this->city,
            'area' => $this->area,
        ];
    }
}