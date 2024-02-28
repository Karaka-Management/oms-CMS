<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Web
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);
use phpOMS\Uri\UriFactory;

/** @var \phpOMS\Views\View $this */
$rules = $this->data['rules'] ?? [];
?>

<?php if (!empty($rules)) : ?>
<input type="checkbox" id="cookie_cc" class="oms-ui-state" name="cookie_consent" value="1" checked>
<div id="cookieCF">
    <form method="POST" action="<?= UriFactory::build('{/api}cookie'); ?>">
        <h1><?= $this->getHtml('CookieConsent', 'CMS', 'Backend'); ?></h1>
        <div class="content"><?= $this->getHtml('CookieExplanation', 'CMS', 'Backend'); ?></div>

        <input type="hidden" name="app" value="Backend">

        <input id="cookieCF_simple" name="customize" type="radio" checked>
        <div>
            <div id="cookieCF_db">
                <label for="cookie_cc"><input type="submit" name="accept_all" value="<?= $this->getHtml('AcceptAll', 'CMS', 'Backend'); ?>"></label>
                <label for="cookie_cc"><input class="secondary" type="submit" name="reject_required" value="<?= $this->getHtml('AcceptRequired', 'CMS', 'Backend'); ?>"></label>
                <label for="cookieCF_c" class="button secondary"><?= $this->getHtml('Customize', 'CMS', 'Backend'); ?></label>
            </div>
        </div>

        <input id="cookieCF_c" name="customize" type="radio">
        <div>
            <div>
                <?php foreach ($rules as $id => $rule) : ?>
                    <div class="consent_element">
                        <label class="checkbox" for="R-<?= $this->printHtml($id); ?><?= $rule['required'] ? '-r' : ''; ?>">
                            <input id="R-<?= $this->printHtml($id); ?>" type="checkbox" name="cookie_consent_rules[]" value="<?= $id; ?>"<?= $rule['required'] ? ' required' : ''; ?><?= $rule['checked'] || $rule['required'] ? ' checked' : ''; ?>>
                            <span class="checkmark"></span>
                            <?= $this->printHtml($rule['l11n'][$this->request->header->l11n->language]['name']); ?>
                        </label>
                        <div><?= $this->printHtml($rule['l11n'][$this->request->header->l11n->language]['description']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="cookieCF_cb">
                <label for="cookie_cc"><input type="submit" name="accept_all" value="<?= $this->getHtml('AcceptAll', 'CMS', 'Backend'); ?>"></label>
                <label for="cookie_cc"><input class="secondary" type="submit" name="reject_required" value="<?= $this->getHtml('AcceptRequired', 'CMS', 'Backend'); ?>"></label>
                <label for="cookie_cc"><input class="secondary" type="submit" name="customized" value="<?= $this->getHtml('Submit', 'CMS', 'Backend'); ?>"></label>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>
