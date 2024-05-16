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

use phpOMS\Localization\ISO639Enum;
use phpOMS\Uri\UriFactory;
use phpOMS\Utils\Parser\Markdown\Markdown;

/**
 * @var \phpOMS\Views\View       $this
 * @var \Modules\CMS\Models\Page $page
 */
$page  = $this->data['page'];
$l11ns = $page->getL11ns();

$languages = [];
$l11nNames = [];
$isNew     = $page->id === 0;

echo $this->data['nav']->render();
?>
<div class="row">
    <div class="col-xs-12< col-md-9">
        <div id="testEditor" class="m-editor">
            <section class="portlet">
                <div class="portlet-body">
                    <form id="fCms" method="<?= $isNew ? 'PUT' : 'POST'; ?>" action="<?= UriFactory::build('{/api}cms/application/page?{?}&csrf={$CSRF}'); ?>">
                        <input id="iTitle" type="text" name="name" value="<?= $page->name; ?>" autocomplete="off">
                    </form>
                </div>
            </section>

            <?php
                foreach ($l11ns as $l11n) :
                    if (!\in_array($l11n->language, $languages)) {
                        $languages[] = $l11n->language;
                    }

                    if (!\in_array($l11n->name, $l11nNames)) {
                        $l11nNames[] = $l11n->name;
                    }

                    if ($l11n->language === ($this->request->getDataString('lang') ?? $this->response->header->l11n->language)) :
            ?>
                <section class="portlet">
                    <div class="portlet-body">
                        <?= $this->data['editor']->render('iPage'); ?>
                    </div>
                </section>

                <div class="box wf-100">
                    <?= $this->data['editor']
                        ->getData('text')
                        ->render('iPage', 'content', 'fCms', $l11n->content, Markdown::parse($l11n->content));
                    ?>
                </div>
            <?php endif; endforeach; ?>
        </div>
    </div>

    <div class="col-xs-12 col-md-3">
        <div class="box">
             <table class="layout wf-100">
                <tr>
                    <?php if (!$isNew) : ?>
                        <td>
                            <input class="cancel" type="submit" name="deleteButton" id="iDeleteButton" form="fCms" value="<?= $this->getHtml('Delete', '0', '0'); ?>">
                        <td class="rT">
                            <input type="submit" name="saveButton" id="iSaveButton" form="fCms" value="<?= $this->getHtml('Save', '0', '0'); ?>">
                    <?php else: ?>
                    <td class="rT">
                        <input class="create" type="submit" name="saveButton" id="iSaveButton" form="fCms" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                    <?php endif; ?>
            </table>
        </div>

        <?php if (!$isNew) : ?>
        <section class="portlet">
            <div class="portlet-head"><?= $this->getHtml('Content'); ?></div>
            <div class="portlet-body">
                <input type="hidden" name="id" value="<?= $page->id; ?>">
                <?php foreach ($languages as $language) : ?>
                    <div class="form-group">
                        <label class="radio" for="iLanguage-<?= $language; ?>">
                            <input
                                id="iLanguage-<?= $language; ?>"
                                type="radio"
                                name="language"
                                form="fCms"
                                value="<?= $language; ?>"
                                data-action='[
                                    {
                                        "listener": "change", "action": [
                                            {
                                                "key": 1, "type": "redirect", "uri": "{%}&lang={![name=\"language\"]:checked}", "target": "self"
                                            }
                                        ]
                                    }
                                ]'
                                <?= ($this->request->getData('lang') ?? $this->request->header->l11n->language) === $language ? ' checked' : ''; ?>>
                            <span class="checkmark"></span>
                            <?= $this->printHtml($language); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="portlet">
            <form id="localizationForm"
                method="<?= $isNew ? 'PUT' : 'POST'; ?>"
                action="<?= UriFactory::build('{/api}cms/application/page?' . ($isNew ? '' : 'id={?id}&') . 'csrf={$CSRF}'); ?>">
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
                                        <option value="<?= $this->printHtml($code); ?>"<?= $code === $this->response->header->l11n->language ? ' selected' : ''; ?>><?= $this->printHtml($language); ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ipt-second">
                                <input type="submit" name="createButton" id="iCreateButton" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($languages)) : ?>
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
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="portlet">
            <form id="elementForm"
                method="<?= $isNew ? 'PUT' : 'POST'; ?>"
                action="<?= UriFactory::build('{/api}cms/application/app?' . ($isNew ? '' : 'id={?id}&') . 'csrf={$CSRF}'); ?>">
                <div class="portlet-head"><?= $this->getHtml('Element'); ?></div>
                <div class="portlet-body">
                    <div class="form-group">
                        <div class="ipt-wrap">
                            <div class="ipt-first">
                                <input type="text">
                            </div>
                            <div class="ipt-second">
                                <input type="submit" name="createButton" id="iCreateButton" value="<?= $this->getHtml('Create', '0', '0'); ?>">
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($l11nNames)) : ?>
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
                    <?php endif; ?>
                </div>
            </form>
        </section>
        <?php endif; ?>
    </div>
</div>
