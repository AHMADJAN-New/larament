<?php

declare(strict_types=1);

return [
    'api_key' => env('OPENAI_API_KEY'),

    'default_model' => env('OPENAI_MODEL', 'gpt-4o-mini'),

    'prompts' => [
        'refine' => [
            'agenda' => 'Expand this meeting agenda into clear, ordered points with sub-points where useful. Preserve the original language. Use plain text only: no markdown, no asterisks (** or *), no underscores, no formatting symbols. Output only the refined text, no commentary.',
            'notes' => 'Turn these rough meeting notes into well-structured meeting minutes. Preserve all facts and decisions. Use clear paragraphs and bullet points where appropriate. Use plain text only: no markdown, no asterisks (** or *), no underscores, no formatting symbols. Output only the refined text, no commentary.',
            'decisions_text' => "Format this as numbered decisions. For each decision: state the decision clearly. Include responsible person and deadline ONLY if they are explicitly mentioned. If missing, OMIT them completely (do not write placeholders like 'not specified'). Use plain text only: no markdown, no asterisks (** or *), no underscores, no formatting symbols. Output only the refined text, no commentary.",
            'followup_text' => "When meeting context is provided, use it to make the follow-up list consistent with the agenda, notes and decisions. List follow-up items clearly as bullets. Include responsible person and due date ONLY if explicitly mentioned. If missing, OMIT them completely (do not write placeholders like 'not specified'). Use plain text only: no markdown, no asterisks (** or *), no underscores, no formatting symbols. Output only the refined text, no commentary.",
            'description' => 'When meeting context is provided (agenda, notes, decisions, follow-up), use it to refine this task so it is clear and consistent with the meeting. Expand into clear, actionable steps. Preserve the original language. Do not invent owners or dates. Use plain text only: no markdown, no asterisks (** or *), no underscores, no formatting symbols. Output only the refined text, no commentary.',
        ],
        'generate_from_notes' => [
            'decisions_text' => "Based on these meeting notes, extract and list all decisions made. Format as numbered decisions. Include responsible person and deadline ONLY if explicitly mentioned. If missing, OMIT them completely (do not write placeholders like 'not specified'). Use plain text only: no markdown, no asterisks (** or *), no underscores, no formatting symbols. Output only the list, no commentary.",
            'followup_text' => "Based on these meeting notes, extract all follow-up tasks and action items. Output bullets with the task text only. If an owner or due date is explicitly mentioned, you may include it, otherwise OMIT it completely (do not write placeholders like 'not specified'). Use plain text only: no markdown, no asterisks (** or *), no underscores, no formatting symbols. Output only the list, no commentary.",
        ],
    ],

    'models' => [
        'gpt-4o-mini' => 'GPT-4o Mini',
        'gpt-4o' => 'GPT-4o',
        'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
    ],

    'language_hints' => [
        'auto' => 'Auto-detect',
        'pashto' => 'Pashto',
        'english' => 'English',
    ],
];
