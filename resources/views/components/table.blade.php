@props(['striped' => false])

<table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200']) }}>
    @if(isset($header))
        <thead class="bg-gray-50">
            {{ $header }}
        </thead>
    @endif

    @if(isset($body))
        {{ $body }}
    @endif

    @if(isset($footer))
        {{ $footer }}
    @endif
</table> 