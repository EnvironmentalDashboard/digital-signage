<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190301001138 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE presentation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, template_id INTEGER NOT NULL, display_id INTEGER NOT NULL, label VARCHAR(255) NOT NULL, skip BOOLEAN NOT NULL, duration INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_9B66E8935DA0FB8 ON presentation (template_id)');
        $this->addSql('CREATE INDEX IDX_9B66E89351A2DF33 ON presentation (display_id)');
        $this->addSql('CREATE TABLE display (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE frame (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, carousel_id INTEGER NOT NULL, url VARCHAR(2000) NOT NULL, duration INTEGER NOT NULL)');
        $this->addSql('CREATE INDEX IDX_B5F83CCDC1CE5B98 ON frame (carousel_id)');
        $this->addSql('CREATE TABLE carousel (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, label VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE template (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, twig CLOB NOT NULL)');
        $this->addSql('CREATE TABLE presentation_carousel_map (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, template_id INTEGER NOT NULL, carousel_id INTEGER NOT NULL, twig_key VARCHAR(20) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A23C0DC5DA0FB8 ON presentation_carousel_map (template_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A23C0DCC1CE5B98 ON presentation_carousel_map (carousel_id)');
        $this->addSql('CREATE TABLE remote_controller (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, template_id INTEGER NOT NULL, label VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_D084D9AC5DA0FB8 ON remote_controller (template_id)');
        $this->addSql('CREATE TABLE button (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, controller_id INTEGER NOT NULL, trigger_frame_id INTEGER NOT NULL, on_display_id INTEGER NOT NULL, twig_key VARCHAR(20) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_3A06AC3DF6D1A74B ON button (controller_id)');
        $this->addSql('CREATE INDEX IDX_3A06AC3D60A20B48 ON button (trigger_frame_id)');
        $this->addSql('CREATE INDEX IDX_3A06AC3DBEFA6858 ON button (on_display_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE presentation');
        $this->addSql('DROP TABLE display');
        $this->addSql('DROP TABLE frame');
        $this->addSql('DROP TABLE carousel');
        $this->addSql('DROP TABLE template');
        $this->addSql('DROP TABLE presentation_carousel_map');
        $this->addSql('DROP TABLE remote_controller');
        $this->addSql('DROP TABLE button');
    }
}
