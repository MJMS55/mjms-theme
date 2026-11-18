<?php

declare(strict_types=1);

namespace OCA\MjmsTheme\AppInfo;

use OCA\MjmsTheme\Listener\ThemeListener;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Http\Events\BeforeLoginTemplateRenderedEvent;
use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;

final class Application extends App implements IBootstrap {
    public const APP_ID = 'mjms_theme';

    public function __construct() {
        parent::__construct(self::APP_ID);
    }

    public function register(IRegistrationContext $context): void {
        $context->registerEventListener(BeforeTemplateRenderedEvent::class, ThemeListener::class);
        $context->registerEventListener(BeforeLoginTemplateRenderedEvent::class, ThemeListener::class);
    }

    public function boot(IBootContext $context): void {
    }
}
