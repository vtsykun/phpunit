<?php

namespace PHPUnit\Util\DependencyResolver;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;

class Solver
{
    /**
     * @param TestSuite $testSuite
     */
    public function resolve(TestSuite $testSuite)
    {
        $this->resolveDependency($testSuite);
    }

    /**
     * @param TestSuite $testSuite
     * @return Problem
     */
    protected function resolveDependency(TestSuite $testSuite)
    {
        $problems = [];

        foreach ($testSuite->tests() as $test) {
            if ($test instanceof TestSuite) {
                $problems[] = $this->resolveDependency($test);
            }

            if ($test instanceof TestCase) {
                $problems[] = new Problem($test->getName(), $test, $test->getDependencies());
            }
        }

        return $this->mergeProblems($problems, $testSuite);
    }

    /**
     * @param Problem[] $problems
     * @param TestSuite $testSuite
     * @return Problem
     */
    protected function mergeProblems(array $problems, $testSuite)
    {
        $tests = [];
        $poolProblems = [];
        $dependencies = [];
        foreach ($problems as $problem) {
            $poolProblems[$problem->getName()][] = $problem;
        }

        $resolver = function (Problem $problem, array $tests = []) use (&$resolver, $poolProblems, $dependencies) {
            $dependencies[$problem->getName()] = true;

            while (!$problem->isEmpty()) {
                $dependency = $problem->pop();
                if (!array_key_exists($dependency, $dependencies) && array_key_exists($dependency, $poolProblems)) {
                    /** @var Problem $element */
                    foreach ($poolProblems[$dependency] as $nextProblem) {
                        $tests = $resolver($nextProblem, $tests);
                    }
                }
            }

            if (!in_array($problem->getObject(), $tests, true)) {
                $tests[] = $problem->getObject();
            }

            return $tests;
        };

        foreach ($problems as $problem) {
            $tests = $resolver($problem, $tests);
        }
        $testSuite->setTests($tests);

        return new Problem($testSuite->getName(), $testSuite, array_keys($dependencies));
    }
}
