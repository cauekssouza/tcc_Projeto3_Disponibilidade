```php
<?php

declare(strict_types=1);

namespace geekcom\ValidatorDocs;

use geekcom\ValidatorDocs\Contracts\ValidatorFormats as Contract;
use geekcom\ValidatorDocs\Formats\Cnpj;
use geekcom\ValidatorDocs\Formats\Cpf;

final class ValidatorFormats
{
    /**
     * Mapa estático de validadores permitidos.
     *
     * Impede que entrada externa seja utilizada diretamente
     * para resolução ou carregamento arbitrário de classes.
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

        // Fail-closed: entrada inválida não gera exceção não tratada.
        if ($value === '' || $document === '') {
            return false;
        }

        // Apenas documentos explicitamente permitidos podem ser resolvidos.
        $validator = self::VALIDATORS[$document] ?? null;

        if ($validator === null) {
            return false;
        }

        /*
         * Evita "new $validator()" somente para verificar o contrato,
         * reduzindo instanciações e consumo desnecessário de recursos.
         */
        if (!is_subclass_of($validator, Contract::class)) {
            return false;
        }

        /*
         * A classe vem exclusivamente da allowlist.
         * Nenhum nome de classe é construído a partir da entrada externa.
         */
        return $validator::validateFormat($value);
    }
}
```
