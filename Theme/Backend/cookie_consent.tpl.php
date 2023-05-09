<?php
/**
 * Karaka
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
$rules = $this->getData('rules') ?? [];
?>

<?php if (!empty($rules)) : ?>
<input type="checkbox" id="cookie_consent_checkbox" class="oms-ui-state" name="cookie_consent" value="1" checked>
<div id="cookieConsentForm">
    <form method="POST" action="<?= UriFactory::build('{/api}cookie'); ?>">
        <h1><?= $this->getHtml('CookieConsent', 'CMS', 'Backend'); ?></h1>
        <div class="content"><?= $this->getHtml('CookieExplanation', 'CMS', 'Backend'); ?></div>

        <input type="hidden" name="app" value="Backend">

        <input id="cookieConsentForm_simple" name="customize" type="radio" checked>
        <div>
            <div id="cookieConsentForm_defaultbuttons">
                <label for="cookie_consent_checkbox"><input type="submit" name="accept_all" value="<?= $this->getHtml('AcceptAll', 'CMS', 'Backend'); ?>"></label>
                <label for="cookie_consent_checkbox"><input class="secondary" type="submit" name="reject_required" value="<?= $this->getHtml('AcceptRequired', 'CMS', 'Backend'); ?>"></label>
                <label for="cookieConsentForm_cutomize" class="button secondary"><?= $this->getHtml('Customize', 'CMS', 'Backend'); ?></label>
            </div>
        </div>

        <input id="cookieConsentForm_cutomize" name="customize" type="radio">
        <div>
            <div>
                <?php foreach ($rules as $id => $rule) : ?>
                    <div class="consent_element">
                        <label class="checkbox" for="R-<?= $this->printHtml($id); ?><?= $rule['required'] ? '-r' : '' ?>">
                            <input id="R-<?= $this->printHtml($id); ?>" type="checkbox" name="cookie_consent_rules[]" value="<?= $id; ?>"<?= $rule['required'] ? ' required' : '' ?><?= $rule['checked'] || $rule['required'] ? ' checked' : ''; ?>>
                            <span class="checkmark"></span>
                            <?= $this->printHtml($rule['l11n'][$this->request->getLanguage()]['name']); ?>
                        </label>
                        <div><?= $this->printHtml($rule['l11n'][$this->request->getLanguage()]['description']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="cookieConsentForm_customizedbuttons">
                <label for="cookie_consent_checkbox"><input type="submit" name="accept_all" value="<?= $this->getHtml('AcceptAll', 'CMS', 'Backend'); ?>"></label>
                <label for="cookie_consent_checkbox"><input class="secondary" type="submit" name="reject_required" value="<?= $this->getHtml('AcceptRequired', 'CMS', 'Backend'); ?>"></label>
                <label for="cookie_consent_checkbox"><input class="secondary" type="submit" name="customized" value="<?= $this->getHtml('Submit', 'CMS', 'Backend'); ?>"></label>
            </div>
        </div>
    </form>
</div>
<?php endif; ?>
