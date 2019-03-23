<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322173809 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE presentation_carousel_map');
        $this->addSql('DROP INDEX UNIQ_72A9D661C1CE5B98');
        $this->addSql('DROP INDEX UNIQ_72A9D661AB627E8B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__carousel_presentation_map AS SELECT id, presentation_id, carousel_id, twig_key FROM carousel_presentation_map');
        $this->addSql('DROP TABLE carousel_presentation_map');
        $this->addSql('CREATE TABLE carousel_presentation_map (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, presentation_id INTEGER NOT NULL, carousel_id INTEGER NOT NULL, template_key VARCHAR(20) NOT NULL, CONSTRAINT FK_72A9D661AB627E8B FOREIGN KEY (presentation_id) REFERENCES presentation (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_72A9D661C1CE5B98 FOREIGN KEY (carousel_id) REFERENCES carousel (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO carousel_presentation_map (id, presentation_id, carousel_id, template_key) SELECT id, presentation_id, carousel_id, twig_key FROM __temp__carousel_presentation_map');
        $this->addSql('DROP TABLE __temp__carousel_presentation_map');
        $this->addSql('CREATE INDEX IDX_72A9D661AB627E8B ON carousel_presentation_map (presentation_id)');
        $this->addSql('CREATE INDEX IDX_72A9D661C1CE5B98 ON carousel_presentation_map (carousel_id)');
        $this->addSql('DROP INDEX IDX_9B66E8935DA0FB8');
        $this->addSql('DROP INDEX IDX_9B66E89351A2DF33');
        $this->addSql('CREATE TEMPORARY TABLE __temp__presentation AS SELECT id, template_id, display_id, label, skip, duration FROM presentation');
        $this->addSql('DROP TABLE presentation');
        $this->addSql('CREATE TABLE presentation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, template_id INTEGER NOT NULL, display_id INTEGER NOT NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, skip BOOLEAN NOT NULL, duration INTEGER NOT NULL, CONSTRAINT FK_9B66E8935DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9B66E89351A2DF33 FOREIGN KEY (display_id) REFERENCES display (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO presentation (id, template_id, display_id, label, skip, duration) SELECT id, template_id, display_id, label, skip, duration FROM __temp__presentation');
        $this->addSql('DROP TABLE __temp__presentation');
        $this->addSql('CREATE INDEX IDX_9B66E8935DA0FB8 ON presentation (template_id)');
        $this->addSql('CREATE INDEX IDX_9B66E89351A2DF33 ON presentation (display_id)');
        $this->addSql('DROP INDEX IDX_B5F83CCDC1CE5B98');
        $this->addSql('CREATE TEMPORARY TABLE __temp__frame AS SELECT id, carousel_id, url, duration FROM frame');
        $this->addSql('DROP TABLE frame');
        $this->addSql('CREATE TABLE frame (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, carousel_id INTEGER NOT NULL, url VARCHAR(2000) NOT NULL COLLATE BINARY, duration INTEGER NOT NULL, CONSTRAINT FK_B5F83CCDC1CE5B98 FOREIGN KEY (carousel_id) REFERENCES carousel (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO frame (id, carousel_id, url, duration) SELECT id, carousel_id, url, duration FROM __temp__frame');
        $this->addSql('DROP TABLE __temp__frame');
        $this->addSql('CREATE INDEX IDX_B5F83CCDC1CE5B98 ON frame (carousel_id)');
        $this->addSql('DROP INDEX IDX_D084D9AC5DA0FB8');
        $this->addSql('CREATE TEMPORARY TABLE __temp__remote_controller AS SELECT id, template_id, label FROM remote_controller');
        $this->addSql('DROP TABLE remote_controller');
        $this->addSql('CREATE TABLE remote_controller (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, template_id INTEGER NOT NULL, label VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_D084D9AC5DA0FB8 FOREIGN KEY (template_id) REFERENCES template (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO remote_controller (id, template_id, label) SELECT id, template_id, label FROM __temp__remote_controller');
        $this->addSql('DROP TABLE __temp__remote_controller');
        $this->addSql('CREATE INDEX IDX_D084D9AC5DA0FB8 ON remote_controller (template_id)');
        $this->addSql('DROP INDEX IDX_3A06AC3DF6D1A74B');
        $this->addSql('DROP INDEX IDX_3A06AC3D60A20B48');
        $this->addSql('DROP INDEX IDX_3A06AC3DBEFA6858');
        $this->addSql('CREATE TEMPORARY TABLE __temp__button AS SELECT id, controller_id, trigger_frame_id, on_display_id, twig_key FROM button');
        $this->addSql('DROP TABLE button');
        $this->addSql('CREATE TABLE button (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, controller_id INTEGER NOT NULL, trigger_frame_id INTEGER NOT NULL, on_display_id INTEGER NOT NULL, twig_key VARCHAR(20) NOT NULL COLLATE BINARY, CONSTRAINT FK_3A06AC3DF6D1A74B FOREIGN KEY (controller_id) REFERENCES remote_controller (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3A06AC3D60A20B48 FOREIGN KEY (trigger_frame_id) REFERENCES frame (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3A06AC3DBEFA6858 FOREIGN KEY (on_display_id) REFERENCES display (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO button (id, controller_id, trigger_frame_id, on_display_id, twig_key) SELECT id, controller_id, trigger_frame_id, on_display_id, twig_key FROM __temp__button');
        $this->addSql('DROP TABLE __temp__button');
        $this->addSql('CREATE INDEX IDX_3A06AC3DF6D1A74B ON button (controller_id)');
        $this->addSql('CREATE INDEX IDX_3A06AC3D60A20B48 ON button (trigger_frame_id)');
        $this->addSql('CREATE INDEX IDX_3A06AC3DBEFA6858 ON button (on_display_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE presentation_carousel_map (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, template_id INTEGER NOT NULL, carousel_id INTEGER NOT NULL, twig_key VARCHAR(20) NOT NULL COLLATE BINARY)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A23C0DC5DA0FB8 ON presentation_carousel_map (template_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3A23C0DCC1CE5B98 ON presentation_carousel_map (carousel_id)');
        $this->addSql('DROP INDEX IDX_3A06AC3DF6D1A74B');
        $this->addSql('DROP INDEX IDX_3A06AC3D60A20B48');
        $this->addSql('DROP INDEX IDX_3A06AC3DBEFA6858');
        $this->addSql('CREATE TEMPORARY TABLE __temp__button AS SELECT id, controller_id, trigger_frame_id, on_display_id, twig_key FROM button');
        $this->addSql('DROP TABLE button');
        $this->addSql('CREATE TABLE button (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, controller_id INTEGER NOT NULL, trigger_frame_id INTEGER NOT NULL, on_display_id INTEGER NOT NULL, twig_key VARCHAR(20) NOT NULL)');
        $this->addSql('INSERT INTO button (id, controller_id, trigger_frame_id, on_display_id, twig_key) SELECT id, controller_id, trigger_frame_id, on_display_id, twig_key FROM __temp__button');
        $this->addSql('DROP TABLE __temp__button');
        $this->addSql('CREATE INDEX IDX_3A06AC3DF6D1A74B ON button (controller_id)');
        $this->addSql('CREATE INDEX IDX_3A06AC3D60A20B48 ON button (trigger_frame_id)');
        $this->addSql('CREATE INDEX IDX_3A06AC3DBEFA6858 ON button (on_display_id)');
        $this->addSql('DROP INDEX IDX_72A9D661AB627E8B');
        $this->addSql('DROP INDEX IDX_72A9D661C1CE5B98');
        $this->addSql('CREATE TEMPORARY TABLE __temp__carousel_presentation_map AS SELECT id, presentation_id, carousel_id, template_key FROM carousel_presentation_map');
        $this->addSql('DROP TABLE carousel_presentation_map');
        $this->addSql('CREATE TABLE carousel_presentation_map (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, presentation_id INTEGER NOT NULL, carousel_id INTEGER NOT NULL, twig_key VARCHAR(20) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO carousel_presentation_map (id, presentation_id, carousel_id, twig_key) SELECT id, presentation_id, carousel_id, template_key FROM __temp__carousel_presentation_map');
        $this->addSql('DROP TABLE __temp__carousel_presentation_map');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72A9D661C1CE5B98 ON carousel_presentation_map (carousel_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_72A9D661AB627E8B ON carousel_presentation_map (presentation_id)');
        $this->addSql('DROP INDEX IDX_B5F83CCDC1CE5B98');
        $this->addSql('CREATE TEMPORARY TABLE __temp__frame AS SELECT id, carousel_id, url, duration FROM frame');
        $this->addSql('DROP TABLE frame');
        $this->addSql('CREATE TABLE frame (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, carousel_id INTEGER NOT NULL, url VARCHAR(2000) NOT NULL, duration INTEGER NOT NULL)');
        $this->addSql('INSERT INTO frame (id, carousel_id, url, duration) SELECT id, carousel_id, url, duration FROM __temp__frame');
        $this->addSql('DROP TABLE __temp__frame');
        $this->addSql('CREATE INDEX IDX_B5F83CCDC1CE5B98 ON frame (carousel_id)');
        $this->addSql('DROP INDEX IDX_9B66E8935DA0FB8');
        $this->addSql('DROP INDEX IDX_9B66E89351A2DF33');
        $this->addSql('CREATE TEMPORARY TABLE __temp__presentation AS SELECT id, template_id, display_id, label, skip, duration FROM presentation');
        $this->addSql('DROP TABLE presentation');
        $this->addSql('CREATE TABLE presentation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, template_id INTEGER NOT NULL, display_id INTEGER NOT NULL, label VARCHAR(255) NOT NULL, skip BOOLEAN NOT NULL, duration INTEGER NOT NULL)');
        $this->addSql('INSERT INTO presentation (id, template_id, display_id, label, skip, duration) SELECT id, template_id, display_id, label, skip, duration FROM __temp__presentation');
        $this->addSql('DROP TABLE __temp__presentation');
        $this->addSql('CREATE INDEX IDX_9B66E8935DA0FB8 ON presentation (template_id)');
        $this->addSql('CREATE INDEX IDX_9B66E89351A2DF33 ON presentation (display_id)');
        $this->addSql('DROP INDEX IDX_D084D9AC5DA0FB8');
        $this->addSql('CREATE TEMPORARY TABLE __temp__remote_controller AS SELECT id, template_id, label FROM remote_controller');
        $this->addSql('DROP TABLE remote_controller');
        $this->addSql('CREATE TABLE remote_controller (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, template_id INTEGER NOT NULL, label VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO remote_controller (id, template_id, label) SELECT id, template_id, label FROM __temp__remote_controller');
        $this->addSql('DROP TABLE __temp__remote_controller');
        $this->addSql('CREATE INDEX IDX_D084D9AC5DA0FB8 ON remote_controller (template_id)');
    }
}
