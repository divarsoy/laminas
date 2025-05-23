<?php
namespace Apartment\Model;
use Laminas\InputFilter\InputFilter;

class ApartmentInputFilter extends InputFilter
{
    public function __construct()
    {
        $this->add([
            'name' => 'name',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => ['message' => "'Name' is required"],
                ],
            ],
        ]);

        $this->add([
            'name' => 'city',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],   
            ],
            'validators' => [
                [
                    'name' => 'NotEmpty',
                    'options' => ['message' => "'City' is required"],
                ],
            ],
        ]);
    }
}