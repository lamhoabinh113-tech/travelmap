<?php
/**
 * Cấu hình AI Assistant
 * Bạn có thể nhập mã API Key của Google Gemini hoặc OpenAI vào đây
 * để Trợ lý AI có thể trả lời thông minh và linh hoạt hơn.
 * 
 * Lấy Gemini API Key miễn phí tại: https://aistudio.google.com/app/apikey
 */

// Load private configuration if exists (keeps API keys off GitHub)
$privateConfig = __DIR__ . '/ai_private.php';
if (is_readable($privateConfig)) {
    require_once $privateConfig;
}

// Default fallback env vars (keep empty on GitHub for security)
if (!getenv('GEMINI_API_KEY')) {
    putenv('GEMINI_API_KEY=');
}
if (!getenv('GROQ_API_KEY')) {
    putenv('GROQ_API_KEY=');
}

// Hoặc nếu bạn muốn dùng OpenAI:
// putenv('OPENAI_API_KEY=');
// putenv('OPENAI_MODEL=gpt-4o-mini');
?>
