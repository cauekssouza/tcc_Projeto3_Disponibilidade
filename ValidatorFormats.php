<?php

namespace geekcom\ValidatorDocs;

use geekcom\ValidatorDocs\Contracts\ValidatorFormats as ValidatorContract;
use InvalidArgumentException;
use RuntimeException;

class ValidatorFormats
{
    private const STRATEGY_NAMESPACE = 'geekcom\\ValidatorDocs\\Formats\\%s';

    public function execute(string $value, string $document): bool
    {
        if ($value === '') {
            throw new InvalidArgumentException('Value not informed.');
        }

        if ($document === '') {
            throw new InvalidArgumentException('Document not informed.');
        }

        $validatorClass = $this->resolveValidatorClass($document);

        return $validatorClass::validateFormat($value);
    }

    private function resolveValidatorClass(string $document): string
    {
        $class = sprintf(
            self::STRATEGY_NAMESPACE,
            ucfirst($document)
        );

        if (!class_exists($class)) {
            throw new RuntimeException(
                sprintf('Validator for document "%s" does not exist.', $document)
            );
        }

        if (!is_subclass_of($class, ValidatorContract::class)) {
            throw new RuntimeException(
                sprintf(
                    'Validator "%s" must implement %s.',
                    $class,
                    ValidatorContract::class
                )
            );
        }

        return $class;
    }
}
