<?php

declare(strict_types=1);

namespace Helhum\TopImage\Definition;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;

class TCA
{
    /**
     * @var array<non-empty-string, mixed>
     */
    private readonly array $tca;

    /**
     * @param array<non-empty-string, mixed>|null $tca
     */
    public function __construct(
        ?array $tca,
    ) {
        $this->tca = $tca ?? $GLOBALS['TCA'];
    }

    public function get(string $path = null, mixed $default = null): mixed
    {
        if ($path === null) {
            return $this->tca;
        }
        try {
            return ArrayUtility::getValueByPath($this->tca, $path, '.');
        } catch (MissingArrayPathException $e) {
            if (\count(\func_get_args()) === 2) {
                return $default;
            }
            throw $e;
        }
    }

    public function set(string $path, mixed $value): self
    {
        return new self(ArrayUtility::setValueByPath($this->tca, $path, $value, '.'));
    }
}
