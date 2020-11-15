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

use phpOMS\System\File\FileUtils;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 * @var string[]           $file
 */
$file    = $this->getData('file') ?? [];
$content = $this->getData('content') ?? [];
$list    = $this->getData('list') ?? [];

$doc      = null;
$isNewDoc = false;

echo $this->getData('nav')->render();
?>

<div class="row">
    <div class="col-xs-12 col-md-8">
        <div class="box" style="height: 100%; display: flex; align-items: stretch;">
            <textarea style="height: 100%;"><?= \str_replace("\n", '&#13;&#10;', $content); ?></textarea>
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
                <ul>
                    <li><a href="<?= UriFactory::build('{/prefix}cms/application/file{?}&file=' . FileUtils::absolute(\rtrim($this->getData('parent'), '/') . '/..')); ?>"><i class="fa fa-folder-o"></i> ..</a>
                <?php foreach ($list as $element) : ?>
                    <li><a href="<?= UriFactory::build('{/prefix}cms/application/file{?}&file=' . \rtrim($this->getData('parent'), '/') . '/' . $element['name']); ?>"><?= $element['type'] === 1 ? '<i class="fa fa-folder-o"></i>' : '<i class="fa fa-file-o"></i>' ?> <?= $element['name']; ?></a>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
