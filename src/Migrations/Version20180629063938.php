<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180629063938 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, isbn VARCHAR(13) NOT NULL, page_number INT NOT NULL, publication_year INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE bookCopy (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, count INT NOT NULL, image_path VARCHAR(255) DEFAULT NULL, file_path VARCHAR(255) DEFAULT NULL, upload_date DATETIME NOT NULL, INDEX IDX_519E74D016A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, plain_password VARCHAR(64) NOT NULL, password VARCHAR(64) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_favorites_comment (user_id INT NOT NULL, comment_id INT NOT NULL, INDEX IDX_D6B1617CA76ED395 (user_id), INDEX IDX_D6B1617CF8697D13 (comment_id), PRIMARY KEY(user_id, comment_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorite_book_copy (user_id INT NOT NULL, book_copy_id INT NOT NULL, INDEX IDX_9E376518A76ED395 (user_id), INDEX IDX_9E3765183B550FE4 (book_copy_id), PRIMARY KEY(user_id, book_copy_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE issuance (id INT AUTO_INCREMENT NOT NULL, reader_id INT DEFAULT NULL, book_copy_id INT NOT NULL, issue_date DATETIME NOT NULL, release_date DATETIME DEFAULT NULL, deadline_date DATETIME DEFAULT NULL, INDEX IDX_795965601717D737 (reader_id), INDEX IDX_795965603B550FE4 (book_copy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, book_copy_id INT NOT NULL, text VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_9474526CF675F31B (author_id), INDEX IDX_9474526C3B550FE4 (book_copy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BDAFD8C85E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE author_book (author_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_2F0A2BEEF675F31B (author_id), INDEX IDX_2F0A2BEE16A2B381 (book_id), PRIMARY KEY(author_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_835033F85E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genre_book (genre_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_70087AC14296D31F (genre_id), INDEX IDX_70087AC116A2B381 (book_id), PRIMARY KEY(genre_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publishing_house (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C868111A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publishing_house_of_book (publishing_house_id INT NOT NULL, book_id INT NOT NULL, INDEX IDX_B855ECB67402924 (publishing_house_id), INDEX IDX_B855ECB16A2B381 (book_id), PRIMARY KEY(publishing_house_id, book_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rating (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, book_copy_id INT NOT NULL, rating INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_D8892622F675F31B (author_id), INDEX IDX_D88926223B550FE4 (book_copy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bookCopy ADD CONSTRAINT FK_519E74D016A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE user_favorites_comment ADD CONSTRAINT FK_D6B1617CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_favorites_comment ADD CONSTRAINT FK_D6B1617CF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_book_copy ADD CONSTRAINT FK_9E376518A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorite_book_copy ADD CONSTRAINT FK_9E3765183B550FE4 FOREIGN KEY (book_copy_id) REFERENCES bookCopy (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE issuance ADD CONSTRAINT FK_795965601717D737 FOREIGN KEY (reader_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE issuance ADD CONSTRAINT FK_795965603B550FE4 FOREIGN KEY (book_copy_id) REFERENCES bookCopy (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C3B550FE4 FOREIGN KEY (book_copy_id) REFERENCES bookCopy (id)');
        $this->addSql('ALTER TABLE author_book ADD CONSTRAINT FK_2F0A2BEEF675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE author_book ADD CONSTRAINT FK_2F0A2BEE16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genre_book ADD CONSTRAINT FK_70087AC14296D31F FOREIGN KEY (genre_id) REFERENCES genre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE genre_book ADD CONSTRAINT FK_70087AC116A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publishing_house_of_book ADD CONSTRAINT FK_B855ECB67402924 FOREIGN KEY (publishing_house_id) REFERENCES publishing_house (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE publishing_house_of_book ADD CONSTRAINT FK_B855ECB16A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D8892622F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rating ADD CONSTRAINT FK_D88926223B550FE4 FOREIGN KEY (book_copy_id) REFERENCES bookCopy (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE bookCopy DROP FOREIGN KEY FK_519E74D016A2B381');
        $this->addSql('ALTER TABLE author_book DROP FOREIGN KEY FK_2F0A2BEE16A2B381');
        $this->addSql('ALTER TABLE genre_book DROP FOREIGN KEY FK_70087AC116A2B381');
        $this->addSql('ALTER TABLE publishing_house_of_book DROP FOREIGN KEY FK_B855ECB16A2B381');
        $this->addSql('ALTER TABLE favorite_book_copy DROP FOREIGN KEY FK_9E3765183B550FE4');
        $this->addSql('ALTER TABLE issuance DROP FOREIGN KEY FK_795965603B550FE4');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C3B550FE4');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D88926223B550FE4');
        $this->addSql('ALTER TABLE user_favorites_comment DROP FOREIGN KEY FK_D6B1617CA76ED395');
        $this->addSql('ALTER TABLE favorite_book_copy DROP FOREIGN KEY FK_9E376518A76ED395');
        $this->addSql('ALTER TABLE issuance DROP FOREIGN KEY FK_795965601717D737');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE rating DROP FOREIGN KEY FK_D8892622F675F31B');
        $this->addSql('ALTER TABLE user_favorites_comment DROP FOREIGN KEY FK_D6B1617CF8697D13');
        $this->addSql('ALTER TABLE author_book DROP FOREIGN KEY FK_2F0A2BEEF675F31B');
        $this->addSql('ALTER TABLE genre_book DROP FOREIGN KEY FK_70087AC14296D31F');
        $this->addSql('ALTER TABLE publishing_house_of_book DROP FOREIGN KEY FK_B855ECB67402924');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE bookCopy');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_favorites_comment');
        $this->addSql('DROP TABLE favorite_book_copy');
        $this->addSql('DROP TABLE issuance');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE author_book');
        $this->addSql('DROP TABLE genre');
        $this->addSql('DROP TABLE genre_book');
        $this->addSql('DROP TABLE publishing_house');
        $this->addSql('DROP TABLE publishing_house_of_book');
        $this->addSql('DROP TABLE rating');
    }
}
