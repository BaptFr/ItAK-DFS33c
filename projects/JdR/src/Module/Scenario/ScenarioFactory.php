<?php

namespace Module\Scenario;

use Module\Scenario\Encounter;
use Module\Scenario\Outcome;
use Module\Scenario\Result;
use Module\Scenario\Scenario;


class ScenarioFactory
{
    public static function createFromData(string $filePath): array //gest erreur if
    {
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true);
    
        $scenarios = [];
        foreach($data as $scenarioData){
            $encounters = [];

            foreach ($scenarioData['encounters'] as $encounterData) {
                $results = [];

                foreach ($encounterData['results'] as $outcomeKey => $score) {
                    $results[] = new Result(
                        $score,
                        Outcome::fromJsonKey($outcomeKey)
                    );
                }

                $encounters[] = new Encounter(
                    $encounterData['title'],
                    $encounterData['flavor'],
                    ...$results
                );
                $scenarios[] = new Scenario(
                    $scenarioData['title'],
                    ...$encounters
                );
            }
        }
        return $scenarios;
    }
}