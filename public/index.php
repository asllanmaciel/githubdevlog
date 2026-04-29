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
    renderDocsPage($docsBase);
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

function renderDocsPage(?string $docsBase): void
{
    if ($docsBase === false) {
        http_response_code(500);
        echo 'Pasta de docs nao encontrada.';
        return;
    }

    $files = listMarkdownFiles($docsBase);
    $filesJson = htmlspecialchars(json_encode($files, JSON_UNESCAPED_UNICODE), ENT_QUOTES);
    $initial = htmlspecialchars($files[0] ?? '', ENT_QUOTES);

    header('Content-Type: text/html; charset=utf-8');
    echo <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>GitHub DevLog AI | Docs</title>
  <style>
    :root{
      --bg: radial-gradient(circle at 15% 15%, #0f172a 0%, #020617 44%, #020617 100%);
      --panel: #ffffff;
      --text: #e2e8f0;
      --muted: #94a3b8;
      --line: rgba(148,163,184,.2);
      --brand1: #6366f1;
      --brand2: #22d3ee;
      --brand3: #34d399;
      --card: rgba(255,255,255,.05);
      --card-2: rgba(15,23,42,.6);
    }
    *{box-sizing:border-box}
    html,body{margin:0;height:100%;font-family:Inter, "Segoe UI", Roboto, Arial, sans-serif;color:var(--text)}
    body{background:var(--bg);min-height:100%;display:flex;flex-direction:column}
    .top{
      padding:22px 24px;
      background: linear-gradient(120deg, rgba(99,102,241,.22), rgba(34,211,238,.16));
      border-bottom:1px solid var(--line);
      backdrop-filter: blur(8px);
      box-shadow:0 10px 24px rgba(2,6,23,.45);
      display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;
    }
    .top h1{margin:0;font-size:22px;letter-spacing:.02em}
    .top p{margin:0;color:var(--muted)}
    .main{flex:1;display:grid;grid-template-columns:320px 1fr;gap:16px;padding:16px;min-height:0}
    .panel{
      background: linear-gradient(180deg, var(--card), var(--card-2));
      border:1px solid var(--line);
      border-radius:18px;
      padding:14px;
      box-shadow: 0 12px 30px rgba(2,6,23,.35);
      backdrop-filter: blur(6px);
      min-height:0;
    }
    .search{
      width:100%;
      background: rgba(15,23,42,.75);
      color:var(--text);
      border:1px solid var(--line);
      border-radius:12px;
      padding:11px 12px;
      outline:none;
      margin-bottom:12px;
    }
    .search:focus{border-color:#7dd3fc;box-shadow:0 0 0 4px rgba(125,211,252,.15)}
    .file-item{
      display:block;
      text-decoration:none;
      color:var(--text);
      padding:10px 12px;
      border-radius:10px;
      margin-bottom:6px;
      transition:all .18s ease;
      border:1px solid transparent;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
      cursor:pointer;
    }
    .file-item:hover{background:rgba(34,211,238,.12);border-color:rgba(125,211,252,.5)}
    .file-item.active{
      background:linear-gradient(110deg, rgba(99,102,241,.26), rgba(34,211,238,.26));
      border-color:#60a5fa;
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.15);
    }
    .toolbar{
      display:flex;gap:8px;align-items:center;justify-content:space-between;
      margin-top:10px;margin-bottom:12px;
    }
    .btn{
      border:none;color:white;border-radius:999px;padding:8px 12px;cursor:pointer;
      background:linear-gradient(90deg,var(--brand1),var(--brand2));
      font-weight:600;
    }
    .btn:hover{filter:brightness(1.08)}
    .viewer{display:flex;flex-direction:column;min-height:0}
    .doc-title{
      margin:0 0 10px 0;font-size:28px;line-height:1.2;
      background: linear-gradient(90deg,#fff,#93c5fd,#5eead4);
      -webkit-background-clip:text;color:transparent;
    }
    .doc-meta{
      display:flex;gap:12px;flex-wrap:wrap;color:#cbd5e1;font-size:13px;margin-bottom:12px
    }
    .content{
      border-radius:14px;
      background:rgba(15,23,42,.75);
      border:1px solid var(--line);
      padding:20px;
      overflow:auto;
      flex:1;
      min-height:0;
    }
    .content h1,.content h2,.content h3{line-height:1.2;color:#f8fafc}
    .content h1{font-size:28px;border-bottom:1px solid var(--line);padding-bottom:8px;margin-top:0}
    .content h2{font-size:22px;margin-top:26px}
    .content h3{font-size:18px}
    .content p,.content li,.content td,.content th{line-height:1.6;color:#dbeafe}
    .content a{color:#67e8f9}
    .content pre{
      background:#020617;padding:14px;border-radius:12px;
      border:1px solid rgba(148,163,184,.3);overflow:auto;
    }
    .content code{font-family: ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,monospace;font-size:13px}
    .content blockquote{
      margin:14px 0;padding:10px 14px;border-left:4px solid #60a5fa;
      background:rgba(56,189,248,.12);color:#bfdbfe;
    }
    .content table{border-collapse:collapse;width:100%}
    .content th,.content td{border:1px solid var(--line);padding:8px}
    .content th{background:rgba(148,163,184,.14)}
    .empty{
      height:100%;display:grid;place-items:center;color:var(--muted);
      border:1px dashed var(--line);border-radius:12px;
    }
    @media (max-width: 920px){
      .main{grid-template-columns:1fr}
      .panel{max-height:280px;overflow:auto}
    }
    .fade-in{animation:fadeIn .35s ease}
    @keyframes fadeIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
  </style>
</head>
<body>
  <header class="top">
    <div>
      <h1>GitHub DevLog AI</h1>
      <p>Documentação viva dos módulos e decisões técnicas</p>
    </div>
    <div>
      <button class="btn" id="refresh">Recarregar</button>
    </div>
  </header>
  <section class="main">
    <aside class="panel">
      <input id="search" class="search" placeholder="Buscar arquivo .md" />
      <div id="list"></div>
    </aside>
    <section class="panel viewer">
      <h2 id="title" class="doc-title">Selecione um documento</h2>
      <div class="doc-meta" id="meta"></div>
      <div class="toolbar">
        <span id="status" style="color:#cbd5e1"></span>
        <button class="btn" id="copyRaw">Copiar markdown bruto</button>
      </div>
      <article id="content" class="content empty">Carregue um arquivo para visualizar.</article>
    </section>
  </section>
<script>
  const initial = "{$initial}";
  let docs = [];
  let rawCache = {};

  const listWrap = document.getElementById('list');
  const title = document.getElementById('title');
  const content = document.getElementById('content');
  const status = document.getElementById('status');
  const meta = document.getElementById('meta');
  const search = document.getElementById('search');
  const refresh = document.getElementById('refresh');
  const copyRaw = document.getElementById('copyRaw');
  let current = '';

  function escapeHtml(value){
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;');
  }

  function basicMarkdown(md){
    let html = escapeHtml(md);
    html = html
      .replace(/^(#{1,6})\s+(.*)$/gm, (m, hashes, title) => {
        const level = hashes.length;
        return `<h${level}>${title.trim()}</h${level}>`;
      })
      .replace(/^---$/gm, '<hr/>')
      .replace(/!\[([^\]]*)\]\(([^)]+)\)/g, '<img alt="$1" src="$2" style="max-width:100%;border-radius:12px;" />')
      .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>')
      .replace(/`([^`]+)`/g, '<code>$1</code>')
      .replace(/^\s*[-*]\s+(.*)$/gm, '<li>$1</li>')
      .replace(/(<li>.*(?:\r?\n<li>.*)*)/g, '<ul>$1</ul>')
      .replace(/^\s*>\s?(.*)$/gm, '<blockquote>$1</blockquote>')
      .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
      .replace(/\*([^*]+)\*/g, '<em>$1</em>');

    html = html.replace(/```([\\s\\S]*?)```/g, (_, code) => `<pre><code>${escapeHtml(code.trim())}</code></pre>`);
    return `<div>${html.replace(/\\n/g, '<br/>')}</div>`;
  }

  function renderList(filter = '') {
    const f = filter.toLowerCase();
    const filtered = docs.filter((file) => file.toLowerCase().includes(f));
    listWrap.innerHTML = '';
    if (filtered.length === 0){
      listWrap.innerHTML = '<p style="color:#94a3b8;padding:10px">Nenhum arquivo encontrado.</p>';
      return;
    }
    filtered.forEach((file) => {
      const el = document.createElement('a');
      el.className = 'file-item';
      el.textContent = file;
      el.href = '#';
      el.onclick = (event) => { event.preventDefault(); openFile(file); };
      if (file === current){
        el.classList.add('active');
      }
      listWrap.appendChild(el);
    });
  }

  async function openFile(file){
    if (!file){ return; }
    current = file;
    status.textContent = 'Carregando...';
    try{
      const response = await fetch('/api/docs/raw?file=' + encodeURIComponent(file));
      if (!response.ok){
        throw new Error('Arquivo indisponível');
      }
      const markdown = await response.text();
      rawCache[file] = markdown;
      title.textContent = file;
      title.className = 'doc-title fade-in';
      content.className = 'content fade-in';
      content.innerHTML = basicMarkdown(markdown);
      meta.textContent = `Arquivo: ${file} • UTF-8 • ${Math.round((new Blob([markdown]).size/1024*100)/100)} KB`;
      status.textContent = 'Pronto';
      document.querySelectorAll('.file-item').forEach((node) => {
        node.classList.toggle('active', node.textContent === file);
      });
    } catch (error){
      title.textContent = file;
      content.textContent = error.message;
      status.textContent = 'Erro ao carregar';
    }
  }

  async function loadDocs(){
    status.textContent = 'Sincronizando docs...';
    const response = await fetch('/api/docs/list');
    const data = await response.json();
    docs = data.files || [];
    renderList(search.value || '');
    status.textContent = `${docs.length} arquivo(s) disponível(is)`;
    if (docs.length > 0) {
      await openFile(initial || docs[0]);
    }
  }

  refresh.addEventListener('click', loadDocs);
  search.addEventListener('input', () => renderList(search.value || ''));

  copyRaw.addEventListener('click', () => {
    if (current && rawCache[current]) {
      navigator.clipboard.writeText(rawCache[current]).then(() => {
        status.textContent = 'Markdown copiado';
      });
    }
  });

  loadDocs();
</script>
</body>
</html>
HTML;
}

function listMarkdownFiles(?string $docsBase): array
{
    if ($docsBase === null || $docsBase === false) {
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
