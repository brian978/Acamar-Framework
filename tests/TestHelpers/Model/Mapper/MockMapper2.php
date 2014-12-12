<?php
/**
 * Acamar-Framework
 *
 * @link      https://github.com/brian978/Acamar-Framework
 * @copyright Copyright (c) 2013
 * @license   https://github.com/brian978/Acamar-Framework/blob/master/LICENSE.txt New BSD License
 */

namespace TestHelpers\Model\Mapper;

use Acamar\Model\Mapper\AbstractMapper;

class MockMapper2 extends AbstractMapper
{
    /**
     * Class name of the entity that the data will be mapped to
     *
     * @var string
     */
    protected $entityClass = '\TestHelpers\Model\Entity\MockEntity';

    /**
     * The map that will be used to populate the object
     *
     * @var array
     */
    protected $map = array(
        'default' => array(
            'joinedId' => 'id',
            'joinedField1' => 'testField1'
        )
    );
}
