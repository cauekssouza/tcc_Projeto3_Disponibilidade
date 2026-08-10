```php
<?php

namespace geekcom\ValidatorDocs;

use geekcom\ValidatorDocs\Contracts\ValidatorFormats as Contract;
use Throwable;

class ValidatorFormats
{
    private const STRATEGY_NAMESPACE = 'geekcom\\ValidatorDocs\\Formats\\%s';

    public function execute(string $value, string $document): bool
    {
        $value = trim($value);
        $document = trim($document);

        if ($value === '' || $document === '') {
            return false;
        }

        $validator = $this->resolveValidator($document);

        if ($validator === null) {
            return false;
        }

        try {
            return $validator::validateFormat($value);
        } catch (Throwable $exception) {
            // Aqui pode ser adicionado logging, caso exista um logger na aplicação.
            return false;
        }
    }

    /**
     * @return class-string<Contract>|null
     */
    private function resolveValidator(string $document): ?string
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $document)) {
            return null;
        }

        $validator = sprintf(
            self::STRATEGY_NAMESPACE,
            ucfirst(strtolower($document))
        );

        if (!class_exists($validator)) {
            return null;
        }

        if (!is_subclass_of($validator, Contract::class)) {
            return null;
        }

        return $validator;
    }
}
```
