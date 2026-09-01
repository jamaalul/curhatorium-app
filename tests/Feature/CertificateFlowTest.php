<?php

namespace Tests\Feature;

use App\Mail\CertificateCopyMail;
use App\Models\CbtModule;
use App\Models\Certificate;
use App\Models\Chapter;
use App\Models\User;
use App\Models\UserCbtModule;
use App\Models\UserChapterProgress;
use App\Models\UserModuleProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('private');
    }

    public function test_certificate_claim_requires_active_ownership_and_completed_progress(): void
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->published()->create();
        Chapter::factory()->for($module, 'module')->create();

        $this->actingAs($user)
            ->post(route('e-class.certificates.claim', $module))
            ->assertForbidden();

        UserCbtModule::factory()->for($user)->for($module, 'module')->create();
        $this->actingAs($user)
            ->post(route('e-class.certificates.claim', $module))
            ->assertUnprocessable();

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_module_without_active_chapter_cannot_receive_certificate(): void
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->published()->create();
        UserCbtModule::factory()->for($user)->for($module, 'module')->create();
        UserModuleProgress::factory()->completed()->for($user)->for($module, 'module')->create();

        $this->actingAs($user)
            ->post(route('e-class.certificates.claim', $module))
            ->assertUnprocessable();
    }

    public function test_eligible_user_can_claim_idempotent_certificate_and_download_private_pdf(): void
    {
        [$user, $module] = $this->eligibleModule();

        $this->actingAs($user)
            ->post(route('e-class.certificates.claim', $module))
            ->assertRedirect();
        $certificate = Certificate::query()->sole();

        $this->assertNotNull($certificate->issued_at);
        $this->assertStringStartsWith('CRH-', $certificate->certificate_number);
        $this->assertNotNull($certificate->pdf_path);
        Storage::disk('private')->assertExists((string) $certificate->pdf_path);

        $this->actingAs($user)
            ->post(route('e-class.certificates.claim', $module))
            ->assertRedirect(route('e-class.certificates.show', $certificate));
        $this->assertDatabaseCount('certificates', 1);

        $this->actingAs($user)
            ->get(route('e-class.certificates.download', $certificate))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_certificate_routes_reject_another_user(): void
    {
        [$owner, $module] = $this->eligibleModule();
        $this->actingAs($owner)->post(route('e-class.certificates.claim', $module));
        $certificate = Certificate::query()->sole();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)->get(route('e-class.certificates.show', $certificate))->assertForbidden();
        $this->actingAs($otherUser)->get(route('e-class.certificates.download', $certificate))->assertForbidden();
        $this->actingAs($otherUser)->post(route('e-class.certificates.email', $certificate))->assertForbidden();
    }

    public function test_certificate_email_is_only_sent_to_owner_with_pdf_attachment(): void
    {
        Mail::fake();
        [$user, $module] = $this->eligibleModule();
        $this->actingAs($user)->post(route('e-class.certificates.claim', $module));
        $certificate = Certificate::query()->sole();

        $this->actingAs($user)
            ->post(route('e-class.certificates.email', $certificate), ['email' => 'attacker@example.com'])
            ->assertRedirect();

        Mail::assertQueued(CertificateCopyMail::class, function (CertificateCopyMail $mail) use ($user, $certificate): bool {
            return $mail->hasTo($user->email)
                && $mail->certificate->is($certificate)
                && count($mail->attachments()) === 1;
        });
        Mail::assertNotQueued(CertificateCopyMail::class, fn (CertificateCopyMail $mail): bool => $mail->hasTo('attacker@example.com'));
    }

    /** @return array{User, CbtModule} */
    private function eligibleModule(): array
    {
        $user = User::factory()->create();
        $module = CbtModule::factory()->published()->create();
        $chapter = Chapter::factory()->for($module, 'module')->create();
        UserCbtModule::factory()->for($user)->for($module, 'module')->create();
        UserChapterProgress::factory()->completed()->for($user)->for($chapter)->create();
        UserModuleProgress::factory()->completed()->for($user)->for($module, 'module')->create();

        return [$user, $module];
    }
}
