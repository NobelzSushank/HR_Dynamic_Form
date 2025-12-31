<?php

namespace Modules\DynamicForm\Http\Controllers;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Core\Http\Controllers\BaseController;
use Modules\DynamicForm\Exports\FormSubmissionsExport;
use Modules\DynamicForm\Models\Form;

class ExportController extends BaseController
{
    public function export(Form $form)
    {
        $filename = Str::slug($form->title) . now()->format('Ymd_His') . ".xlsx";
        return Excel::download(new FormSubmissionsExport($form), $filename);
    }
}
