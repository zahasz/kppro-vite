<x-app-layout>
    @section('title', $message->subject)
    @section('header', 'Wiadomość: ' . $message->subject)

    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
        <!-- Przyciski akcji -->
        <div class="flex justify-between mb-6">
            <div>
                <a href="{{ route('messages.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Wróć do wiadomości
                </a>
            </div>
            
            <div class="flex space-x-3">
                @if($message->sender_id != auth()->id())
                    <a href="{{ route('messages.reply', $message->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-reply mr-2"></i> Odpowiedz
                    </a>
                @endif
                
                <form action="{{ route('messages.destroy', $message->id) }}" method="POST" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Czy na pewno chcesz usunąć tę wiadomość?')">
                        <i class="fas fa-trash mr-2"></i> Usuń
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Informacje o wiadomości -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Od:</p>
                    <div class="flex items-center mt-1">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            {{ substr($message->sender->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $message->sender->name }}</p>
                            <p class="text-sm text-gray-500">{{ $message->sender->email }}</p>
                        </div>
                    </div>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500">Do:</p>
                    <div class="flex items-center mt-1">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            {{ substr($message->recipient->name, 0, 1) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900">{{ $message->recipient->name }}</p>
                            <p class="text-sm text-gray-500">{{ $message->recipient->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <p class="text-sm text-gray-500">Temat:</p>
                <p class="text-base font-medium">{{ $message->subject }}</p>
            </div>
            
            <div class="mt-2">
                <p class="text-sm text-gray-500">Data:</p>
                <p class="text-sm">{{ $message->created_at->format('d.m.Y H:i') }}</p>
            </div>
            
            @if($message->replied_to_id)
                <div class="mt-2">
                    <p class="text-sm text-gray-500">W odpowiedzi na:</p>
                    <a href="{{ route('messages.show', $message->replied_to_id) }}" class="text-sm text-blue-600 hover:text-blue-800">
                        <i class="fas fa-reply-all mr-1"></i> Poprzednia wiadomość
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Treść wiadomości -->
        <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-gray-900 mb-2">Treść wiadomości:</h3>
            <div class="prose prose-sm max-w-none text-gray-800">
                {!! nl2br(e($message->content)) !!}
            </div>
        </div>
        
        <!-- Załączniki -->
        @if(!empty($message->attachments))
            <div class="border border-gray-200 rounded-lg p-4 mb-6">
                <h3 class="font-medium text-gray-900 mb-2">Załączniki:</h3>
                <ul class="divide-y divide-gray-200">
                    @foreach($message->attachments as $index => $attachment)
                        <li class="py-2 flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-paperclip text-gray-400 mr-2"></i>
                                <span class="text-sm text-gray-600">{{ $attachment['name'] }}</span>
                                <span class="text-xs text-gray-400 ml-2">({{ formatFileSize($attachment['size']) }})</span>
                            </div>
                            <a href="{{ route('messages.download', ['message' => $message->id, 'attachment' => $index]) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                <i class="fas fa-download mr-1"></i> Pobierz
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Odpowiedzi -->
        @if($replies->count() > 0)
            <div class="mt-8">
                <h3 class="font-medium text-gray-900 text-lg mb-4">Odpowiedzi ({{ $replies->count() }}):</h3>
                
                <div class="space-y-6">
                    @foreach($replies as $reply)
                        <div class="border border-gray-200 rounded-lg p-4 @if($reply->sender_id == auth()->id()) bg-blue-50 border-blue-200 @endif">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        {{ substr($reply->sender->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $reply->sender->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $reply->created_at->format('d.m.Y H:i') }}</p>
                                    </div>
                                </div>
                                
                                <a href="{{ route('messages.show', $reply->id) }}" class="text-xs text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-external-link-alt mr-1"></i> Zobacz pełną wiadomość
                                </a>
                            </div>
                            
                            <div class="prose prose-sm max-w-none text-gray-800 ml-11">
                                {!! nl2br(e(Str::limit($reply->content, 200))) !!}
                                
                                @if(strlen($reply->content) > 200)
                                    <a href="{{ route('messages.show', $reply->id) }}" class="text-xs text-blue-600 hover:text-blue-800">
                                        Czytaj więcej
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @php
    function formatFileSize($bytes) {
        if ($bytes < 1024) return $bytes . ' B';
        else if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        else return round($bytes / 1048576, 1) . ' MB';
    }
    @endphp
</x-app-layout> 