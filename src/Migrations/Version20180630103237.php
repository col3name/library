<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180630103237 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_desired_book DROP FOREIGN KEY FK_E80C42D032EDFDCB');
        $this->addSql('ALTER TABLE publishing_house_of_book DROP FOREIGN KEY FK_B855ECB67402924');
        $this->addSql('DROP TABLE desired_book');
        $this->addSql('DROP TABLE publishing_house');
        $this->addSql('DROP TABLE publishing_house_of_book');
        $this->addSql('DROP TABLE user_desired_book');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE desired_book (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, url VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, status VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publishing_house (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, UNIQUE INDEX UNIQ_C868111A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publishing_house_of_book (publishing_house_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_B855ECB67402924 (publishing_house_id), INDEX IDX_B855ECB16A2B381 (book_id), PRIMARY KEY(publishing_house_id, book_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_desired_book (user_id INT NOT NULL, desired_book_id INT NOT NULL, INDEX IDX_E80C42D0A76ED395 (user_id), INDEX IDX_E80C42D032EDFDCB (desired_book_id), PRIMARY KEY(user_id, desired_book_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE publishing_house_of_book ADD CONSTRAINT FK_B855ECB16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publishing_house_of_book ADD CONSTRAINT FK_B855ECB67402924 FOREIGN KEY (publishing_house_id) REFERENCES publishing_house (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_desired_book ADD CONSTRAINT FK_E80C42D032EDFDCB FOREIGN KEY (desired_book_id) REFERENCES desired_book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_desired_book ADD CONSTRAINT FK_E80C42D0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
