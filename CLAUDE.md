# MyAdmin Parallels Licensing Plugin

PHP plugin integrating Parallels Plesk/Virtuozzo license management into MyAdmin via the KA (Key Administrator) API.

## Commands

```bash
composer install                  # install deps
composer test                     # run all tests
composer test tests/PluginTest.php        # plugin class tests
composer test tests/ParallelsIncTest.php  # inc function tests
```

## Architecture

- **Plugin class**: `src/Plugin.php` · namespace `Detain\MyAdminParallels` · registers event hooks via `getHooks()`
- **Functions**: `src/parallels.inc.php` · procedural · exports `activate_parallels()`, `deactivate_parallels()`, `deactivate_parallels_by_key()`
- **KA API client**: `\Detain\Parallels\Parallels` (from `detain/parallels-licensing`) · methods: `createKey()`, `getKeyNumbers()`, `terminateKey()`, `retrieveKey()`, `getKeyInfo()`, `getAvailableUpgrades()`, `getAvailableKeyTypesAndFeatures()`
- **CLI scripts**: `bin/parallels.php`, `bin/parallels_getKeyInfo.php`, `bin/parallels_getKeyNumbers.php`, `bin/parallels_retrieveKey.php`, `bin/parallels_getAvailableUpgrades.php`, `bin/parallels_getAvailableKeyTypesAndFeatures.php` · each requires `../../../../include/functions.inc.php`
- **Tests**: `tests/PluginTest.php`, `tests/ParallelsIncTest.php` · bootstrap stubs in `tests/bootstrap.php`
- **Config**: `phpunit.xml.dist` · autoload: `Detain\MyAdminParallels\` → `src/`, `Detain\MyAdminParallels\Tests\` → `tests/`
- **CI/Workflows**: `.github/` contains GitHub Actions workflows for automated testing and deployment pipelines
- **IDE Config**: `.idea/` contains project-specific IDE settings including `inspectionProfiles/`, `deployment.xml`, and `encodings.xml`

## Hook Pattern

```php
// In Plugin::getHooks()
self::$module.'.activate' => [__CLASS__, 'getActivate'],

// Handler — always static, accepts GenericEvent
public static function getActivate(GenericEvent $event) {
    $serviceClass = $event->getSubject();
    if ($event['category'] == get_service_define('PARALLELS')) {
        myadmin_log(self::$module, 'info', 'Message', __LINE__, __FILE__, self::$module, $serviceClass->getId());
        function_requirements('activate_parallels');
        // ... call inc function ...
        $event->stopPropagation();
    }
}
```

## Inc Function Pattern

```php
// In src/parallels.inc.php
function activate_parallels($ipAddress, $type, $addons = '') {
    myadmin_log('licenses', 'info', "message", __LINE__, __FILE__);
    $parallels = new \Detain\Parallels\Parallels();
    try {
        $response = $parallels->createKey($type, $addonsArray, $ipAddress);
        request_log('licenses', false, __FUNCTION__, 'parallels', 'createKey', $request, $response);
    } catch (\XML_RPC2_CurlException $e) {
        request_log('licenses', false, __FUNCTION__, 'parallels', 'createKey', $request, $e->getMessage());
        return false;
    }
    return $response;
}
```

## Test Bootstrap Stubs

`tests/bootstrap.php` stubs: `myadmin_log()`, `get_service_define()`, `function_requirements()`, `request_log()`, `get_module_settings()`. Add new stubs here when `src/` code calls additional MyAdmin globals.

## Conventions

- Module name: `licenses` (used in `myadmin_log()` and `Plugin::$module`)
- Always call `$event->stopPropagation()` after handling in event handlers
- Check `$event['category'] == get_service_define('PARALLELS')` before acting
- Log both before and after KA API calls using `myadmin_log()` + `request_log()`
- Deactivation writes termination state to `../../../../include/config/plesk.json`
- Settings registered via `$settings->add_text_setting()` / `add_password_setting()` / `add_dropdown_setting()`

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md .agents/ .opencode/ 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->
