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

<div class="row">
    <div class="col-xs-12 col-md-8">
        <section class="portlet">
            <div class="portlet-body">
                <pre><code contenteditable><?= \htmlspecialchars($content); ?></code></pre>
            </div>
        </section>
    </div>

    <div class="col-xs-12 col-md-4">
        <!--
        @todo Implement
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Upload'); ?></div>
            <div class="portlet-body"></div>
        </section>
        -->

        <div class="portlet sticky">
            <div class="portlet-head"><?= $this->getHtml('Files'); ?></div>
            <div class="portlet-body">
                <ul>
                    <li><a href="<?= UriFactory::build('{/base}/cms/application/files{?}&file=' . FileUtils::absolute(\rtrim($this->getData('parent'), '/') . '/..')); ?>"><i class="g-icon">folder_open</i> ..</a>
                <?php foreach ($list as $element) : ?>
                    <li><a href="<?= UriFactory::build('{/base}/cms/application/files{?}&file=' . \rtrim($this->getData('parent'), '/') . '/' . $element['name']); ?>"><?= $element['type'] === 1 ? '<i class="g-icon">folder_open</i>' : '<i class="g-icon">article</i>'; ?> <?= $element['name']; ?></a>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
