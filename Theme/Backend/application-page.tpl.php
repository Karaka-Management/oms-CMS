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
use phpOMS\Utils\Parser\Markdown\Markdown;
use phpOMS\Localization\ISO639Enum;
use Modules\CMS\Models\NullPage;

/**
 * @var \phpOMS\Views\View       $this
 * @var \Modules\CMS\Models\Page $page
 */
$page = $this->getData('page');
$l11ns = $page->getL11ns();

$languages = [];
$l11nNames = [];
$isNewPage = $page instanceof NullPage;

echo $this->getData('nav')->render();
?>
<div class="row">
    <div class="col-xs-12 col-md-9">
        <div id="testEditor" class="m-editor">
            <section class="portlet">
                <div class="portlet-body">
                    <input id="iTitle" type="text" name="title" form="docForm" value="<?= $page->name; ?>" autocomplete="off">
                </div>
            </section>

            <?php
            	foreach ($l11ns as $l11n) :
                    if (!\in_array($l11n->getLanguage(), $languages)) {
                        $languages[] = $l11n->getLanguage();
                    }

                    if (!\in_array($l11n->name, $l11nNames)) {
                        $l11nNames[] = $l11n->name;
                    }

            		if ($l11n->getLanguage() === $this->response->getLanguage()) :
            ?>
	            <section class="portlet">
	                <div class="portlet-body">
	                    <?= $this->getData('editor')->render('iNews'); ?>
	                </div>
	            </section>

	            <div class="box wf-100">
	            <?= $this->getData('editor')->getData('text')->render('iNews', 'plain', 'docForm', $l11n->content, Markdown::parse($l11n->content)); ?>
	            </div>
        	<?php endif; endforeach; ?>
        </div>
    </div>

    <div class="col-xs-12 col-md-3">
        <div class="box">
             <table class="layout wf-100">
                <tr>
                    <?php if (!$isNewPage) : ?>
                        <td>
                        <input class="cancel" type="submit" name="deleteButton" id="iDeleteButton" value="<?= $this->getHtml('Delete', '0', '0'); ?>">
                        <td class="rightText">
                        <input type="submit" name="saveButton" id="iSaveButton" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                    <?php else: ?>
                    <td class="rightText">
                        <input class="create" type="submit" name="saveButton" id="iSaveButton" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                    <?php endif; ?>
            </table>
        </div>

        <section class="portlet">
            <form id="docForm"
                method="<?= $isNewPage ? 'PUT' : 'POST'; ?>"
                action="<?= UriFactory::build('{/api}news?' . ($isNewPage ? '' : 'id={?id}&') . 'csrf={$CSRF}'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Content'); ?></div>
                <div class="portlet-body">
                    <?php foreach ($languages as $language) : ?>
                        <div class="form-group">
                            <label class="radio" for="iLanguage">
                                <input type="radio" name="type" id="iLanguage" form="docForm" value="<?= $language ?>"<?= ($this->request->getData('lang') ?? $this->request->getLanguage()) === $language ? ' checked' : ''; ?>>
                                <span class="checkmark"></span>
                                <?= $this->printHtml($language); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </form>
        </section>

        <section class="portlet">
            <form id="docForm"
                method="<?= $isNewPage ? 'PUT' : 'POST'; ?>"
                action="<?= UriFactory::build('{/api}news?' . ($isNewPage ? '' : 'id={?id}&') . 'csrf={$CSRF}'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Localization'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <div class="ipt-wrap">
                            <div class="ipt-first">
                                <select id="iLanguages" name="settings_language">
                                    <?php
                                        $availableLanguages = ISO639Enum::getConstants();
                                        foreach ($availableLanguages as $code => $language) :
                                            $code = \strtolower(\substr($code, 1));
                                            if (\in_array($code, $languages)) {
                                                continue;
                                            }
                                        ?>
                                            <option value="<?= $this->printHtml($code); ?>"><?= $this->printHtml($language); ?>
                                        <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ipt-second">
                                <input type="submit" name="deleteButton" id="iDeleteButton" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="ipt-wrap">
                            <div class="ipt-first">
                                <select id="iLanguages" name="settings_language">
                                    <?php
                                        foreach ($availableLanguages as $code => $language) :
                                            $code = \strtolower(\substr($code, 1));
                                            if (!\in_array($code, $languages)) {
                                                continue;
                                            }
                                        ?>
                                            <option value="<?= $this->printHtml($code); ?>"><?= $this->printHtml($language); ?>
                                        <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ipt-second">
                                <input class="cancel" type="submit" name="deleteButton" id="iDeleteButton" value="<?= $this->getHtml('Delete', '0', '0'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>

        <section class="portlet">
            <form id="docForm"
                method="<?= $isNewPage ? 'PUT' : 'POST'; ?>"
                action="<?= UriFactory::build('{/api}news?' . ($isNewPage ? '' : 'id={?id}&') . 'csrf={$CSRF}'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Element'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <div class="ipt-wrap">
                            <div class="ipt-first">
                                <input type="text">
                            </div>
                            <div class="ipt-second">
                                <input type="submit" name="deleteButton" id="iDeleteButton" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="ipt-wrap">
                            <div class="ipt-first">
                                <select id="iLanguages" name="settings_language">
                                    <?php foreach ($l11nNames as $l11n) : ?>
                                        <option value="<?= $this->printHtml($l11n); ?>"><?= $this->printHtml($l11n); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ipt-second">
                                <input class="cancel" type="submit" name="deleteButton" id="iDeleteButton" value="<?= $this->getHtml('Delete', '0', '0'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
