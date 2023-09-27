<?php

namespace App\Admin\InvitationManagement\Jobs;

use Domain\Invitation\Actions\CreateInvitationAction;
use Domain\Invitation\DataTransferObjects\InvitationDto;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\File\File;

class ImportInvitationsFromExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $filePath
    )
    {
        //
    }

    /**
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public function handle(CreateInvitationAction $createInvitationAction)
    {
        $rows = (new FastExcel())->import(storage_path('app/'.$this->filePath));

        $rows->each(function (array $row) use($createInvitationAction) {
            try {

                $dto = InvitationDto::validateAndCreate($this->prepareRow($row));

                $createInvitationAction->execute($dto);
            }
            catch (Exception $exception) {
                //todo handle exceptions to parse it again for the client
                Log::info($exception->getMessage().' job_direction:' . __DIR__);
            }
        });
    }

    protected function prepareRow(array &$row): array
    {
        return array_combine(
            array_map(function ($key) {
                return $this->trimRowKey($key);
            }, array_keys($row)),
            $row
        );
    }

    private function trimRowKey(string $key):string
    {
        $result =  str_replace(' ', '_', strtolower($key));

        if ($result === 'mobile_number') {
            $result = 'dirty_mobile_number';
        }

        return $result;
    }
}
