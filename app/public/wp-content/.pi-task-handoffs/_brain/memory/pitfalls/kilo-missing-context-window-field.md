---
type: pitfall
title: Some Kilo Code versions miss Context Window field in custom provider UI
severity: low
domain: client-config
source_tasks: [session-2026-05-30 Kilo provider setup]
hit_count: 1
detection_signature: |
  User reports: "không thấy field Context Window / Max Output Tokens" trong
  Kilo custom provider settings. Provider works but no % context display.
last_updated: 2026-05-30
---

# Kilo Code — Context Window field missing trong custom provider UI

## Symptom
User vào Kilo Settings → Providers → Custom Provider → KHÔNG thấy field "Context Window" hay "Max Output Tokens". Provider làm việc bình thường nhưng:
- Counter hiện số tuyệt đối (vd "2") thay vì % context used
- Auto-condense có thể sai timing (không biết model limit)

## Root cause
Tính năng "Model Configuration" trong Kilo OpenAI-compatible provider **version-dependent**:
- Kilo Code v4.0+ có field
- Một số fork/older versions không có
- Hoặc field bị ẩn sau "Advanced" toggle

## Fix (provider-side workaround)
**KHÔNG cần fix Kilo** — fix bằng cách proxy expose context_length trong `/v1/models`:

```ts
// Luna-Proxy server.ts /v1/models handler
return {
    id, object: 'model', owned_by: 'qwen-ai', name: model.name,
    context_length: 262144,
    context_window: 262144,
    max_context_length: 262144,
    max_model_len: 262144,
    max_output_tokens: 65536
};
```

Kilo (newer) reads these fields → auto-knows limits → displays % correctly.

## Prevention
1. Luna-Proxy `/v1/models` luôn include 4 alias context fields (đã implement 2026-05-30)
2. Client compatibility test: thử 2-3 versions Kilo + Cline → confirm field rendered

## Alternative if user wants manual fix
1. Update Kilo to latest version (changelog mentions new Model Configuration)
2. Hoặc chấp nhận no-%-display (functionality still works)

## See also
- Luna-Proxy `src/server.ts /v1/models handler`
- Kilo docs: https://kilo.ai/docs/ai-providers/openai-compatible
