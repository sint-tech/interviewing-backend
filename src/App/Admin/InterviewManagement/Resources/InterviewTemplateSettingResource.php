<?php

namespace App\Admin\InterviewManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class InterviewTemplateSettingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'max_reconnection_tries' => (int) data_get($this,'max_reconnection_tries'),
            'started_at'    => Carbon::make(data_get($this,'started_at',null))?->format('Y-m-d H:m'),
            'ended_at'    => Carbon::make(data_get($this,'ended_at',null))?->format('Y-m-d H:m'),
        ];
    }
}
