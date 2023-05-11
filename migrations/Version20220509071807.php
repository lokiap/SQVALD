<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220509071807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_donnees_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE category_news_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE document_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE event_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE member_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE news_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE partner_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE video_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category_donnees (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE category_news (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE document (id INT NOT NULL, categorydonnees_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, resume VARCHAR(255) DEFAULT NULL, picture VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN NOT NULL, brochure_filename VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D8698A76C5290191 ON document (categorydonnees_id)');
        $this->addSql('CREATE TABLE document_user (document_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(document_id, user_id))');
        $this->addSql('CREATE INDEX IDX_2A275ADAC33F7837 ON document_user (document_id)');
        $this->addSql('CREATE INDEX IDX_2A275ADAA76ED395 ON document_user (user_id)');
        $this->addSql('CREATE TABLE event (id INT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, resume TEXT DEFAULT NULL, date_begin DATE NOT NULL, date_end DATE NOT NULL, place VARCHAR(255) DEFAULT NULL, is_active BOOLEAN NOT NULL, content TEXT DEFAULT NULL, created_at DATE NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3BAE0AA712469DE2 ON event (category_id)');
        $this->addSql('CREATE TABLE event_user (event_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(event_id, user_id))');
        $this->addSql('CREATE INDEX IDX_92589AE271F7E88B ON event_user (event_id)');
        $this->addSql('CREATE INDEX IDX_92589AE2A76ED395 ON event_user (user_id)');
        $this->addSql('CREATE TABLE member (id INT NOT NULL, partner_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, is_valide BOOLEAN DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, place VARCHAR(255) DEFAULT NULL, is_verified BOOLEAN NOT NULL, web_site VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA78E7927C74 ON member (email)');
        $this->addSql('CREATE INDEX IDX_70E4FA789393F8FE ON member (partner_id)');
        $this->addSql('CREATE TABLE news (id INT NOT NULL, title VARCHAR(255) NOT NULL, resume TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN NOT NULL, content TEXT DEFAULT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE news_user (news_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(news_id, user_id))');
        $this->addSql('CREATE INDEX IDX_584E20C2B5A459A0 ON news_user (news_id)');
        $this->addSql('CREATE INDEX IDX_584E20C2A76ED395 ON news_user (user_id)');
        $this->addSql('CREATE TABLE partner (id INT NOT NULL, content TEXT DEFAULT NULL, illustration VARCHAR(255) DEFAULT NULL, btn_url VARCHAR(255) DEFAULT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE video (id INT NOT NULL, link VARCHAR(255) DEFAULT NULL, src VARCHAR(255) DEFAULT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE video_user (video_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(video_id, user_id))');
        $this->addSql('CREATE INDEX IDX_8A048B9529C1004E ON video_user (video_id)');
        $this->addSql('CREATE INDEX IDX_8A048B95A76ED395 ON video_user (user_id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76C5290191 FOREIGN KEY (categorydonnees_id) REFERENCES category_donnees (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAC33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE document_user ADD CONSTRAINT FK_2A275ADAA76ED395 FOREIGN KEY (user_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA712469DE2 FOREIGN KEY (category_id) REFERENCES category_news (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE271F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE event_user ADD CONSTRAINT FK_92589AE2A76ED395 FOREIGN KEY (user_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE member ADD CONSTRAINT FK_70E4FA789393F8FE FOREIGN KEY (partner_id) REFERENCES partner (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE news_user ADD CONSTRAINT FK_584E20C2B5A459A0 FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE news_user ADD CONSTRAINT FK_584E20C2A76ED395 FOREIGN KEY (user_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_user ADD CONSTRAINT FK_8A048B9529C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE video_user ADD CONSTRAINT FK_8A048B95A76ED395 FOREIGN KEY (user_id) REFERENCES member (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE document DROP CONSTRAINT FK_D8698A76C5290191');
        $this->addSql('ALTER TABLE event DROP CONSTRAINT FK_3BAE0AA712469DE2');
        $this->addSql('ALTER TABLE document_user DROP CONSTRAINT FK_2A275ADAC33F7837');
        $this->addSql('ALTER TABLE event_user DROP CONSTRAINT FK_92589AE271F7E88B');
        $this->addSql('ALTER TABLE document_user DROP CONSTRAINT FK_2A275ADAA76ED395');
        $this->addSql('ALTER TABLE event_user DROP CONSTRAINT FK_92589AE2A76ED395');
        $this->addSql('ALTER TABLE news_user DROP CONSTRAINT FK_584E20C2A76ED395');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE video_user DROP CONSTRAINT FK_8A048B95A76ED395');
        $this->addSql('ALTER TABLE news_user DROP CONSTRAINT FK_584E20C2B5A459A0');
        $this->addSql('ALTER TABLE member DROP CONSTRAINT FK_70E4FA789393F8FE');
        $this->addSql('ALTER TABLE video_user DROP CONSTRAINT FK_8A048B9529C1004E');
        $this->addSql('DROP SEQUENCE category_donnees_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE category_news_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE document_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE event_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE member_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE news_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE partner_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE video_id_seq CASCADE');
        $this->addSql('DROP TABLE category_donnees');
        $this->addSql('DROP TABLE category_news');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_user');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_user');
        $this->addSql('DROP TABLE member');
        $this->addSql('DROP TABLE news');
        $this->addSql('DROP TABLE news_user');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE video');
        $this->addSql('DROP TABLE video_user');
    }
}
