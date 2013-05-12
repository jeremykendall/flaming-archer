<?php

/**
 * Flaming Archer
 *
 * @link      http://github.com/jeremykendall/flaming-archer for the canonical source repository
 * @copyright Copyright (c) 2012 Jeremy Kendall (http://about.me/jeremykendall)
 * @license   http://github.com/jeremykendall/flaming-archer/blob/master/LICENSE MIT License
 */

namespace Fa\Validate;

/**
 * Validates the setup inputs
 */
class Setup implements ValidateInterface
{

    /**
     * Data to validate
     *
     * @var array
     */
    protected $data;

    /**
     * Input filter
     *
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter;

    /**
     * Public constructor
     */
    public function __construct()
    {
        $factory = new \Zend\InputFilter\Factory();
        $this->inputFilter = $factory->createInputFilter(array(
            'name' => array(
                'name' => 'name',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty'
                    )
                ),
                'filters' => array(
                    array(
                        'name' => 'strip_tags'
                    ),
                    array(
                        'name' => 'string_trim'
                    )
                )
            ),
            'email' => array(
                'name' => 'email',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'email_address'
                    )
                ),
                'filters' => array(
                    array(
                        'name' => 'strip_tags'
                    ),
                    array(
                        'name' => 'string_trim'
                    )
                )
            ),
            'flickr_api_key' => array(
                'name' => 'flickr_api_key',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty'
                    ),
                    array(
                        'name' => 'regex',
                        'options' => array(
                            'pattern' => '/^[0-9a-f]{32}$/i'
                        )
                    )
                ),
                'filters' => array(
                    array(
                        'name' => 'string_trim'
                    )
                )
            ),
            'password' => array(
                'name' => 'password',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    array(
                        'name' => 'string_length',
                        'options' => array(
                            'min' => 8
                        )
                    )
                )
            ),
            'confirm_password' => array(
                'name' => 'confirm_password',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'identical',
                        'options' => array(
                            'token' => 'password'
                        )
                    )
                )
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(array $data)
    {
        $this->inputFilter->setData($data);

        return $this->inputFilter->isValid();
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->inputFilter->getValues();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->inputFilter->getMessages();
    }

}
