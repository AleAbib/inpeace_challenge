<?php

namespace App\Controller\API;

use App\Entity\Membro;
use App\Repository\IgrejaRepository;
use App\Repository\MembroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

#[Route('/api')]
class MembroController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private IgrejaRepository $igrejaRepository, 
        private MembroRepository $membroRepository 
    ) {}

    /**
     * Endpoint para CADASTRAR um novo Membro.
     * Responde a requisições POST em /api/membro
     */
    #[Route('/membro', name: 'api_membro_create', methods: ['POST'])]
    public function createMembro(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $igrejaId = $data['igrejaId'] ?? null;
        if (!$igrejaId) {
            return $this->json(['error' => 'O campo igrejaId é obrigatório.'], Response::HTTP_BAD_REQUEST);
        }
        
        $igreja = $this->igrejaRepository->find($igrejaId);
        if (!$igreja) {
            return $this->json(['error' => 'A igreja especificada não foi encontrada.'], Response::HTTP_NOT_FOUND); 
        }

        if ($igreja->getMembros()->count() >= $igreja->getLimiteMembros()) {
            return $this->json(
                ['error' => 'Esta igreja já atingiu seu limite máximo de membros.'],
                Response::HTTP_CONFLICT 
            );
        }

        $email = $data['email'] ?? null;
        $existingMembro = $this->membroRepository->findOneBy(['email' => $email, 'igreja' => $igreja]);
        
        if ($existingMembro) {
            return $this->json(
                ['error' => 'Este email já está cadastrado para esta igreja.'],
                Response::HTTP_CONFLICT 
            );
        }

        $membro = new Membro();
        $membro->setNome($data['nome'] ?? null);
        $membro->setDocTipo($data['docTipo'] ?? null);
        $membro->setDocNumero($data['docNumero'] ?? null);
        $membro->setEmail($email); // Já temos o email da validação
        $membro->setTelefone($data['telefone'] ?? null);
        $membro->setEndLogradouro($data['endLogradouro'] ?? null);
        $membro->setEndNumero($data['endNumero'] ?? null);
        $membro->setEndComplemento($data['endComplemento'] ?? null);
        $membro->setEndCidade($data['endCidade'] ?? null);
        $membro->setEndEstado($data['endEstado'] ?? null);
        $membro->setEndCep($data['endCep'] ?? null);
        
        $membro->setDataCadastro(new \DateTime());
        $membro->setDataUltimaAlteracao(new \DateTime());

        if (!empty($data['dataNascimento'])) {
            try {
                $membro->setDataNascimento(new \DateTime($data['dataNascimento']));
            } catch (\Exception $e) {
                return $this->json(['error' => 'Formato de dataNascimento inválido. Use YYYY-MM-DD.'], Response::HTTP_BAD_REQUEST);
            }
        }
        
        $membro->setIgreja($igreja);

        $errors = $this->validator->validate($membro);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST); 
        }

        $this->em->persist($membro);
        $this->em->flush();

        return $this->json([
            'message' => 'Membro cadastrado com sucesso!',
            'data' => $membro,
        ], Response::HTTP_CREATED);
        

    }

}