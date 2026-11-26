<?php

namespace App\Resources\EmployeeEarnings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeEarningResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'income' => (float) $this->income,
            'total_income' => (float) $this->total_income,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
