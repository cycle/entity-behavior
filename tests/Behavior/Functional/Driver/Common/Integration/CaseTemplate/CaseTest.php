<?php

declare(strict_types=1);

namespace Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Integration\CaseTemplate;

use Cycle\ORM\Entity\Behavior\Tests\Traits\TableTrait;
use Cycle\ORM\Select;
use Cycle\ORM\Entity\Behavior\Tests\Functional\Driver\Common\Integration\BaseTest;

abstract class CaseTest extends BaseTest
{
    use TableTrait;

    public function testPreparedData(): void
    {
        // Get entity
        $user = (new Select($this->orm, Entity\User::class))
            ->load('posts')
            ->wherePK(2)
            ->fetchOne();
        \assert($user instanceof Entity\User);

        // Check results
        self::assertNotNull($user->createdAt);
        self::assertNotNull($user->updatedAt);
        self::assertSame(0, $user->createdAt <=> $user->updatedAt);
    }

    public function testCreatedAt(): void
    {
        // Get entity
        $user = $this->orm->make(Entity\User::class, ['login' => 'new-user', 'passwordHash' => '123456789']);

        self::assertFalse(isset($user->createdAt));
        $this->save($user);

        self::assertNotNull($user->createdAt);

        // Reload entity
        $id = $user->id;
        $this->cleanHeap();
        $user = (new Select($this->orm, Entity\User::class))
            ->wherePK($id)
            ->fetchOne();
        self::assertNotNull($user->createdAt);
    }

    public function testUpdatedAt(): void
    {
        // Get entity
        $user = (new Select($this->orm, Entity\User::class))
            ->wherePK(2)
            ->fetchOne();
        \assert($user instanceof Entity\User);
        $updatedAt = $user->updatedAt;

        // Change data
        $user->passwordHash = 'new-password-hash';
        $this->save($user);

        // Check results
        self::assertSame(1, $user->updatedAt <=> $updatedAt);
    }

    protected function setUp(): void
    {
        // Init DB
        parent::setUp();
        $this->prepareOrm(__DIR__ . '/Entity');
        $this->save(...$this->fillData());
        $this->orm->getHeap()->clean();
    }

    private function fillData(): iterable
    {
        /**
         * @var callable(class-string, array): object $c
         */
        $c = \Closure::fromCallable([$this->orm, 'make']);

        // Users
        $u1 = $c(Entity\User::class, ['login' => 'user-1', 'passwordHash' => '123456789']);
        $u2 = $c(Entity\User::class, ['login' => 'user-2', 'passwordHash' => '852741963']);
        $u3 = $c(Entity\User::class, ['login' => 'user-3', 'passwordHash' => '321654987']);
        $u4 = $c(Entity\User::class, ['login' => 'user-4', 'passwordHash' => '321456987']);

        // Posts
        $p1 = $c(Entity\Post::class, ['slug' => 'slug-string-1', 'title' => 'Title 1', 'public' => true, 'content' => 'Foo-bar-baz content 1']);
        $p2 = $c(Entity\Post::class, ['slug' => 'slug-string-2', 'title' => 'Title 2', 'public' => true, 'content' => 'Foo-bar-baz content 2']);
        $p3 = $c(Entity\Post::class, ['slug' => 'slug-string-3', 'title' => 'Title 3', 'public' => false, 'content' => 'Foo-bar-baz content 3']);
        $p4 = $c(Entity\Post::class, ['slug' => 'slug-string-4', 'title' => 'Title 4', 'public' => true, 'content' => 'Foo-bar-baz content 4']);
        $p5 = $c(Entity\Post::class, ['slug' => 'slug-string-5', 'title' => 'Title 5', 'public' => true, 'content' => 'Foo-bar-baz content 5']);
        $p6 = $c(Entity\Post::class, ['slug' => 'slug-string-6', 'title' => 'Title 6', 'public' => true, 'content' => 'Foo-bar-baz content 6']);

        // Link posts with users
        $u1->posts = [$p1];
        $u2->posts = [$p2, $p3];
        $u3->posts = [$p4, $p5, $p6];

        // Comments
        $c1 = $c(Entity\Comment::class, ['post' => $p1, 'user' => $u2, 'content' => 'Foo-bar-baz comment 1', 'public' => true]);
        $c2 = $c(Entity\Comment::class, ['post' => $p1, 'user' => $u1, 'content' => 'Reply to comment 1', 'public' => true]);
        $c3 = $c(Entity\Comment::class, ['post' => $p2, 'user' => $u1, 'content' => 'Foo-bar-baz comment 2', 'public' => true]);
        $c4 = $c(Entity\Comment::class, ['post' => $p2, 'user' => $u2, 'content' => 'Reply to comment 2', 'public' => true]);
        $c5 = $c(Entity\Comment::class, ['post' => $p2, 'user' => $u3, 'content' => 'Foo-bar-baz comment 3', 'public' => true]);
        $c6 = $c(Entity\Comment::class, ['post' => $p2, 'user' => $u4, 'content' => 'Hidden comment', 'public' => false]);
        $c7 = $c(Entity\Comment::class, ['post' => $p2, 'user' => $u4, 'content' => 'Yet another comment', 'public' => true]);

        // Tags
        $t1 = $c(Entity\Tag::class, ['label' => 'tag-1']);
        $t2 = $c(Entity\Tag::class, ['label' => 'tag-2']);
        $t3 = $c(Entity\Tag::class, ['label' => 'tag-3']);
        $t4 = $c(Entity\Tag::class, ['label' => 'tag-4']);
        $t5 = $c(Entity\Tag::class, ['label' => 'tag-5']);

        // Link tags with posts
        $t1->posts = [$p1, $p2];
        $t2->posts = [$p2, $p3];
        $t3->posts = [$p3, $p4, $p5];
        $t4->posts = [$p4, $p5];
        $t5->posts = [$p5, $p6, $p1];

        // yield all the entities
        yield from [$u1, $u2, $u3, $u4];
        yield from [$p1, $p2, $p3, $p4, $p5, $p6];
        yield from [$c1, $c2, $c3, $c4, $c5, $c6, $c7];
        yield from [$t1, $t2, $t3, $t4, $t5];
    }
}
