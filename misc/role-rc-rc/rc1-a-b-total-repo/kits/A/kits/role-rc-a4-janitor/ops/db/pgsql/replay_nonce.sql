CREATE TABLE IF NOT EXISTS replay_nonce
(
    nonce
    TEXT
    PRIMARY
    KEY,
    created_ts
    TIMESTAMPTZ
    NOT
    NULL
    DEFAULT
    NOW
(
),
    expires_ts TIMESTAMPTZ NOT NULL
    );
CREATE INDEX IF NOT EXISTS idx_replay_expires ON replay_nonce(expires_ts);
