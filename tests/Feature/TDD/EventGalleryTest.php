<?php

namespace Tests\Feature\TDD;

use App\Models\Event;
use App\Models\EventImage;
use App\Models\EventType;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\BaseTest;

class EventGalleryTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_gallery_images_uploaded_while_editing_are_saved_and_shown_on_event_page()
    {
        Storage::fake('public');
        $eventType = EventType::create(['eventName' => 'Celebration']);
        $event = Event::create([
            'title' => 'Gallery event',
            'description' => 'Event description',
            'start_date' => '2026-10-14',
            'event_type_id' => $eventType->id,
        ]);

        $this->signIn();

        $this->patch(route('event.update', $event->id), [
            'title' => $event->title,
            'description' => $event->description,
            'start_date' => $event->start_date,
            'event_type_id' => $eventType->id,
            'gallery' => [
                UploadedFile::fake()->image('gallery.jpg'),
            ],
        ])->assertRedirect(route('admin.event.index'));

        $image = EventImage::where('event_id', $event->id)->firstOrFail();
        Storage::disk('public')->assertExists('files/events/' . $image->image);

        $this->get('/front/events/' . $event->id)
            ->assertOk()
            ->assertSee('storage/files/events/' . $image->image, false);
    }
}
