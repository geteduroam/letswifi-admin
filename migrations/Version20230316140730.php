<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316140730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1027Platform'."
        );

        $this->addSql('CREATE TABLE realm_contact (id INT AUTO_INCREMENT NOT NULL, realm VARCHAR(127) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_unicode_ci`, contact_id INT NOT NULL, INDEX IDX_CONTACT_ID (contact_id), UNIQUE INDEX index_realm_contact (realm, contact_id), INDEX IDX_CF88878CFA96DBDA (realm), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE realm_helpdesk (id INT AUTO_INCREMENT NOT NULL, realm VARCHAR(127) NOT NULL, email_address VARCHAR(255) DEFAULT NULL, web VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, lang VARCHAR(4) NOT NULL, name VARCHAR(50) NOT NULL, INDEX IDX_D1E34E45FA96DBDA (realm), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS realm_vhost (http_host VARCHAR(127) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_unicode_ci`, realm VARCHAR(127) CHARACTER SET utf8mb4 DEFAULT \'\' NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX realm (realm), PRIMARY KEY(http_host)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE vhost_realm (id INT AUTO_INCREMENT NOT NULL, realm VARCHAR(127) NOT NULL, http_host VARCHAR(255) NOT NULL, path_prefix VARCHAR(255) NOT NULL, auth_service VARCHAR(80) NOT NULL, auth_config LONGTEXT DEFAULT NULL, INDEX IDX_C76ED703FA96DBDA (realm), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name_id VARCHAR(150) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, display_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, user_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, super_admin TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE network_profile (id INT AUTO_INCREMENT NOT NULL, type_name VARCHAR(20) NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE realm_network_profile (id INT AUTO_INCREMENT NOT NULL, realm VARCHAR(127) NOT NULL, network_profile_id INT DEFAULT NULL, INDEX IDX_1B0010CFFA96DBDA (realm), INDEX IDX_1B0010CF15ABED04 (network_profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE realm_network_profile ADD CONSTRAINT FK_1B0010CFFA96DBDA FOREIGN KEY (realm) REFERENCES realm (realm)');
        $this->addSql('ALTER TABLE realm_network_profile ADD CONSTRAINT FK_1B0010CF15ABED04 FOREIGN KEY (network_profile_id) REFERENCES network_profile (id)');
        $this->addSql('CREATE VIEW realm_signing_user AS  SELECT requester, realm, COUNT(sub) AS accounts,  COUNT(revoked) AS closed_accounts, MIN(issued) AS  first_issued, MAX(expires) AS last_valid FROM realm_signing_log GROUP BY requester, realm');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            !$this->connection->getDatabasePlatform() instanceof \Doctrine\DBAL\Platforms\MariaDb1027Platform,
            "Migration can only be executed safely on '\Doctrine\DBAL\Platforms\MariaDb1027Platform'."
        );

        $this->addSql('DROP TABLE realm_contact');
        $this->addSql('DROP TABLE realm_helpdesk');
        $this->addSql('DROP TABLE realm_vhost');
        $this->addSql('ALTER TABLE vhost_realm DROP FOREIGN KEY FK_C76ED703FA96DBDA');
        $this->addSql('DROP TABLE vhost_realm');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE network_profile');
        $this->addSql('ALTER TABLE realm_network_profile DROP FOREIGN KEY FK_1B0010CFFA96DBDA');
        $this->addSql('ALTER TABLE realm_network_profile DROP FOREIGN KEY FK_1B0010CF15ABED04');
        $this->addSql('DROP TABLE realm_network_profile');
        $this->addSql('DROP VIEW realm_signing_user');
    }
}
