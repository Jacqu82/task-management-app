<?php

declare(strict_types=1);

namespace App\Pagination;

use Doctrine\ORM\Query;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

class PaginationFactory
{
    private const int PAGINATION_LIMIT = 5;

    private RouterInterface $router;
    private RequestStack $request;

    public function __construct(RouterInterface $router, RequestStack $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function createCollection(Query $collection, string $route, array $routeParams = []): PaginatedCollection
    {
        /**
 * @var Request $request 
*/
        $request = $this->request->getCurrentRequest();
        $page = (int) $request->query->get('page', 1);
        $pagerfanta = new Pagerfanta(new QueryAdapter($collection));
        $pagerfanta->setMaxPerPage(self::PAGINATION_LIMIT);

        try {
            $pagerfanta->setCurrentPage($page);
        } catch (OutOfRangeCurrentPageException $exception) {
            throw new BadRequestException($exception->getMessage());
        }

        $paginatedCollection = new PaginatedCollection(
            $pagerfanta->getCurrentPageResults(),
            $pagerfanta->getNbResults(),
            $pagerfanta->getCurrentPage(),
            $pagerfanta->getNbPages()
        );

        $routeParams = array_merge($routeParams, $request->query->all());
        $createLinkUrl = function ($targetPage) use ($route, $routeParams) {
            return $this->router->generate(
                $route,
                array_merge($routeParams, ['page' => $targetPage])
            );
        };

        $paginatedCollection->addLink('self', $createLinkUrl($page));
        $paginatedCollection->addLink('first', $createLinkUrl(1));
        $paginatedCollection->addLink('last', $createLinkUrl($pagerfanta->getNbPages()));

        if ($pagerfanta->hasNextPage()) {
            $paginatedCollection->addLink('next', $createLinkUrl($pagerfanta->getNextPage()));
        }

        if ($pagerfanta->hasPreviousPage()) {
            $paginatedCollection->addLink('prev', $createLinkUrl($pagerfanta->getPreviousPage()));
        }

        return $paginatedCollection;
    }
}
