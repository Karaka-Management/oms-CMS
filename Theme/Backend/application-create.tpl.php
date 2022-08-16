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
 * @link      https://karaka.app
 */
declare(strict_types=1);

/** @var \phpOMS\Views\View $this */
echo $this->getData('nav')->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="cms-application-create" action="<?= \phpOMS\Uri\UriFactory::build('{/api}cms/application/upload'); ?>" method="PUT">
                <div class="portlet-head"><?= $this->getHtml('Application'); ?></div>
                <div class="portlet-body">
                    <table class="layout wf-100" style="table-layout: fixed">
                        <tbody>
                        <tr><td><label for="iTitle"><?= $this->getHtml('Title'); ?></label>
                        <tr><td><input id="iTitle" name="name" type="text" required>
                        <tr><td><label for="iFile"><?= $this->getHtml('File'); ?></label>
                        <tr><td><input id="iFile" name="files" type="file" required>
                        <tr><td><label for="iDescription"><?= $this->getHtml('Description'); ?></label>
                        <tr><td><?= $this->getData('editor')->render('report-editor'); ?>
                        <tr><td><?= $this->getData('editor')->getData('text')->render('report-editor', 'description', 'cms-application-create'); ?>
                    </table>
                </div>
                <div class="portlet-foot">
                    <input type="submit" id="iApplicationCreateButton" name="applicationCreateButton" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>