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

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View                $this
 * @var \Modules\CMS\Models\Application[] $templates
 */
$templates = $this->getData('templates') ?? [];

$doc      = null;
$isNewDoc = false;

echo $this->getData('nav')->render();
?>

<div class="row">
    <div class="col-xs-12 col-md-8">
        <div class="portlet">
            <div class="portlet-body">
                <form id="fEditor" method="<?= $isNewDoc ? 'PUT' : 'POST'; ?>" action="<?= UriFactory::build('{/api}editor?{?}&csrf={$CSRF}'); ?>">
                    <div class="ipt-wrap">
                        <div class="ipt-first"><input name="title" type="text" class="wf-100" value="<?= /*$doc->getTitle();*/ 1; ?>"></div>
                        <div class="ipt-second"><input type="submit" value="<?= $this->getHtml('Save'); ?>"></div>
                    </div>
                </form>
            </div>
        </div>

        <div class="box">
            <textarea></textarea>
        </div>
    </div>

    <div class="col-xs-12 col-md-4">
        <div class="portlet">
            <div class="portlet-head">Upload</div>
                <div class="portlet-body">

                </div>
            </div>

        <div class="portlet">
            <div class="portlet-head">Files</div>
            <div class="portlet-body">
                <!-- Show all files of the application -->
            </div>
        </div>
    </div>
</div>
