<?php
declare (strict_types = 1);

use PHPUnit\Framework\TestCase;

include '/docker/home/db/db.php';
include '/docker/home/functions.php';

final class BasicTest extends TestCase
{
    public function testInsert(): void
    {
        db()->query("TRUNCATE users");
        db()->insert('users', ['name' => 'Person', 'age' => 30, 'address' => 'seoul']);
        $rows = db()->rows('users');
        $this->assertTrue(count($rows) === 1);
        $rows = db()->rows('users', ['name' => 'Person']);
        $this->assertTrue(count($rows) === 1);
        db()->insert('users', ['name' => 'Person', 'age' => 30, 'address' => 'seoul']);
        $rows = db()->rows('users');
        $this->assertTrue(count($rows) === 2);
        $this->assertTrue(db()->count('users') === 2);
    }

    public function testUpdate(): void
    {
        db()->query("TRUNCATE users");
        db()->insert('users', ['name' => 'You', 'age' => 30, 'address' => 'seoul']);
        db()->insert('users', ['name' => 'Me', 'age' => 31, 'address' => 'seoul']);

        $this->assertTrue(db()->count('users') == 2);

        db()->update('users', ['name' => 'Jeo'], ['age' => 30]);

        $this->assertTrue(db()->count('users', ['name' => 'You']) == 0);
        $this->assertTrue(db()->count('users', ['name' => 'Jeo']) == 1);

    }

}