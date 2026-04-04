<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\Estimation;
use App\Models\Option;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{user: User, template: Template, block: Block}
     */
    private function makeTemplateWithBlock(): array
    {
        $user = User::factory()->create();
        $block = Block::create(['name' => 'Hero Section', 'user_id' => $user->id]);

        $template = Template::create([
            'user_id' => $user->id,
            'name' => 'Landing Page',
            'type' => 'hour',
            'currency' => 'EUR',
        ]);

        $template->pages()->create(['name' => 'Site Header', 'type' => 'header', 'order' => 0, 'quantity' => 1]);
        $page = $template->pages()->create(['name' => 'Page Accueil', 'type' => 'regular', 'order' => 1, 'quantity' => 1]);
        $template->pages()->create(['name' => 'Site Footer', 'type' => 'footer', 'order' => 99, 'quantity' => 1]);

        $page->blocks()->create([
            'block_id' => $block->id,
            'order' => 1,
            'quantity' => 1,
            'price_programming' => 5.0,
            'price_integration' => 3.0,
            'price_field_creation' => 1.0,
            'price_content_management' => 0.5,
        ]);

        return compact('user', 'template', 'block');
    }

    public function test_user_can_view_template_list(): void
    {
        $user = User::factory()->create();
        Template::create(['user_id' => $user->id, 'name' => 'Mon Gabarit', 'type' => 'hour', 'currency' => 'EUR']);

        $response = $this->actingAs($user)->get(route('templates.index'));

        $response->assertOk();
        $response->assertSee('Mon Gabarit');
    }

    public function test_user_can_create_a_template(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('templates.store'), [
            'name' => 'E-commerce',
            'type' => 'fixed',
            'currency' => 'EUR',
        ]);

        $response->assertRedirect();

        $template = Template::where('user_id', $user->id)->where('name', 'E-commerce')->first();
        $this->assertNotNull($template);
        $this->assertSame('fixed', $template->type);
        $this->assertSame('EUR', $template->currency);

        $this->assertNotNull($template->headerPage);
        $this->assertNotNull($template->footerPage);
    }

    public function test_template_creation_requires_name(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('templates.store'), [
            'name' => '',
            'type' => 'hour',
            'currency' => 'EUR',
        ])->assertSessionHasErrors('name');
    }

    public function test_user_can_access_template_builder(): void
    {
        ['user' => $user, 'template' => $template] = $this->makeTemplateWithBlock();

        $this->actingAs($user)
            ->get(route('templates.builder', $template))
            ->assertOk()
            ->assertSee($template->name);
    }

    public function test_user_cannot_access_another_users_template_builder(): void
    {
        ['template' => $template] = $this->makeTemplateWithBlock();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->get(route('templates.builder', $template))
            ->assertForbidden();
    }

    public function test_user_can_delete_their_template(): void
    {
        ['user' => $user, 'template' => $template] = $this->makeTemplateWithBlock();

        $this->actingAs($user)
            ->delete(route('templates.destroy', $template))
            ->assertRedirect(route('templates.index'));

        $this->assertNull(Template::find($template->id));
    }

    public function test_user_cannot_delete_another_users_template(): void
    {
        ['template' => $template] = $this->makeTemplateWithBlock();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->delete(route('templates.destroy', $template))
            ->assertForbidden();

        $this->assertNotNull(Template::find($template->id));
    }

    public function test_user_can_duplicate_a_template(): void
    {
        ['user' => $user, 'template' => $template] = $this->makeTemplateWithBlock();

        $this->actingAs($user)
            ->post(route('templates.duplicate', $template))
            ->assertRedirect(route('templates.index'));

        $copies = Template::where('user_id', $user->id)->get();
        $this->assertCount(2, $copies);

        $copy = $copies->firstWhere('name', 'Landing Page (Copie)');
        $this->assertNotNull($copy);

        $this->assertCount($template->pages->count(), $copy->pages);

        $originalBlockCount = $template->pages->flatMap->blocks->count();
        $copyBlockCount = $copy->fresh()->pages->flatMap->blocks->count();
        $this->assertSame($originalBlockCount, $copyBlockCount);
    }

    public function test_create_estimation_from_template_copies_structure(): void
    {
        ['user' => $user, 'template' => $template] = $this->makeTemplateWithBlock();

        $this->actingAs($user)
            ->post(route('templates.create-estimation', $template))
            ->assertRedirect();

        $estimation = Estimation::where('user_id', $user->id)->first();
        $this->assertNotNull($estimation);
        $this->assertSame($template->type, $estimation->type);
        $this->assertSame($template->currency, $estimation->currency);

        $this->assertCount($template->pages->count(), $estimation->pages);

        $templateBlock = $template->pages->firstWhere('type', 'regular')->blocks->first();
        $estimationPage = $estimation->pages->firstWhere('type', 'regular');
        $estimationBlock = $estimationPage->blocks->first();

        $this->assertSame($templateBlock->block_id, $estimationBlock->id);
        $this->assertSame($templateBlock->price_programming, (float) $estimationBlock->pivot->price_programming);
        $this->assertSame($templateBlock->price_integration, (float) $estimationBlock->pivot->price_integration);
    }

    public function test_create_estimation_from_template_copies_addons(): void
    {
        ['user' => $user, 'template' => $template] = $this->makeTemplateWithBlock();

        $addon = Option::create(['name' => 'SEO', 'type' => 'fixed_price', 'value' => 500]);
        $template->addons()->attach($addon->id);

        $this->actingAs($user)->post(route('templates.create-estimation', $template));

        $estimation = Estimation::where('user_id', $user->id)->first();
        $this->assertNotNull($estimation);
        $this->assertTrue($estimation->addons->contains($addon->id));
    }

    public function test_save_estimation_as_template(): void
    {
        $user = User::factory()->create();
        $block = Block::create(['name' => 'Hero', 'user_id' => $user->id]);

        $estimation = Estimation::create([
            'user_id' => $user->id,
            'client_name' => 'Acme Corp',
            'type' => 'hour',
            'currency' => 'EUR',
            'translation_languages_count' => 1,
        ]);

        $page = $estimation->pages()->create(['name' => 'Accueil', 'type' => 'regular', 'order' => 1, 'quantity' => 1]);
        $page->blocks()->attach($block->id, [
            'order' => 1,
            'quantity' => 2,
            'price_programming' => 8.0,
            'price_integration' => 4.0,
            'price_field_creation' => 2.0,
            'price_content_management' => 1.0,
        ]);

        $this->actingAs($user)
            ->post(route('estimations.save-as-template', $estimation), ['name' => 'Mon Nouveau Gabarit'])
            ->assertRedirect(route('estimations.builder', $estimation))
            ->assertSessionHas('message');

        $template = Template::where('user_id', $user->id)->where('name', 'Mon Nouveau Gabarit')->first();
        $this->assertNotNull($template);
        $this->assertSame('hour', $template->type);

        $this->assertCount(1, $template->pages);
        $templatePage = $template->pages->first();
        $this->assertSame('Accueil', $templatePage->name);

        $this->assertCount(1, $templatePage->blocks);
        $templateBlock = $templatePage->blocks->first();
        $this->assertSame(8.0, $templateBlock->price_programming);
        $this->assertSame(2, $templateBlock->quantity);
    }

    public function test_save_as_template_requires_name(): void
    {
        $user = User::factory()->create();
        $estimation = Estimation::create([
            'user_id' => $user->id,
            'client_name' => 'Client',
            'type' => 'hour',
            'currency' => 'EUR',
            'translation_languages_count' => 1,
        ]);

        $this->actingAs($user)
            ->post(route('estimations.save-as-template', $estimation), ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_user_cannot_save_another_users_estimation_as_template(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $estimation = Estimation::create([
            'user_id' => $user->id,
            'client_name' => 'Client',
            'type' => 'hour',
            'currency' => 'EUR',
            'translation_languages_count' => 1,
        ]);

        $this->actingAs($otherUser)
            ->post(route('estimations.save-as-template', $estimation), ['name' => 'Gabarit volé'])
            ->assertForbidden();

        $this->assertDatabaseMissing('templates', ['name' => 'Gabarit volé']);
    }

    public function test_templates_are_isolated_by_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Template::create(['user_id' => $user1->id, 'name' => 'Gabarit User1', 'type' => 'hour', 'currency' => 'EUR']);
        Template::create(['user_id' => $user2->id, 'name' => 'Gabarit User2', 'type' => 'hour', 'currency' => 'EUR']);

        $this->actingAs($user1)
            ->get(route('templates.index'))
            ->assertOk()
            ->assertSee('Gabarit User1')
            ->assertDontSee('Gabarit User2');
    }

    public function test_estimation_create_page_shows_user_templates(): void
    {
        $user = User::factory()->create();
        Template::create(['user_id' => $user->id, 'name' => 'Mon Gabarit Visible', 'type' => 'hour', 'currency' => 'EUR']);

        $this->actingAs($user)
            ->get(route('estimations.create'))
            ->assertOk()
            ->assertSee('Mon Gabarit Visible');
    }

    public function test_estimation_create_page_does_not_show_other_users_templates(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Template::create(['user_id' => $otherUser->id, 'name' => 'Gabarit Privé', 'type' => 'hour', 'currency' => 'EUR']);

        $this->actingAs($user)
            ->get(route('estimations.create'))
            ->assertOk()
            ->assertDontSee('Gabarit Privé');
    }
}
