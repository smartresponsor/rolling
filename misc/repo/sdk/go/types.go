package role

type Scope struct {
	Key       string `json:"key"`
	Type      string `json:"type"`       // global|tenant|resource
	TenantId  *string `json:"tenantId,omitempty"`
	ResourceId *string `json:"resourceId,omitempty"`
}

type Obligation struct {
	Type   string                 `json:"type"`
	Params map[string]interface{} `json:"params"`
}

type Decision struct {
	Allow       bool         `json:"allow"`
	Reason      string       `json:"reason"`
	Obligations []Obligation `json:"obligations"`
	Rev         string       `json:"rev"`
}

type CheckRequest struct {
	Subject string                 `json:"subject"`
	Action  string                 `json:"action"`
	Scope   Scope                  `json:"scope"`
	Context map[string]interface{} `json:"context,omitempty"`
}
