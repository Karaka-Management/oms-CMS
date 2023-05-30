<?php

/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\CMS
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
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
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    public function apiCookieConsent(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $cookieContent = \file_get_contents(__DIR__ . '/../../../Web/' . ($request->getData('app') ?? $this->app->appName) . '/cookie_consent.json');
        if ($cookieContent === false) {
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $cookieRules = \json_decode($cookieContent, true);
        if (!\is_array($cookieRules)) {
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $userSettings = $request->getData('cookie_consent_rules');
        $this->app->cookieJar->set('cookie_consent', \json_encode($userSettings), 86400);

        foreach ($userSettings as $rule) {
            if (isset($cookieRules[$rule])) {
                foreach ($rule['values'] as $key => $value) {
                    if (!empty($value)) {
                        $this->app->cookieJar->set($key, $value);
                    }
                }
            }
        }

        $this->app->cookieJar->save();
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
    public function apiPageCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validatePageCreate($request))) {
            $response->data[$request->uri->__toString()] = new FormValidation($val);
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
        if (($val['name'] = !$request->hasData('name'))
            || ($val['app'] = !$request->hasData('app'))
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
        $page->name     = $request->getDataString('name') ?? '';
        $page->app      = $request->getDataInt('app') ?? 2;
        $page->template = $request->getDataString('template') ?? '';

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
            $response->data['page_l11n_create'] = new FormValidation($val);
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $l11nPage = $this->createPageL11nFromRequest($request);
        $this->createModel($request->header->account, $l11nPage, PageL11nMapper::class, 'page_l11n', $request->getOrigin());

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully created', $l11nPage);
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
        if (($val['name'] = !$request->hasData('name'))
            || ($val['page'] = !$request->hasData('page'))
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
        $pageL11n->ref = $request->getDataInt('page') ?? 0;
        $pageL11n->setLanguage(
            $request->getDataString('language') ?? $request->header->l11n->language
        );
        $pageL11n->name = $request->getDataString('name') ?? '';

        /** @var Page $page */
        $page = PageMapper::get()
            ->where('id', $request->getDataInt('page') ?? 0)
            ->execute();

        /** @var App $app */
        $app = AppMapper::get()
            ->where('id', $page->app)
            ->execute();

        $pageL11n->content = $this->parseCmsKeys($request->getDataString('content') ?? '', $app);

        return $pageL11n;
    }

    /**
     * Searches and replaces well-defined keywords
     *
     * @param string $content     Content to search
     * @param App    $application Application model
     *
     * @return string
     *
     * @since 1.0.0
     */
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
            $response->data[$request->uri->__toString()] = new FormValidation($val);
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
        $request->setData('default_unit', $request->getDataInt('default_int'));
        $request->setData('theme', $request->getDataString('theme') ?? 'Default', true);
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

        $status = $upload->upload($request->files);
        $status = \array_values($status);

        if ($status[0]['status'] !== UploadStatus::OK) {
            return ''; // @codeCoverageIgnore
        }

        $app = MbStringUtils::mb_ucfirst(
            \mb_strtolower(
                $request->getDataString('name') ?? \basename($status[0]['filename'], '.zip')
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
        if (($val['name'] = !$request->hasData('name'))
            || ($val['files'] = empty($request->files))
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
            $response->data[$request->uri->__toString()] = new FormValidation($val);
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

        if ($request->hasData('name')) {
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
        if (($val['content'] = !$request->hasData('content'))) {
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
