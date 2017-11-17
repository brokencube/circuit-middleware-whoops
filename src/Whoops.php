<?php

namespace CircuitMiddleware\Whoops;

use Circuit\Interfaces\Middleware;
use Circuit\Interfaces\Delegate;
use Symfony\Component\HttpFoundation\{Request,Response};

use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Whoops extends Middleware
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
        $method = Run::EXCEPTION_HANDLER;
        $whoops = $this->getWhoopsInstance($request);
        // Output is managed by the middleware pipeline
        $whoops->allowQuit(false);
        
        ob_start();
        $whoops->$method($error);
        $response = ob_get_clean();
        return new Response($response, 500);
    }
    
    private function getWhoopsInstance(Request $request)
    {
        $whoops = new Run();
        if (php_sapi_name() === 'cli') {
            $whoops->pushHandler(new PlainTextHandler);
            return $whoops;
        }
        
        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            $handler = new JsonResponseHandler;
            $handler->addTraceToOutput(true);
        } else {
            $handler = new PrettyPageHandler;
        }
        
        $whoops->pushHandler($handler);
        return $whoops;
    }
}