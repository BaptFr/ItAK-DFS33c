<?php

namespace Application;

use Module\Character\Character;
use Module\Character\Party;
use Module\Mj;
use Module\Scenario\Encounter;
use Module\Scenario\Outcome;
use Module\Scenario\Result;
use Module\Scenario\Scenario;
use Module\Scenario\ScenarioFactory;
$scenarios = ScenarioFactory::createFromData(__DIR__ . '/../data/scenarios.json');

class Application
{
    const DEFAULT_NB_RUNS = 20;

    protected Mj\GameMaster $mj;
    protected Scenario $scenario;
    protected Party $party;

    public function __construct()
    {
        $this->mj = new class
        (
            new Mj\Deck(
                ['♦️','♥️','♠️','♣️'],
                [2,3,4,5,6,7,8,9,10,'V','Q','K',1]
            ),
            new Mj\Deck(
                ['⚽', '🎳', '🥌'],
                range(start: 1, end: 18, step: 1)
            ),
            new Mj\Dice(6),
            new Mj\Dice(10),
            new Mj\Dice(20),
            new Mj\Coin(4),
            new Mj\Coin(6)
        ) extends Mj\GameMaster {
            protected function announce(string $message)
            {
                echo $message."\n";
            }
        };

        $this->party = new Party(
            maxHealthPoints: 4
        );

        $this->scenario = new Scenario(
            'Le Comte est Bon (mais l’équipe est nulle)',
            new Encounter(
                'L’Oracle Bourré au Clerjus d’Ail',
                'Dans deux jours, le destin te prendra... par surprise... ou par les pieds... J’sais plus.',
                new Result(25, Outcome::FAILURE),
                new Result(75, Outcome::SUCCESS),
            ),
            new Encounter(
                'Le Poney Boiteux de la Forêt Moite',
                'Il hennit en ancien elfique et botte à 1d12.',
                new Result(5, Outcome::FUMBLE),
                new Result(25, Outcome::FAILURE),
                new Result(60, Outcome::SUCCESS),
            ),
            new Encounter(
                'Le Village des Gobelins Véganophiles',
                'Goûte au tofu sacré étranger, pour toi c’est à gerber, pour nous il a un bon bouquet !',
                new Result(10, Outcome::FUMBLE),
                new Result(25, Outcome::FAILURE),
                new Result(50, Outcome::SUCCESS),
            ),
            new Encounter(
                'Le Syndicat des Nécromanciens Marxistes',
                'Plus-value ou post-vie ? Il faut choisir, camarade.',
                new Result(10, Outcome::FUMBLE),
                new Result(25, Outcome::FAILURE),
                new Result(50, Outcome::SUCCESS),
            ),
            new Encounter(
                'Le Comte de Torture Administrative',
                'Jean-Didier ne tue pas. Il ajourne à perpétuité. Lex Papyrum, Dolor Eternum.',
                new Result(10, Outcome::FUMBLE),
                new Result(25, Outcome::FAILURE),
                new Result(50, Outcome::SUCCESS),
            )
        );
    }

    public function run(array $argv)
    {
        $nbRolls = $argv[1] ?? self::DEFAULT_NB_RUNS;

        try {
            for ($i = 0; $i < $nbRolls; $i++) {
                $this->mj->entertainParty(
                    $party = clone $this->party,
                    $this->scenario
                );
                echo "\n";
            }
        }
        catch (\Exception $exception) {
            echo $exception."\n";
        }
    }
}
