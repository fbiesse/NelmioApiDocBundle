<?php

namespace Nelmio\ApiDocBundle\Swagger2\Segment;

use Nelmio\ApiDocBundle\Swagger2\SegmentInterface;
use Nelmio\ApiDocBundle\Swagger2\Segment\Parameter\SchemaProperty;

class Schema implements SegmentInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var SchemaProperty[]
     */
    protected $properties = array();

    public function __construct($name, array $properties)
    {
        $this->name = $name;
        $this->properties = $properties;
    }

    public function toArray()
    {

        $required = array();
        $properties = array();

        foreach ($this->properties as $property) {
            if ($property->isRequired()) {
                $required[] = $property;
            }
            $propertyData = array();
            if($property->isCollection()){
                $itemData = array("type" => "string");
                if($property->getSchema() !== null){
                    $itemData = array("\$ref" => '#/definitions/'.$property->getSchema()->getName());
                }
                $propertyData =array(
                    "type" => "array",
                    "items" => $itemData
                );
            }else if($property->getSchema() !== null){
                $propertyData =array(
                    "\$ref" => '#/definitions/'.$property->getSchema()->getName()
                );
            }else{
                $propertyData =array("type" => $property->getType());
            }
            $properties[$property->getName()] = $propertyData;
        }

        $requiredNames = array_map(function ($property) {
            return $property->getName();
        }, $required);
        $data = array(
            //'type' => ' object',
            'required' => $requiredNames,
            'properties' => $properties
        );
        return $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /*
     $result = array();
        $properties = array();

        foreach ($this->properties as $property) {
            $propertyData = array();
            if($property->getSchema() !== null){
                $propertyData =array(
                    "\$ref" => '#/definitions/'.$property->getSchema()->getName(),
                );
            }else{
                $propertyData =array("type" => $property->getType());
            }
            $properties[$property->getName()] = $propertyData;
        }
        return $properties;
     */


}
