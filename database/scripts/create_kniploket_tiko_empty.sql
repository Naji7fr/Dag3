-- Kniploket Tiko - Lege database (alleen tabellen, geen testdata)
-- Zelfde structuur als kniploket_tiko, maar zonder klanten/producten/etc.
--
-- Gebruik:
--   mysql -u root -p < create_kniploket_tiko_empty.sql
--   mysql -u root -p < stored_procedures_empty.sql
--
-- Schakel in Laravel via .env:
--   DB_USE_EMPTY=true   → deze lege database (kniploket_tiko_empty)
--   DB_USE_EMPTY=false  → testdata database (kniploket_tiko)

CREATE DATABASE IF NOT EXISTS kniploket_tiko_empty
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE kniploket_tiko_empty;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS LeverancierOrder;
DROP TABLE IF EXISTS BehandelingPerVoorraad;
DROP TABLE IF EXISTS Voorraad;
DROP TABLE IF EXISTS ProductPerBestelling;
DROP TABLE IF EXISTS Product;
DROP TABLE IF EXISTS Categorie;
DROP TABLE IF EXISTS Bestelling;
DROP TABLE IF EXISTS MedewerkerPerBehandeling;
DROP TABLE IF EXISTS Behandeling;
DROP TABLE IF EXISTS MedewerkerPerContact;
DROP TABLE IF EXISTS KlantPerContact;
DROP TABLE IF EXISTS Contact;
DROP TABLE IF EXISTS Medewerker;
DROP TABLE IF EXISTS Klant;
DROP TABLE IF EXISTS Leverancier;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------------
CREATE TABLE users (
    id                  INT             NOT NULL AUTO_INCREMENT,
    name                VARCHAR(100)    NOT NULL,
    email               VARCHAR(255)    NOT NULL,
    email_verified_at   DATETIME(6)     NULL,
    password            VARCHAR(255)    NOT NULL,
    role                VARCHAR(50)     NOT NULL,
    remember_token      VARCHAR(100)    NULL,
    created_at          DATETIME(6)     NOT NULL,
    updated_at          DATETIME(6)     NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Klant
-- ---------------------------------------------------------------------------
CREATE TABLE Klant (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    UserId              INT             NOT NULL,
    Voornaam            VARCHAR(100)    NOT NULL,
    Tussenvoegsel       VARCHAR(50)     NULL,
    Achternaam          VARCHAR(100)    NOT NULL,
    Relatienummer       VARCHAR(20)     NOT NULL,
    Bijzonderheden      VARCHAR(500)    NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_klant_userid (UserId),
    UNIQUE KEY uq_klant_relatienummer (Relatienummer),
    CONSTRAINT fk_klant_user FOREIGN KEY (UserId) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Medewerker
-- ---------------------------------------------------------------------------
CREATE TABLE Medewerker (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    UserId              INT             NOT NULL,
    Voornaam            VARCHAR(100)    NOT NULL,
    Tussenvoegsel       VARCHAR(50)     NULL,
    Achternaam          VARCHAR(100)    NOT NULL,
    Specialisatie       VARCHAR(100)    NOT NULL,
    Geboortedatum       DATE            NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_medewerker_userid (UserId),
    CONSTRAINT fk_medewerker_user FOREIGN KEY (UserId) REFERENCES users (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Contact
-- ---------------------------------------------------------------------------
CREATE TABLE Contact (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    Straatnaam          VARCHAR(150)    NOT NULL,
    Huisnummer          VARCHAR(10)     NOT NULL,
    Toevoeging          VARCHAR(10)     NULL,
    Postcode            VARCHAR(10)     NOT NULL,
    Plaats              VARCHAR(100)    NOT NULL,
    Email               VARCHAR(255)    NOT NULL,
    Mobiel              VARCHAR(20)     NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- KlantPerContact
-- ---------------------------------------------------------------------------
CREATE TABLE KlantPerContact (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    KlantId             INT             NOT NULL,
    ContactId           INT             NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_klantpercontact (KlantId, ContactId),
    CONSTRAINT fk_klantpercontact_klant FOREIGN KEY (KlantId) REFERENCES Klant (Id),
    CONSTRAINT fk_klantpercontact_contact FOREIGN KEY (ContactId) REFERENCES Contact (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- MedewerkerPerContact
-- ---------------------------------------------------------------------------
CREATE TABLE MedewerkerPerContact (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    MedewerkerId        INT             NOT NULL,
    ContactId           INT             NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_medewerkerpercontact (MedewerkerId, ContactId),
    CONSTRAINT fk_medewerkerpercontact_medewerker FOREIGN KEY (MedewerkerId) REFERENCES Medewerker (Id),
    CONSTRAINT fk_medewerkerpercontact_contact FOREIGN KEY (ContactId) REFERENCES Contact (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Behandeling
-- ---------------------------------------------------------------------------
CREATE TABLE Behandeling (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    Naam                VARCHAR(100)    NOT NULL,
    Omschrijving        VARCHAR(500)    NOT NULL,
    Duurminuten         INT             NOT NULL,
    Prijs               DECIMAL(10, 2)  NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- MedewerkerPerBehandeling
-- ---------------------------------------------------------------------------
CREATE TABLE MedewerkerPerBehandeling (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    MedewerkerId        INT             NOT NULL,
    BehandelingId       INT             NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_medewerkerperbehandeling (MedewerkerId, BehandelingId),
    CONSTRAINT fk_mpb_medewerker FOREIGN KEY (MedewerkerId) REFERENCES Medewerker (Id),
    CONSTRAINT fk_mpb_behandeling FOREIGN KEY (BehandelingId) REFERENCES Behandeling (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Bestelling
-- ---------------------------------------------------------------------------
CREATE TABLE Bestelling (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    KlantId             INT             NOT NULL,
    BestelNummer        INT             NOT NULL,
    Omschrijving        VARCHAR(500)    NOT NULL,
    Datum               DATE            NOT NULL,
    Tijd                TIME            NOT NULL,
    Bestelstatus        VARCHAR(50)     NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_bestelling_bestelnummer (BestelNummer),
    CONSTRAINT fk_bestelling_klant FOREIGN KEY (KlantId) REFERENCES Klant (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Categorie
-- ---------------------------------------------------------------------------
CREATE TABLE Categorie (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    Naam                VARCHAR(100)    NOT NULL,
    Omschrijving        VARCHAR(500)    NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Product
-- ---------------------------------------------------------------------------
CREATE TABLE Product (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    CategorieId         INT             NOT NULL,
    Naam                VARCHAR(150)    NOT NULL,
    Omschrijving        VARCHAR(500)    NOT NULL,
    Merk                VARCHAR(100)    NOT NULL,
    EANcode             VARCHAR(20)     NOT NULL,
    Houdbaarheidsdatum  DATE            NOT NULL,
    InkoopPrijs         DECIMAL(10, 2)  NOT NULL,
    VerkoopPrijs        DECIMAL(10, 2)  NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_product_eancode (EANcode),
    CONSTRAINT fk_product_categorie FOREIGN KEY (CategorieId) REFERENCES Categorie (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- ProductPerBestelling
-- ---------------------------------------------------------------------------
CREATE TABLE ProductPerBestelling (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    ProductId           INT             NOT NULL,
    BestellingId        INT             NOT NULL,
    Aantal              INT             NOT NULL,
    UnitPrijs           DECIMAL(10, 2)  NOT NULL,
    BTWPercentage       DECIMAL(5, 2)   NOT NULL,
    Korting             DECIMAL(10, 2)  NOT NULL DEFAULT 0.00,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    CONSTRAINT fk_ppb_product FOREIGN KEY (ProductId) REFERENCES Product (Id),
    CONSTRAINT fk_ppb_bestelling FOREIGN KEY (BestellingId) REFERENCES Bestelling (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Voorraad
-- ---------------------------------------------------------------------------
CREATE TABLE Voorraad (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    ProductId           INT             NOT NULL,
    AantalOpVoorraad    INT             NOT NULL,
    Aantaluitgegeven    INT             NOT NULL DEFAULT 0,
    Aantalbijgekomen    INT             NOT NULL DEFAULT 0,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_voorraad_product (ProductId),
    CONSTRAINT fk_voorraad_product FOREIGN KEY (ProductId) REFERENCES Product (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- BehandelingPerVoorraad
-- ---------------------------------------------------------------------------
CREATE TABLE BehandelingPerVoorraad (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    BehandelingId       INT             NOT NULL,
    VoorraadId          INT             NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_behandelingpervoorraad (BehandelingId, VoorraadId),
    CONSTRAINT fk_bpv_behandeling FOREIGN KEY (BehandelingId) REFERENCES Behandeling (Id),
    CONSTRAINT fk_bpv_voorraad FOREIGN KEY (VoorraadId) REFERENCES Voorraad (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Leverancier
-- ---------------------------------------------------------------------------
CREATE TABLE Leverancier (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    Naam                VARCHAR(150)    NOT NULL,
    Straatnaam          VARCHAR(150)    NOT NULL,
    Huisnummer          VARCHAR(10)     NOT NULL,
    Toevoeging          VARCHAR(10)     NULL,
    Postcode            VARCHAR(10)     NOT NULL,
    Plaats              VARCHAR(100)    NOT NULL,
    Email               VARCHAR(255)    NOT NULL,
    Mobiel              VARCHAR(20)     NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- LeverancierOrder
-- ---------------------------------------------------------------------------
CREATE TABLE LeverancierOrder (
    Id                  INT             NOT NULL AUTO_INCREMENT,
    Ordernummer         VARCHAR(20)     NOT NULL,
    ProductId           INT             NOT NULL,
    LeverancierId       INT             NOT NULL,
    Aantal              INT             NOT NULL,
    Orderdatum          DATE            NOT NULL,
    Leverdatum          DATE            NULL,
    Leverstatus         VARCHAR(50)     NOT NULL,
    IsActief            BIT(1)          NOT NULL DEFAULT 1,
    Opmerking           VARCHAR(255)    NULL,
    DatumAangemaakt     DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd      DATETIME(6)     NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    UNIQUE KEY uq_leverancierorder_ordernummer (Ordernummer),
    CONSTRAINT fk_leverancierorder_product FOREIGN KEY (ProductId) REFERENCES Product (Id),
    CONSTRAINT fk_leverancierorder_leverancier FOREIGN KEY (LeverancierId) REFERENCES Leverancier (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Minimale login gebruiker (wachtwoord: password)
-- Geen overige testdata — klantenoverzicht start leeg.
-- ---------------------------------------------------------------------------
INSERT INTO users (id, name, email, email_verified_at, password, role, remember_token, created_at, updated_at) VALUES
(1, 'Salon Eigenaar', 'eigenaar@kniplokettiko.nl', NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'eigenaar', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30');
