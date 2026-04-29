<?php

declare(strict_types=1);

require_once __DIR__ . '/../bootstrap.php';

use App\Core\Env;
use App\Http\Controllers\GitHubWebhookController;

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
$docsBase = realpath(__DIR__ . '/../docs');

if ($path === '/webhook/github' && $method === 'POST') {
    (new GitHubWebhookController())->handle();
    exit;
}

if ($path === '/docs' && $method === 'GET') {
    renderDocsPage();
    exit;
}

if ($path === '/api/docs/list' && $method === 'GET') {
    jsonResponse(['files' => listMarkdownFiles($docsBase)], 200);
    exit;
}

if ($path === '/api/docs/raw' && $method === 'GET') {
    $name = sanitizeMarkdownFile((string) ($_GET['file'] ?? ''));
    if ($name === null || $docsBase === false) {
        jsonResponse(['error' => 'Arquivo invalido'], 400);
        exit;
    }
    $file = $docsBase . DIRECTORY_SEPARATOR . $name;
    if (!is_file($file)) {
        jsonResponse(['error' => 'Arquivo nao encontrado'], 404);
        exit;
    }
    header('Content-Type: text/markdown; charset=utf-8');
    echo (string) file_get_contents($file);
    exit;
}

if ($path === '/' && $method === 'GET') {
    jsonResponse([
        'name' => 'GitHub DevLog AI',
        'status' => 'ok',
        'environment' => Env::get('APP_ENV', 'local'),
        'documentation' => '/docs',
    ], 200);
    exit;
}

http_response_code(404);
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'error' => 'Not Found',
    'message' => 'Use GET /docs, GET /api/docs/list, GET /api/docs/raw?file=<nome.md>, POST /webhook/github',
], JSON_UNESCAPED_UNICODE);

