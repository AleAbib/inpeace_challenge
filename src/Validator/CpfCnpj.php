<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class CpfCnpj extends Constraint
{
    public string $message = 'O documento informado não é um {{ tipo }} válido.';
    public string $tipoInvalidoMessage = 'O campo docTipo deve ser "CPF" ou "CNPJ".';
    public string $docTipoField = 'docTipo';
    public string $docNumeroField = 'docNumero';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}