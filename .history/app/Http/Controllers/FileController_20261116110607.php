<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileRequest;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $this->authorize('file.index');
        $files = File::paginate();
        return view("contents.admin.toolbox.file.index", compact("files"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $this->authorize('file.create');
        return view('contents.admin.toolbox.file.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  FileRequest $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(FileRequest $request)
    {

        $this->authorize('file.create');
        $fileData = $this->getFileData($request->input('file'));
        if ($fileData === null) {
            throw ValidationException::withMessages([
                'file' => __('The selected file does not exist.'),
            ]);
        }

        File::create(array_merge($request->only(['title', 'description']), $fileData));
        return redirect()
            ->route("file.index")
            ->with('success', __('file created successfully'));
    }


    /**
     * Get metadata for a file stored on the public disk.
     *
     * @param  string $filePath
     * @return array|null
     */
    private function getFileData(string $filePath): ?array
    {
        $disk = Storage::disk('public');
        if (!$disk->exists($filePath)) {
            return null;
        }

        return [
            'file' => $filePath,
            'file_size' => $disk->size($filePath),
            'file_type' => pathinfo($disk->path($filePath), PATHINFO_EXTENSION),
        ];
    }

    public function preview(string $path)
    {
        $user = auth()->user();
        abort_unless($user->can('file.create') || $user->can('file.edit'), 403);

        $disk = Storage::disk('public');
        abort_unless($disk->exists($path), 404);

        return $disk->response($path);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  File  $file
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit(File $file)
    {
        $this->authorize('file.edit');
        return view('contents.admin.toolbox.file.form', compact(
            "file"
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  FileRequest $request
     * @param  File  $file
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(FileRequest $request, File $file)
    {
        $this->authorize('file.edit');
        $data = $request->only(['title', 'description']);
        $replacementFileData = $this->getFileData($request->input('file'));
        if ($replacementFileData === null) {
            throw ValidationException::withMessages([
                'file' => __('The selected file does not exist.'),
            ]);
        }
        $data = array_merge($data, $replacementFileData);

        $oldFilePath = $file->file;
        $file->update($data);

        if (isset($replacementFileData['file']) && $replacementFileData['file'] !== $oldFilePath) {
            Storage::disk('public')->delete($oldFilePath);
        }

        return redirect()
            ->route("file.index")
            ->with('warning', __('item updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  File  $file
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function destroy(File $file)
    {
        $this->authorize('file.delete');
        Storage::disk('public')->delete($file->file);
        $file->delete();
        return redirect()
            ->route("file.index")
            ->with('danger', __('item deleted successfully'));
    }
}
