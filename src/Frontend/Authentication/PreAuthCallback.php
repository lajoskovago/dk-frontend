<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/8/2016
 * Time: 6:49 PM
 */

namespace Frontend\Authentication;

use N3vrax\DkWebAuthentication\PreAuthCallbackResult;
use N3vrax\DkZendAuthentication\Adapter\CallbackCheck\DbCredentials;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Expressive\Template\TemplateRendererInterface;

class PreAuthCallback
{
    protected $template;

    public function __construct(TemplateRendererInterface $template)
    {
        $this->template = $template;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        if($request->getMethod() === 'POST')
        {
            $data = $request->getParsedBody();
            $identity = '';
            $credential = '';
            if(is_array($data)) {
                $identity = isset($data['identity']) ? $data['identity'] : '';
                $credential = isset($data['credential']) ? $data['credential'] : '';
            }

            if(empty($identity) || empty($credential)) {
                return new HtmlResponse($this->template->render('app::login-page',
                    ['messages' => ['Authentication failure. Credentials are required and cannot be empty'],
                        'identity' => $identity]));
            }

            $credentials = new DbCredentials($identity, $credential);
            $request = $request->withAttribute(DbCredentials::class, $credentials);
        }

        return new PreAuthCallbackResult($request, $response);
    }
}