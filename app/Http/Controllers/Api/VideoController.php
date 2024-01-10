<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Rules\GenerosHasCategoriesRule;
use Illuminate\Http\Request;


/*
 * Auto commit - Padrão de bancos de dados relacionais
 * Modo transação
 *
 * - begin transaction - Marca inicio da transação
 * - transactions - executa todas as transações pertinentes
 * - commit - persiste as transações no banco
 * - rollback - desfaz todas as transações do checkpoint
 *
 */

class VideoController extends BasicCrudController
{

    private $rules;

    public function __construct()
    {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'generos_id' => [
                'required',
                'array',
                'exists:generos,id,deleted_at,NULL',
            ],
            'thumb_file' => 'image|max:' . Video::THUMB_FILE_MAX_SIZE, //5MB
            'banner_file' => 'image|max:' . Video::BANNER_FILE_MAX_SIZE, //10MB
            'trailer_file' => 'mimetypes:video/mp4|max:' . Video::TRAILER_FILE_MAX_SIZE, //1GB
            'video_file' => 'mimetypes:video/mp4|max:' . Video::VIDEO_FILE_MAX_SIZE, //50GB
        ];
    }

    public function store(Request $request)
    {
        // $this->addRuleIfGeneroHasCategories($request);
        $validatedData = $this->validate($request, $this->rulesStore());
        /** @var Video $obj */
        $obj = \DB::transaction(function () use($request, $validatedData) {
            $obj = $this->model()::create($validatedData);
            $obj->categories()->sync(get('categories_id'));
            $obj->genres()->sync(get('genres_id'));  
            throw new Exception();
                
            return $obj;
        });
     
        $obj->refresh();
        return $obj;

    
    }

    public function update(Request $request, $id)
    {
        $obj = $this->findOrFail($id);
        $this->addRuleIfGeneroHasCategories($request);
        $validatedData = $this->validate($request, $this->rulesUpdate());
        $obj->update($validatedData);
        $resource = $this->resource();
        return new $resource($obj);
    }

    protected function addRuleIfGeneroHasCategories(Request $request)
    {
        $categoriesId = $request->get('categories_id');
        $categoriesId = is_array($categoriesId) ? $categoriesId : [];
        $this->rules['generos_id'][] = new GenerosHasCategoriesRule(
            $categoriesId
        );
    }


    protected function model()
    {
        return Video::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resource()
    {
        return VideoResource::class;
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }
}
