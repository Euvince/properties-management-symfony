<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240416083117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE heating (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, property_type_id INT NOT NULL, title VARCHAR(255) NOT NULL, surface DOUBLE PRECISION NOT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, rooms INT NOT NULL, bedrooms INT NOT NULL, floor INT NOT NULL, adress VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, sold TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(255) NOT NULL, picture VARCHAR(255) DEFAULT NULL, INDEX IDX_8BF21CDE9C81C6EB (property_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property_heating (property_id INT NOT NULL, heating_id INT NOT NULL, INDEX IDX_B602226549213EC (property_id), INDEX IDX_B602226520F822 (heating_id), PRIMARY KEY(property_id, heating_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property_specificity (property_id INT NOT NULL, specificity_id INT NOT NULL, INDEX IDX_55458E66549213EC (property_id), INDEX IDX_55458E665F69A929 (specificity_id), PRIMARY KEY(property_id, specificity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specificity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE property ADD CONSTRAINT FK_8BF21CDE9C81C6EB FOREIGN KEY (property_type_id) REFERENCES property_type (id)');
        $this->addSql('ALTER TABLE property_heating ADD CONSTRAINT FK_B602226549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property_heating ADD CONSTRAINT FK_B602226520F822 FOREIGN KEY (heating_id) REFERENCES heating (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property_specificity ADD CONSTRAINT FK_55458E66549213EC FOREIGN KEY (property_id) REFERENCES property (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE property_specificity ADD CONSTRAINT FK_55458E665F69A929 FOREIGN KEY (specificity_id) REFERENCES specificity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE property DROP FOREIGN KEY FK_8BF21CDE9C81C6EB');
        $this->addSql('ALTER TABLE property_heating DROP FOREIGN KEY FK_B602226549213EC');
        $this->addSql('ALTER TABLE property_heating DROP FOREIGN KEY FK_B602226520F822');
        $this->addSql('ALTER TABLE property_specificity DROP FOREIGN KEY FK_55458E66549213EC');
        $this->addSql('ALTER TABLE property_specificity DROP FOREIGN KEY FK_55458E665F69A929');
        $this->addSql('DROP TABLE heating');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE property_heating');
        $this->addSql('DROP TABLE property_specificity');
        $this->addSql('DROP TABLE property_type');
        $this->addSql('DROP TABLE specificity');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
