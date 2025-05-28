<?php

namespace Module\Scenario;

use Module\Scenario\Encounter;
use Module\Scenario\Outcome;
use Module\Scenario\Result;
use Module\Scenario\Scenario;


class ScenarioFactory
{
    public function createFromData(string $filePath): array 
    {
        $jsonContent = file_get_contents($filePath);
        //changer pour associative
        $data = json_decode(
            json: $jsonContent, 
            associative: true
        );
    
        $scenarios = [];
        foreach($data as $scenarioData){
            $encounters = [];
            //Dans encounter -> results
            
            foreach ($data as $scenarioData) {
                foreach ($scenarioData['encounters'] as $encounterData) {
                    $results = [];
                    foreach ($encounterData['results'] as $outcomeKey => $score) {
                        $results[] = new Result(
                            $score,
                            Outcome::fromJsonKey($outcomeKey)
                        );
                    }

                //Encounter 
                $encounters[] = new Encounter(
                    $encounterData['title'],
                    $encounterData['flavor'],
                    ...$results
                );
            }

                $scenarios[] = new Scenario(
                    $scenarioData['title'],
                    ...$encounters
                );   
            }
        }
    return $scenarios;
    }
}