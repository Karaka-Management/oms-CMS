<?php

/**
 * Orange Management
 *
 * PHP Version 8.0
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
use Modules\Media\Models\UploadFile;
use Modules\Media\Models\UploadStatus;
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
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Validate application create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool> Returns the validation array of the request
     *
     * @since 1.0.0
     */
    private function validateApplicationCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))) {
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
    public function apiApplicationCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateApplicationCreate($request))) {
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $application = $this->createApplicationFromRequest($request);
        $this->createModel($request->header->account, $application, ApplicationMapper::class, 'application', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Application', 'Application successfully created.', $application);
    }

    /**
     * Method to create task from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Application Returns the created application from the request
     *
     * @since 1.0.0
     */
    private function createApplicationFromRequest(RequestAbstract $request) : Application
    {
        $app       = new Application();
        $app->name = (string) ($request->getData('name') ?? '');

        return $app;
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
    public function apiApplicationInstall(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateApplicationInstall($request))) {
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $app = $this->upploadApplication($request);
        if (empty($app)) {
            return;
        }

        $request->setData('appSrc', 'Modules/CMS/tmp/' . $app);
        $request->setData('appDest', 'Web/' . $app);
        $request->setData('theme', $request->getData('theme') ?? 'Default', true);
        $this->app->moduleManager->get('Admin')->apiInstallApplication($request, $response);

        Directory::delete(__DIR__ . '/../tmp');

        $application = $this->createApplicationFromRequest($request);
        $this->createModel($request->header->account, $application, ApplicationMapper::class, 'application', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Application', 'Application successfully created.', $application);
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
    private function upploadApplication(RequestAbstract $request) : string
    {
        if (!\is_dir(__DIR__ . '/../tmp')) {
            \mkdir(__DIR__ . '/../tmp');
        }

        $upload = new UploadFile();
        $upload->setOutputDir(__DIR__ . '/../tmp');

        $status = $upload->upload($request->getFiles());
        if ($status[0]['status'] !== UploadStatus::OK) {
            return '';
        }

        $app = MbStringUtils::mb_ucfirst(
            \mb_strtolower(
                $request->getData('name') ?? \basename($status[0]['filename'], '.zip')
            )
        );

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
    public function apiApplicationTemplateUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateApplicationTemplateUpdate($request))) {
            $response->set($request->uri->__toString(), new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        /** @var Application $app */
        $app = ApplicationMapper::get($request->getData('id'));

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
    public function apiApplicationUpdate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
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
    public function apiApplicationTemplateCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
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
    public function apiApplicationFilesList(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Application $app */
        $app  = ApplicationMapper::get((int) $request->getData('id'));
        $path = (string) $request->getData('path') ?? '/';

        $content = \scandir(__DIR__ . '/../../../Web/' . MbStringUtils::mb_ucfirst(\mb_strtolower($app->name)) . $path);

        if ($content === false) {
            $content = [];
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Application Content', 'Directory content successfull returned', $content);
    }
}
