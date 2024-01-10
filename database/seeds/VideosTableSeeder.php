<?php

use App\Models\Genero;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VideosTableSeeder extends Seeder
{
    private $allGeneros;
    private $relations = [
        'generos_id' => [],
        'categories_id' => []
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $dir = Storage::getDriver()->getAdapter()->getPathPrefix();
        \File::deleteDirectory($dir, true);
        
        $self = $this;
        $this->allGeneros = Genero::all();
        Model::reguard(); //mass assignment
        factory(\App\Models\Video::class, 100)
            ->make()
            ->each(function (Video $video) use ($self) {
                $self->fetchRelations();
                \App\Models\Video::create(
                    array_merge(
                        $video->toArray(), //thumb_file, banner_file
                        [
                            'thumb_file' => $self->getImageFile(),
                            'banner_file' => $self->getImageFile(),
                            'trailer_file' => $self->getVideoFile(),
                            'video_file' => $self->getVideoFile(),
                        ],
                        $this->relations
                    )
                );
            });
        Model::unguard();
    }

    public function fetchRelations()
    {
        $subGeneros = $this->allGeneros->random(5)->load('categories');
        $categoriesId = [];
        foreach ($subGeneros as $genero) {
            array_push($categoriesId, ...$genero->categories->pluck('id')->toArray());
        }
        $categoriesId = array_unique($categoriesId);
        $generosId = $subGeneros->pluck('id')->toArray();
        $this->relations['categories_id'] = $categoriesId;
        $this->relations['generos_id'] = $generosId;
    }

    public function getImageFile()
    {
        return new UploadedFile(
            storage_path('faker/thumbs/Laravel Framework.png'),
            'Laravel Framework.png'
        );
    }

    public function getVideoFile()
    {
        return new UploadedFile(
            storage_path('faker/videos/01-Como vai funcionar os uploads.mp4'),
            '01-Como vai funcionar os uploads.mp4'
        );
    }
}
