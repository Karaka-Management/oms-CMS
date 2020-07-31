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


/**
 * @var \phpOMS\Views\View                $this
 * @var \Modules\CMS\Models\Application[] $templates
 */
$template = $this->getData('template') ?? __DIR__ . '/application-template.tpl.php';

echo $this->getData('nav')->render();
?>

<div class="row">
    <div class="box col-xs-12">
        <section id="tplFile" class="box wf-100"
            data-update-content=".inner"
            data-update-element="#tplFile .textTitle, #tplFile .textContent"
            data-tag="form"
            data-method="POST"
            data-uri="<?= \phpOMS\Uri\UriFactory::build('{/api}cms/application/template?id={?id}&tpl={?tpl}&csrf={$CSRF}'); ?>">
            <div class="inner">
                <div class="vC">
                    <button class="save hidden"><?= $this->getHtml('Save', '0', '0') ?></button>
                    <button class="cancel hidden"><?= $this->getHtml('Cancel', '0', '0') ?></button>
                    <button class="update"><?= $this->getHtml('Edit', '0', '0') ?></button>
                </div>
                <template></template><!-- todo: this is required because of selectorLength + i in Form.js = first element = add template, second element = edit element. Fix -->
                <template></template><!-- todo: this is required because of selectorLength + i in Form.js = first element = add template, second element = edit element. Fix -->
                <template>
                    <input type="text" class="textTitle" value="<?= $this->printHtml(\basename($template, '.tpl.php')); ?>" data-tpl-text="/cms/template/name" data-tpl-value="/cms/template/name" name="name">
                </template>
                <template>
                    <textarea class="textContent" data-tpl-text="/cms/template/content" data-tpl-value="/cms/template/content" data-marker="tpl" name="content"></textarea>
                </template>
                <h1 class="textTitle" data-tpl-text="/cms/template/name" data-tpl-value="/cms/template/name"><?= $this->printHtml(\basename($template, '.tpl.php')); ?></h1>
                <pre class="textContent" data-tpl-text="/cms/template/content" data-tpl-value="/cms/template/content"><?= $this->printHtml(\file_get_contents($template)); ?></pre>
            </div>
        </section>
    </div>
</div>