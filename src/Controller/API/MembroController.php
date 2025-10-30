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

    #[Route('/membro/{id}/transferir', name: 'api_membro_transfer', methods: ['POST'])]
    public function transferirMembro(int $id, Request $request): JsonResponse
    {
        $membro = $this->membroRepository->find($id);
        if (!$membro) {
            return $this->json(['error' => 'Membro não encontrado.'], Response::HTTP_NOT_FOUND); 
        }

        $data = json_decode($request->getContent(), true);
        $igrejaDestinoId = $data['igrejaDestinoId'] ?? null;

        if (!$igrejaDestinoId) {
            return $this->json(['error' => 'O campo igrejaDestinoId é obrigatório.'], Response::HTTP_BAD_REQUEST);
        }

        $igrejaDestino = $this->igrejaRepository->find($igrejaDestinoId);
        if (!$igrejaDestino) {
            return $this->json(['error' => 'A igreja de destino não foi encontrada.'], Response::HTTP_NOT_FOUND); // 404
        }


        if ($membro->getIgreja()->getId() === $igrejaDestino->getId()) {
            return $this->json(['error' => 'O membro já pertence a esta igreja.'], Response::HTTP_BAD_REQUEST);
        }

        $dataUltimaTransferencia = $membro->getDataUltimaTransferencia();
        if ($dataUltimaTransferencia !== null) {
            $hoje = new \DateTime();
            $diferencaDias = $dataUltimaTransferencia->diff($hoje)->days;

            if ($diferencaDias < 10) {
                return $this->json(
                    ['error' => 'Este membro só pode ser transferido novamente em ' . (10 - $diferencaDias) . ' dias.'],
                    Response::HTTP_CONFLICT 
                );
            }
        }

        $email = $membro->getEmail();
        $existingMembro = $this->membroRepository->findOneBy(['email' => $email, 'igreja' => $igrejaDestino]);
        if ($existingMembro) {
            return $this->json(
                ['error' => 'A igreja de destino já possui um membro com este e-mail.'],
                Response::HTTP_CONFLICT 
            );
        }

        if ($igrejaDestino->getMembros()->count() >= $igrejaDestino->getLimiteMembros()) {
            return $this->json(
                ['error' => 'A igreja de destino já atingiu seu limite máximo de membros.'],
                Response::HTTP_CONFLICT 
            );
        }


        $membro->setIgreja($igrejaDestino); 
        $membro->setDataUltimaTransferencia(new \DateTime()); 
        $membro->setDataUltimaAlteracao(new \DateTime()); 

        $this->em->flush();

        return $this->json(
            $membro,
            Response::HTTP_OK, 
            [],
            ['groups' => ['membro:read', 'igreja:read']]
        );
    }

    #[Route('/membro/{id}', name: 'api_membro_delete', methods: ['DELETE'])]
    public function deleteMembro(int $id): JsonResponse
    {
        $membro = $this->membroRepository->find($id);

        if (!$membro) {
            return $this->json(['error' => 'Membro não encontrado.'], Response::HTTP_NOT_FOUND); // 404
        }


        $this->em->remove($membro);
        $this->em->flush(); 

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/membro/{id}', name: 'api_membro_update', methods: ['PATCH'])]
    public function updateMembro(int $id, Request $request): JsonResponse
    {
        $membro = $this->membroRepository->find($id);

        if (!$membro) {
            return $this->json(['error' => 'Membro não encontrado.'], Response::HTTP_NOT_FOUND); // 404
        }

        $data = json_decode($request->getContent(), true);

        if (array_key_exists('email', $data) && $data['email'] !== $membro->getEmail()) {

            $existingMembro = $this->membroRepository->findOneBy(['email' => $data['email'], 'igreja' => $membro->getIgreja()]);
            if ($existingMembro) {
                return $this->json(
                    ['error' => 'Este email (' . $data['email'] . ') já está cadastrado para esta igreja.'],
                    Response::HTTP_CONFLICT 
                );
            }
            $membro->setEmail($data['email']);
        }

        if (array_key_exists('nome', $data)) {
            $membro->setNome($data['nome']);
        }
        if (array_key_exists('docTipo', $data)) {
            $membro->setDocTipo($data['docTipo']);
        }
        if (array_key_exists('docNumero', $data)) {
            $membro->setDocNumero($data['docNumero']);
        }
        if (array_key_exists('telefone', $data)) {
            $membro->setTelefone($data['telefone']);
        }
        if (array_key_exists('endLogradouro', $data)) {
            $membro->setEndLogradouro($data['endLogradouro']);
        }
        if (array_key_exists('endNumero', $data)) {
            $membro->setEndNumero($data['endNumero']);
        }
        if (array_key_exists('endCidade', $data)) {
            $membro->setEndCidade($data['endCidade']);
        }
        if (array_key_exists('endEstado', $data)) {
            $membro->setEndEstado($data['endEstado']);
        }
        if (array_key_exists('endCep', $data)) {
            $membro->setEndCep($data['endCep']);
        }
        if (array_key_exists('endComplemento', $data)) {
            $membro->setEndComplemento($data['endComplemento']);
        }
        if (array_key_exists('dataNascimento', $data)) {
            try {
                $membro->setDataNascimento(new \DateTime($data['dataNascimento']));
            } catch (\Exception $e) {
                return $this->json(['error' => 'Formato de dataNascimento inválido. Use YYYY-MM-DD.'], Response::HTTP_BAD_REQUEST);
            }
        }

        $membro->setDataUltimaAlteracao(new \DateTime());

        $errors = $this->validator->validate($membro);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST); 
        }

        $this->em->flush(); 

        return $this->json(
            $membro,
            Response::HTTP_OK, 
            [],
            ['groups' => ['membro:read', 'igreja:read']]
        );
    }

    #[Route('/membro/{id}', name: 'api_membro_show', methods: ['GET'])]
    public function showMembro(int $id): JsonResponse 
    {
        $membro = $this->membroRepository->find($id);

        if (!$membro) {
            return $this->json(['error' => 'Membro não encontrado.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json(
            $membro, 
            Response::HTTP_OK, 
            [], 
            ['groups' => ['membro:read', 'igreja:read']] 
        );
    }

    #[Route('/igreja/{id}/membros', name: 'api_membros_por_igreja_list', methods: ['GET'])]
    public function listMembrosPorIgreja(int $id): JsonResponse 
    {
        $igreja = $this->igrejaRepository->find($id);

        if (!$igreja) {
            return $this->json(['error' => 'Igreja não encontrada.'], Response::HTTP_NOT_FOUND);
        }

        $membros = $igreja->getMembros();

        return $this->json(
            $membros, 
            Response::HTTP_OK, 
            [], 
            ['groups' => ['membro:read', 'igreja:read']] 
        );
    }
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
        ], Response::HTTP_CREATED, [], ['groups' => ['membro:read', 'igreja:read']]);
        

    }


}