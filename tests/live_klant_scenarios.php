<?php

/**
 * Live user story tests tegen http://127.0.0.1:8000
 * Run: php tests/live_klant_scenarios.php
 */

declare(strict_types=1);

$baseUrl = 'http://127.0.0.1:8000';
$cookieFile = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kniploket_test_cookies.txt';
@unlink($cookieFile);

$passed = 0;
$failed = 0;

function request(string $method, string $url, array $options = []): array
{
    global $cookieFile;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_COOKIEJAR => $cookieFile,
        CURLOPT_COOKIEFILE => $cookieFile,
        CURLOPT_HEADER => true,
        CURLOPT_TIMEOUT => 30,
    ]);

    if (! empty($options['form'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($options['form']));
    }

    $raw = curl_exec($ch);
    if ($raw === false) {
        throw new RuntimeException('cURL error: '.curl_error($ch));
    }

    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    return [
        'status' => $status,
        'headers' => substr($raw, 0, $headerSize),
        'body' => substr($raw, $headerSize),
    ];
}

function getCsrfToken(string $html): string
{
    if (! preg_match('/name="_token" value="([^"]+)"/', $html, $matches)) {
        throw new RuntimeException('CSRF token niet gevonden.');
    }

    return $matches[1];
}

function assertContains(string $needle, string $haystack, string $label): void
{
    global $passed, $failed;
    if (str_contains($haystack, $needle)) {
        echo "  PASS: {$label}\n";
        $passed++;
    } else {
        echo "  FAIL: {$label} (verwacht: {$needle})\n";
        $failed++;
    }
}

function assertTrue(bool $condition, string $label): void
{
    global $passed, $failed;
    if ($condition) {
        echo "  PASS: {$label}\n";
        $passed++;
    } else {
        echo "  FAIL: {$label}\n";
        $failed++;
    }
}

echo "=== Live User Story Tests ===\n\n";

