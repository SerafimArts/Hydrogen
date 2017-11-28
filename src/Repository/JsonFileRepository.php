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
 * Class JsonFileRepository
 */
abstract class JsonFileRepository extends MemoryRepository
{
    /**
     * @var array|null
     */
    private $loaded;

    /**
     * @return string
     */
    abstract protected function getFilePath(): string;

    /**
     * @return iterable
     * @throws \LogicException
     */
    public function getData(): iterable
    {
        if ($this->loaded === null) {
            $file = $this->getFilePath();

            if (! \is_readable($file) || ($data = @\file_get_contents($file)) === false) {
                $error = \sprintf('Could not read json file "%s"', $file);
                throw new \LogicException($error);
            }

            $this->loaded = @\json_decode($data, true);

            if (\json_last_error() !== \JSON_ERROR_NONE) {
                $error = \sprintf('Could not parse json body of file "%s": %s', $file, \json_last_error_msg());
                throw new \LogicException($error);
            }
        }

        return $this->loaded;
    }
}
