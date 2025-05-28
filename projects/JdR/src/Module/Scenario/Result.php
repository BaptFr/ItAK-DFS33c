<?php

namespace Module\Scenario;

/**
 * An Encounter possible result
 */
class Result
{
    public function __construct(
        public readonly int $probability,
        public readonly Outcome $outcome
    ) {
    }
}
