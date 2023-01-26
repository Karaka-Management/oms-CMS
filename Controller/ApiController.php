<?php

/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\CMS
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */

declare(strict_types=1);

namespace Modules\CMS\Controller;

use Modules\Admin\Models\App;
use Modules\Admin\Models\AppMapper;
use Modules\CMS\Models\Page;
use Modules\CMS\Models\PageL11nMapper;
use Modules\CMS\Models\PageMapper;
use Modules\Media\Models\UploadFile;
use Modules\Media\Models\UploadStatus;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\System\File\Local\Directory;
use phpOMS\Utils\IO\Zip\Zip;
use phpOMS\Utils\MbStringUtils;

/**
 * Api controller for the CMS module.
 *
 * @package Modules\CMS
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Api method to create a page
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPageCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validatePageCreate($request))) {
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $application = $this->createPageFromRequest($request);
        $this->createModel($request->header->account, $application, PageMapper::class, 'page', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Page', 'Page successfully created.', $application);
    }

    /**
     * Validate page create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validatePageCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))
            || ($val['app'] = empty($request->getData('app')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create page from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Page
     *
     * @since 1.0.0
     */
    private function createPageFromRequest(RequestAbstract $request) : Page
    {
        $page           = new Page();
        $page->name     = (string) ($request->getData('name') ?? '');
        $page->app      = (int) ($request->getData('app') ?? 2);
        $page->template = (string) ($request->getData('template') ?? '');

        return $page;
    }

    /**
     * Api method to create a page
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPageL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validatePageL11nCreate($request))) {
            $response->set('page_l11n_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $l11nPage = $this->createPageL11nFromRequest($request);
        $this->createModel($request->header->account, $l11nPage, PageL11nMapper::class, 'page_l11n', $request->getOrigin());

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Page localization successfully created', $l11nPage);
    }

    /**
     * Validate page l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validatePageL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))
            || ($val['page'] = empty($request->getData('page')))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create page localization from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createPageL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $pageL11n      = new BaseStringL11n();
        $pageL11n->ref = (int) ($request->getData('page') ?? 0);
        $pageL11n->setLanguage((string) (
            $request->getData('language') ?? $request->getLanguage()
        ));
        $pageL11n->name    = (string) ($request->getData('name') ?? '');

        $page = PageMapper::get()
            ->where('id', (int) ($request->getData('page') ?? 0))
            ->execute();

        $app = AppMapper::get()
            ->where('id', $page->app)
            ->execute();

        $pageL11n->content = $this->parseCmsKeys((string) ($request->getData('content') ?? ''), $app);

        return $pageL11n;
    }

    private function parseCmsKeys(string $content, App $application) : string
    {
        if ($content === '') {
            return '';
        }

        $content = \str_replace('{APPNAME}', $application->name, $content);

        return $content;
    }

    /**
     * Api method to install a application
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationInstall(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateApplicationInstall($request))) {
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $app = self::uploadApplication($request);
        if (empty($app) || $app === '-1') {
            // @codeCoverageIgnoreStart
            $response->set($request->uri->__toString(), new FormValidation());
            $response->header->status = RequestStatusCode::R_400;

            return;
            // @codeCoverageIgnoreEnd
        }

        $request->setData('appSrc', 'Modules/CMS/tmp/' . $app);
        $request->setData('appDest', 'Web/' . $app);
        $request->setData('appName', $app);
        $request->setData('theme', $request->getData('theme') ?? 'Default', true);
        $this->app->moduleManager->get('Admin')->apiInstallApplication($request, $response);
        $this->app->moduleManager->get('Admin')->apiApplicationCreate($request, $response);

        Directory::delete(__DIR__ . '/../tmp');
    }

    /**
     * Upload the application archiv
     *
     * @param RequestAbstract $request Request
     *
     * @return string
     *
     * @since 1.0.0
     */
    public static function uploadApplication(RequestAbstract $request) : string
    {
        Directory::delete(__DIR__ . '/../tmp');
        if (!\is_dir(__DIR__ . '/../tmp')) {
            \mkdir(__DIR__ . '/../tmp');
        }

        $upload                   = new UploadFile();
        $upload->outputDir        = __DIR__ . '/../tmp';
        $upload->preserveFileName = true;

        $status = $upload->upload($request->getFiles());
        $status = \array_values($status);

        if ($status[0]['status'] !== UploadStatus::OK) {
            return ''; // @codeCoverageIgnore
        }

        $app = MbStringUtils::mb_ucfirst(
            \mb_strtolower(
                $request->getData('name') ?? \basename($status[0]['filename'], '.zip')
            )
        );

        // cannot create existing application name
        if (\is_dir(__DIR__ . '/../../../' . $app)
            || \is_dir(__DIR__ . '/../../../Web/' . $app)
        ) {
            \unlink(__DIR__ . '/../tmp/' . $status[0]['filename']);

            return '-1';
        }

        Zip::unpack(__DIR__ . '/../tmp/' . $status[0]['filename'], __DIR__ . '/../tmp/' . $app);
        \unlink(__DIR__ . '/../tmp/' . $status[0]['filename']);

        return $app;
    }

    /**
     * Validate application install request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool> Returns the validation array of the request
     *
     * @since 1.0.0
     */
    private function validateApplicationInstall(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))
            || ($val['files'] = empty($request->getFiles()))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to update a template
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationTemplateUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateApplicationTemplateUpdate($request))) {
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        /** @var App $app */
        $app = AppMapper::get()->where('id', $request->getData('id'))->execute();

        $webPath  = \realpath(__DIR__ . '/../../../Web/');
        $basePath = \realpath(__DIR__ . '/../../../Web/' . MbStringUtils::mb_ucfirst(\mb_strtolower($app->name)) . '/tpl/');

        if ($basePath === false
            || $webPath === false
            || ($path = \realpath($basePath . '/' . $request->getDatA('tpl'))) === false
            || \stripos($path, $webPath) !== 0
        ) {
            return;
        }

        \file_put_contents($path, $request->getData('content'));

        if (!empty($request->getData('name'))) {
            $old = $path;
            $new = $basePath . '/' . $request->getData('name') . '.tpl.php';
            \rename($old, $new);
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Application Template', 'Template successfully updated', $request->getData('content'));
    }

    /**
     * Validate application template update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool> Returns the validation array of the request
     *
     * @since 1.0.0
     */
    private function validateApplicationTemplateUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['content'] = empty($request->getData('content')))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create a task
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationUpdate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
    }

    /**
     * Api method to create a task
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationTemplateCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
    }

    /**
     * Api method to list files of a application
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationFilesList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var App $app */
        $app  = AppMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $path = (string) ($request->getData('path') ?? '/');

        $content = \scandir(__DIR__ . '/../../../Web/' . MbStringUtils::mb_ucfirst(\mb_strtolower($app->name)) . $path);

        if ($content === false) {
            $content = [];
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Application Content', 'Directory content successfull returned', $content);
    }
}
