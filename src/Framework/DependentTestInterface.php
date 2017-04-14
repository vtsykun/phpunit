<?php

namespace PHPUnit\Framework;

interface DependentTestInterface extends Test
{
    /**
     * Get list of tests name that this test depends from
     *
     * @return string
     */
    public function getDependencies();

    /**
     * @return string
     */
    public function getName();
}
