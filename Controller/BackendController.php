<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\CMS
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\CMS\Controller;

use Modules\Admin\Models\AppMapper;
use Modules\CMS\Models\PageMapper;
use Modules\CMS\Models\PostMapper;
use phpOMS\Application\ApplicationType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Security\Guard;
use phpOMS\Views\View;

/**
 * CMS class.
 *
 * @package Modules\CMS
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802001, $request, $response);

        if ($request->getData('ptype') === 'p') {
            $view->data['applications'] = AppMapper::getAll()->where('type', ApplicationType::WEB)->where('id', $request->getDataInt('offset') ?? 0, '<')->limit(25)->executeGetArray();
        } elseif ($request->getData('ptype') === 'n') {
            $view->data['applications'] = AppMapper::getAll()->where('type', ApplicationType::WEB)->where('id', $request->getDataInt('offset') ?? 0, '>')->limit(25)->executeGetArray();
        } else {
            $view->data['applications'] = AppMapper::getAll()->where('type', ApplicationType::WEB)->where('id', 0, '>')->limit(25)->executeGetArray();
        }

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802001, $request, $response);

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewPageList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-page-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802101, $request, $response);

        $pages = PageMapper::getAll()
            ->where('app', $request->getData('app'))
            ->executeGetArray();

        $view->data['list'] = $pages;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPage(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-page');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802101, $request, $response);

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        $page = PageMapper::get()
            ->with('l11n')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $view->data['page'] = $page;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationPosts(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-post-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802101, $request, $response);

        $posts = PostMapper::getAll()
            ->where('app', $request->getData('app'))
            ->executeGetArray();

        $view->data['list'] = $posts;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     */
    public function viewApplicationFile(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/CMS/Theme/Backend/application-file');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1007802101, $request, $response);

        /** @var \Modules\Admin\Models\App $app */
        $app               = AppMapper::get()->where('type', ApplicationType::WEB)->where('id', $request->getData('id'))->execute();
        $view->data['app'] = $app;

        $basePath = \realpath(__DIR__ . '/../../../Web/');
        $path     = \realpath($basePath . '/' . \ucfirst(\strtolower($app->name)) . '/' . ($request->getDataString('file') ?? ''));

        if ($path === false || \stripos($path, $basePath . '/') !== 0 || $basePath === false) {
            $path = \realpath($basePath . '/' . \ucfirst(\strtolower($app->name)));
        }

        if ($path === false || $basePath === false || !Guard::isSafePath($path)) {
            $view->setTemplate('/Web/Backend/Error/404');
            $response->header->status = RequestStatusCode::R_404;

            return $view;
        }

        if (!\is_dir($path)) {
            $path = \dirname($path);
        }

        $tempList = ($dirs = \scandir($path)) === false ? [] : $dirs;

        $temp1    = [];
        $temp2    = [];
        $fileList = [];

        foreach ($tempList as $element) {
            if ($element === '.' || $element === '..') {
                continue;
            } elseif (\is_file($path . '/' . $element)) {
                $temp2[] = ['name' => $element, 'type' => 0];
            } else {
                $temp1[] = ['name' => $element, 'type' => 1];
            }
        }

        $file   = \realpath($basePath . '/' . \ucfirst(\strtolower($app->name)) . '/' . ($request->getDataString('file') ?? ''));
        $parent = $file === false || \is_dir($file) ? $request->getDataString('file') ?? '' : \dirname($request->getDataString('file') ?? '');

        if ($file === false || !\is_file($file) || \stripos($file, $basePath) !== 0) {
            $file = empty($temp2) ? false : \realpath(\rtrim($path, '/') . '/' . $temp2[0]['name']);
        }

        $fileList = \array_merge($temp1, $temp2);

        if ($file === false || !Guard::isSafePath($file)) {
            $view->setTemplate('/Web/Backend/Error/404');
            $response->header->status = RequestStatusCode::R_404;

            return $view;
        }

        $view->data['content'] = $file === false ? '' : \file_get_contents($file);
        $view->data['parent']  = $parent;
        $view->data['list']    = $fileList;

        return $view;
    }
}
