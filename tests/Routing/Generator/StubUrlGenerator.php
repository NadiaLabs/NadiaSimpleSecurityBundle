<?php

/*
 * This file is part of the NadiaSimpleSecurityBundle package.
 *
 * (c) Leo <leo.on.the.earth@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nadia\Bundle\NadiaSimpleSecurityBundle\Tests\Routing\Generator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * Class StubUrlGenerator
 */
class StubUrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var array
     */
    private $expectedUrls;

    /**
     * StubUrlGenerator constructor.
     *
     * @param array $expectedUrls A array with "route name" as key and "URL" as value
     */
    public function __construct(array $expectedUrls)
    {
        $this->expectedUrls = $expectedUrls;
    }

    public function setContext(RequestContext $context)
    {
        // TODO: Implement setContext() method.
    }

    public function getContext()
    {
        // TODO: Implement getContext() method.
    }

    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        return $this->expectedUrls[$name];
    }
}
