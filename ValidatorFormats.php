<?php

declare(strict_types=1);

namespace geekcom\ValidatorDocs;

use geekcom\ValidatorDocs\Contracts\ValidatorFormats as Contract;
use geekcom\ValidatorDocs\Formats\Cnpj;
use geekcom\ValidatorDocs\Formats\Cpf;

final class ValidatorFormats
{
    /**
     * Allowlist explícita de validadores suportados.
     *
     * Evita resolução arbitrária de classes baseada diretamente
     * em entrada externa (CWE-470).
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

        /*
         * Fail-safe / fail-closed:
         * entradas inválidas são rejeitadas sem lançar uma exceção
         * capaz de interromper o fluxo da aplicação.
         */
        if ($value === '' || $document === '') {
            return false;
        }

        /*
         * A entrada nunca é utilizada para montar um nome de classe.
         * Somente classes presentes na allowlist podem ser resolvidas.
         */
        $validator = self::VALIDATORS[$document] ?? null;

        if ($validator === null) {
            return false;
        }

        /*
         * Defesa adicional contra configuração incorreta da allowlist.
         *
         * is_subclass_of() verifica o contrato sem instanciar a classe,
         * evitando "new $validator()" e consumo desnecessário de recursos.
         */
        if (!is_subclass_of($validator, Contract::class)) {
            return false;
        }

        return $validator::validateFormat($value);
    }
}
