<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BasicCrudController;
use Symfony\Contracts\Service\Attribute\Required;

class CategoryController extends BasicCrudController
{
    private $rules =[
        'name' => 'required|max:255',
        'description' => 'nullable',
        'is_active' => 'boolean'

    ];

    protected function model()  {
        return Catergory::class;
    }
   
    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }
}
