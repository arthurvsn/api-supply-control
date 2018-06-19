<?php
namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
class VerifyJWTToken
{
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
                $this->response->setType("N");
                $this->response->setMessages("Token expired", $e->getStatusCode());
                return response()->json($this->response->toString());
            }
            else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) 
            {
                $this->response->setType("N");
                $this->response->setMessages("Token invalid", $e->getStatusCode());
                return response()->json($this->response->toString());
            }
            else
            {
                $this->response->setType("N");
                $this->response->setMessages("Token is required");
                return response()->json($this->response->toString());
            }
        }
       return $next($request);
    }
}