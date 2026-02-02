CREATE TABLE IF NOT EXISTS replay_nonce
(
    nonce
    TEXT
    PRIMARY
    KEY,
    created_ts
    INTEGER
    NOT
    NULL,
    expires_ts
    INTEGER
    NOT
    NULL
);
CREATE INDEX IF NOT EXISTS idx_replay_expires ON replay_nonce(expires_ts);
