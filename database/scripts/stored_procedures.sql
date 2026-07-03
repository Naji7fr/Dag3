-- Stored Procedures voor Kniploket Tiko (Klant module)
-- Voer uit na create_kniploket_tiko.sql
--
-- Gebruik:
--   mysql -u root -p < stored_procedures.sql

CREATE DATABASE IF NOT EXISTS kniploket_tiko
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE kniploket_tiko;

-- Update: Rename 'Combi behandelingen' (ID 2) to 'Stylen'
UPDATE Behandeling SET Naam = 'Stylen' WHERE Id = 2;

DELIMITER $$

DROP PROCEDURE IF EXISTS sp_Klant_GetAll$$
CREATE PROCEDURE sp_Klant_GetAll(IN p_Postcode VARCHAR(10))
BEGIN
    /*
     * Haalt alle actieve klanten op met contact- en accountgegevens.
     * Gebruikt INNER JOIN tussen Klant, KlantPerContact, Contact en users.
     * Optionele filter op postcode (exacte match, hoofdlettergevoelig genormaliseerd).
     */
    SELECT
        k.Id,
        k.Voornaam,
        k.Tussenvoegsel,
        k.Achternaam,
        k.Relatienummer,
        k.Bijzonderheden,
        k.UserId,
        c.Id AS ContactId,
        c.Straatnaam,
        c.Huisnummer,
        c.Toevoeging,
        c.Postcode,
        c.Plaats,
        c.Email AS ContactEmail,
        c.Mobiel,
        u.email AS AccountEmail,
        u.name AS AccountName
    FROM Klant k
    INNER JOIN KlantPerContact kpc ON kpc.KlantId = k.Id
    INNER JOIN Contact c ON c.Id = kpc.ContactId
    INNER JOIN users u ON u.id = k.UserId
    WHERE k.IsActief = 1
      AND (
          p_Postcode IS NULL
          OR p_Postcode = ''
          OR c.Postcode = UPPER(REPLACE(p_Postcode, ' ', ''))
      )
    ORDER BY k.Voornaam ASC;
END$$

DROP PROCEDURE IF EXISTS sp_Klant_GetById$$
CREATE PROCEDURE sp_Klant_GetById(IN p_KlantId INT)
BEGIN
    /*
     * Haalt één klant op via INNER JOIN op gerelateerde tabellen.
     */
    SELECT
        k.Id,
        k.Voornaam,
        k.Tussenvoegsel,
        k.Achternaam,
        k.Relatienummer,
        k.Bijzonderheden,
        k.UserId,
        c.Id AS ContactId,
        c.Straatnaam,
        c.Huisnummer,
        c.Toevoeging,
        c.Postcode,
        c.Plaats,
        c.Email AS ContactEmail,
        c.Mobiel,
        u.email AS AccountEmail
    FROM Klant k
    INNER JOIN KlantPerContact kpc ON kpc.KlantId = k.Id
    INNER JOIN Contact c ON c.Id = kpc.ContactId
    INNER JOIN users u ON u.id = k.UserId
    WHERE k.Id = p_KlantId
      AND k.IsActief = 1
    LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_Klant_IsContactEmailInUse$$
CREATE PROCEDURE sp_Klant_IsContactEmailInUse(
    IN p_Email VARCHAR(255),
    IN p_ContactId INT
)
BEGIN
    /*
     * Controleert of een contact-e-mailadres al door een ander contact wordt gebruikt.
     */
    SELECT COUNT(*) AS Aantal
    FROM Contact
    WHERE Email = p_Email
      AND Id != p_ContactId;
END$$

DROP PROCEDURE IF EXISTS sp_Klant_Update$$
CREATE PROCEDURE sp_Klant_Update(
    IN p_KlantId INT,
    IN p_ContactId INT,
    IN p_Voornaam VARCHAR(100),
    IN p_Tussenvoegsel VARCHAR(50),
    IN p_Achternaam VARCHAR(100),
    IN p_Bijzonderheden VARCHAR(500),
    IN p_Straatnaam VARCHAR(150),
    IN p_Huisnummer VARCHAR(10),
    IN p_Toevoeging VARCHAR(10),
    IN p_Postcode VARCHAR(10),
    IN p_Plaats VARCHAR(100),
    IN p_ContactEmail VARCHAR(255),
    IN p_Mobiel VARCHAR(20)
)
BEGIN
    /*
     * Werkt klant- en contactgegevens atomisch bij binnen een transactie.
     */
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE Klant
    SET
        Voornaam = p_Voornaam,
        Tussenvoegsel = p_Tussenvoegsel,
        Achternaam = p_Achternaam,
        Bijzonderheden = p_Bijzonderheden,
        DatumGewijzigd = CURRENT_TIMESTAMP(6)
    WHERE Id = p_KlantId;

    UPDATE Contact
    SET
        Straatnaam = p_Straatnaam,
        Huisnummer = p_Huisnummer,
        Toevoeging = p_Toevoeging,
        Postcode = UPPER(REPLACE(p_Postcode, ' ', '')),
        Plaats = p_Plaats,
        Email = p_ContactEmail,
        Mobiel = p_Mobiel,
        DatumGewijzigd = CURRENT_TIMESTAMP(6)
    WHERE Id = p_ContactId;

    COMMIT;
