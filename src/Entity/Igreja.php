<?php

namespace App\Entity;

use App\Repository\IgrejaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: IgrejaRepository::class)]
class Igreja
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'O nome não pode estar em branco.')]
    #[Assert\Length(
        min: 3,
        minMessage: 'O nome deve ter pelo menos {{ limit }} caracteres.'
    )]
    #[ORM\Column(length: 255)]
    private ?string $nome = null;

    #[Assert\NotBlank(message: 'O tipo do documento não pode estar em branco.')]
    #[Assert\Choice(
        choices: ['CPF', 'CNPJ'],
        message: 'O tipo do documento deve ser "CPF" ou "CNPJ".'
    )]
    #[ORM\Column(length: 20)]
    private ?string $docTipo = null;

    #[Assert\NotBlank(message: 'O número do documento não pode estar em branco.')]
    #[ORM\Column(length: 50)]
    private ?string $docNumero = null;

    #[Assert\NotBlank(message: 'O código interno não pode estar em branco.')]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $codigoInterno = null;

    #[ORM\Column(length: 50)]
    private ?string $telefone = null;

    #[Assert\NotBlank(message: 'O logradouro não pode estar em branco.')]
    #[ORM\Column(length: 255)]
    private ?string $endLogradouro = null;

    #[Assert\NotBlank(message: 'O número do endereço não pode estar em branco.')]
    #[ORM\Column(length: 30)]
    private ?string $endNumero = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $endComplemento = null;

    #[Assert\NotBlank(message: 'A cidade não pode estar em branco.')]
    #[ORM\Column(length: 100)]
    private ?string $endCidade = null;

    #[Assert\NotBlank(message: 'O estado não pode estar em branco.')]
    #[Assert\Length(min: 2, max: 2, exactMessage: 'O estado deve ter 2 caracteres (ex: SP).')]
    #[ORM\Column(length: 2)]
    private ?string $endEstado = null;

    #[Assert\NotBlank(message: 'O CEP não pode estar em branco.')]
    #[ORM\Column(length: 10)]
    private ?string $endCep = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[Assert\NotBlank(message: 'O limite de membros não pode estar em branco.')]
    #[Assert\Type(type: 'integer', message: 'O limite de membros deve ser um número inteiro.')]
    #[Assert\Positive(message: 'O limite de membros deve ser um número positivo.')]
    #[ORM\Column]
    private ?int $limiteMembros = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dataCadastro = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dataUltimaAlteracao = null;

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

    public function getCodigoInterno(): ?string
    {
        return $this->codigoInterno;
    }

    public function setCodigoInterno(string $codigoInterno): static
    {
        $this->codigoInterno = $codigoInterno;

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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): static
    {
        $this->website = $website;

        return $this;
    }

    public function getLimiteMembros(): ?int
    {
        return $this->limiteMembros;
    }

    public function setLimiteMembros(int $limiteMembros): static
    {
        $this->limiteMembros = $limiteMembros;

        return $this;
    }

    public function getDataCadastro(): ?\DateTimeInterface
    {
        return $this->dataCadastro;
    }

    public function setDataCadastro(?\DateTimeInterface $dataCadastro): static
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
}
