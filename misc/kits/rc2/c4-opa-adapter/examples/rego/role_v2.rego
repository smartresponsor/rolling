package role.v2

default allow := false

# Input shape (example):
# input.subject.id : string
# input.action : string
# input.scope.key : string
# input.context : object (free form)

allow {
  input.action == "message.read"
  glob_allow[input.subject.id]
}

glob_allow := { "u1", "admin" }

# Structured decision
decision := {"allow": allow, "reason": reason, "obligations": obligations}

reason := r {
  allow
  r := "ok"
} else := r {
  not allow
  r := "no_rule"
}

obligations := o {
  not allow
  o := [{"type":"degraded","params":{"source":"opa"}}]
} else := o {
  allow
  o := []
}
