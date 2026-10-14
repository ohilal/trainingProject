<?php

namespace Tests\Feature\TDD;

use App\Http\Requests\FileRequest;
use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\BaseTest;

class FileManagerTest extends BaseTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->setBaseRoute('file');
        $this->setBaseModel('App\Models\File');
       
    }



    /**
     * A basic test to check access level controller with without access to page
     *
     * @return void
     */
    public function test_acl()
    {
        $this->withOutPermissionUser();
        $this->withOutAccessLevel();
    }


    /**
     * A basic test to validation is worked.
     *
     * @return void
     */
    public function test_validation()
    {
        $this->setValidationRules((new FileRequest())->rules());
        $this->signIn();
        $this->validation();
    }



    /**
     * A basic test to create form is worked correctly.
     *
     * @return void
     */
    public function test_create_form()
    {
        Storage::disk('public')->put('avatar.txt', '');
        
        $this->signIn();
        $this->create();
    }

    /**
     * A basic test to update method with authenticated verfied.
     *
     * @return void
     */
    public function test_update_form()
    {
        Storage::disk('public')->put('avatar.txt', '');
        $this->signIn();
        $this->update();
    }

    public function test_update_changes_title_and_description_with_replacement_file()
    {
        Storage::fake('public');
        Storage::disk('public')->put('files/original.txt', 'original');
        Storage::disk('public')->put('files/replacement.txt', 'replacement');

        $file = File::create([
            'title' => 'Original title',
            'description' => 'Original description',
            'file' => 'files/original.txt',
            'file_size' => 8,
            'file_type' => 'txt',
        ]);

        $this->signIn();

        $this->patch(route('file.update', $file->id), [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'file' => 'files/replacement.txt',
        ])->assertRedirect(route('file.index'));

        $this->assertDatabaseHas('files', [
            'id' => $file->id,
            'title' => 'Updated title',
            'description' => 'Updated description',
            'file' => 'files/replacement.txt',
        ]);
        Storage::disk('public')->assertMissing('files/original.txt');
        Storage::disk('public')->assertExists('files/replacement.txt');
    }



    /**
     * A basic test to delete method and response correctly.
     *
     * @return void
     */
    public function test_delete_form()
    {
        $this->signIn();
        $this->destroy();
    }
}
