<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180629062539 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE favorite_book_copy (user_id INT NOT NULL, book_copy_id INT NOT NULL, INDEX IDX_9E376518A76ED395 (user_id), INDEX IDX_9E3765183B550FE4 (book_copy_id), PRIMARY KEY(user_id, book_copy_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite_book_copy ADD CONSTRAINT FK_9E376518A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_book_copy ADD CONSTRAINT FK_9E3765183B550FE4 FOREIGN KEY (book_copy_id) REFERENCES bookCopy (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE book_ratings');
        $this->addSql('ALTER TABLE bookcopy DROP FOREIGN KEY FK_519E74D063379586');
        $this->addSql('DROP INDEX IDX_519E74D063379586 ON bookcopy');
        $this->addSql('ALTER TABLE bookcopy DROP comments_id');
        $this->addSql('ALTER TABLE user ADD plain_password VARCHAR(64) NOT NULL, CHANGE avatar avatar VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE comment ADD book_copy_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C3B550FE4 FOREIGN KEY (book_copy_id) REFERENCES bookCopy (id)');
        $this->addSql('CREATE INDEX IDX_9474526C3B550FE4 ON comment (book_copy_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE book_ratings (user_id INT NOT NULL, rating_id INT NOT NULL, INDEX IDX_5D962F5FA76ED395 (user_id), INDEX IDX_5D962F5FA32EFC6 (rating_id), PRIMARY KEY(user_id, rating_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_ratings ADD CONSTRAINT FK_5D962F5FA32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_ratings ADD CONSTRAINT FK_5D962F5FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE favorite_book_copy');
        $this->addSql('ALTER TABLE bookCopy ADD comments_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE bookCopy ADD CONSTRAINT FK_519E74D063379586 FOREIGN KEY (comments_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_519E74D063379586 ON bookCopy (comments_id)');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C3B550FE4');
        $this->addSql('DROP INDEX IDX_9474526C3B550FE4 ON comment');
        $this->addSql('ALTER TABLE comment DROP book_copy_id');
        $this->addSql('ALTER TABLE user DROP plain_password, CHANGE avatar avatar VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
