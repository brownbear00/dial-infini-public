<?php
/**
 * Dial Infini — core bootstrap: paths, storage, helpers.
 * Data is stored in flat JSON files (data/*.json.php) – no database required.
 */
if (!defined('ROOT')) define('ROOT', dirname(__DIR__));
define('DATA_DIR', ROOT . '/data');
define('UPLOAD_DIR', ROOT . '/uploads');
date_default_timezone_set('UTC');
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', '0');

require_once __DIR__ . '/schema.php';

/* ---------- storage ---------- */
function store_path($name) { return DATA_DIR . '/' . preg_replace('/[^a-z0-9_-]/i', '', $name) . '.json.php'; }
function store_read($name, $default = []) {
    $f = store_path($name);
    if (!is_file($f)) return $default;
    $raw = file_get_contents($f);
    $raw = preg_replace('/^<\?php[^\n]*\?>\R?/', '', $raw);
    $d = json_decode($raw, true);
    return is_array($d) ? $d : $default;
}
function store_write($name, $data) {
    if (!is_dir(DATA_DIR)) @mkdir(DATA_DIR, 0755, true);
    $out = "<?php http_response_code(403); exit; ?>\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents(store_path($name), $out, LOCK_EX) !== false;
}

/* ---------- base url / routing ---------- */
function base_path() {
    static $b = null;
    if ($b !== null) return $b;
    $doc = rtrim(str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: ($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $root = str_replace('\\', '/', realpath(ROOT));
    if ($doc && strpos($root, $doc) === 0) $b = substr($root, strlen($doc));
    else $b = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/x/y'))), '/');
    if ($b === '.' || $b === '\\') $b = '';
    return $b;
}
function asset($p) {
    if (!$p) return '';
    if (preg_match('#^(https?:)?//#', $p) || $p[0] === '#' || strpos($p, 'data:') === 0) return $p;
    return base_path() . '/' . ltrim($p, '/');
}
function site_origin() {
    $u = trim(setting('site_url'));
    if ($u) return rtrim($u, '/');
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    return ($https ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . base_path();
}
function url($page = '') {
    $page = trim($page, '/');
    $clean = setting('clean_urls');
    if ($page === '' || $page === 'home' || $page === 'index') return base_path() . '/';
    return base_path() . '/' . ($clean ? $page : $page . '.php');
}
/** Resolve a user-entered link: "/pricing" => internal page, others untouched. */
function href($l) {
    $l = trim((string)$l);
    if ($l === '') return '#';
    if ($l[0] === '/' && substr($l, 0, 2) !== '//') return url(substr($l, 1));
    return $l;
}

/* ---------- escaping & rich text ---------- */
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
/** Escapes text; *word* becomes a highlighted span. */
function rich($s) {
    $s = e($s);
    return preg_replace('/\*(.+?)\*/s', '<span class="hl">$1</span>', $s);
}
function nl($s) { return nl2br(e($s)); }
function lines($s) { return array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$s)), 'strlen')); }

/* ---------- settings & content ---------- */
function all_settings() {
    static $s = null;
    if ($s !== null) return $s;
    $s = [];
    foreach (settings_schema() as $grp) foreach ($grp['fields'] as $k => $f) $s[$k] = $f['default'] ?? '';
    foreach (store_read('settings') as $k => $v) $s[$k] = $v;
    return $s;
}
function setting($k) { $s = all_settings(); return $s[$k] ?? ''; }
function reset_settings_cache() { /* statics can't be reset; callers redirect after save */ }

function page_content($page) {
    static $cache = [];
    if (isset($cache[$page])) return $cache[$page];
    $schema = pages_schema();
    $saved = store_read('content');
    $out = [];
    foreach ($schema[$page]['sections'] ?? [] as $sk => $sec) {
        foreach ($sec['fields'] as $fk => $f) {
            $out[$sk][$fk] = $saved[$page][$sk][$fk] ?? ($f['default'] ?? '');
        }
    }
    return $cache[$page] = $out;
}
/** c('home.hero.title') */
function c($path) {
    static $current = null;
    [$p, $s, $f] = explode('.', $path) + [null, null, null];
    $d = page_content($p);
    return $d[$s][$f] ?? '';
}

function seo_for($page) {
    $schema = pages_schema();
    $saved = store_read('seo');
    $out = [];
    foreach (seo_fields() as $k => $f) $out[$k] = $saved[$page][$k] ?? ($schema[$page]['seo'][$k] ?? ($f['default'] ?? ''));
    return $out;
}

/* ---------- form field collection (used by admin) ---------- */
function collect_fields($fields, $input) {
    $out = [];
    foreach ($fields as $k => $f) {
        $v = $input[$k] ?? null;
        switch ($f['type']) {
            case 'toggle': $out[$k] = !empty($v) ? 1 : 0; break;
            case 'list':
                $items = [];
                if (is_array($v)) foreach ($v as $row) {
                    if (!is_array($row)) continue;
                    $item = collect_fields($f['item'], $row);
                    $has = false; foreach ($item as $iv) if ($iv !== '' && $iv !== 0) $has = true;
                    if ($has) $items[] = $item;
                }
                $out[$k] = $items; break;
            case 'html': $out[$k] = is_string($v) ? $v : ''; break;
            case 'password': $out[$k] = is_string($v) ? $v : ''; break;
            default: $out[$k] = is_string($v) ? trim(str_replace("\r\n", "\n", $v)) : '';
        }
    }
    return $out;
}

/* ---------- misc ---------- */
function json_out($d, $code = 200) { http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode($d); exit; }
function client_ip() { return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; }
function slugify($s) { $s = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $s), '-')); return $s ?: 'file'; }
