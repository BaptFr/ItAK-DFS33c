<?php

namespace Module\Mj;

use Module\Character\Party;
use Module\Scenario\Encounter;
use Module\Scenario\Outcome;
use Module\Scenario\Scenario;

/**
 * The Game master class, using various GameAccessories to give results to users.
 */
class GameMaster
{
    private array $gameAccessories;

    public function __construct(
        GameAccessory ...$gameAccessories
    ) {
        $this->gameAccessories = $gameAccessories;
    }

    protected function announce(string $message)
    {
    }

    public function pleaseGiveMeACrit() : int
    {
        // select a random game accessory
        return $this->gameAccessories[array_rand($this->gameAccessories)]
            ->generateRandomPercentScore()
        ;
    }

    private function applyOutcome(Party $party, Outcome $outcome, int $score) : bool
    {
        switch ($outcome) {

            case Outcome::FUMBLE:
                $this->announce(sprintf("> 💀 fumble 💀 (%s)", $score));
                $party->kill();

                return false;

            case Outcome::FAILURE:
                $this->announce(sprintf("> 🔥 failure 🔥 (%s)", $score));
                $party->hurt();

                return false;

            case Outcome::SUCCESS:
                $this->announce(sprintf("> ✨ success ✨ (%s)", $score));

                return true;

            case Outcome::CRITICAL:
                $this->announce(sprintf("> ⚡️ critical success ⚡️ (%s)", $score));
                $party->heal();

                return true;
        }
    }

    public function entertainParty(Party $party, Scenario $scenario)
    {
        foreach ($scenario->play() as $encounter) {
            if (!$party->isAlive()) {
                break;
            }

            $this->announce(sprintf(
                "%s\n> %s",
                $encounter->title,
                $encounter->flavour,
            ));

            for ($currentTry = 0; $currentTry < Encounter::MAX_TRIES; $currentTry++) {
                $outcome = $encounter->resolve(
                    $score = $this->pleaseGiveMeACrit() + $currentTry * Encounter::EXPE_BUFF
                );

                if ($this->applyOutcome(
                        //new \stdClass,
                        $party,
                        $outcome,
                        $score
                    )) {
                    break;
                }

                // too much failures
                if ($currentTry + 1 == Encounter::MAX_TRIES) {
                    $party->kill();
                }
            }
        }

        $this->announce($party->isAlive() ?
            '⚡️ The party wins !' :
            '💀 The party is dead !'
        );
    }
}
