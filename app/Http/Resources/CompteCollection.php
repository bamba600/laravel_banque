<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompteCollection extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = CompteResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => true,
            'data' => $this->collection,
            'pagination' => [
                'currentPage' => $this->resource->currentPage(),
                'totalPages' => $this->resource->lastPage(),
                'totalItems' => $this->resource->total(),
                'itemsPerPage' => $this->resource->perPage(),
                'hasNext' => $this->resource->hasMorePages(),
                'hasPrevious' => $this->resource->currentPage() > 1
            ],
            'links' => [
                'self' => $request->url() . '?' . $request->getQueryString(),
                'next' => $this->resource->nextPageUrl(),
                'first' => $request->url() . '?' . preg_replace('/[?&]page=\d+/', '', $request->getQueryString()) . '&page=1',
                'last' => $request->url() . '?' . preg_replace('/[?&]page=\d+/', '&page=' . $this->resource->lastPage(), $request->getQueryString())
            ]
        ];
    }
}