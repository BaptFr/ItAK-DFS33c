<?php

namespace Lib\Persistence\File;

use Lib\File\File;
use Lib\Persistence\Datasource;

class FileDatasource implements Datasource
{
    public function __construct(
        //File - reader
        private File $sourceFile,
        private array $drivers
    ) {}

    public function loadAll(): \Iterator
    {
        //boucle quel driver pour fichier
        foreach ($this->drivers as $driver) {
            if ($driver->accepts($this->sourceFile)) {
                $data = $driver->extractData($this->sourceFile);
                foreach ($data as $line) {
                    yield $line;
                }
                return;
            }
        }
        throw new \RuntimeException("No driver for this file extension");
    }
}
