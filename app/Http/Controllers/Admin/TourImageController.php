<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTourImageRequest;
use App\Models\Tour;
use App\Models\TourImage;
use App\Services\CloudinaryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TourImageController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Upload photos for a tour.
     */
    public function store(StoreTourImageRequest $request, Tour $tour): RedirectResponse
    {
        $files = $request->file('images');
        if (!is_array($files)) {
            $files = [$files];
        }

        $hasCover = $tour->images()->where('is_cover', true)->exists();
        $maxOrder = (int) $tour->images()->max('display_order');

        DB::transaction(function () use ($files, $tour, &$hasCover, &$maxOrder) {
            foreach ($files as $index => $file) {
                $uploadResult = $this->cloudinaryService->upload($file, 'tours');
                $isCover = !$hasCover;

                TourImage::create([
                    'tour_id' => $tour->tour_id,
                    'cloudinary_public_id' => $uploadResult['cloudinary_public_id'],
                    'secure_url' => $uploadResult['secure_url'],
                    'format' => $uploadResult['format'],
                    'width' => $uploadResult['width'],
                    'height' => $uploadResult['height'],
                    'bytes' => $uploadResult['bytes'],
                    'is_cover' => $isCover,
                    'display_order' => $maxOrder + $index + 1,
                ]);

                if ($isCover) {
                    $hasCover = true;
                }
            }
        });

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Tải lên hình ảnh Tour thành công.');
    }

    /**
     * Set selected image as cover photo.
     */
    public function setCover(Tour $tour, TourImage $image): RedirectResponse
    {
        if ($image->tour_id !== $tour->tour_id) {
            abort(404);
        }

        DB::transaction(function () use ($tour, $image) {
            $tour->images()->update(['is_cover' => false]);
            $image->update(['is_cover' => true]);
        });

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Đã đặt làm ảnh đại diện cho Tour.');
    }

    /**
     * Delete an image.
     */
    public function destroy(Tour $tour, TourImage $image): RedirectResponse
    {
        if ($image->tour_id !== $tour->tour_id) {
            abort(404);
        }

        $wasCover = $image->is_cover;
        $publicId = $image->cloudinary_public_id;

        DB::transaction(function () use ($image, $tour, $wasCover) {
            $image->delete();

            if ($wasCover) {
                $nextImage = $tour->images()->orderBy('display_order')->first();
                if ($nextImage) {
                    $nextImage->update(['is_cover' => true]);
                }
            }
        });

        $this->cloudinaryService->delete($publicId);

        return redirect()->route('admin.tours.show', $tour)
            ->with('success', 'Đã xóa hình ảnh khỏi Tour.');
    }
}
