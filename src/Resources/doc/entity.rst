Entity features
===============

Requirements
------------

Entity MUST have ``id`` identifier. All properties must have setter and getter (except using custom mapper interface);


Validation
----------

Symfony
``````

If entity has some symfony @Assert validators -> inline entity persister check all property and return error messages to user.


Custom validator
````````````````

If entity implements interface ``XcoreCMS\InlineEditing\Model\Entity\Mapper\InlineMapperInterface``, you can define your own getter and setter (with custom validation).

.. code-block:: php

    use XcoreCMS\InlineEditing\Exception\InvalidDataException;
    use XcoreCMS\InlineEditing\Model\Entity\Mapper\InlineMapperInterface;

    class Feed implements InlineMapperInterface
    {
        private $content;

        /**
         * Method for getting value
         * @param string $property
         * @return mixed
         */
        public function getInlineData(string $property)
        {
            if ($property === 'content') {
                return $this->content;
            }

            return '';
        }

        /**
         * Method for user validation and setting property value
         * @param string $property
         * @param mixed $data
         * @return void
         * @throws InvalidDataException
         */
        public function setInlineData(string $property, $data): void
        {
            if ($property === 'content') {
                if (!$data || strpos($data, 'hello') === false) {
                    throw new InvalidDataException('Content can not be blank and must contains word "hello"');
                }

                $this->content = $data;
            }
        }
    }
..
