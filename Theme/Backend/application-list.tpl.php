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

use phpOMS\Uri\UriFactory;

/**
 * @var \phpOMS\Views\View                $this
 * @var \Modules\CMS\Models\Application[] $applications
 */
$applications = $this->data['applications'] ?? [];

$previous = empty($applications) ? '{/base}/cms/application/list' : '{/base}/cms/application/list?{?}&offset=' . \reset($applications)->id . '&ptype=p';
$next     = empty($applications) ? '{/base}/cms/application/list' : '{/base}/cms/application/list?{?}&offset=' . \end($applications)->id . '&ptype=n';

echo $this->data['nav']->render();
?>
<div class="row">
    <div class="col-xs-12">
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Applications'); ?><i class="g-icon download btn end-xs">download</i></div>
            <div class="slider">
            <table id="applicationList" class="default sticky">
                <thead>
                <tr>
                    <td>
                    <td class="wf-100"><?= $this->getHtml('Name'); ?>
                    <td><?= $this->getHtml('Link'); ?>
                <tbody>
                <?php $count = 0;
                    foreach ($applications as $key => $application) : ++$count;
                        $url = UriFactory::build('{/base}/cms/application/page/list?{?}&app=' . $application->id); ?>
                    <tr tabindex="0" data-href="<?= $url; ?>">
                        <td>
                        <td data-label="<?= $this->getHtml('Name'); ?>"><a href="<?= $url; ?>"><?= $this->printHtml($application->name); ?></a>
                        <td><a class="content" href="<?= UriFactory::build(\strtolower($application->name)); ?>"><?= $this->getHtml('Link'); ?></a>
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
