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
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View         $this
 * @var \Modules\CMS\Models\Page[] $list
 */
$list = $this->getData('list') ?? [];

$previous = empty($list) ? '{/lang}/{/app}/cms/application/page?{?}' : '{/lang}/{/app}/cms/application/page?{?}&id=' . \reset($list)->getId() . '&ptype=p';
$next     = empty($list) ? '{/lang}/{/app}/cms/application/page?{?}' : '{/lang}/{/app}/cms/application/page?{?}&id=' . \end($list)->getId() . '&ptype=n';

echo $this->getData('nav')->render();
?>
<div class="row">
    <div class="col-xs-12">
        <div class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Pages'); ?><i class="fa fa-download floatRight download btn"></i></div>
            <div class="slider">
            <table id="pageList" class="default sticky">
                <thead>
                <tr>
                    <td>
                    <td class="wf-100"><?= $this->getHtml('ID', '0', '0'); ?>
                <tbody>
                <?php $count = 0;
                    foreach ($list as $key => $page) : ++$count;
                        $url = UriFactory::build('{/lang}/{/app}/cms/application/page?{?}&id=' . $page->getId()); ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td>
                        <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($page->name); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
        </div>
    </div>
</div>
