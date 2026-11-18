<?php

declare(strict_types=1);

namespace OCA\MjmsTheme\Listener;

use OCA\MjmsTheme\AppInfo\Application;
use OCP\AppFramework\Http\Events\BeforeLoginTemplateRenderedEvent;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\IConfig;
use OCP\Util;

/** @implements IEventListener<BeforeTemplateRenderedEvent|BeforeLoginTemplateRenderedEvent> */
final class ThemeListener implements IEventListener {
    private bool $loaded = false;

    public function __construct(private IConfig $config) {
    }

    public function handle(Event $event): void {
        if ($this->loaded || !($event instanceof BeforeTemplateRenderedEvent || $event instanceof BeforeLoginTemplateRenderedEvent)) {
            return;
        }

        Util::addStyle(Application::APP_ID, 'institutional');
        Util::addStyle(Application::APP_ID, 'workspace');
        if ($this->config->getAppValue(Application::APP_ID, 'compact', 'yes') === 'yes') {
            Util::addStyle(Application::APP_ID, 'compact');
        }
        if ($this->config->getAppValue(Application::APP_ID, 'modern_icons', 'yes') === 'yes') {
            Util::addStyle(Application::APP_ID, 'icons');
            Util::addStyle(Application::APP_ID, 'command-icons');
        }
        $this->loaded = true;
    }
}
