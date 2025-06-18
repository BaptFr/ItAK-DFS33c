<?php

namespace Lib\Persistence\File\Driver;

use Lib\File\File;
use Lib\Persistence\File\FileReader;

class JsonFileDriver implements FileReader
{
    public function accepts(File $sourceFile): bool
    {
        return strtolower($sourceFile->extension) === 'json';
    }
    public function extractData(File $sourceFile): iterable
    {
        if (!$this->accepts($sourceFile)) {
            throw new \BadMethodCallException("File not supported by driver " . $sourceFile);
        }

        $content = $sourceFile->getContents();
        $jsonDecoded = json_decode($content, true);
        if ($jsonDecoded === null) {
            throw new \RuntimeException("JSON read extracData error" . $sourceFile);
        }
        return $jsonDecoded;
    }
}
