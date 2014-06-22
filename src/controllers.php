<?php
/**
 * @author: David Castello Alfaro <dcastello at gmail.com>
 */

use Model\Message;
use Model\User;
use Repository\MessageRepository;
use Repository\UserRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app->get(
    "/",
    function (Request $request) use ($app) {

        $messageRepository = $app['message.repository'];
        if (null !== $userSession = $request->getSession()->get('user')) {
            $messages = $messageRepository->findBoardForUser($userSession['id'], 'home');

            return $app['twig']->render('index.html.twig', array('messages' => $messages));
        }

        return $app['twig']->render('index.html.twig');
    }
)->bind('home');

$app->get(
    "/login",
    function (Request $request) use ($app) {

        $urlGenerator = $app['url_generator'];
        if (null !== $userSession = $request->getSession()->get('user')) {
            $app->redirect($urlGenerator->generate('home'));
        }

        return $app['twig']->render('login.html.twig');
    }
)->bind('login');

$app->post(
    "/login-check",
    function (Request $request) use ($app) {

        $urlGenerator = $app['url_generator'];
        if (null !== $userSession = $request->getSession()->get('user')) {
            $app->redirect($urlGenerator->generate('home'));
        }

        $login = $request->get('login');
        $password = $request->get('password');

        if ($login == $password) {

            $userRepository = $app['user.repository'];
            $user = $userRepository->findByLogin($login);

            if ($user !== false) {
                $request->getSession()->start();
                $request->getSession()->set(
                    'user',
                    array(
                        'id' => $user->getId(),
                        'login' => $user->getLogin()
                    )
                );

                $request->getSession()->getFlashBag()->add('success', 'Login successfully!');

                return $app->redirect($urlGenerator->generate('user_profile', array('userId' => $user->getId())));
            }
        }

        return $app->redirect($urlGenerator->generate('login'));
    }
)->bind('login_check');

$app->get(
    "/logout",
    function (Request $request) use ($app) {

        $urlGenerator = $app['url_generator'];
        if (null === $userSession = $request->getSession()->get('user')) {
            return $app->redirect($urlGenerator->generate('login'));
        }

        $request->getSession()->set('user', null);

        $request->getSession()->getFlashBag()->add('success', 'Logout successfully!');

        return $app->redirect($urlGenerator->generate('login'));
    }
)->bind('logout');

$app->get(
    "/redis-status",
    function () use ($app) {
        $status = ($app['predis']->ping()) ? 'Connected' : 'Disconnected';

        return $app['twig']->render('redis-status.html.twig', array('status' => $status));
    }
)->bind('redis_status');

$app->get(
    "/user/create",
    function () use ($app) {
        return $app['twig']->render('user-create.html.twig');
    }
)->bind('user_create');

$app->post(
    "/user/insert",
    function (Request $request) use ($app) {

        try {
            $urlGenerator = $app['url_generator'];

            /** @var UserRepository $userRepository */
            $userRepository = $app['user.repository'];

            if ($userRepository->existUser($request->get('userId'))) {

                $request->getSession()->getFlashBag()->add('info', 'User exist!');

                return $app->redirect($urlGenerator->generate('user_create'));
            }

            $login = "login" . $request->get('userId');
            $user = new User();
            $user->setLogin($login);
            $user->setName("Name $login");

            $userRepository->insertUser($user);
            $request->getSession()->getFlashBag()->add('success', "User $login Created!");

            return $app->redirect($urlGenerator->generate('user_create'));

        } catch (\Exception $exception) {
            $request->getSession()->getFlashBag()->add('error', $exception->getMessage());

            return $app->redirect($urlGenerator->generate('user_create'));
        }
    }
)->bind('user_insert');

