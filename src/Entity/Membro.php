<?php

namespace App\Entity;

use App\Repository\MembroRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MembroRepository::class)]
class Membro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['membro:read'])]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'O nome não pode estar em branco.')]
    #[Assert\Length(min: 3, minMessage: 'O nome deve ter pelo menos {{ limit }} caracteres.')]
    #[ORM\Column(length: 255)]
    #[Groups(['membro:read'])]
    private ?string $nome = null;

    #[Assert\NotBlank(message: 'O tipo do documento não pode estar em branco.')]
    #[Assert\Choice(choices: ['CPF', 'CNPJ'], message: 'O tipo do documento deve ser "CPF" ou "CNPJ".')]
    #[ORM\Column(length: 20)]
    #[Groups(['membro:read'])]
    private ?string $docTipo = null;

    #[Assert\NotBlank(message: 'O número do documento não pode estar em branco.')]
    #[ORM\Column(length: 50)]
    #[Groups(['membro:read'])]
    private ?string $docNumero = null;

    #[Assert\NotBlank(message: 'A data de nascimento não pode estar em branco.')]
    #[Assert\LessThanOrEqual(value: 'today', message: 'A data de nascimento não pode ser uma data futura.')]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['membro:read'])]
    private ?\DateTimeInterface $dataNascimento = null;

    #[Assert\NotBlank(message: 'O email não pode estar em branco.')]
    #[Assert\Email(message: 'O email "{{ value }}" não é um email válido.')]
    #[ORM\Column(length: 255)]
    #[Groups(['membro:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups(['membro:read'])]
    private ?string $telefone = null;

    #[Assert\NotBlank(message: 'O logradouro não pode estar em branco.')]
    #[ORM\Column(length: 255)]
    #[Groups(['membro:read'])]
    private ?string $endLogradouro = null;

    #[Assert\NotBlank(message: 'O número do endereço não pode estar em branco.')]
    #[ORM\Column(length: 30)]
    #[Groups(['membro:read'])]
    private ?string $endNumero = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Groups(['membro:read'])]
    private ?string $endComplemento = null;

    #[Assert\NotBlank(message: 'A cidade não pode estar em branco.')]
    #[ORM\Column(length: 100)]
    #[Groups(['membro:read'])]
    private ?string $endCidade = null;

    #[Assert\NotBlank(message: 'O estado não pode estar em branco.')]
    #[Assert\Length(min: 2, max: 2, exactMessage: 'O estado deve ter 2 caracteres (ex: SP).')]
    #[Groups(['membro:read'])]
    #[ORM\Column(length: 2)]
    private ?string $endEstado = null;

    #[Assert\NotBlank(message: 'O CEP não pode estar em branco.')]
    #[ORM\Column(length: 10)]
    #[Groups(['membro:read'])]
    private ?string $endCep = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['membro:read'])]
    private ?\DateTimeInterface $dataCadastro = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['membro:read'])]
    private ?\DateTimeInterface $dataUltimaAlteracao = null;

    #[Assert\NotNull(message: 'A igreja é obrigatória.')] 
    #[ORM\ManyToOne(inversedBy: 'membros')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['membro:read'])]
    private ?Igreja $igreja = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): static
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDocTipo(): ?string
    {
        return $this->docTipo;
    }

    public function setDocTipo(string $docTipo): static
    {
        $this->docTipo = $docTipo;

        return $this;
    }

    public function getDocNumero(): ?string
    {
        return $this->docNumero;
    }

    public function setDocNumero(string $docNumero): static
    {
        $this->docNumero = $docNumero;

        return $this;
    }

    public function getDataNascimento(): ?\DateTimeInterface
    {
        return $this->dataNascimento;
    }

    public function setDataNascimento(\DateTimeInterface $dataNascimento): static
    {
        $this->dataNascimento = $dataNascimento;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefone(): ?string
    {
        return $this->telefone;
    }

    public function setTelefone(?string $telefone): static
    {
        $this->telefone = $telefone;

        return $this;
    }

    public function getEndLogradouro(): ?string
    {
        return $this->endLogradouro;
    }

    public function setEndLogradouro(string $endLogradouro): static
    {
        $this->endLogradouro = $endLogradouro;

        return $this;
    }

    public function getEndNumero(): ?string
    {
        return $this->endNumero;
    }

    public function setEndNumero(string $endNumero): static
    {
        $this->endNumero = $endNumero;

        return $this;
    }

    public function getEndComplemento(): ?string
    {
        return $this->endComplemento;
    }

    public function setEndComplemento(?string $endComplemento): static
    {
        $this->endComplemento = $endComplemento;

        return $this;
    }

    public function getEndCidade(): ?string
    {
        return $this->endCidade;
    }

    public function setEndCidade(string $endCidade): static
    {
        $this->endCidade = $endCidade;

        return $this;
    }

    public function getEndEstado(): ?string
    {
        return $this->endEstado;
    }

    public function setEndEstado(string $endEstado): static
    {
        $this->endEstado = $endEstado;

        return $this;
    }

    public function getEndCep(): ?string
    {
        return $this->endCep;
    }

    public function setEndCep(string $endCep): static
    {
        $this->endCep = $endCep;

        return $this;
    }

    public function getDataCadastro(): ?\DateTimeInterface
    {
        return $this->dataCadastro;
    }

    public function setDataCadastro(\DateTimeInterface $dataCadastro): static
    {
        $this->dataCadastro = $dataCadastro;

        return $this;
    }

    public function getDataUltimaAlteracao(): ?\DateTimeInterface
    {
        return $this->dataUltimaAlteracao;
    }

    public function setDataUltimaAlteracao(?\DateTimeInterface $dataUltimaAlteracao): static
    {
        $this->dataUltimaAlteracao = $dataUltimaAlteracao;

        return $this;
    }

    public function getIgreja(): ?Igreja
    {
        return $this->igreja;
    }

    public function setIgreja(?Igreja $igreja): static
    {
        $this->igreja = $igreja;

        return $this;
    }
}
