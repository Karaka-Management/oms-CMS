<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\CMS
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View         $this
 * @var \Modules\CMS\Models\Page[] $list
 */
$list = $this->data['list'] ?? [];

$previous = empty($list) ? '{/base}/cms/application/post?{?}' : '{/base}/cms/application/post?{?}&offset=' . \reset($list)->id . '&ptype=p';
$next     = empty($list) ? '{/base}/cms/application/post?{?}' : '{/base}/cms/application/post?{?}&offset=' . \end($list)->id . '&ptype=n';

echo $this->data['nav']->render();
?>
<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Posts'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="postList" class="default sticky">
                <thead>
                <tr>
                    <td>
                    <td class="wf-100"><?= $this->getHtml('ID', '0', '0'); ?>
                <tbody>
                <?php $count = 0;
                    foreach ($list as $key => $post) : ++$count;
                        $url = UriFactory::build('{/base}/cms/application/post?{?}&id=' . $post->id); ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td>
                        <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($post->name); ?></a>
                <?php endforeach; ?>
                <?php if ($count === 0) : ?>
                <tr><td colspan="3" class="empty"><?= $this->getHtml('Empty', '0', '0'); ?>
                <?php endif; ?>
            </table>
            </div>
            <!--
            <div class="portlet-foot">
                <a tabindex="0" class="button" href="<?= UriFactory::build($previous); ?>"><?= $this->getHtml('Previous', '0', '0'); ?></a>
                <a tabindex="0" class="button" href="<?= UriFactory::build($next); ?>"><?= $this->getHtml('Next', '0', '0'); ?></a>
            </div>
            -->
        </section>
    </div>
</div>
