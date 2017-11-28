<?php
/**
 * This file is part of Hydrogen package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Serafim\Hydrogen\Repository;

/**
 * Class PhpFileRepository
 */
abstract class PhpFileRepository extends MemoryRepository
{
    /**
     * @var array|null
     */
    private $loaded;

    /**
     * @return iterable
     * @throws \LogicException
     */
    public function getData(): iterable
    {
        if ($this->loaded === null) {
            $file = $this->getFilePath();

            if (! \is_readable($file)) {
                $error = \sprintf('Could not find php file "%s"', $file);
                throw new \LogicException($error);
            }

            \ob_start();
            $this->loaded = require $file;
            \ob_end_clean();

            if (! \is_iterable($this->loaded)) {
                $error = \sprintf('File "%s" contains non-iterable data.', $file);
                throw new \LogicException($error);
            }
        }

        return $this->loaded;
    }

    /**
     * @return string
     */
    abstract protected function getFilePath(): string;
}