function renderDocsPage(): void
{
    $logo = 'GHDL';
    header('Content-Type: text/html; charset=utf-8');
    echo <<<'HTML'
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>GitHub DevLog AI - Documentacao</title>
  <style>
    :root {
      --bg: #040711;
      --surface: #0b1224;
      --surface-soft: #111a33;
      --text: #eaf2ff;
      --muted: #9fb1d2;
      --border: rgba(148, 163, 184, 0.22);
      --primary: linear-gradient(110deg, #7c3aed, #0ea5e9);
      --primary-soft: rgba(124, 58, 237, 0.16);
    }
    * { box-sizing: border-box; }
    html, body {
      margin: 0;
      min-height: 100%;
      background: radial-gradient(circle at 20% 0%, #111e39 0%, #050812 55%, #040711 100%);
      color: var(--text);
      font-family: "Inter", "Segoe UI", Roboto, Arial, sans-serif;
    }
    body { display: grid; grid-template-rows: auto 1fr; }
    .header {
      padding: 22px 28px;
      display: flex;
      justify-content: space-between;
      gap: 16px;
      align-items: center;
      border-bottom: 1px solid var(--border);
      background: linear-gradient(120deg, rgba(124, 58, 237, 0.12), rgba(14, 165, 233, 0.12));
      backdrop-filter: blur(4px);
    }
    .title {
      display: flex; gap: 12px; align-items: center;
    }
    .logo {
      width: 42px; height: 42px; border-radius: 14px;
      display: grid; place-items: center;
      font-weight: 700;
      background: var(--primary);
      box-shadow: 0 8px 24px rgba(14,165,233,.28);
    }
    h1 { margin: 0; font-size: 24px; letter-spacing: 0.3px; }
    .header p { margin: 0; color: var(--muted); }
    .btn {
      border: 1px solid var(--border);
      background: var(--surface-soft);
      color: var(--text);
      border-radius: 999px;
      padding: 10px 16px;
      cursor: pointer;
      transition: .2s transform, .2s background;
    }
    .btn:hover { transform: translateY(-1px); background: var(--primary-soft); }
    .layout {
      padding: 18px;
      display: grid;
      grid-template-columns: 360px 1fr;
      gap: 16px;
      min-height: calc(100vh - 94px);
    }
    .panel {
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 16px;
      background: rgba(17, 26, 51, 0.68);
      box-shadow: 0 22px 50px rgba(3, 6, 23, 0.5);
      backdrop-filter: blur(8px);
      min-height: 0;
    }
    .search {
      width: 100%;
      border: 1px solid var(--border);
      border-radius: 12px;
      background: #050d1e;
      color: var(--text);
      padding: 11px 12px;
      margin-bottom: 14px;
    }
    .item {
      display: block;
      text-decoration: none;
      color: var(--text);
      border: 1px solid transparent;
      border-radius: 10px;
      padding: 10px 12px;
      margin-bottom: 8px;
      transition: .2s;
    }
    .item:hover { border-color: rgba(96, 165, 250, .6); background: rgba(96,165,250,.08);}
    .item.active { background: rgba(14,165,233,.16); border-color: rgba(56,189,248,.7);}
    .section-title {
      color: var(--muted);
      text-transform: uppercase;
      font-size: 11px;
      letter-spacing: 1.4px;
      margin: 6px 0 12px;
    }
    .viewer-title {
      font-size: 30px;
      line-height: 1.15;
      margin: 0 0 10px;
      background: linear-gradient(90deg, #e2e8f0, #93c5fd, #22d3ee);
      -webkit-background-clip: text;
      color: transparent;
    }
    .meta {
      color: var(--muted);
      font-size: 13px;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }
    .viewer {
      min-height: 0;
      display: grid;
      grid-template-rows: auto auto auto 1fr;
      gap: 10px;
    }
    #content {
      border-radius: 14px;
      background: #020817;
      border: 1px solid var(--border);
      padding: 16px;
      overflow: auto;
      min-height: 0;
      line-height: 1.55;
    }
    #content pre {
      white-space: pre-wrap;
      background: #0b1022;
      border-radius: 10px;
      padding: 12px;
      border: 1px solid rgba(148,163,184,.2);
      overflow: auto;
      color: #dbeafe;
    }
    .toolbar {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      flex-wrap: wrap;
      align-items: center;
      margin-top: 6px;
    }
    .status {
      color: var(--muted);
      font-size: 13px;
    }
    @media (max-width: 980px) {
      .layout { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <header class="header">
    <div class="title">
      <div class="logo">{$logo}</div>
      <div>
        <h1>GitHub DevLog AI</h1>
        <p>Leitor de documentacao com experiencia premium</p>
      </div>
    </div>
    <button class="btn" id="reload">Recarregar lista</button>
  </header>
  <main class="layout">
    <section class="panel">
      <div class="section-title">Arquivos</div>
      <input class="search" id="search" placeholder="Pesquisar documentos..." />
      <div id="files"></div>
    </section>
    <section class="panel viewer">
      <h2 class="viewer-title" id="docTitle">Selecione um documento</h2>
      <div class="meta" id="meta"></div>
      <div class="toolbar">
        <div class="status" id="status"></div>
        <button class="btn" id="copyBtn">Copiar markdown bruto</button>
      </div>
      <div id="content">Carregando a lista de documentos...</div>
    </section>
  </main>
  <script>
    const filesWrap = document.getElementById('files');
    const search = document.getElementById('search');
    const docTitle = document.getElementById('docTitle');
    const meta = document.getElementById('meta');
    const status = document.getElementById('status');
    const content = document.getElementById('content');
    const copyBtn = document.getElementById('copyBtn');
    const reloadBtn = document.getElementById('reload');
    const rawCache = new Map();
    let docs = [];
    let current = '';

    function formatSize(text) {
      const kb = Math.max(1, Math.round((new TextEncoder().encode(text).length / 1024) * 100) / 100);
      return `${kb} KB`;
    }

    function escapeHtml(text) {
      return String(text)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;');
    }

    function renderMarkdown(raw) {
      const escaped = escapeHtml(raw);
      return escaped
        .replace(/^### (.*)$/gm, '<h3>$1</h3>')
        .replace(/^## (.*)$/gm, '<h2>$1</h2>')
        .replace(/^# (.*)$/gm, '<h1>$1</h1>')
        .replace(/```([\\s\\S]*?)```/g, (m, code) => `<pre><code>${code.trim()}</code></pre>`)
        .replace(/\\n/g, '<br/>')
        .replace(/\\*\\*(.*?)\\*\\*/g, '<strong>$1</strong>')
        .replace(/\\*(.*?)\\*/g, '<em>$1</em>');
    }

    function renderList(filter = '') {
      const term = filter.toLowerCase();
      const filtered = docs.filter((file) => file.toLowerCase().includes(term));
      filesWrap.innerHTML = '';
      if (!filtered.length) {
        filesWrap.innerHTML = '<div style="color:#94a3b8">Nenhum documento encontrado.</div>';
        return;
      }
      for (const file of filtered) {
        const a = document.createElement('a');
        a.className = 'item' + (file === current ? ' active' : '');
        a.textContent = file;
        a.href = '#';
        a.onclick = (event) => { event.preventDefault(); loadDoc(file); };
        filesWrap.appendChild(a);
      }
    }

    async function loadDocs() {
      status.textContent = 'Sincronizando...';
      const response = await fetch('/api/docs/list');
      const payload = await response.json();
      docs = payload.files || [];
      renderList(search.value || '');
      status.textContent = `${docs.length} documento(s) encontrado(s).`;
      if (!current && docs.length > 0) {
        await loadDoc(docs[0]);
      }
    }

    async function loadDoc(file) {
      if (!file) return;
      current = file;
      status.textContent = 'Carregando conteudo...';
      try {
        const response = await fetch('/api/docs/raw?file=' + encodeURIComponent(file));
        if (!response.ok) {
          throw new Error('Arquivo indisponivel');
        }
        const text = await response.text();
        rawCache.set(file, text);
        docTitle.textContent = file;
        content.innerHTML = renderMarkdown(text);
        meta.textContent = `Arquivo: ${file} • ${formatSize(text)} • UTF-8`;
        status.textContent = 'Pronto';
        renderList(search.value || '');
      } catch (error) {
        status.textContent = error.message || 'Erro';
        content.textContent = error.message || 'Erro';
      }
    }

    copyBtn.addEventListener('click', async () => {
      if (!current || !rawCache.has(current)) return;
      await navigator.clipboard.writeText(rawCache.get(current));
      status.textContent = 'Markdown copiado';
    });

    search.addEventListener('input', () => renderList(search.value || ''));
    reloadBtn.addEventListener('click', () => loadDocs());
    loadDocs();
  </script>
</body>
</html>
HTML;
}

function listMarkdownFiles(?string $docsBase): array
{
    if ($docsBase === false) {
        return [];
    }
    $glob = glob($docsBase . DIRECTORY_SEPARATOR . '*.md');
    if ($glob === false) {
        return [];
    }
    $files = [];
    foreach ($glob as $file) {
        $files[] = basename((string) $file);
    }
    sort($files, SORT_NATURAL | SORT_FLAG_CASE);
    return $files;
}

function sanitizeMarkdownFile(string $name): ?string
{
    $value = trim($name);
    if ($value === '') {
        return null;
    }
    if (preg_match('/^[A-Za-z0-9._-]+\.md$/', $value) !== 1) {
        return null;
    }
    return $value;
}

function jsonResponse(array $data, int $status): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
