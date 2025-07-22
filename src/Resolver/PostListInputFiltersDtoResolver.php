<?php
/**
 * PostListInputFiltersDto resolver.
 */

namespace App\Resolver;

use App\Dto\PostListInputFiltersDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * PostListInputFiltersDtoResolver class.
 */
class PostListInputFiltersDtoResolver implements ValueResolverInterface
{
    /**
     * Returns the possible value(s).
     *
     * @param Request          $request  HTTP Request
     * @param ArgumentMetadata $argument Argument metadata
     *
     * @return iterable Iterable
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_a($argumentType, PostListInputFiltersDto::class, true)) {
            return [];
        }

        $categoryId = $request->query->get('categoryId');

        return [new PostListInputFiltersDto($categoryId)];
    }
}
