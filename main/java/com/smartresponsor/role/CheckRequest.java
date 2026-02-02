package com.smartresponsor.role;

import java.util.Map;

public class CheckRequest {
    public String subject;
    public String action;
    public Scope scope;
    public Map<String,Object> context;
}
