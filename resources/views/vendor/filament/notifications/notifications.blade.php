@php
    use Filament\Support\Enums\Alignment;
    use Filament\Support\Enums\VerticalAlignment;
@endphp

<div>
    <div
        @class([
            'fi-no fixed inset-4 z-50 mx-auto flex gap-3 pointer-events-auto',
            match (static::$alignment) {
                Alignment::Start, Alignment::Left => 'items-start',
                Alignment::Center => 'items-center',
                Alignment::End, Alignment::Right => 'items-end',
                default => null,
            },
            match (static::$verticalAlignment) {
                VerticalAlignment::Start => 'flex-col-reverse justify-end',
                VerticalAlignment::End => 'flex-col justify-end',
                VerticalAlignment::Center => 'flex-col justify-center',
            },
        ])
        style="position: fixed; top: 1rem; right: 1rem; max-width: 24rem; z-index: 9999;"
        role="status"
    >
        @foreach ($notifications as $notification)
            {{ $notification }}
        @endforeach
    </div>

    @if ($broadcastChannel = $this->getBroadcastChannel())
        <x-filament-notifications::echo :channel="$broadcastChannel" />
    @endif
</div> 