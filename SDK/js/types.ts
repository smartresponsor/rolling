export type ScopeType = 'global' | 'tenant' | 'resource';

export interface AccessCheckRequest {
    subjectId: string;
    action: string;
    scopeType: ScopeType;
    tenantId?: string;
    resourceId?: string;
    context?: Record<string, unknown>;
}

export interface Obligation {
    type: string;
    params: Record<string, unknown>;
}

export interface AccessDecision {
    decision: 'ALLOW' | 'DENY';
    reason: string;
    obligations: Obligation[];
    scope?: string | null;
}

export interface BatchRequest {
    requests: AccessCheckRequest[];
}

export interface BatchResult {
    count: number;
    results: AccessDecision[];
}
