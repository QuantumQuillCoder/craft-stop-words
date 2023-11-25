<?php

namespace quantumquillcoder\craftstopwords;

use Craft;
use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\helpers\ElementHelper;
use quantumquillcoder\craftstopwords\helpers\SlugHelper;
use quantumquillcoder\craftstopwords\models\Settings;
use yii\base\Event;

/**
 * stop-words plugin
 *
 * @method static StopWords getInstance()
 * @method Settings getSettings()
 * @author QuantumQuillCoder <fruite.simple0w@icloud.com>
 * @copyright QuantumQuillCoder
 * @license https://craftcms.github.io/license/ Craft License
 */

class StopWords extends Plugin
{
    public static $plugin;
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
        });
    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        $data = $this->getTemplateVariables();
        return Craft::$app->view->renderTemplate('stop-words/_settings.twig', $data);
    }

    private function attachEventHandlers(): void
    {
        Event::on(
            Entry::class,
            Element::EVENT_BEFORE_SAVE,
            function(ModelEvent $event) {
                $entry = $event->sender;
    
                if (!ElementHelper::isDraftOrRevision($entry) && isset($entry->sectionId)) {
                    $templateVariables = $this->getTemplateVariables();
                    
                    if (isset($entry->section) && isset($entry->site) && isset($entry->slug)) {
                        $selectedHandles = array_column(
                            array_filter($templateVariables['sections'], fn($section) => $section['checked']),
                            'value'
                        );
                        if (in_array($entry->section->handle, $selectedHandles)) {
                            $lang = explode('-', $entry->site->language)[0];
                            $entry->slug = SlugHelper::removeStopWords($entry->slug, $lang) ?: ElementHelper::tempSlug();
                        }
                    }
                }
            },
            null,
            true
        );
    }

    public function getTemplateVariables()
    {
        $allSections = Craft::$app->sections->getAllSections();
        $enabledSections = StopWords::$plugin->getSettings()->enabledSections;
        $sectionOptions = [];
        foreach ($allSections as $section) {
            $isChecked = !is_string($enabledSections) ? in_array($section['handle'], $enabledSections) : false;
            $sectionOptions[] = ['label' => $section['name'], 'value' => $section['handle'], 'checked' => $isChecked];
        }
        $result = ['sections' => $sectionOptions];
        return $result;
    }
}
