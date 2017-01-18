<?php

namespace GnarPhp\Request;

use Illuminate\Validation\Factory as ValidatorFactory;


class RequestEntity
{
    /**
     * @example ['field' => 'required|string']
     * @var array
     */
    protected $fields = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var bool
     */
    protected $useValidator = true;

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var array
     */
    protected $requestData = [];

    /**
     * @var array
     */
    protected $validatorMessages = [];

    /**
     * @var array
     */
    protected $validatorCustomAttributes = [];

    /**
     * @var string
     */
    protected $lang = 'en';


    /**
     * RequestEntity constructor.
     * @param array|null $requestData
     * @param array|null $validatorMessages
     * @param array|null $validatorCustomAttributes
     */
    public function __construct(array $requestData = null, array $validatorMessages = null, array $validatorCustomAttributes = null)
    {
        if(!is_null($requestData)) {
            $this->requestData = $requestData;
            $this->make($requestData);
        }

        if(!is_null($validatorMessages)) {
            $this->validatorMessages = $validatorMessages;
        }

        if(!is_null($validatorCustomAttributes)) {
            $this->validatorCustomAttributes = $validatorCustomAttributes;
        }
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if(!empty($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }


    /**
     * @return string
     */
    public function toJson() : string
    {
        return json_encode($this->data);
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return $this->data;
    }

    /**
     * @param array|null $requestData
     * @return $this
     */
    public function make(array $requestData = null)
    {
        foreach($this->fields as $field => $rules) {
            $this->data[$field] = $requestData[$field] ?? null;
        }

        if($this->useValidator) {
            $this->validate();
        }

        return $this;
    }

    /**
     * @param array|null $validatorMessages
     * @param array|null $validatorCustomAttributes
     * @return Validator
     */
    public function validate(array $validatorMessages = null, array $validatorCustomAttributes = null)
    {
        if(!is_null($validatorMessages)) {
            $this->validatorMessages = $validatorMessages;
        }

        if(!is_null($validatorCustomAttributes)) {
            $this->validatorCustomAttributes = $validatorCustomAttributes;
        }

        $this->validator = $this->setupValidator()->make(
            $this->requestData,
            $this->fields,
            $this->validatorMessages,
            $this->validatorCustomAttributes
        );

        return $this->validator;
    }

    /**
     * If the Validator is not found make a new one
     *
     * @return ValidatorFactory
     */
    protected function setupValidator()
    {
        // the goal is to be portable...right?
        if(class_exists(\Validator::class)) {
            return \Validator;
        }
        return new \Illuminate\Validation\Factory(new \Symfony\Component\Translation\Translator($this->lang));
    }

    /**
     * @return Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function setLang(string $lang)
    {
        $this->lang = $lang;
    }

}