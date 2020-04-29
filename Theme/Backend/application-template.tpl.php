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
$template = $this->getData('template') ?? __DIR__ . '/application-template.tpl.php';

echo $this->getData('nav')->render();
?>

<div class="row">
    <div class="box col-xs-12">
        <section id="tplFile" class="box wf-100"
            data-ui-content=".inner"
            data-ui-element="#tplFile .textContent"
            data-tag="form"
            data-method="POST"
            data-uri="<?= \phpOMS\Uri\UriFactory::build('{/api}cms?{?}&csrf={$CSRF}'); ?>">
            <div class="inner">
                <div class="vC">
                    <button class="save hidden"><?= $this->getHtml('Save', '0', '0') ?></button>
                    <button class="cancel hidden"><?= $this->getHtml('Cancel', '0', '0') ?></button>
                    <button class="update"><?= $this->getHtml('Edit', '0', '0') ?></button>
                </div>
                <!-- if markdown show markdown editor, if image show image editor, if text file show textarea only on edit -->

                <template></template><!-- todo: this is required because of selectorLength + i in Form.js = first element = add template, second element = edit element. Fix -->
                <template>
                <textarea class="textContent" data-tpl-text="/cms/template" data-tpl-value="/cms/template" data-marker="tpl" name="template"></textarea>
                </template>
                <pre class="textContent" data-tpl-text="/cms/template" data-tpl-value="/cms/template"><?= $this->printHtml(\file_get_contents($template)); ?></pre>
            </div>
        </section>
    </div>
</div>