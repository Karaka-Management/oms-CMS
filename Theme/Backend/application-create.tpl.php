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

/** @var \phpOMS\Views\View $this */
echo $this->data['nav']->render(); ?>

<div class="row">
    <div class="col-xs-12 col-md-6">
        <div class="portlet">
            <form id="cms-application-create" action="<?= \phpOMS\Uri\UriFactory::build('{/api}cms/application/upload'); ?>" method="PUT">
                <div class="portlet-head"><?= $this->getHtml('Application'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <label for="iTitle"><?= $this->getHtml('Title'); ?></label>
                        <input id="iTitle" name="name" type="text" required>
                    </div>

                    <div class="form-group">
                        <label for="iFile"><?= $this->getHtml('File'); ?></label>
                        <input id="iFile" name="files" type="file" required>
                    </div>

                    <div class="form-group">
                        <label for="iDescription"><?= $this->getHtml('Description'); ?></label>
                        <?= $this->getData('editor')->render('report-editor'); ?>
                    </div>

                    <div class="form-group">
                        <?= $this->getData('editor')->getData('text')->render('report-editor', 'description', 'cms-application-create'); ?>
                    </div>
                </div>
                <div class="portlet-foot">
                    <input type="submit" id="iApplicationCreateButton" name="applicationCreateButton" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                </div>
            </form>
        </div>
    </div>
</div>