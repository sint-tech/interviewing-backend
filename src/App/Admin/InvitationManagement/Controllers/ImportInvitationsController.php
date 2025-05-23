<?php

namespace App\Admin\InvitationManagement\Controllers;

use App\Admin\InvitationManagement\Jobs\ImportInvitationsFromExcelJob;
use App\Admin\InvitationManagement\Requests\ImportInvitationRequest;
use Illuminate\Http\JsonResponse;
use Support\Controllers\Controller;

class ImportInvitationsController extends Controller
{
    public function __invoke(ImportInvitationRequest $request): JsonResponse
    {
        $file = $request->file('file');

        $file_name = time().'_'.$file->getClientOriginalName();

        $file_path = $request->file('file')->store('public/imported-excels/invitations/'.$file_name);

        $response = ImportInvitationsFromExcelJob::dispatchSync(
            storage_path('app/'.$file_path),
            $request->validated('vacancy_id'),
            $request->validated('interview_template_id'),
            $request->date('should_be_invited_at'),
        );

    return response()->json($response, $response['status_code'] ?? 200);
    }
}
