<?php
/**
 *  +-------------------------------------------------------------------------------------------
 *  | Coffin [ 花开不同赏，花落不同悲。欲问相思处，花开花落时。 ]
 *  +-------------------------------------------------------------------------------------------
 *  | This is not a free software, without any authorization is not allowed to use and spread.
 *  +-------------------------------------------------------------------------------------------
 *  | Copyright (c) 2006~2024 All rights reserved.
 *  +-------------------------------------------------------------------------------------------
 *  | @author: coffin's laughter | <chuanshuo_yongyuan@163.com>
 *  +-------------------------------------------------------------------------------------------
 */

namespace Modules\Recipe\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Media\Repositories\FileRepository;
use Modules\Recipe\Entities\Recipe;
use Modules\Recipe\Repositories\RecipeRepository;

class RecipeController extends AdminBaseController
{
    /**
     * @var FileRepository
     */
    private $file;
    /**
     * @var RecipeRepository
     */
    private $recipe;

    public function __construct(RecipeRepository $recipe, FileRepository $file)
    {
        parent::__construct();

        $this->recipe = $recipe;
        $this->file = $file;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('recipe::admin.recipes.create');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Recipe $recipe)
    {
        $this->recipe->destroy($recipe);

        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('recipe::recipes.title.recipes')]));

        return redirect()->route('admin.recipe.recipe.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Recipe $recipe)
    {
        $galleryFiles = $this->file->findMultipleFilesByZoneForEntity('gallery', $recipe);
        $featured_image = $this->file->findFileByZoneForEntity('featured_image', $recipe);

        return view('recipe::admin.recipes.edit', compact('recipe', 'galleryFiles', 'featured_image'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $recipes = $this->recipe->all();

        return view('recipe::admin.recipes.index', compact('recipes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->recipe->create($request->all());

        flash()->success(trans('core::core.messages.resource created', ['name' => trans('recipe::recipes.title.recipes')]));

        return redirect()->route('admin.recipe.recipe.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Recipe $recipe, Request $request)
    {
        $this->recipe->update($recipe, $request->all());

        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('recipe::recipes.title.recipes')]));

        return redirect()->route('admin.recipe.recipe.index');
    }
}
