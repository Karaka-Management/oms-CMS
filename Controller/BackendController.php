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
use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

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

        if ($request->getData('ptype') === 'p') {
            $view->setData('applications', ApplicationMapper::getBeforePivot((int) ($request->getData('id') ?? 0), null, 25));
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('applications', ApplicationMapper::getAfterPivot((int) ($request->getData('id') ?? 0), null, 25));
        } else {
            $view->setData('applications', ApplicationMapper::getAfterPivot(0, null, 25));
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
    public function viewApplicationFile(RequestAbstract $request, ResponseAbstract $response, $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-file');
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802101, $request, $response));

        /** @var Application $app */
        $app = ApplicationMapper::get($request->getData('id'));
        $view->addData('app', $app);

        $basePath = \realpath(__DIR__ . '/../../../Web/');
        if ($basePath === false
            || ($path = \realpath($basePath . '/' . \ucfirst(\strtolower($app->getName())) . '/tpl/' . $request->getDatA('tpl'))) === false
            || \stripos($path, $basePath) !== 0
        ) {
            return $view;
        }

        $view->addData('template', $path);

        return $view;
    }
}
