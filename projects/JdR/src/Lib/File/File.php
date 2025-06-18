<?php

namespace Lib\File;

class File
{
    /**
     * @see https://www.php.net/manual/fr/function.fopen.php
     * @var resource
     */
    private $fileStream = false;

    public readonly string $dirname;
    public readonly string $basename;
    public readonly string $extension;
    public readonly string $filename;

    public function __construct(
        private string $filePath
    ) {
        if (!is_readable($this->filePath)) {
            throw new \InvalidArgumentException(sprintf(
                'Unreadable file; looked at "%s"',
                $this->filePath
            ));
        }

        foreach (pathinfo($this->filePath) as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $this->$key = $value;
        }
    }

    public function getContents(): string
    {
        if (!$content = file_get_contents($this->filePath)) {
            throw new \RuntimeException(sprintf(
                'Unavailable to read "%s" content.',
                $this->filePath
            ));
        }

        return $content;
    }

    /**
     * @see https://www.php.net/manual/fr/function.fopen.php
     * @return resource
     */
    public function open($mode = 'r')
    {
        // already opened ?
        if ($this->fileStream) {
            // 2 écoles, on renvoie le stream courant, ou on stop le process.

            // return $this->fileStream;

            // ou

            $this->close();

            throw new \LogicException(sprintf(
                'File "%s" has already been opened.',
                $this->filePath
            ));

            // je préfère stopper le processus pour éviter de propager d'éventuelles erreurs
            // et de corrompre des données
        }

        if (!$this->fileStream = fopen($this->filePath, $mode)) {
            throw new \RuntimeException(sprintf(
                'Unavailable to open "%s" file in "%s" mode.',
                $this->filePath,
                $mode
            ));
        }

        return $this->fileStream;
    }

    /**
     * @see https://www.php.net/manual/fr/function.fclose.php
     * @throws RuntimeException
     * 
     * Peut-être un peu bourrin de jeter une exception si le fichier ne se ferme pas, mais c'est la responsabilité
     * de cette classe de faire ce travail, elle doit informer le système si quelque chose ne se passe pas bien.
     * Si le programme appelant ne souhaite pas en tenir compte, il pose un bloc try / catch / finally
     */
    public function close()
    {
        if (!$this->fileStream) {       // not opened ? it's ok
            return;
        }

        if (!fclose($this->fileStream)) {
            throw new \RuntimeException(sprintf(
                'Unavailable to close stream on file "%s".',
                $this->filePath
            ));
        }
    }

    /**
     * On force ici la fermeture du fichier si ouvert à la destruction de la dernière référence sur cet objet
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * On force la fermeture du flux fichier dans le clone
     */
    public function __clone()
    {
        $this->close();
    }

    public function __toString(): string
    {
        return $this->basename;
    }
}
