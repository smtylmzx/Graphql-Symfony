<?php


namespace App\Controller;


use App\GraphQL\Resolvers;
use GraphQL\Executor\Executor;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Utils\BuildSchema;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GraphqlController extends AbstractController
{
    /**
     * @Route("/graphql", name="test")
     * @param Request $request
     * @return JsonResponse
     */
    public function graphqlTest(Request $request): JsonResponse
    {
        $this->setResolvers();

        $appPath = $this->getParameter('kernel.project_dir');

        $schemaContent = file_get_contents($appPath . '/src/GraphQL/schema.graphqls');
        $schema = BuildSchema::build($schemaContent);

        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);

        $query = $input['query'];
        $variableValues = $input['variables'] ?? null;

        $result = GraphQL::executeQuery($schema, $query, null, null, $variableValues);
        $output = $result->toArray();

        return new JsonResponse([
            'post' => 'test',
            'user' => $output,
            'success' => true
        ]);
    }

    public function setResolvers(): void
    {
        $resolvers = Resolvers::getResolver();

        Executor::setDefaultFieldResolver(function ($source, $args, $context, ResolveInfo $info) use ($resolvers) {
            $fieldName = $info->fieldName;

            if (is_null($fieldName)) {
                throw new \RuntimeException('Could not get $fieldName from ResolveInfo');
            }

            if (is_null($info->parentType)) {
                throw new \RuntimeException('Could not get $parentType from ResolveInfo');
            }

            $parentTypeName = $info->parentType->name;

            if (isset($resolvers[$parentTypeName])) {
                $resolver = $resolvers[$parentTypeName];

                if (is_array($resolver) && array_key_exists($fieldName, $resolver)) {
                    $value = $resolver[$fieldName];

                    return is_callable($value) ? $value($source, $args, $context, $info) : $value;
                }

                if (is_object($resolver) && isset($resolver->{$fieldName})) {
                    $value = $resolver->{$fieldName};

                    return is_callable($value) ? $value($source, $args, $context, $info) : $value;
                }
            }

            return Executor::defaultFieldResolver($source, $args, $context, $info);
        });
    }
}
