<?php

namespace CircuitMiddleware\Whoops;

use Circuit\Interfaces\Middleware;
use Circuit\Interfaces\Delegate;
use Symfony\Component\HttpFoundation\{Request,Response};

use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Whoops implements Middleware
{
    public function process(Request $request, Delegate $delegate) : Response
    {
        try {
            return $delegate->process($request);
        } catch (\Exception $e) {
            return $this->handle($e, $request);
        }    
    }
    
    public function handle($error, Request $request) : Response
    {
        $whoops = new Run();
        
        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            $handler = new JsonResponseHandler;
            $handler->addTraceToOutput(true);
        } else {
            $handler = new PrettyPageHandler;
        }
        
        $whoops->pushHandler($handler);
        $whoops->allowQuit(false);
        
        ob_start();
        $whoops->{Run::EXCEPTION_HANDLER}($error);
        $response = ob_get_clean();
        return new Response($response, 500);
    }
}