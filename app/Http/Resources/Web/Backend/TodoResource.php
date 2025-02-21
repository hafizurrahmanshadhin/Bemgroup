<?php

namespace App\Http\Resources\Web\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'                  => $this->id,
            'title'               => $this->title,
            'email'               => $this->email,
            'due_date'            => $this->due_date->format('Y-m-d H:i'),
            'description'         => $this->description,
            'reminder_email_sent' => $this->reminder_email_sent,
            'status'              => $this->status,
        ];
    }
}
