<?php

namespace geekcom\ValidatorDocs;

use geekcom\ValidatorDocs\Contracts\ValidatorFormats as Contract;
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

        if (!class_exists($validatorClass)) {
            throw new RuntimeException(
                sprintf('Validator for document "%s" does not exist.', $document)
            );
        }

        if (!is_subclass_of($validatorClass, Contract::class)) {
            throw new RuntimeException(
                sprintf(
                    'Validator "%s" must implement %s.',
                    $validatorClass,
                    Contract::class
                )
            );
        }

        return $validatorClass::validateFormat($value);
    }

    private function resolveValidatorClass(string $document): string
    {
        return sprintf(
            self::STRATEGY_NAMESPACE,
            ucfirst($document)
        );
    }
}