$app->get(
    "/user/{userIdToFollow}/follow",
    function (Request $request) use ($app) {

        $urlGenerator = $app['url_generator'];
        if (null === $userSession = $request->getSession()->get('user')) {
            return $app->redirect($urlGenerator->generate('login'));
        }

        /** @var UserRepository $userRepository */
        $userRepository = $app['user.repository'];
        $userIdToFollow = $request->get('userIdToFollow');

        $userToFollow = $userRepository->findById($userIdToFollow);

        if ($userToFollow == false) {
            $request->getSession()->getFlashBag()->add('error', 'User not exist!');

            return $app->redirect($urlGenerator->generate('users'));
        }

        $userRepository->followUser($request->getSession()->get('user')['id'], $userIdToFollow);

        $request->getSession()->getFlashBag()->add(
            'success',
            'You are now following to ' . $userToFollow->getName() . '!'
        );

        return $app->redirect($urlGenerator->generate('users'));
    }
)->bind('user_follow');

$app->get(
    "/user/{userIdToUnfollow}/unfollow",
    function (Request $request) use ($app) {

        $urlGenerator = $app['url_generator'];
        if (null === $userSession = $request->getSession()->get('user')) {
            return $app->redirect($urlGenerator->generate('login'));
        }

        /** @var UserRepository $userRepository */
        $userRepository = $app['user.repository'];
        $userIdToUnfollow = $request->get('userIdToUnfollow');

        $userToUnfollow = $userRepository->findById($userIdToUnfollow);

        if ($userToUnfollow == false) {
            $request->getSession()->getFlashBag()->add('error', 'User not exist!');

            return $app->redirect($urlGenerator->generate('users'));
        }

        $userRepository->unfollowUser($request->getSession()->get('user')['id'], $userIdToUnfollow);

        $request->getSession()->getFlashBag()->add(
            'success',
            'You are now not following to ' . $userToUnfollow->getName() . '!'
        );

        return $app->redirect($urlGenerator->generate('users'));
    }
)->bind('user_unfollow');

$app->post(
    "/user/message",
    function (Request $request) use ($app) {

        $urlGenerator = $app['url_generator'];

        if (null === $userSession = $request->getSession()->get('user')) {
            return $app->redirect($urlGenerator->generate('login'));
        }

        /** @var MessageRepository $messageRepository */
        $messageRepository = $app['message.repository'];
        /** @var UserRepository $userRepository */
        $userRepository = $app['user.repository'];

        if (!$userRepository->existUser($userSession['id'])) {
            $request->getSession()->getFlashBag()->add('error', 'User not exist!');

            return $app->redirect($urlGenerator->generate('home'));
        }

        $user = $userRepository->findById($userSession['id']);
        $messageText = $request->get('userMessage');

        $message = new Message();
        $message->setMessage($messageText);
        $message->setPostedAt(date('Ymdhsi'));
        $message->setUser($user);

        $messageRepository->insert($message);

        $request->getSession()->getFlashBag()->add('success', 'Message added!');

        return $app->redirect($urlGenerator->generate('home'));
    }
)->bind('user_add_message');

$app->get(
    "/users",
    function () use ($app) {

        /** @var UserRepository $userRepository */
        $userRepository = $app['user.repository'];
        $users = $userRepository->findAll();

        return $app['twig']->render('users.html.twig', array('users' => $users));
    }
)->bind('users');

$app->get(
    "/user/{userId}/profile",
    function (Request $request) use ($app) {

        $urlGenerator = $app['url_generator'];
        /** @var MessageRepository $messageRepository */
        $messageRepository = $app['message.repository'];
        /** @var UserRepository $userRepository */
        $userRepository = $app['user.repository'];

        if (!$userRepository->existUser($request->get('userId'))) {
            $request->getSession()->getFashBag()->add('error', "User Not Exist!");

            return $app->redirect($urlGenerator->generate('users'));
        }

        $user = $userRepository->findById($request->get('userId'));
        $messages = $messageRepository->findBoardForUser($request->get('userId'), 'profile');
        $following = $userRepository->findAllFollowing($user->getId());
        $followers = $userRepository->findAllFollowers($user->getId());

        return $app['twig']->render(
            'profile.html.twig',
            array(
                'user' => $user,
                'messages' => $messages,
                'following' => $following,
                'followers' => $followers
            )
        );
    }
)->bind('user_profile');

$app->error(
    function (\Exception $e, $code) use ($app) {
        if ($app['debug']) {
            return;
        }

        return new Response($app['twig']->render('error.html.twig'));
    }
);