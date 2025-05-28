<?php

namespace Module\Scenario;

enum Outcome : string
{
    case FUMBLE = 'fumble';
    case FAILURE = 'echec';
    case SUCCESS = 'succes';
    case CRITICAL = 'critique';

    //Adadpté au JSON
    public static function fromJsonKey(string $key) : self //
    {
        return match(strtolower($key)) {
            'fumble' => self::FUMBLE,
            'failure', 'echec' => self::FAILURE,
            'success', 'succes' => self::SUCCESS,
            'critique' => self::CRITICAL,
            default => throw new \InvalidArgumentException("Key Outcome invalide") 
        };
    }
}