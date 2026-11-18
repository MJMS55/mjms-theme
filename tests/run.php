<?php

declare(strict_types=1);

// Isolated contract tests. No Nextcloud instance or third-party test runner needed.
namespace OCP\EventDispatcher {
    class Event {}
    interface IEventListener { public function handle(Event $event): void; }
}
namespace OCP\AppFramework\Http\Events {
    class BeforeTemplateRenderedEvent extends \OCP\EventDispatcher\Event {
        public function __construct(private bool $loggedIn) {}
        public function isLoggedIn(): bool { return $this->loggedIn; }
    }
}
namespace OCP {
    interface IConfig { public function getAppValue(string $app, string $key, string $default = ''): string; }
    class Util {
        public static array $styles = [];
        public static function addStyle(string $app, ?string $file = null): void {
            self::$styles[] = [$app, $file];
        }
    }
}
namespace OCP\AppFramework {
    class App { public function __construct(public string $id) {} }
}
namespace OCP\AppFramework\Bootstrap {
    interface IBootContext {}
    interface IRegistrationContext { public function registerEventListener(string $event, string $listener): void; }
    interface IBootstrap {
        public function register(IRegistrationContext $context): void;
        public function boot(IBootContext $context): void;
    }
}
namespace {
    use OCA\MjmsTheme\AppInfo\Application;
    use OCA\MjmsTheme\Listener\ThemeListener;
    use OCP\AppFramework\Http\Events\BeforeTemplateRenderedEvent;
    use OCP\EventDispatcher\Event;
    use OCP\Util;

    require __DIR__ . '/../lib/AppInfo/Application.php';
    require __DIR__ . '/../lib/Listener/ThemeListener.php';

    function check(bool $condition, string $message): void {
        if (!$condition) { throw new \RuntimeException($message); }
        echo "PASS: $message\n";
    }
    function config(string $compact = 'yes', string $icons = 'yes'): \OCP\IConfig {
        return new class($compact, $icons) implements \OCP\IConfig {
            public function __construct(private string $compact, private string $icons) {}
            public function getAppValue(string $app, string $key, string $default = ''): string {
                check($app === 'mjms_theme' && in_array($key, ['compact', 'modern_icons'], true), 'Configuration uses the correct app and key');
                return $key === 'compact' ? $this->compact : $this->icons;
            }
        };
    }

    $context = new class implements \OCP\AppFramework\Bootstrap\IRegistrationContext {
        public array $listeners = [];
        public function registerEventListener(string $event, string $listener): void { $this->listeners[$event] = $listener; }
    };
    $app = new Application();
    $app->register($context);
    check($app->id === 'mjms_theme', 'Bootstrap uses the installable app ID');
    check(($context->listeners[BeforeTemplateRenderedEvent::class] ?? '') === ThemeListener::class, 'Template listener registered');

    $listener = new ThemeListener(config());
    $listener->handle(new Event());
    $listener->handle(new BeforeTemplateRenderedEvent(false));
    check(Util::$styles === [], 'Unrelated and anonymous requests load no theme');
    $listener->handle(new BeforeTemplateRenderedEvent(true));
    check(Util::$styles === [['mjms_theme', 'institutional'], ['mjms_theme', 'compact'], ['mjms_theme', 'icons'], ['mjms_theme', 'command-icons']], 'Authenticated requests load both icon stylesheets');
    $listener->handle(new BeforeTemplateRenderedEvent(true));
    check(count(Util::$styles) === 4, 'Repeated rendering does not duplicate assets');

    foreach (['no', 'unexpected'] as $value) {
        Util::$styles = [];
        (new ThemeListener(config($value)))->handle(new BeforeTemplateRenderedEvent(true));
        check(Util::$styles === [['mjms_theme', 'institutional'], ['mjms_theme', 'icons'], ['mjms_theme', 'command-icons']], "Compact disabled for value: $value");
    }

    $root = dirname(__DIR__);
    $xml = simplexml_load_file($root . '/appinfo/info.xml');
    check($xml !== false && (string)$xml->id === Application::APP_ID, 'Manifest app ID matches bootstrap');
    check((string)$xml->dependencies->nextcloud['min-version'] === '35' && (string)$xml->dependencies->nextcloud['max-version'] === '36', 'Manifest targets Nextcloud 35 through 36');
    Util::$styles = [];
    (new ThemeListener(config('yes', 'no')))->handle(new BeforeTemplateRenderedEvent(true));
    check(Util::$styles === [['mjms_theme', 'institutional'], ['mjms_theme', 'compact']], 'Native icon option omits replacement stylesheet');
    foreach (['institutional', 'compact', 'icons', 'command-icons'] as $asset) {
        check(is_file($root . '/css/' . $asset . '.css'), "Stylesheet exists: $asset");
    }
    $css = file_get_contents($root . '/css/icons.css') . file_get_contents($root . '/css/command-icons.css');
    preg_match_all("~url\\('\\.\\./(img/icons/[^']+)'\\)~", $css, $matches);
    $assets = array_unique($matches[1]);
    check(count($assets) > 15, 'Application and command icon mappings have bundled assets');
    foreach ($assets as $path) {
        $svg = simplexml_load_file($root . '/' . $path);
        check($svg !== false && (string)$svg['viewBox'] === '0 0 24 24', 'Valid SVG: ' . basename($path));
    }
    check(is_file($root . '/img/icons/LICENSE'), 'Lucide license included');
    echo "All isolated contract tests passed. Server integration is not covered.\n";
}
