<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'full_name' => $this->first_name . ' ' . $this->last_name,
            'age' => $this->age,
            'imageUrl' => $this->imageUrl,
            'role' => $this->role,
            'isVerify' => $this->isVerify ? 'true' : 'false',
        ];
    }
}