<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * User Story tests — Klant Read (01) en Klant Update (02).
 */
class KlantUserStoriesTest extends TestCase
{
    private function loginAsEigenaar(): User
    {
        $user = User::query()->where('email', 'eigenaar@kniplokettiko.nl')->first();

        if ($user === null) {
            $this->markTestSkipped('Eigenaar testaccount niet gevonden in database.');
        }

        $this->actingAs($user);

        return $user;
    }

    public function test_klantengegevens_zijn_succesvol_weergegeven(): void
    {
        $this->loginAsEigenaar();

        $response = $this->get(route('home'));
        $response->assertOk();

        $response = $this->get(route('klanten.index'));
        $response->assertOk();
        $response->assertSee('Overzicht klanten');
        $response->assertSee('Contact e-mail');
        $response->assertSee('6 klant(en)');
        $response->assertSee('Ahmed');
        $response->assertSee('Gevonden klanten');
    }

    public function test_geen_klanten_voor_onbekende_postcode(): void
    {
        $this->loginAsEigenaar();

        $response = $this->get(route('klanten.index', ['postcode' => '9999ZZ']));
        $response->assertOk();
        $response->assertSee('Er zijn geen klanten bekent die de geselecteerde postcode hebben');
    }

    public function test_klantengegevens_succesvol_gewijzigd(): void
    {
        $this->loginAsEigenaar();

        $show = $this->get(route('klanten.show', 1));
        $show->assertOk();
        $show->assertSee('Klantdetail');
        $show->assertSee('Wijzigen');

        $edit = $this->get(route('klanten.edit', 1));
        $edit->assertOk();
        $edit->assertSee('Klant wijzigen');

        $uniqueEmail = 'piet.test.'.time().'@example.com';

        $update = $this->put(route('klanten.update', 1), [
            'naam' => 'Piet van Loenen',
            'contact_email' => $uniqueEmail,
            'straatnaam' => 'Oudegracht',
            'huisnummer' => '88',
            'toevoeging' => 'A',
            'postcode' => '3512AB',
            'plaats' => 'Utrecht',
            'mobiel' => '+31 6 1234 61 71',
            'bijzonderheden' => 'Voorkeur voor ochtendafspraken.',
        ]);

        $update->assertRedirect(route('klanten.index', ['postcode' => '3512AB']));
        $update->assertSessionHas('success', 'Klantgegevens bijgewerkt.');

        $index = $this->get(route('klanten.index', ['postcode' => '3512AB']));
        $index->assertOk();
        $index->assertSee('Klantgegevens bijgewerkt');
        $index->assertSee($uniqueEmail);
        $index->assertViewHas('autoHideFlash', true);
        $index->assertViewHas('flashAutoHideMs', 3000);

        // Restore original email for repeat runs
        $this->put(route('klanten.update', 1), [
            'naam' => 'Piet van Loenen',
            'contact_email' => 'piet.van.loenen@gmail.com',
            'straatnaam' => 'Oudegracht',
            'huisnummer' => '88',
            'toevoeging' => 'A',
            'postcode' => '3512AB',
            'plaats' => 'Utrecht',
            'mobiel' => '+31 6 1234 61 71',
            'bijzonderheden' => 'Voorkeur voor ochtendafspraken.',
        ]);
    }

    public function test_klantengegevens_niet_gewijzigd_bij_dubbel_emailadres(): void
    {
        $this->loginAsEigenaar();

        $response = $this->from(route('klanten.edit', 1))
            ->put(route('klanten.update', 1), [
                'naam' => 'Piet van Loenen',
                'contact_email' => 'jan.jansen@outlook.com',
                'straatnaam' => 'Oudegracht',
                'huisnummer' => '88',
                'toevoeging' => 'A',
                'postcode' => '3512AB',
                'plaats' => 'Utrecht',
                'mobiel' => '+31 6 1234 61 71',
                'bijzonderheden' => 'Voorkeur voor ochtendafspraken.',
            ]);

        $response->assertRedirect(route('klanten.edit', 1));
        $response->assertSessionHas('error', 'Klantgegevens zijn niet bijgewerkt.');
        $response->assertSessionHasErrors([
            'contact_email' => 'Het e-mailadres is al in gebruik',
        ]);

        $followUp = $this->get(route('klanten.edit', 1));
        $followUp->assertSee('Klantgegevens zijn niet bijgewerkt');
        $followUp->assertSee('Het e-mailadres is al in gebruik');
    }
}
