<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210128234427 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account_balance (id VARCHAR(255) NOT NULL, balance INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE accounts_max_transaction_volumes (account_id VARCHAR(255) NOT NULL, max_transaction_volume_id INTEGER NOT NULL, PRIMARY KEY(account_id, max_transaction_volume_id))');
        $this->addSql('CREATE INDEX IDX_76B66B859B6B5FBA ON accounts_max_transaction_volumes (account_id)');
        $this->addSql('CREATE INDEX IDX_76B66B85FDE20226 ON accounts_max_transaction_volumes (max_transaction_volume_id)');
        $this->addSql('CREATE TABLE max_transaction_volume (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, max_volume INTEGER NOT NULL)');
        $this->addSql('CREATE TABLE "transaction" (id VARCHAR(255) NOT NULL, account_id VARCHAR(255) DEFAULT NULL, amount INTEGER NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_723705D19B6B5FBA ON "transaction" (account_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE account_balance');
        $this->addSql('DROP TABLE accounts_max_transaction_volumes');
        $this->addSql('DROP TABLE max_transaction_volume');
        $this->addSql('DROP TABLE "transaction"');
    }
}
