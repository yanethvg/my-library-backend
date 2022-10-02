<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BookResource extends JsonResource
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
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'genre' =>  new GenreResource($this->genre),
            'stock' => $this->stock,
            'year_published' => $this->year_published,	
            'created_at' =>  Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d-m-Y'),
            'updated_at' =>  Carbon::createFromFormat('Y-m-d H:i:s', $this->updated_at)->format('d-m-Y')
        ];
    }
}
