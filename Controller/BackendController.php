<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\CMS
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\CMS\Controller;

use Modules\CMS\Models\Application;
use Modules\CMS\Models\ApplicationMapper;

use phpOMS\Views\View;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Contract\RenderableInterface;
use phpOMS\System\File\Local\Directory;

/**
 * CMS class.
 *
 * @package Modules\CMS
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationList(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-list');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802001, $request, $response));

        if ($request->getData('ptype') === '-') {
            $view->setData('applications', ApplicationMapper::getBeforePivot((int) ($request->getData('id') ?? 0), null, 25));
        } else {
            $view->setData('applications', ApplicationMapper::getAfterPivot((int) ($request->getData('id') ?? 0), null, 25));
        }

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-create');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802001, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationContents(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-contents');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802101, $request, $response));

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationTemplates(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-templates');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802101, $request, $response));

        /** @var Application $app */
        $app  = ApplicationMapper::get($request->getData('id'));
        $tpls = Directory::list(__DIR__ . '/../../../Web/' . \ucfirst(\strtolower($app->getName())) . '/tpl', '.*tpl\.php');

        $view->addData('templates', $tpls);

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationTemplate(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-template');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802101, $request, $response));

        /** @var Application $app */
        $app = ApplicationMapper::get($request->getData('id'));
        $view->addData('app', $app);

        if (($path = \realpath(__DIR__ . '/../../../Web/' . \ucfirst(\strtolower($app->getName())) . '/tpl/' . $request->getDatA('tpl'))) === false
            || \stripos($path, \realpath(__DIR__ . '/../../../Web/')) !== 0
        ) {
            return $view;
        }

        $view->addData('template', $path);

        return $view;
    }
}
