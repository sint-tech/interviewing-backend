<?php

namespace App\Admin\InvitationManagement\Controllers;

use App\Admin\InvitationManagement\Jobs\ImportInvitationsFromExcelJob;
use Domain\Invitation\Actions\CreateInvitationAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Support\Controllers\Controller;

class ImportInvitationsController extends Controller
{
    public function __invoke(Request $request,CreateInvitationAction $createInvitationAction): JsonResponse
    {
        $request->validate([
            'file'  => ['required',File::types([
                'application/csv','application/excel',
                'application/vnd.ms-excel', 'application/vnd.msexcel',
                'text/csv', 'text/anytext', 'text/plain', 'text/x-c',
                'text/comma-separated-values',
                'inode/x-empty', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
            )]
        ]);

        $file = $request->file('file');

        $file_name = time() . '_' . $file->getClientOriginalName();

        $file_path  = $request->file('file')->store('public/imported-excels/invitations/'. $file_name);

        dispatch(new ImportInvitationsFromExcelJob($file_path));

        return message_response('file uploaded, we will send you notification once importing finished!');
    }
}
