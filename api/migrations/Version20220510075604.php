<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220510075604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ban (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, date INT NOT NULL, is_permanent TINYINT(1) DEFAULT 0 NOT NULL, reason LONGTEXT DEFAULT NULL, date_deban INT DEFAULT NULL, UNIQUE INDEX UNIQ_62FED0E5A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, subject_id INT NOT NULL, user_id INT DEFAULT NULL, message LONGTEXT NOT NULL, date INT NOT NULL, INDEX IDX_9474526C23EDC87 (subject_id), INDEX IDX_9474526CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE form_deban (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ban_id INT NOT NULL, message LONGTEXT NOT NULL, date INT NOT NULL, is_refused TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_B8908C95A76ED395 (user_id), UNIQUE INDEX UNIQ_B8908C951255CD1D (ban_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, date INT NOT NULL, is_closed TINYINT(1) DEFAULT 0, INDEX IDX_FBCE3E7AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT \'default.jpeg\' NOT NULL, is_admin TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ban ADD CONSTRAINT FK_62FED0E5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE form_deban ADD CONSTRAINT FK_B8908C95A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE form_deban ADD CONSTRAINT FK_B8908C951255CD1D FOREIGN KEY (ban_id) REFERENCES ban (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form_deban DROP FOREIGN KEY FK_B8908C951255CD1D');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C23EDC87');
        $this->addSql('ALTER TABLE ban DROP FOREIGN KEY FK_62FED0E5A76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE form_deban DROP FOREIGN KEY FK_B8908C95A76ED395');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AA76ED395');
        $this->addSql('DROP TABLE ban');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE form_deban');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE user');
    }
}
