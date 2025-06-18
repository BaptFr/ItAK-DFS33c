<?php

namespace Application;

use Lib\File\File;
use Lib\ValueObject\PositiveInt;
use Lib\Persistence\File\FileDatasource;
use Lib\Persistence\File\Driver\JsonFileDriver;
use Module\Character\Model as Character;
use Module\Mj\Model as Mj;
use Module\Scenario\Factory\ScenarioFactory;


class Application
{
    const DEFAULT_NB_RUNS = 1;

    protected Mj\GameMaster $mj;
    protected Character\Party $party;

    public function __construct(
        private string $dataDir
    ) {
        $this->mj = new class(
            new Mj\Deck(
                ['♦️', '♥️', '♠️', '♣️'],
                [2, 3, 4, 5, 6, 7, 8, 9, 10, 'V', 'Q', 'K', 1]
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
                echo $message . "\n";
            }
        };

        $this->party = new Character\Party(
            new Character\Character('🪓Gertrude', new PositiveInt(10)),
            new Character\Character('🔥Zehirmann', new PositiveInt(15), new PositiveInt(11)),
            new Character\Character('🗡️ Enoriel', new PositiveInt(15), new PositiveInt(11)),
            new Character\Character('⚔️ Wrandrall', new PositiveInt(10)),
        );
    }

    public function run($script, ?int $nbRuns = self::DEFAULT_NB_RUNS)
    {
        try {
            $scenarioFactory = new ScenarioFactory(
                //Passer un array de drivers
                new FileDatasource(
                    new File($this->dataDir . '/scenarios.json'),
                    [new JsonFileDriver()]
                )
            );




            for ($i = 0; $i < $nbRuns; $i++) {
                $party = clone $this->party;  // create a new Party on each run

                foreach ($scenarioFactory->createScenarios() as $scenario) {
                    echo (
                        $this->mj->entertain($party, $scenario) ?
                        "\n>>> 🤘 Victory 🤘 <<<\n\n" :
                        "\n>>> 💀 Defeat 💀 <<<\n\n"
                    );
                }
            }
        } catch (\Exception $exception) {
            echo $exception . "\n";
            debug_print_backtrace();
            echo "\n";
        }
    }
}
