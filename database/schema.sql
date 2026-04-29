CREATE TABLE IF NOT EXISTS repositories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    external_id INTEGER NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    owner VARCHAR(255) NOT NULL,
    html_url VARCHAR(255),
    default_branch VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS push_events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    repository_id INTEGER NOT NULL,
    event_id VARCHAR(255) NOT NULL UNIQUE,
    ref VARCHAR(255) NOT NULL,
    before_sha VARCHAR(80),
    after_sha VARCHAR(80),
    compare_url VARCHAR(500),
    pushed_at DATETIME,
    pusher_name VARCHAR(255),
    pusher_email VARCHAR(255),
    raw_payload TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(repository_id) REFERENCES repositories(id)
);

CREATE TABLE IF NOT EXISTS commits (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    push_event_id INTEGER NOT NULL,
    sha VARCHAR(80) NOT NULL,
    message TEXT,
    author_name VARCHAR(255),
    author_email VARCHAR(255),
    committed_at DATETIME,
    url VARCHAR(500),
    added_files TEXT,
    modified_files TEXT,
    removed_files TEXT,
    is_distinct BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(push_event_id) REFERENCES push_events(id)
);

CREATE TABLE IF NOT EXISTS devlogs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    push_event_id INTEGER NOT NULL,
    generated_by VARCHAR(50) NOT NULL,
    model VARCHAR(100),
    summary_text TEXT NOT NULL,
    raw_response TEXT,
    usage_info TEXT,
    created_at DATETIME,
    FOREIGN KEY(push_event_id) REFERENCES push_events(id)
);
