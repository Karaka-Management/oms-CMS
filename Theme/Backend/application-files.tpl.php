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

echo $this->getData('nav')->render();
?>
<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Templates'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <table id="applicationList" class="default">
                <thead>
                <tr>
                    <td>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                <tbody>
                <?php $count = 0; foreach ($templates as $key => $tpl) : ++$count;
                $url = UriFactory::build('{/prefix}cms/application/template?{?}&tpl=' . $tpl); ?>
                    <tr data-href="<?= $url; ?>">
                        <td>
                        <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($tpl); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
