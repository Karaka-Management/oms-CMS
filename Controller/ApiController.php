<?php

/**
 * Jingga
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
    /**
     * Api method to handle cookie consent settings
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiCookieConsent(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $cookieContent = \file_get_contents(__DIR__ . '/../../../Web/' . ($request->getDataString('app') ?? $this->app->appName) . '/cookie_consent.json');
        if ($cookieContent === false) {
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $cookieRules = \json_decode($cookieContent, true);
        if (!\is_array($cookieRules)) {
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $userSettings = $request->getDataJson('cookie_consent_rules');
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
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPageCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validatePageCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $application = $this->createPageFromRequest($request);
        $this->createModel($request->header->account, $application, PageMapper::class, 'page', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $application);
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
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPageL11nCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validatePageL11nCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $l11nPage = $this->createPageL11nFromRequest($request);
        $this->createModel($request->header->account, $l11nPage, PageL11nMapper::class, 'page_l11n', $request->getOrigin());
        $this->createStandardCreateResponse($request, $response, $l11nPage);
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

        return \str_replace('{APPNAME}', $application->name, $content);
    }

    /**
     * Api method to install a application
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationInstall(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateApplicationInstall($request))) {
            $response->data[$request->uri->__toString()] = new FormValidation($val);
            $response->header->status                    = RequestStatusCode::R_400;

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
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationTemplateUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateApplicationTemplateUpdate($request))) {
            $response->data[$request->uri->__toString()] = new FormValidation($val);
            $response->header->status                    = RequestStatusCode::R_400;

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

        $this->createStandardCreateResponse($request, $response, $request->getData('content'));
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
     * Api method to list files of a application
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiApplicationFilesList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var App $app */
        $app  = AppMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $path = $request->getDataString('path') ?? '/';

        $content = \scandir(__DIR__ . '/../../../Web/' . MbStringUtils::mb_ucfirst(\mb_strtolower($app->name)) . $path);

        if ($content === false) {
            $content = [];
        }

        $this->createStandardReturnResponse($request, $response, $content);
    }

    /**
     * Api method to update Page
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPageUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validatePageUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var \Modules\CMS\Models\Page $old */
        $old = PageMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updatePageFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, PageMapper::class, 'page', $request->getOrigin());

        if (!$request->hasData('content')) {
            $this->createStandardUpdateResponse($request, $response, $new);

            return;
        }

        /** @var \phpOMS\Localization\BaseStringL11n[] $l11ns */
        $l11ns = PageL11nMapper::getAll()
            ->where('ref', (int) $request->getData('id'))
            ->where('language', $request->getDataString('language') ?? $request->header->l11n->language)
            ->execute();

        if (empty($l11ns)) {
            $this->createInvalidUpdateResponse($request, $response, []);

            return;
        }

        $request->setData('page', (int) $request->getData('id'), true);

        $contents = $request->getDataJson('content');
        foreach ($contents as $content) {
            // @todo: fix this, the next line is wrong. I need to also pass an id array to know to which id the content belongs
            $request->setData('id', \reset($l11ns)->id, true);

            $request->setData('content', $content, true);
            $this->apiPageL11nUpdate($request, $response, $data);
        }

        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update Page from request.
     *
     * @param RequestAbstract $request Request
     * @param Page            $new     Model to modify
     *
     * @return Page
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    public function updatePageFromRequest(RequestAbstract $request, Page $new) : Page
    {
        $new->name     = $request->getDataString('name') ?? $new->name;
        $new->app      = $request->getDataInt('app') ?? $new->app;
        $new->template = $request->getDataString('template') ?? $new->template;

        return $new;
    }

    /**
     * Validate Page update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validatePageUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to delete Page
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPageDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validatePageDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var \Modules\CMS\Models\Page $page */
        $page = PageMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $page, PageMapper::class, 'page', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $page);
    }

    /**
     * Validate Page delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validatePageDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to update PageL11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPageL11nUpdate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validatePageL11nUpdate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var BaseStringL11n $old */
        $old = PageL11nMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updatePageL11nFromRequest($request, clone $old);

        $this->updateModel($request->header->account, $old, $new, PageL11nMapper::class, 'page_l11n', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
    }

    /**
     * Method to update PageL11n from request.
     *
     * @param RequestAbstract $request Request
     * @param BaseStringL11n  $new     Model to modify
     *
     * @return BaseStringL11n
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    public function updatePageL11nFromRequest(RequestAbstract $request, BaseStringL11n $new) : BaseStringL11n
    {
        $new->ref = $request->getDataInt('page') ?? $new->ref;
        $new->setLanguage(
            $request->getDataString('language') ?? $new->language
        );
        $new->name = $request->getDataString('name') ?? $new->name;

        /** @var Page $page */
        $page = PageMapper::get()
            ->where('id', $request->getDataInt('page') ?? 0)
            ->execute();

        /** @var App $app */
        $app = $page->id !== 0 ? AppMapper::get()
            ->where('id', $page->app)
            ->execute()
            : null;

        $new->content = $request->hasData('content')
            ? $this->parseCmsKeys($request->getDataString('content') ?? '', $app)
            : $new->content;

        return $new;
    }

    /**
     * Validate PageL11n update request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validatePageL11nUpdate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to delete PageL11n
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPageL11nDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validatePageL11nDelete($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidDeleteResponse($request, $response, $val);

            return;
        }

        /** @var BaseStringL11n $pageL11n */
        $pageL11n = PageL11nMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $pageL11n, PageL11nMapper::class, 'page_l11n', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $pageL11n);
    }

    /**
     * Validate PageL11n delete request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @todo: implement
     *
     * @since 1.0.0
     */
    private function validatePageL11nDelete(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['id'] = !$request->hasData('id'))) {
            return $val;
        }

        return [];
    }
}
