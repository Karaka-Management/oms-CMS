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
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\Utils\Parser\Markdown\Markdown;
use Modules\CMS\Models\ApplicationMapper;
use Modules\Media\Models\UploadFile;
use Modules\Media\Models\UploadStatus;
use phpOMS\System\File\Local\Directory;
use phpOMS\Utils\IO\Zip\Zip;

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
        if (($val['name'] = empty($request->getData('name')))
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
    public function apiApplicationCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateApplicationCreate($request))) {
            $response->set($request->getUri()->__toString(), new FormValidation($val));

            return;
        }

        $application = $this->createApplicationFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $application, ApplicationMapper::class, 'application');
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
        $app = new Application();
        $app->setName((string) ($request->getData('name') ?? ''));

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
            $response->set($request->getUri()->__toString(), new FormValidation($val));

            return;
        }

        if (!\file_exists(__DIR__ . '/../tmp')) {
            \mkdir(__DIR__ . '/../tmp');
        }

        $upload = new UploadFile();
        $upload->setOutputDir(__DIR__ . '/../tmp');

        $status = $upload->upload($request->getFiles());

        if ($status[0]['status'] !== UploadStatus::OK) {
            return;
        }

        $app = \ucfirst(
            \strtolower(
                $request->getData('name') ?? \basename($status[0]['filename'], '.zip')
            )
        );

        Zip::unpack(__DIR__ . '/../tmp/' . $status[0]['filename'], __DIR__ . '/../tmp/' . $app);
        \unlink(__DIR__ . '/../tmp/' . $status[0]['filename']);

        $request->setData('appSrc', 'Modules/CMS/tmp/' . $app);
        $request->setData('appDest', 'Web/' . $app);
        $request->setData('theme', $request->getData('theme') ?? 'Default', true);
        $this->app->moduleManager->get('Admin')->apiInstallApplication($request, $response);

        Directory::delete(__DIR__ . '/../tmp');

        $application = $this->createApplicationFromRequest($request);
        $this->createModel($request->getHeader()->getAccount(), $application, ApplicationMapper::class, 'application');
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Application', 'Application successfully created.', $application);
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
}
