-- PostgreSQL table for HMAC anti-replay
CREATE TABLE IF NOT EXISTS replay_nonce
(
    nonce
    TEXT
    PRIMARY
    KEY,
    expires_ts
    BIGINT
    NOT
    NULL
);
CREATE INDEX IF NOT EXISTS idx_replay_nonce_expires ON replay_nonce(expires_ts);
