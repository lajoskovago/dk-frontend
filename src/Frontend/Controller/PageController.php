<?php
/**
 * Created by PhpStorm.
 * User: n3vrax
 * Date: 6/10/2016
 * Time: 7:22 PM
 */

namespace Frontend\Controller;

use Frontend\User\UserEntity;
use Frontend\User\UserMapperInterface;
use N3vrax\DkBase\Controller\AbstractActionController;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\RedirectResponse;

class PageController extends AbstractActionController
{
    protected $userMapper;
    
    public function __construct(UserMapperInterface $userMapper)
    {
        $this->userMapper = $userMapper;
    }

    public function indexAction()
    {
        return new RedirectResponse($this->urlHelper()->generate('home'));
    }

    public function aboutUsAction()
    {
        return new HtmlResponse($this->template()->render('page::about-us'));
    }

    public function whoWeAreAction()
    {
        return new HtmlResponse($this->template()->render('page::who-we-are'));
    }

    public function userListAction()
    {
        /** @var HydratingResultSet $users */
        $users = $this->userMapper->findAllUsers();

        return new HtmlResponse($this->template()->render('page::user-list', ['users' => $users]));
    }

    public function testAddUserAction()
    {
        $request = $this->getRequest();
        $query = $request->getQueryParams();
        $who = $query['who'] ?: 'vasile69';
        $data = null;
        switch ($who)
        {
            case 'vasile69':
                $data = [
                    'username' => 'vasile69',
                    'email' => 'vasile@laba.com',
                    'role' => 'user',
                    'password' => '1234'
                ];
                break;

            case 'cristi89':
                $data = new UserEntity();
                $data->setId(5);
                $data->setEmail('cristi@test.com');
                $data->setUsername('cristi89');
                $data->setRole('member');
                $data->setPassword('12345');
                break;

            default:
                break;
        }

        if($data) {
            try {
                $this->userMapper->saveUser($data);
                $this->flashMessenger()->addInfo('Successfully saved the user');
            }
            catch(\Exception $e) {
                $this->flashMessenger()->addError($e->getMessage());
            }
        }

        return new RedirectResponse($this->urlHelper()->generate('pages', ['action' => 'user-list']));

    }
}