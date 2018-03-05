<?php
/**
 * This file is part of the BEAR.Resource package.
 *
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace Sandbox\Resource\Resource\App;

use BEAR\Resource\Module\ResourceModule;
use BEAR\Resource\ResourceInterface;
use BEAR\Resource\ResourceObject;
use Ray\Di\Injector;

require dirname(__DIR__) . '/vendor/autoload.php';

class User extends ResourceObject
{
    protected $users = [
        ['name' => 'Athos', 'age' => 15, 'blog_id' => 0],
        ['name' => 'Aramis', 'age' => 16, 'blog_id' => 1],
        ['name' => 'Porthos', 'age' => 17, 'blog_id' => 2]
    ];

    public function onGet($id)
    {
        return $this->users[$id];
    }
}
/* @var $resource \BEAR\Resource\ResourceInterface */
$resource = (new Injector(new ResourceModule('Sandbox\Resource'), __DIR__ . '/tmp'))->getInstance(ResourceInterface::class);
/* @var $result ResourceObject */
$ro = $resource->get->uri('app://self/user')(['id' => 1]);
echo "code:{$ro->code}" . PHP_EOL;
echo 'headers:' . PHP_EOL;
print_r($ro->headers) . PHP_EOL;
echo 'body:' . PHP_EOL;
print_r($ro->body) . PHP_EOL;

//code:200
//headers:
//Array
//(
//)
//body:
//Array
//(
//    [name] => Aramis
//    [age] => 16
//    [blog_id] => 1
//)
