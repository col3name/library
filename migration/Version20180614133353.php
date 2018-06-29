<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180614133353 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE publishing_house (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C868111A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publishing_house_of_book (publishing_house_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_B855ECB67402924 (publishing_house_id), INDEX IDX_B855ECB16A2B381 (book_id), PRIMARY KEY(publishing_house_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, book_copy_id INT NOT NULL, text VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_D8892622F675F31B (author_id), INDEX IDX_D88926223B550FE4 (book_copy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_ratings (user_id INT NOT NULL, rating_id INT NOT NULL, INDEX IDX_5D962F5FA76ED395 (user_id), INDEX IDX_5D962F5FA32EFC6 (rating_id), PRIMARY KEY(user_id, rating_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE publishing_house_of_book ADD CONSTRAINT FK_B855ECB67402924 FOREIGN KEY (publishing_house_id) REFERENCES publishing_house (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publishing_house_of_book ADD CONSTRAINT FK_B855ECB16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926223B550FE4 FOREIGN KEY (book_copy_id) REFERENCES bookCopy (id)');
        $this->addSql('ALTER TABLE book_ratings ADD CONSTRAINT FK_5D962F5FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_ratings ADD CONSTRAINT FK_5D962F5FA32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book ADD description LONGTEXT NOT NULL, ADD isbn VARCHAR(13) NOT NULL, ADD page_number INT NOT NULL, ADD publication_year INT NOT NULL');
        $this->addSql('ALTER TABLE bookcopy ADD image_path VARCHAR(255) NOT NULL, ADD file_path VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE publishing_house_of_book DROP FOREIGN KEY FK_B855ECB67402924');
        $this->addSql('ALTER TABLE book_ratings DROP FOREIGN KEY FK_5D962F5FA32EFC6');
        $this->addSql('DROP TABLE publishing_house');
        $this->addSql('DROP TABLE publishing_house_of_book');
        $this->addSql('DROP TABLE rating');
        $this->addSql('DROP TABLE book_ratings');
        $this->addSql('ALTER TABLE book DROP description, DROP isbn, DROP page_number, DROP publication_year');
        $this->addSql('ALTER TABLE bookCopy DROP image_path, DROP file_path');
    }
}
