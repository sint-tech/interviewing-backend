<?php

namespace App\Admin\InvitationManagement\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Queue\SerializesModels;
use Domain\Organization\Models\Employee;
use Illuminate\Queue\InteractsWithQueue;
use App\Exceptions\LimitExceededException;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OpenSpout\Common\Exception\IOException;
use Illuminate\Contracts\Auth\Authenticatable;
use Domain\Invitation\Actions\CreateInvitationAction;
use Domain\Invitation\DataTransferObjects\InvitationDto;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Reader\Exception\ReaderNotOpenedException;

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
     * @throws LimitExceededException
     */
    public function handle(CreateInvitationAction $createInvitationAction)
    {
        $rows = (new FastExcel())->import($this->filePath);

        $total = $rows->count();

        if ($this->creator instanceof Employee && ($total > $this->creator->organization->invitationsLeft())) {
            throw new LimitExceededException('You have exceeded your invitation limit');
        }

        $exceptions = [];

        $rows->each(function (array $row) use ($createInvitationAction, &$exceptions) {
            try {
                $dto = InvitationDto::validateAndCreate($this->prepareRow($row));

                $createInvitationAction->execute($dto);
            } catch (Exception $exception) {
                $exceptions[] = $exception->getMessage();
                //todo handle exceptions to parse it again for the client
                Log::info($exception->getMessage() . ' job_direction:' . __DIR__);
            }
        });

        return [
            'total' => $total,
            'errors' => count($exceptions),
            'sent' => $total - count($exceptions),
            'errors_messages' => array_unique($exceptions),
            'status_code' => count($exceptions) > 0 ? 400 : 200,
        ];
    }

    protected function prepareRow(array &$row): array
    {
        $data = array_combine(
            array_map(function ($key) {
                return $this->trimRowKey($key);
            }, array_keys($row)),
            $row
        );

        if (isset($data['mobile_country_code']) && ((string) $data['mobile_country_code'])[0] !== '+') {
            $data['mobile_country_code'] = '+' . $data['mobile_country_code'];
        }

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
