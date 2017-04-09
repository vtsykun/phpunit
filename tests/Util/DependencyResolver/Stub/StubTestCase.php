<?php

namespace PHPUnit\Util\DependencyResolver\Stub;

use PHPUnit\Framework\DependentTestInterface;
use PHPUnit\Framework\TestResult;

class StubTestCase implements DependentTestInterface
{
    protected $name;

    protected $dependencies;

    public function __construct($name, array $dependencies)
    {
        $this->name = $name;
        $this->dependencies = $dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function run(TestResult $result = null)
    {
    }
}
