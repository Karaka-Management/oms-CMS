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

use phpOMS\System\File\FileUtils;
use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View $this
 * @var string[]           $file
 */
$app     = $this->data['app'];
$file    = $this->data['file'] ?? [];
$content = $this->data['content'] ?? [];
$list    = $this->data['list'] ?? [];

$doc      = null;
$isNewDoc = false;

echo $this->data['nav']->render();
?>

<div class="row fill-all">
    <div class="col-xs-12 col-md-8" style="display: flex;">
        <div class="box fill-all">
            <pre><code contenteditable><?= \htmlspecialchars($content); ?></code></pre>
        </div>
    </div>

    <div class="col-xs-12 col-md-4">
        <div class="box">
            <a href="<?= UriFactory::build('{/base}/' . $app->name); ?>" class="button">Application</a>
        </div>

        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Upload'); ?></div>
                <div class="portlet-body">

                </div>
            </div>

        <div class="portlet sticky">
            <div class="portlet-head"><?= $this->getHtml('Files'); ?></div>
            <div class="portlet-body">
                <ul>
                    <li><a href="<?= UriFactory::build('{/base}/cms/application/file{?}&file=' . FileUtils::absolute(\rtrim($this->getData('parent'), '/') . '/..')); ?>"><i class="fa fa-folder-o"></i> ..</a>
                <?php foreach ($list as $element) : ?>
                    <li><a href="<?= UriFactory::build('{/base}/cms/application/file{?}&file=' . \rtrim($this->getData('parent'), '/') . '/' . $element['name']); ?>"><?= $element['type'] === 1 ? '<i class="fa fa-folder-o"></i>' : '<i class="fa fa-file-o"></i>'; ?> <?= $element['name']; ?></a>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
