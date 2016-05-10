<?php

namespace Nelmio\ApiDocBundle\Swagger2;

use Nelmio\ApiDocBundle\DataTypes;
use Nelmio\ApiDocBundle\Swagger2\Segment\Schema;

class SchemaRegistry
{
    /**
     * @var Schema[]
     */
    protected $schemas = array();

    public function getSchemas(){
        return $this->schemas;

    }

    public function register($className, array $parameters)
    {
        $transformedName = $this->createName($className);
        if (isset($this->schemas[$transformedName])) {
            return $this->schemas[$transformedName];
        }

        $schemaProperties = array();

        if (is_array($parameters)) {
            foreach ($parameters as $name => $parameter) {

                if (!isset($parameters['type'])) {
                    //$parameters['type'] = 'object';
                    //continue;
                }
                $property           = new Segment\Parameter\SchemaProperty($name);
                $schemaProperties[] = $property;

                switch ($parameter['actualType']) {
                    case DataTypes::MODEL:
                        if (isset($parameter['children'])) {
                            $property->setSchema(
                                $this->register(
                                    $parameter['subType'],
                                    isset($parameter['children']) ? $parameter['children'] : null
                                )
                            );
                        }
                        break;
                    case DataTypes::COLLECTION:
                        if (isset($parameter['children'])) {
                            $property->setSchema(
                                $this->register(
                                    $parameter['subType'],
                                    isset($parameter['children']) ? $parameter['children'] : null
                                )
                            );
                        }
                        $property->setCollection(true);
                        break;
                    default :
                        $property->setType(TypeMap::type($parameter['actualType']));
                        break;
                }
            }
        }
        $schema               = new Schema($transformedName, $schemaProperties);
        $this->schemas[$transformedName] = $schema;

        return $schema;
    }

    public function createName($name)
    {
        return str_replace('\\', '.', $name);
    }
}
