<?php

namespace geekcom\ValidatorDocs;

use geekcom\ValidatorDocs\Contracts\ValidatorFormats as Contract;
use Throwable;

class ValidatorFormats
{
    private const STRATEGY_NAMESPACE = 'geekcom\\ValidatorDocs\\Formats\\%s';

    public function execute(string $value, string $document): bool
    {
        if (trim($value) === '' || trim($document) === '') {
            return false;
        }

        $validator = sprintf(
            self::STRATEGY_NAMESPACE,
            ucfirst(trim($document))
        );

        if (!class_exists($validator)) {
            return false;
        }

        if (!is_subclass_of($validator, Contract::class)) {
            return false;
        }

        try {
            return $validator::validateFormat($value);
        } catch (Throwable $exception) {
            return false;
        }
    }
}
