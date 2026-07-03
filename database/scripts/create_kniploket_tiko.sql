-- Kniploket Tiko - Database create script
-- Bron: Testdata_vulling_tabellen__kniploket_tiko.docx
-- Elke tabel bevat de systeemvelden: IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd
--
-- Gebruik:
--   mysql -u root -p < create_kniploket_tiko.sql
--   mysql -u root -p < stored_procedures.sql

CREATE DATABASE IF NOT EXISTS kniploket_tiko
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE kniploket_tiko;

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

-- ===========================================================================
-- Testdata
-- ===========================================================================

INSERT INTO users (id, name, email, email_verified_at, password, role, remember_token, created_at, updated_at) VALUES
(1,  'Salon Eigenaar',      'eigenaar@kniplokettiko.nl',           NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'eigenaar',  NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(2,  'Fatima El Amrani',    'fatima@kniplokettiko.nl',             NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(3,  'Sanne de Vries',      'sanne.devries@kniplokettiko.nl',      NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(4,  'Mohamed El Idrissi',  'mohamed.elidrissi@kniplokettiko.nl',  NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(5,  'Lisa van Dijk',       'lisa.vandijk@kniplokettiko.nl',       NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(6,  'Youssef Benali',      'youssef.benali@kniplokettiko.nl',     NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(7,  'Noor Bakker',         'noor.bakker@kniplokettiko.nl',        NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(8,  'Kevin Smit',          'kevin.smit@kniplokettiko.nl',         NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(9,  'Aylin Demir',         'aylin.demir@kniplokettiko.nl',        NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(10, 'Tom Verhoeven',       'tom.verhoeven@kniplokettiko.nl',      NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(11, 'Romy Jacobs',         'romy.jacobs@kniplokettiko.nl',        NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'medewerker', NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(12, 'Piet van Loenen',     'piet.van.loenen@gmail.com',           NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'klant',      NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(13, 'Jan Jansen',          'jan.jansen@outlook.com',              NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'klant',      NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(14, 'Saskia de Boer',      'saskia.deboer@yahoo.com',             NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'klant',      NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(15, 'Ahmed Mansouri',      'ahmed.mansouri@icloud.com',           NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'klant',      NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(16, 'Marieke van den Berg','marieke.vandenberg@ziggo.nl',         NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'klant',      NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30'),
(17, 'Daan Visser',         'daan.visser@live.nl',                 NULL, '$2y$12$8BvFqZV8R9r1XaH1Ir6L4ugxmFfymIc2fandPxDYGEWtlrBLvm2um', 'klant',      NULL, '2026-07-02 09:09:30', '2026-07-02 09:09:30');

INSERT INTO Klant (Id, UserId, Voornaam, Tussenvoegsel, Achternaam, Relatienummer, Bijzonderheden) VALUES
(1, 12, 'Piet',    'van',     'Loenen',       'KL-2026-001', 'Voorkeur voor ochtendafspraken.'),
(2, 13, 'Jan',     NULL,      'Jansen',       'KL-2026-002', 'Allergie voor sterk geparfumeerde producten.'),
(3, 14, 'Saskia',  'de',      'Boer',         'KL-2026-003', 'Komt elke zes weken.'),
(4, 15, 'Ahmed',   NULL,      'Mansouri',     'KL-2026-004', 'Wil strakke fade.'),
(5, 16, 'Marieke', 'van den', 'Berg',         'KL-2026-005', 'Gevoelige hoofdhuid.'),
(6, 17, 'Daan',    NULL,      'Visser',       'KL-2026-006', 'Liefst einde middag.');

INSERT INTO Medewerker (Id, UserId, Voornaam, Tussenvoegsel, Achternaam, Specialisatie, Geboortedatum) VALUES
(1,  2, 'Fatima',  NULL, 'El Amrani',  'Knippen',    '1988-04-12'),
(2,  3, 'Sanne',   'de', 'Vries',      'Kleuren',    '1996-09-25'),
(3,  4, 'Mohamed', NULL, 'El Idrissi', 'Extensions', '1992-02-14'),
(4,  5, 'Lisa',    'van','Dijk',       'Stylen',     '1998-07-08'),
(5,  6, 'Youssef', NULL, 'Benali',     'Knippen',    '1990-11-30'),
(6,  7, 'Noor',    NULL, 'Bakker',     'Kleuren',    '1997-05-21'),
(7,  8, 'Kevin',   NULL, 'Smit',       'Extensions', '2001-03-17'),
(8,  9, 'Aylin',   NULL, 'Demir',      'Stylen',     '1999-12-04'),
(9, 10, 'Tom',     NULL, 'Verhoeven',  'Knippen',    '1995-08-19'),
(10, 11, 'Romy',   NULL, 'Jacobs',     'Knippen',    '2010-01-15');

INSERT INTO Contact (Id, Straatnaam, Huisnummer, Toevoeging, Postcode, Plaats, Email, Mobiel) VALUES
(1,  'Kanaalstraat',              '12',  NULL, '3511AB', 'Utrecht', 'fatima@kniplokettiko.nl',        '0612345678'),
(2,  'Croeselaan',                '101', NULL, '3521BJ', 'Utrecht', 'sanne.devries@kniplokettiko.nl', '0611111111'),
(3,  'Amsterdamsestraatweg',      '223', NULL, '3551CG', 'Utrecht', 'mohamed.elidrissi@kniplokettiko.nl', '0611111112'),
(4,  'Maliebaan',                 '17',  NULL, '3581CC', 'Utrecht', 'lisa.vandijk@kniplokettiko.nl',  '0611111113'),
(5,  'Balijelaan',                '63',  NULL, '3521GM', 'Utrecht', 'youssef.benali@kniplokettiko.nl','0611111114'),
(6,  'Nachtegaalstraat',          '95',  NULL, '3581AE', 'Utrecht', 'noor.bakker@kniplokettiko.nl',   '0611111115'),
(7,  'Bernardlaan',               '7',   NULL, '3527GA', 'Utrecht', 'kevin.smit@kniplokettiko.nl',    '0611111116'),
(8,  'Laan van Nieuw-Guinea',     '141', NULL, '3531JE', 'Utrecht', 'aylin.demir@kniplokettiko.nl',   '0611111117'),
(9,  'Marnixlaan',                '205', NULL, '3552HD', 'Utrecht', 'tom.verhoeven@kniplokettiko.nl', '0611111118'),
(10, 'Haroekoeplein',             '29',  NULL, '3531WK', 'Utrecht', 'romy.jacobs@kniplokettiko.nl',   '0611111119'),
(11, 'Oudegracht',                '88',  'A',  '3512AB', 'Utrecht', 'piet.van.loenen@gmail.com',      '+31 6 1234 61 71'),
(12, 'Biltstraat',                '44',  NULL, '3572BC', 'Utrecht', 'jan.jansen@outlook.com',         '+31 6 1234 61 72'),
(13, 'Merelstraat',               '12',  NULL, '3514CN', 'Utrecht', 'saskia.deboer@yahoo.com',        '+31 6 1234 61 73'),
(14, 'Winkel van Sinkelstraat',   '4',   NULL, '3511KV', 'Utrecht', 'ahmed.mansouri@icloud.com',      '+31 6 1234 61 74'),
(15, 'Adelaarstraat',             '50',  NULL, '3514CH', 'Utrecht', 'marieke.vandenberg@ziggo.nl',    '+31 6 1234 61 75'),
(16, 'Vleutenseweg',              '73',  NULL, '3532HA', 'Utrecht', 'daan.visser@live.nl',            '+31 6 1234 61 76');

INSERT INTO KlantPerContact (Id, KlantId, ContactId) VALUES
(1, 1, 11), (2, 2, 12), (3, 3, 13), (4, 4, 14), (5, 5, 15), (6, 6, 16);

INSERT INTO MedewerkerPerContact (Id, MedewerkerId, ContactId) VALUES
(1, 1, 1), (2, 2, 2), (3, 3, 3), (4, 4, 4), (5, 5, 5),
(6, 6, 6), (7, 7, 7), (8, 8, 8), (9, 9, 9), (10, 10, 10);

INSERT INTO Behandeling (Id, Naam, Omschrijving, Duurminuten, Prijs) VALUES
(1, 'Knippen',              'Haar knippen en eventueel stylen.',              30,  30.00),
(2, 'Combi behandelingen',  'Combinatie van knippen, kleuren en stylen.',     90,  90.00),
(3, 'Kleuren',              'Haar kleuren (diverse technieken).',             60,  60.00),
(4, 'Permanent',            'Permanente omvorming van het haar.',            120, 110.00),
(5, 'Extensions',           'Plaatsen en verzorgen van extensions.',         180, 250.00);

INSERT INTO MedewerkerPerBehandeling (Id, MedewerkerId, BehandelingId) VALUES
(1, 1, 1), (2, 1, 3), (3, 1, 2), (4, 2, 1), (5, 2, 3),
(6, 3, 1), (7, 3, 3), (8, 4, 1), (9, 4, 3), (10, 4, 2), (11, 5, 4);

INSERT INTO Bestelling (Id, KlantId, BestelNummer, Omschrijving, Datum, Tijd, Bestelstatus) VALUES
(1,  1, 600101, 'Salonproducten besteld na knipafspraak.',              '2026-06-20', '09:15:00', 'Ontvangen'),
(2,  2, 600102, 'Aanvulling op thuisverzorging na kleuradvies.',        '2026-06-23', '11:40:00', 'Bevestigd'),
(3,  3, 600103, 'Stylingproducten besteld na behandeling.',              '2026-06-25', '14:20:00', 'Inverwerking'),
(4,  4, 600104, 'Baardverzorging besteld voor verzending.',              '2026-06-28', '16:05:00', 'Verzonden'),
(5,  5, 600105, 'Stylingproducten afgerond en afgeleverd.',              '2026-06-30', '10:30:00', 'Afgeleverd'),
(6,  1, 600106, 'Salonproducten status-test (Ontvangen)',                '2026-06-10', '08:20:00', 'Ontvangen'),
(7,  2, 600107, 'Haarverzorging status-test (Ontvangen)',                '2026-06-14', '11:35:00', 'Ontvangen'),
(8,  3, 600108, 'Stylingproducten status-test (Ontvangen)',              '2026-06-15', '13:22:00', 'Ontvangen'),
(9,  4, 600109, 'Bevestigde bestelling Wax Gel',                         '2026-06-11', '09:15:00', 'Bevestigd'),
(10, 5, 600110, 'Bevestigde bestelling Color Creme',                     '2026-06-13', '15:50:00', 'Bevestigd'),
(11, 6, 600111, 'Bevestigde bestelling Conditioner',                     '2026-06-17', '10:44:00', 'Bevestigd'),
(12, 1, 600112, 'Order in verwerking Masker',                            '2026-06-18', '11:10:00', 'Inverwerking'),
(13, 2, 600113, 'Order in verwerking Baardolie',                         '2026-06-21', '16:25:00', 'Inverwerking'),
(14, 3, 600114, 'Order in verwerking Styling Clay',                      '2026-06-22', '14:45:00', 'Inverwerking'),
(15, 4, 600115, 'Verzonden testorder Shampoo',                           '2026-06-23', '13:05:00', 'Verzonden'),
(16, 5, 600116, 'Verzonden testorder Heat Protect',                      '2026-06-24', '15:30:00', 'Verzonden'),
(17, 6, 600117, 'Verzonden testorder Strong Hold Gel',                   '2026-06-27', '16:18:00', 'Verzonden'),
(18, 1, 600118, 'Afgeleverde kleurcreme en conditioner',                 '2026-06-28', '10:12:00', 'Afgeleverd'),
(19, 2, 600119, 'Afgeleverde haarverzorgingsset',                        '2026-06-29', '15:43:00', 'Afgeleverd'),
(20, 3, 600120, 'Afgeleverde stylingproducten',                          '2026-06-30', '09:58:00', 'Afgeleverd');

INSERT INTO Categorie (Id, Naam, Omschrijving) VALUES
(1, 'Haarverzorging', 'Producten voor wassen en verzorgen.'),
(2, 'Kleurproducten', 'Producten voor kleurbehandelingen.'),
(3, 'Styling',        'Producten voor afwerking en styling.'),
(4, 'Accessoires',    'Accessoires voor verkoop in de salon.');

INSERT INTO Product (Id, CategorieId, Naam, Omschrijving, Merk, EANcode, Houdbaarheidsdatum, InkoopPrijs, VerkoopPrijs) VALUES
(1,  1, 'Hydrating Shampoo',      'Milde salonshampoo voor dagelijks gebruik.',              'Tiko Care',  '0871234500001', '2027-07-01',  6.50, 14.95),
(2,  1, 'Repair Conditioner',     'Voedende conditioner voor beschadigd haar.',              'Tiko Care',  '0871234500002', '2027-10-15',  7.25, 16.95),
(3,  1, 'Scalp Balance Masker',   'Kalmerend haarmasker voor gevoelige hoofdhuid.',          'Tiko Care',  '0871234500003', '2027-05-20',  8.75, 19.95),
(4,  1, 'Baardolie Cedar',        'Verzorgende olie voor baardbehandelingen.',               'Tiko Beard', '0871234500004', '2027-09-30',  5.75, 12.95),
(5,  2, 'Color Creme 6.1',        'Professionele asdonkerblonde kleurcreme.',                'Tiko Color', '0871234500005', '2026-12-31', 12.50, 24.95),
(6,  2, 'Color Creme 7.43',       'Koperblonde salonkleur met warme ondertoon.',             'Tiko Color', '0871234500006', '2027-01-31', 12.75, 25.95),
(7,  2, 'Developer 6 Procent',    'Oxidatiecreme voor kleurbehandelingen.',                  'Tiko Color', '0871234500007', '2027-03-31',  5.95, 11.95),
(8,  3, 'Matte Styling Clay',     'Matte clay met flexibele hold.',                          'Tiko Style', '0871234500008', '2027-08-31',  4.95, 12.95),
(9,  3, 'Strong Hold Gel',        'Sterke hold styling gel.',                                  'Tiko Style', '0871234500009', '2027-03-31',  4.25,  9.95),
(10, 3, 'Heat Protect Spray',     'Beschermende spray voor föhnen en stylen.',                 'Tiko Style', '0871234500010', '2027-11-30',  6.10, 15.95);

INSERT INTO ProductPerBestelling (Id, ProductId, BestellingId, Aantal, UnitPrijs, BTWPercentage, Korting) VALUES
(1,  1,  1,  2, 14.95, 21.00,  0.00),
(2,  2,  1,  1, 16.95, 21.00, 10.00),
(3,  2,  2,  3, 16.95, 21.00,  0.00),
(4,  5,  2,  2, 24.95, 21.00,  5.00),
(5,  3,  3,  1, 19.95, 21.00,  0.00),
(6,  8,  3,  2, 12.95, 21.00, 15.00),
(7,  4,  4,  1, 12.95, 21.00,  0.00),
(8,  9,  5,  1,  9.95, 21.00,  0.00),
(9,  10, 5,  1, 15.95, 21.00,  7.50),
(10, 1,  6,  2, 14.95, 21.00,  0.00),
(11, 2,  6,  1, 16.95, 21.00, 30.00),
(12, 3,  7,  1, 19.95, 21.00,  0.00),
(13, 4,  8,  1, 12.95, 21.00, 10.00),
(14, 5,  9,  1, 24.95, 21.00, 15.00),
(15, 2,  10, 2, 16.95, 21.00,  0.00),
(16, 8,  11, 1, 12.95, 21.00, 25.00),
(17, 1,  12, 1, 14.95, 21.00,  0.00),
(18, 3,  13, 1, 19.95, 21.00,  0.00),
(19, 8,  14, 2, 12.95, 21.00, 50.00),
(20, 1,  15, 1, 14.95, 21.00,  0.00),
(21, 10, 16, 1, 15.95, 21.00, 40.00),
(22, 9,  17, 1,  9.95, 21.00,  0.00),
(23, 5,  18, 1, 24.95, 21.00, 20.00),
(24, 2,  19, 1, 16.95, 21.00,  0.00),
(25, 8,  20, 1, 12.95, 21.00, 10.00);

INSERT INTO Voorraad (Id, ProductId, AantalOpVoorraad, Aantaluitgegeven, Aantalbijgekomen) VALUES
(1,  1, 40, 0, 40),
(2,  2, 28, 2, 30),
(3,  3, 18, 0, 18),
(4,  4, 20, 0, 20),
(5,  5, 25, 0, 25),
(6,  6, 16, 1, 17),
(7,  7, 32, 3, 35),
(8,  8, 22, 0, 22),
(9,  9, 35, 0, 35),
(10, 10, 24, 1, 25);

INSERT INTO BehandelingPerVoorraad (Id, BehandelingId, VoorraadId) VALUES
(1, 1, 1), (2, 1, 3), (3, 2, 1), (4, 2, 2), (5, 2, 3), (6, 3, 2), (7, 4, 3), (8, 5, 4);

INSERT INTO Leverancier (Id, Naam, Straatnaam, Huisnummer, Toevoeging, Postcode, Plaats, Email, Mobiel) VALUES
(1, 'Van Duuren Haircosmetics', 'Prinses Irenestraat',  '12',  'A',   '3584AN', 'Utrecht',   'inkoop@vanduurenhaircosmetics.nl',       '+31 623456121'),
(2, 'ColorPro Benelux',         'Gibraltarstraat',      '234', NULL,  '5611AA', 'Eindhoven', 'orders@colorpro-benelux.nl',             '+31 623456122'),
(3, 'SalonStyle Supplies',      'Der Kinderenstraat',   '456', 'Bis', '3011AB', 'Rotterdam', 'service@salonstylesupplies.nl',          '+31 623456123'),
(4, 'BarberCare Nederland',     'Nachtegaalstraat',     '233', 'A',   '4811AA', 'Breda',     'bestellingen@barbercare-nederland.nl',   '+31 623456124'),
(5, 'HairTools Groothandel',    'Bertram Russellstraat','45',  NULL,  '8011AB', 'Zwolle',    'contact@hairtools-groothandel.nl',       '+31 623456125');

INSERT INTO LeverancierOrder (Id, Ordernummer, ProductId, LeverancierId, Aantal, Orderdatum, Leverdatum, Leverstatus) VALUES
(1,  'ORD-2026-1001', 1,  1, 12, '2026-05-04', NULL,         'Inbehandeling'),
(2,  'ORD-2026-1002', 5,  2,  8, '2026-05-05', NULL,         'Inbehandeling'),
(3,  'ORD-2026-1003', 9,  3, 10, '2026-05-06', '2026-05-08', 'Geleverd'),
(4,  'ORD-2026-1004', 7,  2,  6, '2026-05-07', NULL,         'Nietleverbaar'),
(5,  'ORD-2026-1005', 10, 4,  9, '2026-05-08', NULL,         'Inbehandeling'),
(6,  'ORD-2026-1006', 2,  1,  7, '2026-05-09', NULL,         'Inbehandeling'),
(7,  'ORD-2026-1007', 3,  1,  6, '2026-05-10', NULL,         'Inbehandeling'),
(8,  'ORD-2026-1008', 4,  4,  5, '2026-05-10', NULL,         'Inbehandeling'),
(9,  'ORD-2026-1009', 6,  2,  6, '2026-05-11', NULL,         'Inbehandeling'),
(10, 'ORD-2026-1010', 8,  3,  8, '2026-05-11', NULL,         'Inbehandeling');
