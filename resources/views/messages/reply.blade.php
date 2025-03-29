<x-app-layout>
    @section('title', 'Odpowiedz na wiadomość')
    @section('header', 'Odpowiedz na wiadomość')

    <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6">
        <!-- Oryginalna wiadomość -->
        <div class="bg-gray-50 rounded-lg p-4 mb-6 border-l-4 border-blue-500">
            <h3 class="font-medium text-gray-900 mb-2">Oryginalna wiadomość:</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                <div>
                    <p class="text-xs text-gray-500">Od:</p>
                    <p class="text-sm font-medium">{{ $replyTo->sender->name }}</p>
                </div>
                
                <div>
                    <p class="text-xs text-gray-500">Data:</p>
                    <p class="text-sm">{{ $replyTo->created_at->format('d.m.Y H:i') }}</p>
                </div>
            </div>
            
            <div class="mb-3">
                <p class="text-xs text-gray-500">Temat:</p>
                <p class="text-sm font-medium">{{ $replyTo->subject }}</p>
            </div>
            
            <div>
                <p class="text-xs text-gray-500 mb-1">Treść:</p>
                <div class="prose prose-sm max-w-none text-gray-700 text-sm bg-white p-3 rounded border border-gray-200">
                    {!! nl2br(e(Str::limit($replyTo->content, 300))) !!}
                    
                    @if(strlen($replyTo->content) > 300)
                        <a href="{{ route('messages.show', $replyTo->id) }}" class="text-xs text-blue-600 hover:text-blue-800">
                            Pokaż całą wiadomość
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Formularz odpowiedzi -->
        <h3 class="font-medium text-gray-900 mb-4">Twoja odpowiedź:</h3>
        
        <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <input type="hidden" name="replied_to_id" value="{{ $replyTo->id }}">
            <input type="hidden" name="recipient_id" value="{{ $recipient->id }}">
            
            <!-- Temat -->
            <div class="mb-4">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Temat</label>
                <input type="text" name="subject" id="subject" required
                       class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       value="{{ $subject }}">
                @error('subject')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Treść -->
            <div class="mb-4">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Treść wiadomości</label>
                <textarea name="content" id="content" rows="6" required
                         class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">{{ old('content') }}</textarea>
                @error('content')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Załączniki -->
            <div class="mb-6">
                <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1">Załączniki (opcjonalnie)</label>
                <div class="flex items-center">
                    <label for="attachments" class="cursor-pointer bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-paperclip mr-2"></i> Wybierz pliki
                    </label>
                    <input type="file" name="attachments[]" id="attachments" multiple 
                           class="sr-only" onchange="updateFileList(this)">
                    <span id="selected-files" class="ml-3 text-sm text-gray-500">Nie wybrano plików</span>
                </div>
                <ul id="file-list" class="mt-2 space-y-1 text-sm text-gray-500"></ul>
                @error('attachments')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                @error('attachments.*')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Przyciski -->
            <div class="flex justify-between">
                <a href="{{ route('messages.show', $replyTo->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-arrow-left mr-2"></i> Wróć do wiadomości
                </a>
                
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <i class="fas fa-paper-plane mr-2"></i> Wyślij odpowiedź
                </button>
            </div>
        </form>
    </div>

    <script>
    function updateFileList(input) {
        const fileList = document.getElementById('file-list');
        const selectedFilesText = document.getElementById('selected-files');
        
        fileList.innerHTML = '';
        
        if (input.files.length > 0) {
            selectedFilesText.textContent = `Wybrano ${input.files.length} ${input.files.length === 1 ? 'plik' : 'pliki'}`;
            
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const li = document.createElement('li');
                li.className = 'flex items-center text-sm';
                
                const icon = document.createElement('i');
                icon.className = 'fas fa-file mr-2 text-blue-500';
                
                const fileName = document.createElement('span');
                fileName.textContent = file.name;
                
                const fileSize = document.createElement('span');
                fileSize.className = 'ml-2 text-gray-400';
                fileSize.textContent = `(${formatFileSize(file.size)})`;
                
                li.appendChild(icon);
                li.appendChild(fileName);
                li.appendChild(fileSize);
                fileList.appendChild(li);
            }
        } else {
            selectedFilesText.textContent = 'Nie wybrano plików';
        }
    }

    function formatFileSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        else return (bytes / 1048576).toFixed(1) + ' MB';
    }
    </script>
</x-app-layout> 