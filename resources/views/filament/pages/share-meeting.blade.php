<x-filament-panels::page>
    @if($meeting && $shareLink)
        @include('share.meeting-content', [
            'meeting' => $meeting,
            'shareLink' => $shareLink,
            'variant' => $variant,
            'variants' => $variants,
            'message' => $message,
            'whatsAppUrl' => $whatsAppUrl,
            'formAction' => request()->url(),
        ])
    @else
        <p class="text-gray-500 dark:text-gray-400">د مجلس لینک نشته یا ختم شوی.</p>
    @endif
</x-filament-panels::page>
