-- Stored Procedures voor Kniploket Tiko (Klant- en Bestelling module)
-- Voer uit na create_kniploket_tiko_empty.sql
--
-- Gebruik:
--   mysql -u root -p < stored_procedures_empty.sql

CREATE DATABASE IF NOT EXISTS kniploket_tiko_empty
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE kniploket_tiko_empty;

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
     * Haalt Ã©Ã©n klant op via INNER JOIN op gerelateerde tabellen.
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

DROP PROCEDURE IF EXISTS sp_Bestelling_GetAll$$
CREATE PROCEDURE sp_Bestelling_GetAll(IN p_Status VARCHAR(50))
BEGIN
    /*
     * Haalt alle actieve bestellingen op met klant- en productgegevens.
     * Gebruikt INNER JOIN tussen Bestelling, Klant, ProductPerBestelling en Product.
     * Optionele filter op bestelstatus.
     */
    SELECT
        b.Id,
        b.BestelNummer,
        b.Omschrijving,
        b.Datum,
        b.Tijd,
        b.Bestelstatus,
        k.Id AS KlantId,
        k.Voornaam,
        k.Tussenvoegsel,
        k.Achternaam,
        k.Relatienummer,
        COUNT(ppb.Id) AS AantalProducten,
        ROUND(SUM(
            ((ppb.UnitPrijs * ppb.Aantal) - ppb.Korting)
            * (1 + (ppb.BTWPercentage / 100))
        ), 2) AS Totaal
    FROM Bestelling b
    INNER JOIN Klant k ON k.Id = b.KlantId AND k.IsActief = 1
    INNER JOIN ProductPerBestelling ppb ON ppb.BestellingId = b.Id AND ppb.IsActief = 1
    INNER JOIN Product p ON p.Id = ppb.ProductId AND p.IsActief = 1
    WHERE b.IsActief = 1
      AND (
          p_Status IS NULL
          OR p_Status = ''
          OR b.Bestelstatus = (p_Status COLLATE utf8mb4_unicode_ci)
      )
    GROUP BY
        b.Id,
        b.BestelNummer,
        b.Omschrijving,
        b.Datum,
        b.Tijd,
        b.Bestelstatus,
        k.Id,
        k.Voornaam,
        k.Tussenvoegsel,
        k.Achternaam,
        k.Relatienummer
    ORDER BY b.Datum DESC, b.Tijd DESC, b.BestelNummer DESC;
END$$

DELIMITER ;

