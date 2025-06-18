<?php

namespace Lib\Persistence\File;

use Lib\File\File;

interface FileReader
{
    public function accepts(File $sourceFile): bool;

    public function extractData(File $sourceFile): iterable;
}
