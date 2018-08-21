<?php
namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use \App\Response\Response;

class VerifyJWTToken
{
    private $messages;
    private $response;

    public function __construct() 
    {
        $this->messages = \Config::get('messages');
        $this->response = new Response();
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (isset($_SERVER['HTTP_TOKEN']))
        {
            $token = $_SERVER['HTTP_TOKEN'];
        }
        else 
        {
            $token = $request->input('token');
        }

        try
        {
            $user = JWTAuth::toUser($token);
        }
        catch (JWTException $e)
        {
            if($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) 
            {
                return response()->json($this->response->toString("N", $this->messages['token']['expired']));
            }
            else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) 
            {
                return response()->json($this->response->toString("N", $this->messages['token']['invalid']));
            }
            else
            {
                return response()->json($this->response->toString("N", $this->messages['token']['riquered']));
            }
        }
       return $next($request);
    }
}