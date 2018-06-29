<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180614130147 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE issuance (id INT AUTO_INCREMENT NOT NULL, reader_id INT DEFAULT NULL, book_copy_id INT NOT NULL, issue_date DATETIME NOT NULL, release_date DATETIME DEFAULT NULL, deadline_date DATETIME DEFAULT NULL, INDEX IDX_795965601717D737 (reader_id), INDEX IDX_795965603B550FE4 (book_copy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE issuance ADD CONSTRAINT FK_795965601717D737 FOREIGN KEY (reader_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE issuance ADD CONSTRAINT FK_795965603B550FE4 FOREIGN KEY (book_copy_id) REFERENCES bookCopy (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE issuance');
    }
}
