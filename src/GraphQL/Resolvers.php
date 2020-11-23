<?php


namespace App\GraphQL;


class Resolvers
{
    public static function getResolver(): array
    {
        return [
            'Query' => [
                'getBooks' => function ($root, $args, $context) {
                    return [
                        ['id' => 1, 'title' => 'PHP 8']
                    ];
                },
                'getAuthors' => function ($root, $args, $context) {
                    return [
                        ['id' => 1, 'name' => 'Amir Etemad']
                    ];
                }
            ]
        ];
//        return [
//            'Query' => [
//                'getBooks' => function ($root, $args, $context) {
//                    return $context['db']->fetchAll("SELECT $args FROM book");
//                },
//                'getAuthors' => function ($root, $args, $context) {
//                    return $context['db']->fetchAll("SELECT * FROM author");
//                }
//            ]
//        ];
    }
}