END$$

-- ---------------------------------------------------------------------------
-- Medewerker Stored Procedures
-- ---------------------------------------------------------------------------

DROP PROCEDURE IF EXISTS sp_Medewerker_GetAll$$
CREATE PROCEDURE sp_Medewerker_GetAll(IN p_Specialisatie VARCHAR(100))
BEGIN
    /*
     * Haalt alle actieve medewerkers op met contact- en accountgegevens.
     * Gebruikt INNER JOIN tussen Medewerker, MedewerkerPerContact, Contact en users.
     * Optionele filter op specialisatie.
     */
    SELECT
        m.Id,
        m.Voornaam,
        m.Tussenvoegsel,
        m.Achternaam,
        m.Specialisatie,
        m.Geboortedatum,
        m.UserId,
        c.Id AS ContactId,
        c.Straatnaam,
        c.Huisnummer,
        c.Toevoeging,
        c.Postcode,
        c.Plaats,
        c.Email AS ContactEmail,
        c.Mobiel,
        u.email AS AccountEmail,
        u.name AS AccountName
    FROM Medewerker m
    INNER JOIN MedewerkerPerContact mpc ON mpc.MedewerkerId = m.Id
    INNER JOIN Contact c ON c.Id = mpc.ContactId
    INNER JOIN users u ON u.id = m.UserId
    WHERE m.IsActief = 1
      AND (
          p_Specialisatie IS NULL
          OR p_Specialisatie = ''
          OR m.Specialisatie = p_Specialisatie
      )
    ORDER BY m.Voornaam ASC;
END$$

DROP PROCEDURE IF EXISTS sp_Medewerker_GetById$$
CREATE PROCEDURE sp_Medewerker_GetById(IN p_MedewerkerId INT)
BEGIN
    /*
     * Haalt één medewerker op via INNER JOIN op gerelateerde tabellen.
     */
    SELECT
        m.Id,
        m.Voornaam,
        m.Tussenvoegsel,
        m.Achternaam,
        m.Specialisatie,
        m.Geboortedatum,
        m.UserId,
        c.Id AS ContactId,
        c.Straatnaam,
        c.Huisnummer,
        c.Toevoeging,
        c.Postcode,
        c.Plaats,
        c.Email AS ContactEmail,
        c.Mobiel,
        u.email AS AccountEmail
    FROM Medewerker m
    INNER JOIN MedewerkerPerContact mpc ON mpc.MedewerkerId = m.Id
    INNER JOIN Contact c ON c.Id = mpc.ContactId
    INNER JOIN users u ON u.id = m.UserId
    WHERE m.Id = p_MedewerkerId
      AND m.IsActief = 1
    LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS sp_Medewerker_GetSpecialisaties$$
CREATE PROCEDURE sp_Medewerker_GetSpecialisaties()
BEGIN
    /*
     * Haalt alle unieke behandelingnamen op (specialisaties) uit de Behandeling tabel.
     * Dit zorgt ervoor dat alle behandelingstypen in de dropdown verschijnen,
     * ook als er geen medewerker aan toegewezen is.
     */
    SELECT DISTINCT b.Naam AS Specialisatie
    FROM Behandeling b
    WHERE b.IsActief = 1
    ORDER BY b.Naam ASC;
END$$

DROP PROCEDURE IF EXISTS sp_Medewerker_Update$$
CREATE PROCEDURE sp_Medewerker_Update(
    IN p_MedewerkerId INT,
    IN p_ContactId INT,
    IN p_Voornaam VARCHAR(100),
    IN p_Tussenvoegsel VARCHAR(50),
    IN p_Achternaam VARCHAR(100),
    IN p_Specialisatie VARCHAR(100),
    IN p_Geboortedatum DATE,
    IN p_Straatnaam VARCHAR(150),
    IN p_Huisnummer VARCHAR(10),
    IN p_Toevoeging VARCHAR(10),
    IN p_Postcode VARCHAR(10),
    IN p_Plaats VARCHAR(100),
    IN p_ContactEmail VARCHAR(255),
    IN p_Mobiel VARCHAR(20)
)
BEGIN
    /*
     * Werkt medewerker- en contactgegevens atomisch bij binnen een transactie.
     * Gebruikt INNER JOINs voor validatie en update.
     */
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    UPDATE Medewerker
    SET
        Voornaam = p_Voornaam,
        Tussenvoegsel = p_Tussenvoegsel,
        Achternaam = p_Achternaam,
        Specialisatie = p_Specialisatie,
        Geboortedatum = p_Geboortedatum,
        DatumGewijzigd = CURRENT_TIMESTAMP(6)
    WHERE Id = p_MedewerkerId;

    UPDATE Contact
    SET
        Straatnaam = p_Straatnaam,
        Huisnummer = p_Huisnummer,
        Toevoeging = p_Toevoeging,
        Postcode = UPPER(REPLACE(p_Postcode, ' ', '')),
        Plaats = p_Plaats,
        Email = p_ContactEmail,
        Mobiel = p_Mobiel,
        DatumGewijzigd = CURRENT_TIMESTAMP(6)
    WHERE Id = p_ContactId;

    COMMIT;
END$$

DELIMITER ;
