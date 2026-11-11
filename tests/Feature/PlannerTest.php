<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Planner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PlannerTest extends TestCase
{
    use RefreshDatabase;

    private function client(string $u = 'ana'): Client
    {
        return Client::create(['name' => ucfirst($u), 'email' => "$u@test.local", 'username' => $u, 'phone' => '0812', 'password' => bcrypt('x')]);
    }

    public function test_tamu_diarahkan_ke_login(): void
    {
        $this->get('/client/planner')->assertRedirect(route('client.login'));
    }

    public function test_planner_dibuat_dengan_checklist_template_di_semua_tab(): void
    {
        $c = $this->client();
        foreach (['ringkasan', 'checklist', 'anggaran', 'tamu'] as $tab) {
            $this->actingAs($c, 'client')->get("/client/planner?tab=$tab")->assertOk();
        }

        $planner = $c->planner()->first();
        $this->assertSame(count(array_merge(...array_values(Planner::TEMPLATE))), $planner->tasks()->count());
    }

    public function test_tanggal_pernikahan_menjadwalkan_ulang_checklist(): void
    {
        $c = $this->client();
        $this->actingAs($c, 'client')->get('/client/planner');

        $this->actingAs($c, 'client')->put('/client/planner', ['wedding_date' => '2027-12-20', 'budget' => 150000000])
            ->assertRedirect(route('client.planner', ['tab' => 'ringkasan']));

        $tasks = $c->planner()->first()->tasks;
        $this->assertSame('2026-12-20', $tasks->firstWhere('phase', '12m')->due_date->toDateString());
        $this->assertSame('2027-12-13', $tasks->firstWhere('phase', '1w')->due_date->toDateString());
        $this->assertSame('2027-12-20', $tasks->firstWhere('phase', 'day')->due_date->toDateString());
    }

    public function test_checklist_toggle_dan_terlambat(): void
    {
        Carbon::setTestNow('2027-06-01');
        $c = $this->client();
        $this->actingAs($c, 'client')->put('/client/planner', ['wedding_date' => '2027-07-01']);
        $this->actingAs($c, 'client')->get('/client/planner');
        $task = $c->planner()->first()->tasks()->where('phase', '12m')->first();

        $this->assertTrue($task->isOverdue());
        $this->actingAs($c, 'client')->patch("/client/planner/tasks/{$task->id}/toggle");
        $this->assertNotNull($task->fresh()->done_at);
        $this->assertFalse($task->fresh()->isOverdue());
        $this->actingAs($c, 'client')->patch("/client/planner/tasks/{$task->id}/toggle");
        $this->assertNull($task->fresh()->done_at);
        $this->assertNotSame('Diretas', $task->fresh()->title);
        Carbon::setTestNow();
    }

    public function test_catatan_tugas_disimpan_diedit_dan_tampil(): void
    {
        $c = $this->client();
        $this->actingAs($c, 'client')->get('/client/planner'); // membuat checklist template
        $this->actingAs($c, 'client')
            ->post('/client/planner/tasks', ['title' => 'Survey gedung', 'phase' => '12m', 'note' => "Gedung A: 17 Agustus\nKontak Bu Rina"])
            ->assertSessionHasNoErrors();
        $task = $c->planner()->first()->tasks()->where('title', 'Survey gedung')->first();
        $this->assertSame("Gedung A: 17 Agustus\nKontak Bu Rina", $task->note);

        // Edit tugas template: tambah catatan tanpa mengubah status selesai
        $template = $c->planner()->first()->tasks()->where('title', 'Tentukan tanggal & konsep acara')->first();
        $template->update(['done_at' => now()]);
        $this->actingAs($c, 'client')
            ->put("/client/planner/tasks/{$template->id}", ['title' => $template->title, 'phase' => '12m', 'note' => 'Akad 17 Agustus 2027, resepsi 18 Agustus'])
            ->assertSessionHasNoErrors();
        $this->assertSame('Akad 17 Agustus 2027, resepsi 18 Agustus', $template->fresh()->note);
        $this->assertNotNull($template->fresh()->done_at);

        $this->actingAs($c, 'client')->get('/client/planner?tab=checklist')->assertSee('Akad 17 Agustus 2027, resepsi 18 Agustus');

        // Catatan boleh dikosongkan, dan dibatasi 2000 karakter
        $this->actingAs($c, 'client')->put("/client/planner/tasks/{$template->id}", ['title' => $template->title, 'phase' => '12m', 'note' => ''])->assertSessionHasNoErrors();
        $this->assertNull($template->fresh()->note);
        $this->actingAs($c, 'client')->put("/client/planner/tasks/{$template->id}", ['title' => $template->title, 'phase' => '12m', 'note' => str_repeat('x', 2001)])->assertSessionHasErrors('note');
    }

    public function test_anggaran_validasi_dibayar_tidak_melebihi_estimasi(): void
    {
        $c = $this->client();
        $this->actingAs($c, 'client')
            ->post('/client/planner/budget', ['category' => 'venue', 'name' => 'Gedung', 'estimate' => 1000, 'paid' => 2000])
            ->assertSessionHasErrors('paid');
        $this->actingAs($c, 'client')
            ->post('/client/planner/budget', ['category' => 'bukan-kategori', 'name' => 'X', 'estimate' => 1])
            ->assertSessionHasErrors('category');
        $this->actingAs($c, 'client')
            ->post('/client/planner/budget', ['category' => 'venue', 'name' => 'Gedung', 'estimate' => 1000, 'paid' => 400])
            ->assertSessionHasNoErrors();

        $this->assertSame('400.00', $c->planner()->first()->budgetItems()->first()->paid);
    }

    public function test_tamu_rsvp(): void
    {
        $c = $this->client();
        $this->actingAs($c, 'client')->post('/client/planner/guests', ['name' => 'Budi', 'side' => 'pria', 'pax' => 2])->assertSessionHasNoErrors();
        $g = $c->planner()->first()->guests()->first();
        $this->assertSame('pending', $g->rsvp);

        $this->actingAs($c, 'client')->patch("/client/planner/guests/{$g->id}/rsvp", ['rsvp' => 'hadir']);
        $this->assertSame('hadir', $g->fresh()->rsvp);
        $this->actingAs($c, 'client')->patch("/client/planner/guests/{$g->id}/rsvp", ['rsvp' => 'mungkin'])->assertSessionHasErrors('rsvp');
    }

    public function test_client_tidak_bisa_mengubah_data_client_lain(): void
    {
        $ana = $this->client('ana');
        $budi = $this->client('budi');
        $this->actingAs($ana, 'client')->get('/client/planner');
        $this->actingAs($ana, 'client')->post('/client/planner/guests', ['name' => 'Tamu Ana', 'side' => 'wanita', 'pax' => 1]);
        $this->actingAs($ana, 'client')->post('/client/planner/budget', ['category' => 'venue', 'name' => 'Gedung Ana', 'estimate' => 10]);

        $p = $ana->planner()->first();
        $task = $p->tasks()->first();
        $guest = $p->guests()->first();
        $item = $p->budgetItems()->first();

        $this->actingAs($budi, 'client')->patch("/client/planner/tasks/{$task->id}/toggle")->assertNotFound();
        $this->actingAs($budi, 'client')->delete("/client/planner/tasks/{$task->id}")->assertNotFound();
        $this->actingAs($budi, 'client')->put("/client/planner/tasks/{$task->id}", ['title' => 'Diretas', 'phase' => '12m', 'note' => 'x'])->assertNotFound();
        $this->actingAs($budi, 'client')->put("/client/planner/guests/{$guest->id}", ['name' => 'Diretas', 'side' => 'pria', 'pax' => 1])->assertNotFound();
        $this->actingAs($budi, 'client')->patch("/client/planner/guests/{$guest->id}/rsvp", ['rsvp' => 'tidak'])->assertNotFound();
        $this->actingAs($budi, 'client')->delete("/client/planner/budget/{$item->id}")->assertNotFound();

        $this->assertNull($task->fresh()->done_at);
        $this->assertNotSame('Diretas', $task->fresh()->title);
        $this->assertSame('Tamu Ana', $guest->fresh()->name);
        $this->assertSame('pending', $guest->fresh()->rsvp);
        $this->assertNotNull($item->fresh());
        $this->actingAs($budi, 'client')->get('/client/planner?tab=tamu')->assertDontSee('Tamu Ana');
    }

    public function test_reset_menghapus_semua_data_dan_mulai_dari_awal(): void
    {
        $ana = $this->client('ana');
        $budi = $this->client('budi');
        foreach ([$ana, $budi] as $c) {
            $this->actingAs($c, 'client')->get('/client/planner');
            $this->actingAs($c, 'client')->put('/client/planner', ['partner_1' => 'Lama', 'budget' => 5000]);
            $this->actingAs($c, 'client')->post('/client/planner/guests', ['name' => 'Tamu', 'side' => 'pria', 'pax' => 1]);
            $this->actingAs($c, 'client')->post('/client/planner/budget', ['category' => 'venue', 'name' => 'Gedung', 'estimate' => 10]);
        }
        $lama = $ana->planner()->first();
        $lama->tasks()->first()->update(['done_at' => now(), 'note' => 'catatan lama']);

        // Tanpa konfirmasi yang benar: tidak terhapus
        $this->actingAs($ana, 'client')->delete('/client/planner', ['confirm' => 'reset'])->assertSessionHasErrors('confirm');
        $this->actingAs($ana, 'client')->delete('/client/planner')->assertSessionHasErrors('confirm');
        $this->assertNotNull($lama->fresh());

        $this->actingAs($ana, 'client')->delete('/client/planner', ['confirm' => 'RESET'])
            ->assertRedirect(route('client.planner', ['tab' => 'ringkasan']));

        $this->assertNull($lama->fresh());
        foreach (['planner_tasks', 'planner_budget_items', 'planner_guests'] as $tabel) {
            $this->assertDatabaseMissing($tabel, ['planner_id' => $lama->id]);
        }

        // Dibuka lagi: planner baru yang bersih dengan checklist template
        $this->actingAs($ana, 'client')->get('/client/planner')->assertOk()->assertDontSee('Lama');
        $baru = $ana->planner()->first();
        $this->assertNotSame($lama->id, $baru->id);
        $this->assertSame(0, $baru->guests()->count());
        $this->assertSame(0, $baru->tasks()->whereNotNull('done_at')->count());

        // Planner client lain tidak tersentuh
        $this->assertSame('Lama', $budi->planner()->first()->partner_1);
        $this->assertSame(1, $budi->planner()->first()->guests()->count());
    }

    public function test_admin_melihat_daftar_dan_detail_planner(): void
    {
        $admin = \App\Models\User::create(['username' => 'admin', 'email' => 'a@test.local', 'password' => bcrypt('x')]);
        $ana = $this->client('ana');
        $this->actingAs($ana, 'client')->get('/client/planner');
        $this->actingAs($ana, 'client')->put('/client/planner', ['partner_1' => 'Raka', 'partner_2' => 'Laras', 'wedding_date' => now()->addMonth()->toDateString()]);
        $this->actingAs($ana, 'client')->post('/client/planner/guests', ['name' => 'Pak Budi', 'side' => 'pria', 'pax' => 3]);
        $planner = $ana->planner()->first();

        $this->get('/admin/planners')->assertRedirect(route('admin.login'));
        $this->actingAs($ana, 'client')->get('/admin/planners')->assertRedirect(route('admin.login'));

        $this->actingAs($admin, 'admin')->get('/admin/planners')->assertOk()->assertSee('Raka')->assertSee('Ana');
        $this->actingAs($admin, 'admin')->get('/admin/planners?q=laras')->assertOk()->assertSee('Raka');
        $this->actingAs($admin, 'admin')->get('/admin/planners?q=tidakada')->assertOk()->assertDontSee('Raka');
        $this->actingAs($admin, 'admin')->get("/admin/planners/{$planner->id}")->assertOk()->assertSee('Pak Budi')->assertSee('ana@test.local');
        $this->actingAs($admin, 'admin')->get('/admin/planners/99999')->assertNotFound();
    }
}
