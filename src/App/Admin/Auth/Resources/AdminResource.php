<?php

namespace App\Admin\Auth\Resources;

use Domain\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property User $resource
 */
class AdminResource extends JsonResource
{
    public function toArray(Request $request)
    {
        return [
            'id' => (int) $this->resource->getKey(),
            'name' => (string) $this->resource->name,
            'email' => (string) $this->resource->email,
        ];
    }
}
