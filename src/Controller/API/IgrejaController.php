<?php

namespace App\Controller\API;

use App\Entity\Igreja;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException; 

#[Route('/api')]
class IgrejaController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {}

    /**
     * Endpoint para CADASTRAR uma nova Igreja.
     * Responde a requisições POST em /api/igreja
     */
    #[Route('/igreja', name: 'api_igreja_create', methods: ['POST'])]
    public function createIgreja(Request $request): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $igreja = new Igreja();

        $igreja->setNome($data['nome'] ?? null);
        $igreja->setDocTipo($data['docTipo'] ?? null);
        $igreja->setDocNumero($data['docNumero'] ?? null);
        $igreja->setCodigoInterno($data['codigoInterno'] ?? null);
        $igreja->setTelefone($data['telefone'] ?? null);
        $igreja->setEndLogradouro($data['endLogradouro'] ?? null);
        $igreja->setEndNumero($data['endNumero'] ?? null);
        $igreja->setEndComplemento($data['endComplemento'] ?? null);
        $igreja->setEndCidade($data['endCidade'] ?? null);
        $igreja->setEndEstado($data['endEstado'] ?? null);
        $igreja->setEndCep($data['endCep'] ?? null);
        $igreja->setWebsite($data['website'] ?? null);
        $igreja->setLimiteMembros((int)($data['limiteMembros'] ?? 0));
        
        $igreja->setDataCadastro(new \DateTime());
        $igreja->setDataUltimaAlteracao(new \DateTime()); 


        $errors = $this->validator->validate($igreja);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST); 
        }

        try {
            $this->em->persist($igreja);
            $this->em->flush(); 

        } catch (UniqueConstraintViolationException $e) {
            return $this->json(
                ['error' => 'O campo codigoInterno já está em uso.'],
                Response::HTTP_CONFLICT 
            );
        }

        return $this->json([
            'message' => 'Igreja cadastrada com sucesso!',
            'id' => $igreja->getId(),
            'data' => $igreja,
        ], Response::HTTP_CREATED, [], ['groups' => 'igreja:read']);
    }
}