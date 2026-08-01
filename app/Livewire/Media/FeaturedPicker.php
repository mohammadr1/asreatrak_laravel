<?php

namespace App\Livewire\Media;

use App\Models\Media;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeaturedPicker extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'tailwind';

    /**
     * جستجو
     */
    public string $search = '';

    /**
     * فایل انتخاب شده
     */
    public ?int $selected = null;

    /**
     * آپلود فایل
     */
    public $upload;

    /**
     * فقط تصاویر
     */
    public string $type = 'image';

    /**
     * تعداد نمایش
     */
    public int $perPage = 24;

    protected $queryString = [];

    protected $rules = [
        'upload' => 'nullable|image|max:10240',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedUpload()
    {
        $this->validate();

        $this->uploadImage();

        $this->dispatch('mediaUploaded');
    }

    public function select(int $id): void
    {
        $this->selected = $id;
    }

    public function uploadImage(): void
    {
        if (!$this->upload) {
            return;
        }

        $file = $this->upload;

        $directory = now()->format('Y/m');

        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            "media/{$directory}",
            $filename,
            'public'
        );

        [$width, $height] = getimagesize($file->getRealPath());


        $media = Media::create([

            'uuid' => (string) Str::uuid(),

            'disk' => 'public',

            'directory' => "media/{$directory}",

            'filename' => $filename,

            'original_path' => $path,

            'extension' => $file->getClientOriginalExtension(),

            'mime_type' => $file->getMimeType(),

            'size' => $file->getSize(),

            'width' => $width,

            'height' => $height,

            'duration' => null,

            'type' => 'image',

            'title' => pathinfo(
                $file->getClientOriginalName(),
                PATHINFO_FILENAME
            ),

            'alt' => null,

            'caption' => null,

            'copyright' => null,

            'uploaded_by' => Auth::id(),

            'visibility' => 'public',

            'is_active' => true,

            'hash' => md5_file(
                Storage::disk('public')->path($path)
            ),

            'metadata' => json_encode([]),

            'has_watermark' => false,

            'watermark_type' => null,

            'watermark_id' => null,

        ]);

        $this->selected = $media->id;

        $this->upload = null;

        $this->resetPage();

        $this->dispatch('mediaUploaded');
    }
        public function delete(int $id): void
    {
        $media = Media::find($id);

        if (! $media) {
            return;
        }

        Storage::disk($media->disk)->delete(
            $media->original_path
        );

        $media->delete();

        if ($this->selected === $id) {
            $this->selected = null;
        }
    }

    public function getMediaProperty()
    {
        return Media::query()

            ->active()

            ->images()

            ->when($this->search, function ($query) {

                $query->where(function ($q) {

                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('filename', 'like', "%{$this->search}%");

                });

            })

            ->latest()

            ->paginate($this->perPage);
    }

    public function choose()
    {
        if (! $this->selected) {
            return;
        }

        $this->dispatch(
            'featured-image-selected',
            id: $this->selected
        );
    }

    public function render()
    {
        return view(
            'livewire.media.featured-picker',
            [
                'media' => $this->media,
            ]
        );
    }
}