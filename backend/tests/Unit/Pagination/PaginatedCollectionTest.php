<?php

declare(strict_types=1);

namespace App\Tests\Pagination;

use App\Pagination\PaginatedCollection;
use PHPUnit\Framework\TestCase;

class PaginatedCollectionTest extends TestCase
{
    public function testJsonSerializeReturnsCorrectStructure(): void
    {
        $data = [
            ['id' => 1, 'title' => 'Task 1'],
            ['id' => 2, 'title' => 'Task 2'],
        ];
        $totalCount = 10;
        $currentPage = 1;
        $totalPages = 5;

        $collection = new PaginatedCollection($data, $totalCount, $currentPage, $totalPages);
        $collection->addLink('self', '/api/tasks?page=1');
        $collection->addLink('next', '/api/tasks?page=2');

        $expected = [
            'data' => $data,
            'meta' => [
                'totalCount' => 10,
                'itemsPerPage' => 2,
                'currentPage' => 1,
                'totalPages' => 5,
            ],
            'links' => [
                'self' => '/api/tasks?page=1',
                'next' => '/api/tasks?page=2',
            ],
        ];

        $this->assertSame($expected, $collection->jsonSerialize());
    }

    public function testEmptyCollection(): void
    {
        $collection = new PaginatedCollection([], 0, 1, 1);

        $expected = [
            'data' => [],
            'meta' => [
                'totalCount' => 0,
                'itemsPerPage' => 0,
                'currentPage' => 1,
                'totalPages' => 1,
            ],
            'links' => [],
        ];

        $this->assertSame($expected, $collection->jsonSerialize());
    }

    public function testItemsPerPageWhenCollectionHasElements(): void
    {
        $data = [['id' => 1], ['id' => 2], ['id' => 3]];
        $collection = new PaginatedCollection($data, 10, 1, 4);

        $this->assertSame(3, $collection->jsonSerialize()['meta']['itemsPerPage']);
    }

    public function testAddSingleLink(): void
    {
        $collection = new PaginatedCollection([], 0, 1, 1);
        $collection->addLink('self', '/api/tasks?page=1');

        $this->assertSame(['/api/tasks?page=1'], array_values($collection->jsonSerialize()['links']));
    }
}
