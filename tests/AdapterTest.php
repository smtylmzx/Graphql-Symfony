<?php


namespace App\Tests;


use App\Service\Adapter\EBookAdapter;
use App\Service\Adapter\Kindle;
use App\Service\Adapter\PaperBook;
use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    public function testCanTurnPageOnBook()
    {
        $book = new PaperBook();
        $book->open();
        $book->turnPage();

        self::assertSame(2, $book->getPage());
    }

    public function testCanTurnPageOnKindleLikeInANormalBook()
    {
        $kindle = new Kindle();
        $book = new EBookAdapter($kindle);
        $book->open();
        $book->turnPage();

        self::assertSame(2, $book->getPage());
    }
}
