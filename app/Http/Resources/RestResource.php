<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'status' => true,
            'message' => 'Operation On Wallet successfully registered',
            'wallet' => [
                'user_id' => $this->user_id,
                'current_balance' => $this->current_balance,
                'created_at' => $this->created_at,
            ]
        ];
    }
}
