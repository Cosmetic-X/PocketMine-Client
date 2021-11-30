<?php
namespace cosmeticx\util\async;

use Closure;
use Exception;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;

class AsyncAgent {
    use SingletonTrait;

    public array $syncClosures = [];

    /**
     * @param Closure $function - function (array $data) {}
     * @param Closure|null $completeFunction - function (Server $server, mixed $result): void{}
     */
    static function submitAsyncTask(Closure $function, Closure $completeFunction = null, array $data = []): void{
        $id = uniqid();
        AsyncAgent::getInstance()->syncClosures[$id] = $completeFunction;
        Server::getInstance()->getAsyncPool()->submitTask(
            new class($function, $id, $data) extends AsyncTask {
                public function __construct(private Closure $function, private string $id, private array $data){
                }

                function onRun(){
                    $function = $this->function;
                    $this->setResult($function($this->data));
                }

                function onCompletion(Server $server){
                    try{
                        $completeFunction = AsyncAgent::getInstance()->syncClosures[$this->id] ?? null;
                        if($completeFunction === null) return;
                        $completeFunction($server, $this->getResult());
                    }catch(Exception $e){
                        $server->getLogger()->logException($e);
                    }
                }
            });
    }
}