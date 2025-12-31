<?php

namespace Modules\DynamicForm\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Controllers\BaseController;
use Modules\DynamicForm\Imports\FormSubmissionsImport;
use Modules\DynamicForm\Models\Form;

class ImportController extends BaseController
{
    /**
     * @param Request $request
     * @param Form $form
     *
     * @return JsonResponse
     */
    public function import(Request $request, Form $form): JsonResponse
    {
        
        $request->validate([ 'file' => ['required','file','mimes:xlsx,csv'] ]);
        Excel::import(new FormSubmissionsImport($form), $request->file('file'));
        return $this->successResponse(
            message: $this->lang("import-completed"),
            responseCode: Response::HTTP_CREATED
        );
    }
}
