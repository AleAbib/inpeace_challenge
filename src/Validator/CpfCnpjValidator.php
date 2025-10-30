<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Respect\Validation\Validator as v;

class CpfCnpjValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CpfCnpj) {
            throw new UnexpectedTypeException($constraint, CpfCnpj::class);
        }

        $docTipo = $value->getDocTipo();
        $docNumero = $value->getDocNumero();

        if (null === $docNumero || '' === $docNumero) {
            return;
        }

        // Limpeza de robustez: remove caracteres não numéricos
        $docNumeroLimpo = preg_replace('/[^0-9]/', '', $docNumero);

        if ($docTipo === 'CPF') {
            if (!v::cpf()->validate($docNumeroLimpo)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ tipo }}', 'CPF')
                    ->atPath($constraint->docNumeroField)
                    ->addViolation();
            }
        } elseif ($docTipo === 'CNPJ') {
            if (!v::cnpj()->validate($docNumeroLimpo)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ tipo }}', 'CNPJ')
                    ->atPath($constraint->docNumeroField)
                    ->addViolation();
            }
        } else {
            $this->context->buildViolation($constraint->tipoInvalidoMessage)
                ->atPath($constraint->docTipoField)
                ->addViolation();
        }
    }
}