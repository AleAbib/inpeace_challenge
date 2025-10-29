<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251029181458 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE igreja (id INT AUTO_INCREMENT NOT NULL, nome VARCHAR(255) NOT NULL, doc_tipo VARCHAR(20) NOT NULL, doc_numero VARCHAR(50) NOT NULL, codigo_interno VARCHAR(255) NOT NULL, telefone VARCHAR(50) NOT NULL, end_logradouro VARCHAR(255) NOT NULL, end_numero VARCHAR(30) NOT NULL, end_complemento VARCHAR(100) DEFAULT NULL, end_cidade VARCHAR(100) NOT NULL, end_estado VARCHAR(2) NOT NULL, end_cep VARCHAR(10) NOT NULL, website VARCHAR(255) DEFAULT NULL, limite_membros INT NOT NULL, data_cadastro DATETIME DEFAULT NULL, data_ultima_alteracao DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_9FA6E75369D2337E (codigo_interno), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE igreja');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
