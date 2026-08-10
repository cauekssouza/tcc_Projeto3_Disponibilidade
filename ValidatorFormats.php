```php
<?php

declare(strict_types=1);

namespace geekcom\ValidatorDocs;

use geekcom\ValidatorDocs\Contracts\ValidatorFormats as Contract;
use geekcom\ValidatorDocs\Formats\Cpf;
use geekcom\ValidatorDocs\Formats\Cnpj;

class ValidatorFormats
{
    /**
     * Mapa estático de validadores permitidos.
     *
     * Evita resolução dinâmica de classes baseada diretamente
     * em entrada controlada pelo usuário (CWE-470).
     *
     * @var array<string, class-string<Contract>>
     */
    private const VALIDATORS = [
        'cpf' => Cpf::class,
        'cnpj' => Cnpj::class,
    ];

    public function execute(string $value, string $document): bool
    {
        $value = trim($value);
        $document = strtolower(trim($document));

        // Fail-closed: entradas inválidas não geram exceções não tratadas.
        if ($value === '' || $document === '') {
            return false;
        }

        // Apenas documentos explicitamente permitidos podem ser resolvidos.
        $validator = self::VALIDATORS[$document] ?? null;

        if ($validator === null) {
            return false;
        }

        /*
         * Valida a implementação do contrato sem criar uma instância,
         * evitando consumo desnecessário de memória/CPU (CWE-400).
         */
        if (!is_subclass_of($validator, Contract::class)) {
            return false;
        }

        return $validator::validateFormat($value);
    }
}
```
