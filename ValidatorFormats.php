<?php

namespace geekcom\ValidatorDocs;

use geekcom\ValidatorDocs\Contracts\ValidatorFormats as Contract;
use InvalidArgumentException;
use RuntimeException;

class ValidatorFormats
{
    private const STRATEGY_NAMESPACE = 'geekcom\\ValidatorDocs\\Formats\\';

    public function execute(string $value, string $document): bool
    {
        if ($value === '') {
            throw new InvalidArgumentException('Value not informed.');
        }

        if ($document === '') {
            throw new InvalidArgumentException('Document not informed.');
        }

        $validator = $this->resolveValidator($document);

        return $validator::validateFormat($value);
    }

    /**
     * @return class-string<Contract>
     */
    private function resolveValidator(string $document): string
    {
        $validator = self::STRATEGY_NAMESPACE . ucfirst($document);

        if (!class_exists($validator)) {
            throw new RuntimeException(
                sprintf('Validator for document "%s" does not exist.', $document)
            );
        }

        if (!is_subclass_of($validator, Contract::class)) {
            throw new RuntimeException(
                sprintf(
                    'Validator "%s" must implement %s.',
                    $validator,
                    Contract::class
                )
            );
        }

        return $validator;
    }
}
