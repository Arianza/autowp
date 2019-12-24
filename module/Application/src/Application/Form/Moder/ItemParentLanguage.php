<?php

namespace Application\Form\Moder;

use Traversable;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Autowp\ZFComponents\Filter\SingleSpaces;

class ItemParentLanguage extends Form implements InputFilterProviderInterface
{
    private $language = 'en';

    /**
     * @param  array|Traversable $options
     * @return Element|ElementInterface
     * @throws Exception\InvalidArgumentException
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (isset($options['language'])) {
            $this->language = $options['language'];
        }

        return $this;
    }

    public function __construct($name = null, $options = [])
    {
        parent::__construct($name, $options);

        $elements = [
            [
                'name'    => 'name',
                'type'    => 'Text',
                'options' => [
                    'label' => 'Name'
                ],
                'attributes' => [
                    'maxlength' => \Application\Model\ItemParent::MAX_LANGUAGE_NAME
                ]
            ]
        ];

        foreach ($elements as $element) {
            $this->add($element);
        }
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [
            'name' => [
                'required' => false,
                'filters'  => [
                    ['name' => 'StringTrim'],
                    ['name' => SingleSpaces::class]
                ],
                'validators' => [
                    [
                        'name' => 'StringLength',
                        'options' => [
                            'min' => 0,
                            'max' => \Application\Model\ItemParent::MAX_LANGUAGE_NAME
                        ]
                    ]
                ]
            ]
        ];
    }
}
