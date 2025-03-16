<?php

declare(strict_types=1);

namespace App\Pagination;

use JsonSerializable;

class PaginatedCollection implements JsonSerializable
{
    private iterable $data;
    private int $totalCount;
    private int $itemsPerPage;
    private int $currentPage;
    private int $totalPages;
    private array $links = [];

    public function __construct(iterable $data, int $totalCount, int $currentPage, int $totalPages)
    {
        $this->data = $data;
        $this->totalCount = $totalCount;
        $this->itemsPerPage = count($data);
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
    }

    public function addLink($rel, $url): void
    {
        $this->links[$rel] = $url;
    }

    public function jsonSerialize(): array
    {
        return [
            'data' => $this->data,
            'meta' => [
                'totalCount' => $this->totalCount,
                'itemsPerPage' => $this->itemsPerPage,
                'currentPage' => $this->currentPage,
                'totalPages' => $this->totalPages,
            ],
            'links' => $this->links,
        ];
    }
}
