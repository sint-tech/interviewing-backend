<?php

namespace App\Organization\InvitationManagement\Jobs;

use Domain\Invitation\Actions\CreateInvitationAction;
use Domain\Invitation\DataTransferObjects\InvitationDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;
use Rap2hpoutre\FastExcel\FastExcel;

class ImportInvitationsFromExcelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Authenticatable $creator;

    public function __construct(
        protected string $filePath,
        protected int $vacancy_id,
        protected ?int $interview_template_id,
        protected \DateTime $should_be_invited_at,
    ) {
        $this->creator = auth()->user();
    }

    /**
     * @throws IOException
     * @throws UnsupportedTypeException
     * @throws ReaderNotOpenedException
     */
    public function handle(CreateInvitationAction $createInvitationAction)
    {
        $rows = (new FastExcel())->import($this->filePath);

        $rows->each(function (array $row) use ($createInvitationAction) {
            try {
                $dto = InvitationDto::validateAndCreate($this->prepareRow($row));

                $createInvitationAction->execute($dto);
            } catch (\Exception $exception) {
                dd($exception);
                //todo handle exceptions to parse it again for the client
                Log::info($exception->getMessage().' job_direction:'.__DIR__);
            }
        });
    }

    protected function prepareRow(array &$row): array
    {
        $data = array_combine(
            array_map(function ($key) {
                return $this->trimRowKey($key);
            }, array_keys($row)),
            $row
        );

        $data['vacancy_id'] = $this->vacancy_id;

        $data['interview_template_id'] = $this->interview_template_id;

        $data['should_be_invited_at'] = $this->should_be_invited_at->format('Y-m-d H:i');

        $data['creator'] = $this->creator;

        return $data;
    }

    private function trimRowKey(string $key): string
    {
        $result = str_replace(' ', '_', strtolower($key));

        if ($result === 'mobile_number') {
            $result = 'dirty_mobile_number';
        }

        return $result;
    }
}
