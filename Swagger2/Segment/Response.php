<?php


namespace Nelmio\ApiDocBundle\Swagger2\Segment;


class Response
{
    private $httpCode;
    private $data;
    private $isCollection;

    /**
     * Response constructor.
     * @param $httpCode
     * @param $type
     * @param $isCollection
     */
    public function __construct($httpCode, $data)
    {
        $this->httpCode     = $httpCode;
        $this->data         = $data;
    }

    public function toArray()
    {
        $responseData = array();
        if(isset($this->data['type'])&& isset($this->data['type']['class'])){
            $responseData['schema'] = array('$ref' => '#/definitions/'.$this->createName($this->data['type']['class']));
        }

        return $responseData
        ;
    }

    public function createName($name)
    {
        return str_replace('\\', '.', $name);
    }

    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->httpCode;
    }
}