try {
    // Login als eigenaar
    echo "Setup: inloggen als eigenaar\n";
    $loginPage = request('GET', "{$baseUrl}/login");
    assertTrue($loginPage['status'] === 200, 'Loginpagina bereikbaar');

    $token = getCsrfToken($loginPage['body']);
    $login = request('POST', "{$baseUrl}/login", [
        'form' => [
            '_token' => $token,
            'email' => 'eigenaar@kniplokettiko.nl',
            'password' => 'password',
        ],
    ]);
    assertTrue(in_array($login['status'], [302, 303], true), 'Login redirect na succes');

    // Scenario 3: Klantengegevens succesvol weergegeven
    echo "\nScenario: Klantengegevens zijn succesvol weergegeven\n";
    $home = request('GET', "{$baseUrl}/");
    assertTrue($home['status'] === 200, 'Homepagina geladen');

    $index = request('GET', "{$baseUrl}/klanten");
    assertTrue($index['status'] === 200, 'Klantenoverzicht geladen');
    assertContains('Overzicht klanten', $index['body'], 'Titel overzicht');
    assertContains('Contact e-mail', $index['body'], 'Kolom contact e-mail');
    assertContains('Gevonden klanten', $index['body'], 'Klantenteller zichtbaar');
    assertContains('6 klant(en)', $index['body'], 'Alle 6 klanten in totaal');
    assertContains('Ahmed', $index['body'], 'Klant zichtbaar in overzicht');

    // Scenario 4: Geen klanten voor onbekende postcode
    echo "\nScenario: Er zijn geen klantengegevens gevonden (postcode)\n";
    $search = request('GET', "{$baseUrl}/klanten?postcode=9999ZZ");
    assertTrue($search['status'] === 200, 'Postcode zoekresultaat geladen');
    assertContains(
        'Er zijn geen klanten bekent die de geselecteerde postcode hebben',
        $search['body'],
        'Melding onbekende postcode'
    );

    // Scenario 1: Succesvol wijzigen
    echo "\nScenario: Klantengegevens zijn succesvol gewijzigd\n";
    $show = request('GET', "{$baseUrl}/klanten/1");
    assertTrue($show['status'] === 200, 'Klant detail pagina');
    assertContains('Klantdetail', $show['body'], 'Detail titel');
    assertContains('Wijzigen', $show['body'], 'Wijzig knop op detail');

    $edit = request('GET', "{$baseUrl}/klanten/1/wijzigen");
    assertTrue($edit['status'] === 200, 'Klant wijzigen pagina');
    assertContains('Klant wijzigen', $edit['body'], 'Wijzig titel');

    $editToken = getCsrfToken($edit['body']);
    $uniqueEmail = 'piet.live.test.'.time().'@example.com';

    $update = request('POST', "{$baseUrl}/klanten/1", [
        'form' => [
            '_token' => $editToken,
            '_method' => 'PUT',
            'naam' => 'Piet van Loenen',
            'contact_email' => $uniqueEmail,
            'straatnaam' => 'Oudegracht',
            'huisnummer' => '88',
            'toevoeging' => 'A',
            'postcode' => '3512AB',
            'plaats' => 'Utrecht',
            'mobiel' => '+31 6 1234 61 71',
            'bijzonderheden' => 'Voorkeur voor ochtendafspraken.',
        ],
    ]);
    assertTrue(in_array($update['status'], [302, 303], true), 'Redirect na opslaan');
    assertContains('/klanten?postcode=3512AB', $update['headers'], 'Redirect naar klantenoverzicht met postcode');

    $afterUpdate = request('GET', "{$baseUrl}/klanten?postcode=3512AB");
    assertContains('Klantgegevens bijgewerkt', $afterUpdate['body'], 'Succesmelding');
    assertContains($uniqueEmail, $afterUpdate['body'], 'Nieuw e-mailadres in overzicht');
    assertContains('flash-alert', $afterUpdate['body'], 'Flash alert element aanwezig');
    assertContains('3000', $afterUpdate['body'], 'Auto-hide na 3 seconden geconfigureerd');

    // Scenario 2: Dubbel e-mailadres
    echo "\nScenario: Er zijn geen klantengegevens gewijzigd (dubbel e-mail)\n";
    $edit2 = request('GET', "{$baseUrl}/klanten/1/wijzigen");
    $editToken2 = getCsrfToken($edit2['body']);

    $duplicate = request('POST', "{$baseUrl}/klanten/1", [
        'form' => [
            '_token' => $editToken2,
            '_method' => 'PUT',
            'naam' => 'Piet van Loenen',
            'contact_email' => 'jan.jansen@outlook.com',
            'straatnaam' => 'Oudegracht',
            'huisnummer' => '88',
            'toevoeging' => 'A',
            'postcode' => '3512AB',
            'plaats' => 'Utrecht',
            'mobiel' => '+31 6 1234 61 71',
            'bijzonderheden' => 'Voorkeur voor ochtendafspraken.',
        ],
    ]);
    assertTrue(in_array($duplicate['status'], [302, 303], true), 'Redirect bij validatiefout');

    $editAfterFail = request('GET', "{$baseUrl}/klanten/1/wijzigen");
    assertContains('Klantgegevens zijn niet bijgewerkt', $editAfterFail['body'], 'Foutmelding bovenaan');
    assertContains('Het e-mailadres is al in gebruik', $editAfterFail['body'], 'Veldfout contact e-mail');

    // Restore original email
    echo "\nCleanup: origineel e-mailadres herstellen\n";
    $editRestore = request('GET', "{$baseUrl}/klanten/1/wijzigen");
    $restoreToken = getCsrfToken($editRestore['body']);
    request('POST', "{$baseUrl}/klanten/1", [
        'form' => [
            '_token' => $restoreToken,
            '_method' => 'PUT',
            'naam' => 'Piet van Loenen',
            'contact_email' => 'piet.van.loenen@gmail.com',
            'straatnaam' => 'Oudegracht',
            'huisnummer' => '88',
            'toevoeging' => 'A',
            'postcode' => '3512AB',
            'plaats' => 'Utrecht',
            'mobiel' => '+31 6 1234 61 71',
            'bijzonderheden' => 'Voorkeur voor ochtendafspraken.',
        ],
    ]);
    echo "  DONE: origineel e-mailadres hersteld\n";

} catch (Throwable $e) {
    echo "\nERROR: {$e->getMessage()}\n";
    exit(1);
}

echo "\n=== Resultaat: {$passed} passed, {$failed} failed ===\n";
exit($failed > 0 ? 1 : 0);
